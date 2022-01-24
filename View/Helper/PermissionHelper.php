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
App::uses('UsersComponent', 'Controller/Component');
/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class PermissionHelper extends Helper {

	var $helpers = array('Html', 'Session');

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
	}

	// data for task center filtered data file
	function task_center_filtered_data($project_id = null, $user_id = null,$page = 0,$pCount = null,$assigned_user = null,$as_reaction = null,$element_sts = null,$assign_sorting = null,$element_sorting= null,$wsp_sorting = null,$selected_dates = null,$element_title = null,$startdateSort=null,$enddateSort = null,$elementtasktype = null){

		$current_user_id = $this->Session->read('Auth.User.id');
		if( !empty($page) && $page > 0 ){
			$pageOffect = "LIMIT ".$page.", 10 ";
		} else {
			$pageOffect = "LIMIT 0, 10 ";
		}

		if(isset($pCount) && $pCount == 1 ){
			$pageOffect = '';
		}

		// assignment user selected===========================
		$assign_data = " LEFT JOIN element_assignments ON element_assignments.element_id=elements.id ";
		$assign_user_data = '';
		if( isset($assigned_user) && !empty($assigned_user) ){
			$assign_user_data = " AND element_assignments.assigned_to = $assigned_user ";
		}

		//element assign reaction ============================
		$assign_reaction = '';

		if( isset($as_reaction) && $as_reaction == 5 ){
			//waiting
			$assign_reaction = ' AND element_assignments.reaction = 0';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 1 ){
			//accept
			$assign_reaction = ' AND element_assignments.reaction = 1';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 2 ){
			//not accept but start working
			$assign_reaction = ' AND element_assignments.reaction = 2';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 3 ){
			//disengaged
			$assign_reaction = ' AND element_assignments.reaction = 3';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 4 ){
			//disengaged
			$assign_reaction = '  AND ( element_assignments.element_id is null ) ';
		}
		//============== Element Status  =======================
		//echo $assign_reaction; die;
		$sts_query = '';
		$ele_sts_arr = [];
		if( isset($element_sts) && !empty($element_sts) ){

			foreach($element_sts as $ele_sts_val){

				if( $ele_sts_val == 'CMP' ){

					$ele_sts_arr[] = ' OR (elements.sign_off=1 and elements.date_constraints=1 ) ';
				}

				if( $ele_sts_val == 'PRG' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'PND' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}


				if( $ele_sts_val == 'OVD' ){
					$ele_sts_arr[] = ' OR (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'NON' ){
					$ele_sts_arr[] = ' OR ( elements.date_constraints=0 ) ';

				}
			}

		}

		if( isset($ele_sts_arr) && !empty($ele_sts_arr) ){

			$ele_sts_arr[0] = str_replace("OR"," ",$ele_sts_arr[0]);;
			$sts_query .= ' AND (';
			$sts_query .= implode(" ",$ele_sts_arr);

			$sts_query .= ' ) ';
		}

		$orderby = " elements.start_date DESC ";
		//$assign_sorting = null,$element_status_sorting= null,$wsp_sorting
		if( isset($assign_sorting) && !empty($assign_sorting) ){
			$orderby = " element_assignments.reaction $assign_sorting ";
		}
		if( isset($element_sorting) && !empty($element_sorting) ){
			$orderby = " elements.title $element_sorting ";
		}
		if( isset($wsp_sorting) && !empty($wsp_sorting) ){
			$orderby = " workspaces.title $wsp_sorting ";
		}
		$selectedDates = '';

		if( isset($selected_dates) && !empty($selected_dates) ){


			$selectedDates .= ' AND ( ';
			$dateString = explode(' - ',$selected_dates);

			$startDate = date('Y-m-d',strtotime($dateString[0]));
			//$endDate = date('Y-m-d',strtotime($dateString[1]));

			$endDate = ( isset($dateString[1]) && !empty($dateString[1]) ) ? date('Y-m-d',strtotime(trim($dateString[1]))) : date('Y-m-d',strtotime($dateString[0]));


			$selectedDates .=" elements.start_date BETWEEN '$startDate' AND '$endDate' OR elements.end_date BETWEEN '$startDate' AND '$endDate' ";

			$selectedDates .= ' ) ';

		}

		$element_search = '';
		if( isset($element_title) && !empty($element_title) ){
			$element_title = addcslashes($element_title,"'");
			$element_search = " AND elements.title like '%".$element_title."%'  ";
		}

		//$startdateSort=null,$enddateSort = null
		$sortbyStartDate = null;
		$currentDate = date('Y-m-d');
		if( isset($startdateSort) && !empty($startdateSort) ){

			if($startdateSort == 'elements'){
			//	$sortbyStartDate = " AND $startdateSort.start_date IS NOT NULL AND $startdateSort.date_constraints = 1 ";
			}else{
			//	$sortbyStartDate = " AND $startdateSort.start_date IS NOT NULL  ";
			}

			/*
			$sortbyStartDate = " AND $startdateSort.start_date >= '$currentDate' AND $startdateSort.start_date IS NOT NULL $dateconstraints ";

			*/

			$orderby = "-$startdateSort.start_date  DESC , $startdateSort.start_date ASC ";
		}

		$sortbyEndDate = null;
		if( isset($enddateSort) && !empty($enddateSort) ){

			if($enddateSort == 'elements'){
			//	$sortbyEndDate = " AND  $enddateSort.end_date IS NOT NULL AND $enddateSort.date_constraints = 1 ";
			}else{
				//$sortbyEndDate = " AND  $enddateSort.end_date IS NOT NULL";
			}
			/*
			$sortbyEndDate = " AND $enddateSort.end_date >= '$currentDate'   AND $enddateSort.end_date IS NOT NULL  $dateconstraints "; */


			$orderby = "-$enddateSort.end_date  DESC ,$enddateSort.end_date ASC ";
		}

		// e($orderby, 1);

		$element_tast_type = '';
		$tasktykpe_conditions = '';
		$taskTyepwhere = '';
		if( isset($elementtasktype) && !empty($elementtasktype) ){
			$element_tast_type = $elementtasktype;
			$tasktykpe_conditions = " left join element_types on
					user_permissions.element_id = element_types.element_id and
					element_types.project_id = $project_id

				 ";

				//right join project_element_types ON element_types.project_id = $project_id
				//project_element_types.id = element_types.type_id and

				$ttquery = " SELECT count(*) as totalgeneral FROM project_element_types WHERE id IN ($element_tast_type) AND title = 'General' ";
				$generaltt = ClassRegistry::init('ProjectElementType')->query($ttquery);
				$extratypecondition = '';
				$taskTyepwhere = '';
				if( isset($generaltt) && isset($generaltt[0][0]['totalgeneral']) && $generaltt[0][0]['totalgeneral'] > 0 ){
					$extratypecondition = "  or ( element_types.element_id is null )  ";
					$taskTyepwhere = " AND ( element_types.type_id IN ($element_tast_type) $extratypecondition ) ";
				} else {
					$taskTyepwhere = " AND element_types.type_id IN ($element_tast_type) $extratypecondition ";
				}

		}


		//======================================================

		//echo $assign_reaction;

		if( isset($user_id) && !empty($user_id) && $current_user_id != $user_id ){

			  $query = "SELECT

							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic,'org_id',user_details.organization_id, 'org_name',user_details.org_name, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_permissions.user_id from user_permissions where user_permissions.element_id = elements.id)  ) as user_detail,


							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic,'org_id',user_details.organization_id, 'org_name',user_details.org_name, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email,'first_name',user_details.first_name, 'last_name',user_details.last_name )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = $user_id ) as selected_assign_user ,

							user_permissions.role,
							user_permissions.project_id,
							user_permissions.permit_add,
							user_permissions.permit_read,
							user_permissions.permit_edit,
							user_permissions.permit_delete,
							user_permissions.permit_copy,
							user_permissions.permit_move,

							workspaces.id as ws_id,
							workspaces.title as ws_title,
							workspaces.start_date as ws_start,
							workspaces.end_date as ws_end,
							workspaces.color_code as ws_code,
							workspaces.sign_off as ws_signoff,
							(select permit_edit from user_permissions where user_permissions.user_id= $current_user_id and user_permissions.project_id = $project_id and user_permissions.workspace_id = b.workspace_id and user_permissions.area_id IS NULL Group BY area_id) as wsp_permit_edit,

							(CASE
								WHEN (workspaces.sign_off=1) THEN 'CMP'
								WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) THEN 'PRG'
								WHEN (DATE(NOW()) < DATE(workspaces.start_date)) THEN 'PND'
								WHEN (DATE(workspaces.end_date) < DATE(NOW())) THEN 'OVD'
								WHEN (DATE(workspaces.start_date) > DATE(NOW())) THEN 'not_started'
								ELSE 'NON'
							END) AS ws_status,

							elements.id as ele_id,
							elements.title as ele_title,
							elements.start_date as ele_start,
							elements.end_date as ele_end,
							elements.color_code as ele_code,
							elements.date_constraints as ele_date_constraints,


							(CASE

								WHEN (elements.sign_off=1) THEN 'completed'
								WHEN ( DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1 ) THEN 'progress'

								WHEN ( DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'not_started'

								WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'overdue'

								WHEN (DATE(elements.start_date) > DATE(NOW()) and elements.sign_off!=1   and elements.date_constraints=1 ) THEN 'not_started'
								ELSE 'not_spacified'
							END) AS ele_status,


							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id,'profile_pic',user_details.profile_pic, 'org_name',user_details.org_name,'org_id',user_details.organization_id, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email,'first_name',user_details.first_name, 'last_name',user_details.last_name	 )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = element_assignments.assigned_to ) as assign_received_user,

							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic,'org_id',user_details.organization_id, 'org_name',user_details.org_name, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = element_assignments.created_by ) as assign_created_user,

							element_assignments.created_by as created_by,
							element_assignments.assigned_to as assigned_to,
							element_assignments.reaction as reaction,
							element_assignments.created as created,
							element_assignments.modified as modified,

							(CASE
								WHEN (element_dependencies.is_critical=1) THEN '1' ELSE '0'
							END) AS dep_is_critical,

							(SELECT JSON_ARRAYAGG((CASE WHEN (dependency=1) THEN 'successor' WHEN (dependency=2) THEN 'predecessor' ELSE 'no' END)) AS ele_status FROM `element_dependancy_relationships` WHERE element_id = user_permissions.element_id ) as dependency_type,


							Reminder.id as rem_id,
							Reminder.user_id as rem_userid,
							Reminder.element_id as rem_elementid,
							Reminder.reminder_date as rem_date,
							Reminder.comments as rem_comments,
							Reminder.created as rem_created,
							Reminder.modified as rem_modified

						FROM
							user_permissions user_permissions

						INNER JOIN user_permissions b on b.element_id = user_permissions.element_id
						INNER JOIN elements on elements.id = user_permissions.element_id

						#LEFT JOIN element_dependencies
							#ON element_dependencies.element_id=elements.id

						LEFT JOIN element_dependencies
							ON element_dependencies.element_id = elements.id
						LEFT JOIN element_dependancy_relationships edr
							ON edr.element_dependancy_id = element_dependencies.id



						LEFT JOIN reminders Reminder
							ON Reminder.element_id=user_permissions.element_id

						INNER JOIN workspaces workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
						$assign_data
						#LEFT JOIN element_assignments
							#ON element_assignments.element_id=elements.id
						INNER JOIN projects
							ON projects.id=user_permissions.project_id

						$tasktykpe_conditions

					WHERE
					(user_permissions.user_id in ($current_user_id) and b.user_id in ($user_id)) and user_permissions.element_id IS NOT NULL and user_permissions.role not in('Sharer','Group Sharer') and user_permissions.project_id = $project_id $assign_user_data $assign_reaction $sts_query $sortbyStartDate $sortbyEndDate $selectedDates $element_search $taskTyepwhere group by user_permissions.element_id order by $orderby $pageOffect ";


		} else {

			 $query = "SELECT


				(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'org_name',user_details.org_name,'org_id',user_details.organization_id, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_permissions.user_id from user_permissions where user_permissions.element_id = elements.id)  ) as user_detail,

				(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'org_name',user_details.org_name,'org_id',user_details.organization_id, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email,'first_name',user_details.first_name, 'last_name',user_details.last_name )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = $current_user_id ) as selected_assign_user,

				elements.id as ele_id,
				elements.title as ele_title,
				elements.start_date as ele_start,
				elements.end_date as ele_end,
				elements.color_code as ele_code,
				elements.date_constraints as ele_date_constraints,

				user_permissions.role,
				user_permissions.project_id,
				user_permissions.permit_add,
				user_permissions.permit_read,
				user_permissions.permit_edit,
				user_permissions.permit_delete,
				user_permissions.permit_copy,
				user_permissions.permit_move,

				workspaces.id as ws_id,
				workspaces.title as ws_title,
				workspaces.start_date as ws_start,
				workspaces.end_date as ws_end,
				workspaces.color_code as ws_code,
				workspaces.sign_off as ws_signoff,
				(select permit_edit from user_permissions where user_permissions.user_id = $current_user_id and user_permissions.project_id = $project_id and user_permissions.workspace_id = workspaces.id and user_permissions.area_id IS NULL Group BY area_id) as wsp_permit_edit,

				(CASE
					WHEN (workspaces.sign_off=1) THEN 'CMP'

					WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) THEN 'PRG'
					WHEN (DATE(NOW()) < DATE(workspaces.start_date)) THEN 'PND'
					WHEN (DATE(workspaces.end_date) < DATE(NOW())) THEN 'OVD'
					WHEN (DATE(workspaces.start_date) > DATE(NOW())) THEN 'not_started'
					ELSE 'NON'
				END) AS ws_status,

				(CASE

					WHEN (elements.sign_off=1) THEN 'completed'

					WHEN ( DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1 ) THEN 'progress'

					WHEN ( DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'not_started'

					WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'overdue'

					WHEN (DATE(elements.start_date) > DATE(NOW()) and elements.sign_off!=1   and elements.date_constraints=1 ) THEN 'not_started'
					ELSE 'not_spacified'

				END) AS ele_status,

				(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'org_name',user_details.org_name,'org_id',user_details.organization_id, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email,'last_name',user_details.last_name,'first_name',user_details.first_name )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = element_assignments.assigned_to ) as assign_received_user,

				(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'org_name',user_details.org_name,'org_id',user_details.organization_id, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id = element_assignments.created_by ) as assign_created_user,

				element_assignments.created_by as created_by,
				element_assignments.assigned_to as assigned_to,
				element_assignments.reaction as reaction,
				element_assignments.created as created,
				element_assignments.modified as modified,
				(CASE
					WHEN (element_dependencies.is_critical=1) THEN '1' ELSE '0'
				END) AS dep_is_critical,

				(SELECT JSON_ARRAYAGG((CASE WHEN (dependency=1) THEN 'successor'       WHEN (dependency=2) THEN 'predecessor' ELSE 'no' END)) AS ele_status FROM element_dependancy_relationships WHERE element_id = user_permissions.element_id ) as dependency_type,




				Reminder.id as rem_id,
				Reminder.user_id as rem_userid,
				Reminder.element_id as rem_elementid,
				Reminder.reminder_date as rem_date,
				Reminder.comments as rem_comments,
				Reminder.created as rem_created,
				Reminder.modified as rem_modified

					FROM `user_permissions`

					INNER JOIN elements
						ON elements.id=user_permissions.element_id

					$assign_data

					#LEFT JOIN element_dependencies
						#ON element_dependencies.element_id=elements.id
					LEFT JOIN element_dependencies
							ON element_dependencies.element_id = elements.id
						LEFT JOIN element_dependancy_relationships edr
							ON edr.element_dependancy_id = element_dependencies.id



					LEFT JOIN reminders Reminder
						ON Reminder.element_id=user_permissions.element_id

					INNER JOIN workspaces
						ON workspaces.id=user_permissions.workspace_id
						 and workspaces.studio_status = 0
					INNER JOIN projects
						ON projects.id=user_permissions.project_id

					$tasktykpe_conditions

					WHERE
						user_permissions.project_id = $project_id and user_permissions.user_id = $current_user_id and user_permissions.element_id is not null $assign_user_data $assign_reaction $sts_query $sortbyStartDate $sortbyEndDate $selectedDates $element_search $taskTyepwhere group by user_permissions.element_id order by $orderby $pageOffect ";

		}
		// pr($query, 1);
		return ClassRegistry::init('UserPermission')->query($query);
	}

	// data for task center filtered data file
	function task_center_filtered_data_count($project_id = null, $user_id = null,$page = 0,$pCount = null,$assigned_user = null,$as_reaction = null,$element_sts = null,$assign_sorting = null,$element_sorting= null,$wsp_sorting = null,$selected_dates = null,$element_title = null,$startdateSort=null,$enddateSort = null,$elementtasktype = null){
		$current_user_id = $this->Session->read('Auth.User.id');


		/*============= Filter ========================*/
		// assignment user selected===========================
		$assign_data = " LEFT JOIN element_assignments ON element_assignments.element_id=elements.id ";
		$assign_user_data = '';
		if( isset($assigned_user) && !empty($assigned_user) ){
			$assign_user_data = " AND element_assignments.assigned_to = $assigned_user ";
		}

		//element assign reaction ============================
		$assign_reaction = '';

		if( isset($as_reaction) && $as_reaction == 5 ){
			//waiting
			$assign_reaction = ' AND element_assignments.reaction = 0';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 1 ){
			//accept
			$assign_reaction = ' AND element_assignments.reaction = 1';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 2 ){
			//not accept but start working
			$assign_reaction = ' AND element_assignments.reaction = 2';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 3 ){
			//disengaged
			$assign_reaction = ' AND element_assignments.reaction = 3';
		}
		if( isset($as_reaction) && !empty($as_reaction) && $as_reaction == 4 ){
			//disengaged
			$assign_reaction = '  AND ( element_assignments.element_id is null ) ';
		}
		//============== Element Status  =======================
		//echo $assign_reaction; die;
		$sts_query = '';
		$ele_sts_arr = [];
		// pr($element_sts); die;
		if( isset($element_sts) && !empty($element_sts) ){

			foreach($element_sts as $ele_sts_val){

				if( $ele_sts_val == 'CMP' ){

					$ele_sts_arr[] = ' OR (elements.sign_off=1 and elements.date_constraints=1 ) ';
				}

				if( $ele_sts_val == 'PRG' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'PND' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}


				if( $ele_sts_val == 'OVD' ){
					$ele_sts_arr[] = ' OR (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'NON' ){
					$ele_sts_arr[] = ' OR ( elements.date_constraints=0 ) ';

				}
			}

		}

		if( isset($ele_sts_arr) && !empty($ele_sts_arr) ){

			$ele_sts_arr[0] = str_replace("OR"," ",$ele_sts_arr[0]);;
			$sts_query .= ' AND (';
			$sts_query .= implode(" ",$ele_sts_arr);

			$sts_query .= ' ) ';
		}

		$orderby = " elements.title DESC ";
		//$assign_sorting = null,$element_status_sorting= null,$wsp_sorting
		if( isset($assign_sorting) && !empty($assign_sorting) ){
			$orderby = " element_assignments.reaction $assign_sorting ";
		}
		if( isset($element_sorting) && !empty($element_sorting) ){
			$orderby = " elements.title $element_sorting ";
		}
		if( isset($wsp_sorting) && !empty($wsp_sorting) ){
			$orderby = " workspaces.title $wsp_sorting ";
		}
		$selectedDates = '';
		if( isset($selected_dates) && !empty($selected_dates) ){

			$selectedDates .= ' AND ( ';
			$dateString = explode(' - ',$selected_dates);
			$startDate = date('Y-m-d',strtotime($dateString[0]));
			$endDate = ( isset($dateString[1]) && !empty($dateString[1]) ) ? date('Y-m-d',strtotime($dateString[1])) : date('Y-m-d',strtotime($dateString[0]));

			$selectedDates .=" elements.start_date BETWEEN '$startDate' AND '$endDate' OR elements.end_date BETWEEN '$startDate' AND '$endDate' ";

			$selectedDates .= ' ) ';

		}

		$element_search = '';
		if( isset($element_title) && !empty($element_title) ){
			//$element_search = " AND elements.title like '%".$element_title."%'";
			$element_title = addcslashes($element_title,"'");
			$element_search = " AND elements.title like '%".$element_title."%'  ";
		}


		$sortbyStartDate = null;
		$currentDate = date('Y-m-d');
		if( isset($startdateSort) && !empty($startdateSort) ){

			if($startdateSort == 'elements'){
				//$sortbyStartDate = " AND $startdateSort.date_constraints = 1 ";
			}

			/*
			$sortbyStartDate = " AND $startdateSort.start_date >= '$currentDate' AND $startdateSort.start_date IS NOT NULL $startdateconstraints ";
			*/
		//	$orderby = " $startdateSort.start_date ASC ";
			$orderby = "-$startdateSort.start_date  DESC ,$startdateSort.start_date ASC ";
		}

		$sortbyEndDate = null;
		if( isset($enddateSort) && !empty($enddateSort) ){

			if($enddateSort == 'elements'){
			//	$sortbyEndDate = " AND $enddateSort.date_constraints = 1 ";
			}
			/*
			$sortbyEndDate = " AND $enddateSort.end_date >= '$currentDate'   AND $enddateSort.end_date IS NOT NULL  $dateconstraints "; */
			$orderby = "-$enddateSort.end_date  DESC ,$enddateSort.end_date ASC ";
		}

		$element_tast_type = '';
		$tasktykpe_conditions = '';
		$taskTyepwhere = '';
		if( isset($elementtasktype) && !empty($elementtasktype) ){
			$element_tast_type = $elementtasktype;
			$tasktykpe_conditions = " left join element_types on
					user_permissions.element_id = element_types.element_id and
					element_types.project_id = $project_id

				 ";

				//right join project_element_types ON element_types.project_id = $project_id
				//project_element_types.id = element_types.type_id and

				$ttquery = " SELECT count(*) as totalgeneral FROM project_element_types WHERE id IN ($element_tast_type) AND title = 'General' ";
				$generaltt = ClassRegistry::init('ProjectElementType')->query($ttquery);
				$extratypecondition = '';
				$taskTyepwhere = '';
				if( isset($generaltt) && isset($generaltt[0][0]['totalgeneral']) && $generaltt[0][0]['totalgeneral'] > 0 ){
					$extratypecondition = "  or ( element_types.element_id is null )  ";
					$taskTyepwhere = " AND ( element_types.type_id IN ($element_tast_type) $extratypecondition ) ";
				} else {
					$taskTyepwhere = " AND element_types.type_id IN ($element_tast_type) $extratypecondition ";
				}

		}

		/*============= ====== ========================*/

		if( isset($user_id) && !empty($user_id) && $current_user_id != $user_id ){

			 $query = "SELECT
							count(user_permissions.element_id)
						FROM
							user_permissions user_permissions

						INNER JOIN user_permissions b on b.element_id = user_permissions.element_id
						INNER JOIN elements on elements.id = user_permissions.element_id

						LEFT JOIN element_dependencies
							ON element_dependencies.element_id=elements.id

						LEFT JOIN reminders Reminder
							ON Reminder.element_id=user_permissions.element_id

						INNER JOIN workspaces workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
						LEFT JOIN element_assignments
							ON element_assignments.element_id=elements.id
						INNER JOIN projects
							ON projects.id=user_permissions.project_id
						 $tasktykpe_conditions
					WHERE

					(user_permissions.user_id in ($current_user_id) and b.user_id in ($user_id)) and user_permissions.element_id IS NOT NULL and user_permissions.role not in('Sharer','Group Sharer') and user_permissions.project_id = $project_id $assign_user_data $assign_reaction $sts_query $sortbyStartDate $sortbyEndDate $selectedDates $element_search $taskTyepwhere order by $orderby

					";


		} else {

			  $query = "SELECT
							count(user_permissions.element_id)
					FROM `user_permissions`

					INNER JOIN elements
						ON elements.id=user_permissions.element_id
					LEFT JOIN element_assignments
						ON element_assignments.element_id=elements.id

					LEFT JOIN element_dependencies
						ON element_dependencies.element_id=elements.id

					LEFT JOIN reminders Reminder
						ON Reminder.element_id=user_permissions.element_id

					INNER JOIN workspaces
						ON workspaces.id=user_permissions.workspace_id
						 and workspaces.studio_status = 0
					INNER JOIN projects
						ON projects.id=user_permissions.project_id

					$tasktykpe_conditions

					WHERE
						user_permissions.project_id = $project_id and user_permissions.user_id = $current_user_id and user_permissions.element_id is not null $assign_user_data $assign_reaction $sts_query $sortbyStartDate $sortbyEndDate $selectedDates $element_search $taskTyepwhere order by $orderby
						";

		}
			//echo $query;
			return ClassRegistry::init('UserPermission')->query($query);
	}


	// element assigned users
	public function elementAssignedUser($project_id = null, $user_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');

		if( isset($user_id) && !empty($user_id) && $current_user_id != $user_id ){
			//, user_permissions.element_id
			 $query = "SELECT

							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic,'org_id',user_details.organization_id, 'org_name',user_details.org_name, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select element_assignments.assigned_to from user_permissions where element_assignments.element_id = elements.id)  ) as user_detail

						FROM
							user_permissions user_permissions
						INNER JOIN user_permissions b on b.element_id = user_permissions.element_id
						INNER JOIN elements on elements.id = user_permissions.element_id

						INNER JOIN workspaces workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
						LEFT JOIN element_assignments
							ON element_assignments.element_id=elements.id
						INNER JOIN projects
							ON projects.id=user_permissions.project_id

					WHERE (user_permissions.user_id IN ($current_user_id) and b.user_id IN ($user_id)) and user_permissions.element_id IS NOT NULL and user_permissions.role not IN ('Sharer','Group Sharer') and user_permissions.project_id IN ($project_id)  and element_assignments.assigned_to > 0 GROUP by element_assignments.assigned_to";


		} else {
			//, user_permissions.element_id
			 $query = "SELECT

							(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic,'org_id',user_details.organization_id, 'org_name',user_details.org_name, 'job_role',user_details.job_role, 'job_title',user_details.job_title, 'email',users.email )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select element_assignments.assigned_to from user_permissions where element_assignments.element_id = elements.id)  ) as user_detail


						FROM `user_permissions`

							INNER JOIN elements
								ON elements.id=user_permissions.element_id
							LEFT JOIN element_assignments
								ON element_assignments.element_id=elements.id

							INNER JOIN workspaces
								ON workspaces.id=user_permissions.workspace_id
								 and workspaces.studio_status = 0
							INNER JOIN projects
								ON projects.id=user_permissions.project_id
					WHERE
						user_permissions.project_id IN ($project_id) and user_permissions.user_id = $current_user_id and user_permissions.element_id is not null and element_assignments.assigned_to > 0  GROUP by element_assignments.assigned_to ";

		}

		return ClassRegistry::init('UserPermission')->query($query);

	}

	public function projectTaskType($projectids = null, $user_id = null){

	$current_user_id = $this->Session->read('Auth.User.id');

	if( isset($user_id) && !empty($user_id) && $current_user_id != $user_id ){

		//a.role, a.project_id, projects.title, a.user_id, b.user_id,

		$sql_old = "SELECT project_element_types.id as ele_type_id, project_element_types.title as ele_type_title

			 FROM user_permissions
			 a inner join user_permissions b on a.project_id = b.project_id

			 INNER JOIN projects on projects.id = a.project_id

			 LEFT JOIN element_types on
				a.element_id=  element_types.element_id
			 right JOIN project_element_types on
				project_element_types.id =  element_types.type_id

			 WHERE (a.user_id = $current_user_id and b.user_id = $user_id) and b.element_id IS NOT NULL and a.role not in('Sharer','Group Sharer')  and a.project_id IN ($projectids) GROUP BY a.project_id order by project_element_types.title ASC ";

					$sql =	"SELECT
						DISTINCT(ET.type_id), project_element_types.title as ele_type_title,
						project_element_types.id as ele_type_id
					FROM `element_types` ET
						inner join project_element_types ON
						project_element_types.id = ET.type_id and
						project_element_types.project_id = $projectids join  user_permissions ON user_permissions.element_id = ET.element_id
						inner join user_permissions b on user_permissions.element_id = b.element_id
					WHERE
						(user_permissions.user_id = $current_user_id and b.user_id = $user_id) and  project_element_types.type_status = 1 and user_permissions.project_id=$projectids or ( project_element_types.title='General' ) order by project_element_types.title ASC ";


		} else {

			$sql = "SELECT
						DISTINCT(ET.type_id), project_element_types.title as ele_type_title,
						project_element_types.id as ele_type_id
					FROM `element_types` ET
						inner join project_element_types ON
						project_element_types.id = ET.type_id and
						project_element_types.project_id = $projectids join  user_permissions ON user_permissions.element_id = ET.element_id
					WHERE
						user_permissions.user_id = $current_user_id and project_element_types.type_status = 1 and user_permissions.project_id=$projectids or ( project_element_types.title='General' ) order by project_element_types.title ASC ";

		}
		//echo $sql;
		return ClassRegistry::init('UserPermission')->query($sql);

	}

	public function currentUserTasks(){
		$current_user_id = $this->Session->read('Auth.User.id');
		$assign_data = " LEFT JOIN element_assignments ON element_assignments.element_id=elements.id ";

		$assign_user_data = " AND element_assignments.assigned_to = $current_user_id ";

		$sql = "SELECT

				(count(distinct(user_permissions.element_id))) total_tasks

				FROM user_permissions

				INNER JOIN elements ON
					elements.id=user_permissions.element_id and
					elements.studio_status = 0 and
					elements.sign_off != 1

				INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
				$assign_data
				WHERE
					user_permissions.user_id IN ($current_user_id)
                    $assign_user_data
				";
			$currentUser = ClassRegistry::init('UserPermission')->query($sql);

			if( isset($currentUser) && !empty($currentUser) && $currentUser[0][0]['total_tasks'] > 0 ){
				return $currentUser[0][0]['total_tasks'];
			} else {
				return 0;
			}

	}

	public function program_project($program_id = null,$status = null, $user_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');

		$sts_query = '';
		if( isset($status) && !empty($status) ){

			//============== Element Status  =============
			$ele_sts_arr = [];
			//$namedparam = '';
			if( $status == 1 ){
				//$namedparam = 'OVD';
				$ele_sts_arr[] = ' OR (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 4 ){
				//$namedparam = 'NON';
				$ele_sts_arr[] = ' OR ( elements.date_constraints=0 ) ';
			}
			if( $status == 5 ){
				//$namedparam = 'CMP';
				$ele_sts_arr[] = ' OR (elements.sign_off=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 7 ){
				//$namedparam = 'PRG';
				$ele_sts_arr[] = ' OR (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 6 ){
				//$namedparam = 'PND';
				$ele_sts_arr[] = ' OR (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}


			/************************************************/

			if( isset($ele_sts_arr) && !empty($ele_sts_arr) ){

				$ele_sts_arr[0] = str_replace("OR"," ",$ele_sts_arr[0]);;
				$sts_query .= ' AND (';
				$sts_query .= implode(" ",$ele_sts_arr);

				$sts_query .= ' ) ';
			}

		}


		$sql = "SELECT
			projects.title,a.role,a.project_id,
			(count(distinct(a.element_id))) total_tasks
			FROM `user_permissions` a

				INNER JOIN projects on projects.id = a.project_id
				INNER JOIN elements on elements.id = a.element_id
				INNER JOIN project_programs on project_programs.project_id = a.project_id AND project_programs.program_id = $program_id
				INNER JOIN workspaces on workspaces.id = a.workspace_id

			WHERE a.user_id = $current_user_id and a.element_id is NOT NULL and workspaces.studio_status = 0 $sts_query GROUP BY a.project_id ORDER BY projects.title ASC";
			/* and (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) */

			//echo $sql;

			return ClassRegistry::init('UserPermission')->query($sql);


	}

	// Task Status
	function task_status($element_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($element_id) && !empty($element_id)  ){

			$sql = "Select
					(CASE

						WHEN (elements.sign_off=1) THEN 'completed'
						WHEN ( DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1 ) THEN 'progress'

						WHEN ( DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'not_started'

						WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'overdue'

						ELSE 'not_spacified'
					END) AS ele_status

					FROM elements

					Where elements.id = $element_id
					";
			return ClassRegistry::init('UserPermission')->query($sql);
		}

	}

	// Workspace Status
	function wsp_status($workspace_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($workspace_id) && !empty($workspace_id)  ){

			$sql = "Select

						(CASE
							WHEN (workspaces.sign_off=1) THEN 'completed'
							WHEN ((DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) and workspaces.sign_off!=1  ) THEN 'progress'
							WHEN ((DATE(NOW()) < DATE(workspaces.start_date)) and workspaces.sign_off!=1 ) THEN 'not_started'
							WHEN ((DATE(workspaces.end_date) < DATE(NOW())) and workspaces.sign_off!=1 ) THEN 'overdue'

							ELSE 'not_spacified'
						END) AS ws_status

						FROM workspaces

					Where workspaces.id = $workspace_id
					";
			return ClassRegistry::init('UserPermission')->query($sql);
		}

	}

	// Project Status
	function project_status($project_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($project_id) && !empty($project_id)  ){

			$sql = "Select

						(CASE
							WHEN (projects.sign_off=1) THEN 'completed'
							WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
							WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
							WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'

							ELSE 'not_spacified'
						END) AS prj_status

						FROM projects


					Where projects.id = $project_id
					";
			return ClassRegistry::init('UserPermission')->query($sql);
		}

	}


	// Project Status
	function worksapce_status($id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($id) && !empty($id)  ){

			$sql = "Select

						(CASE
							WHEN (workspaces.sign_off=1) THEN 'completed'
							WHEN ((DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date))  and workspaces.sign_off!=1 ) THEN 'progress'
							WHEN ((DATE(NOW()) < DATE(workspaces.start_date)) and workspaces.sign_off!=1) THEN 'not_started'
							WHEN ((DATE(workspaces.end_date) < DATE(NOW())) and workspaces.sign_off!=1) THEN 'overdue'

							ELSE 'not_spacified'
						END) AS prj_status

						FROM workspaces


					Where workspaces.id = $id
					";
			return ClassRegistry::init('UserPermission')->query($sql);
		}

	}


	function wsp_of_project($project_id = null, $user_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($project_id) && !empty($project_id)  ){

			$query = "SELECT Workspace.id, Workspace.title, Workspace.start_date, Workspace.end_date, Workspace.created

			FROM `user_permissions`
			inner join project_workspaces on user_permissions.workspace_id = project_workspaces.workspace_id
			inner join workspaces Workspace on
				user_permissions.workspace_id = Workspace.id and
				Workspace.studio_status != 1

			WHERE user_permissions.project_id = $project_id
				and user_permissions.user_id = $current_user_id
				and user_permissions.area_id is null

			group by user_permissions.workspace_id
			order by Workspace.title ";

			return ClassRegistry::init('UserPermission')->query($query);
		}

	}

	function area_of_wsp($workspace_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($workspace_id) && !empty($workspace_id)  ){

			$query = "SELECT Area.id, Area.title

			FROM `areas` AS Area
			WHERE Area.workspace_id = $workspace_id";
			return ClassRegistry::init('Area')->query($query);
		}

	}

	function task_of_area($workspace_id = null, $area_id = null, $fields = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($area_id) && !empty($area_id)  ){

			$selection = '';
			if(!isset($fields) || empty($fields)){
				$selection = 'Element.id,Element.title';
			}
			else{
				$selection = $fields;
			}

			$query = "SELECT $selection

				FROM `user_permissions`

				inner join elements Element on
					user_permissions.element_id = Element.id

				WHERE user_permissions.area_id = $area_id AND user_permissions.element_id IS NOT NULL
				group by user_permissions.element_id ";
			return ClassRegistry::init('UserPermission')->query($query);
		}

	}


	// Project creator
	function projectCreator($project_id = null){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		/* if($count) {
			$selection = 'Count(*) as owners';
		} */
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator') ";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project owners
	function projectOwners($project_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as owners';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project sharer
	function projectSharer($project_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as sharers';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Sharer', 'Group Sharer') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}


	// Project task counters
	function projectTaskCounters($project_id = null ){

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			sum(elements.sign_off=1) AS CMP,
			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD
		FROM
			user_permissions
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0
		INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0

		WHERE
			user_permissions.user_id IN ($user_id) AND
			user_permissions.project_id IN (SELECT project_id from user_permissions where user_permissions.user_id = $user_id and user_permissions.workspace_id is null)   AND user_permissions.project_id = $project_id";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project wsp counters
	function projectWspCounters($project_id = null ){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT

				sum(Workspace.sign_off=1) AS CMP,
				sum(Workspace.start_date IS NULL) AS NON,
				sum( ( DATE(NOW())  BETWEEN DATE(Workspace.start_date) AND DATE(Workspace.end_date) ) AND Workspace.sign_off != 1 ) AS PRG,
				sum((DATE(NOW())<DATE(Workspace.start_date))  and Workspace.sign_off!=1) AS PND,
				sum((DATE(Workspace.end_date)<DATE(NOW()))  and Workspace.sign_off!=1) AS OVD

			FROM user_permissions
			inner join workspaces Workspace on
				user_permissions.workspace_id = Workspace.id and
				Workspace.studio_status != 1
			WHERE user_permissions.project_id = $project_id
				and user_permissions.user_id = $user_id
				 AND user_permissions.area_id is NULL";


		$result =  ClassRegistry::init('UserPermission')->query($query);

		return (isset($result) && !empty($result)) ? $result : array();
	}

	public function project_cost_status_text( $projectbudget = null, $estimatcost = null, $spendcost = null) {

		$costStatus = 'None Set';
		if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = 'None Set';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = 'Estimates Initiated';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0)) {

			$costStatus = 'Spending Initiated';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $estimatcost)) {

			$costStatus = 'Exceeded Estimate';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $estimatcost)) {

			$costStatus = 'Within Estimates';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = 'Budget Set';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {

			$costStatus = 'Over Budget';

		} else if (

			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost <= $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost <= 0))

		) {
			$costStatus = 'On Budget';

		} else if (
			(isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget)) {

			$costStatus = 'On Budget, at Risk';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is first new condition
			$costStatus = 'Over Budget';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is second new condition

			$costStatus = 'Over Budget';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (!isset($spendcost) || $spendcost <= 0)) {

			$costStatus = 'On Budget, at Risk';

		}

		return $costStatus;
	}

	// task owners
	function taskUsers($element_id = null){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_permissions.role";

		$query = "SELECT
			CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_permissions.role

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.element_id = $element_id AND user_permissions.element_id is not null order by role ASC";

		return $total = ClassRegistry::init('UserPermission')->query($query);

	}

	// wsp owners
	function workspaceUserPermissions($user_id = null, $workspace_id = null){
		$selection = " permit_read, permit_edit, permit_delete, permit_add ";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			WHERE user_permissions.user_id = $user_id AND  user_permissions.workspace_id = $workspace_id AND area_id is null LIMIT 0,1";

		return $total = ClassRegistry::init('UserPermission')->query($query);

	}

	// wsp owners
	function workspaceUsers($project_id = null, $workspace_id = null){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.project_id = $project_id AND  user_permissions.workspace_id = $workspace_id AND area_id is null order by role ASC";

		return $total = ClassRegistry::init('UserPermission')->query($query);

	}

	// wsp owners
	function workspaceOwners($project_id = null, $workspace_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as owners';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.project_id = $project_id AND  user_permissions.workspace_id = $workspace_id AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner') AND area_id is null order by role ASC";

		$total = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($total[0][0]['owners']) && !empty($total[0][0]['owners']) ) ? $total[0][0]['owners'] : 0;
	}

	// wsp sharers
	function workspaceSharers($project_id = null, $workspace_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as sharers';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.project_id = $project_id AND  user_permissions.workspace_id = $workspace_id AND user_permissions.role IN ('Sharer', 'Group Sharer') AND area_id is null order by role ASC";

		$total = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($total[0][0]['sharers']) && !empty($total[0][0]['sharers']) ) ? $total[0][0]['sharers'] : 0;
	}

	// wsp task counters
	function wspTaskCounters($project_id = null, $workspace_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			efforts.total_hours,
			efforts.blue_completed_hours,
			efforts.green_remaining_hours,
			efforts.amber_remaining_hours,
			efforts.red_remaining_hours,
			efforts.none_remaining_hours,
			#efforts.remaining_hours_color,
			efforts.change_hours,
			#efforts.remaining_hours,
			wlevel.confidence_level,
			wlevel.confidence_class,
			wlevel.confidence_arrow,
			wlevel.level,
			wlevel.level_count,
			wlevel.wlevel_id,
			sum(elements.sign_off=1) AS CMP,
			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD
		FROM
			user_permissions
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0
		INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN
				(
				    #--------------------------------------------
				    #get level counts
				    SELECT
				        wfl.ws_id as wlevel_id,
				        wfl.level_current as level,
						wfl.level_count as level_count,
						wfl.confidence_class as confidence_class,
						wfl.confidence_level as confidence_level,
						wfl.confidence_arrow as confidence_arrow,
						wfl.confidence_order as confidence_order,
						wfl.confidence_order_asc as confidence_order_asc

				    FROM
				    (
				        SELECT
				            levels.workspace_id AS ws_id,
				            levels.element_id AS tid,
				            round(AVG(levels.level)) as level_current,
							COUNT(levels.level) as level_count,
					(CASE
						WHEN ( round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'Low'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'Medium Low'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'Medium High'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'red'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'orange'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'yellow'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'lowgrey'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'mediumlowgrey'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'mediumhighgrey'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow,

					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 1
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 2
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 3
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 4
						ELSE 0 # not set
					END) AS confidence_order,
                    (CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 4
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 3
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 2
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 1
						ELSE 5 # not set
					END) AS confidence_order_asc


				        FROM
				            element_levels levels
						INNER JOIN elements on elements.id = levels.element_id

				        WHERE
							levels.is_active = 1
					group by ws_id
				    ) AS wfl #workspace

				) AS wlevel ON #workspace
				user_permissions.workspace_id = wlevel.wlevel_id

			left join (
				SELECT
					ws.workspace_id,
					#...
					#additional columns for top level query:
					ef.total_hours,
					ef.blue_completed_hours,
					ef.green_remaining_hours,
					ef.amber_remaining_hours,
					ef.red_remaining_hours,
					ef.none_remaining_hours,
					#ef.remaining_hours_color,
					ef.change_hours
					#ef.remaining_hours
				FROM #mock top level query
				(
					SELECT $workspace_id AS workspace_id
				) AS ws
				#section to add to top level progress bar query:
				LEFT JOIN #get workspace effort data
				(
					SELECT
						ue.workspace_id,
						#ue.remaining_hours_color,
						SUM(ue.change_hours) change_hours,
						SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
						SUM(ue.completed_hours) AS blue_completed_hours,
						SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours
						#SUM((ue.remaining_hours)) AS remaining_hours
					FROM
					(
						SELECT
							ee.workspace_id,
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
					GROUP BY ue.workspace_id
				) AS ef ON
					ws.workspace_id = ef.workspace_id
			)	as efforts on #wsp
			user_permissions.workspace_id = efforts.workspace_id


		WHERE
			user_permissions.user_id IN ($user_id) AND
			user_permissions.project_id IN (SELECT project_id from user_permissions where user_permissions.user_id = $user_id and user_permissions.workspace_id is null) AND user_permissions.project_id = $project_id AND user_permissions.workspace_id = $workspace_id";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}

	// wsp task counters
	function wspTasks($project_id = null, $workspace_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			elements.id
		FROM
			user_permissions
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0
		INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0

		WHERE
			user_permissions.user_id IN ($user_id) AND
			user_permissions.project_id = $project_id AND user_permissions.workspace_id = $workspace_id";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}


	// wsp owners
	function taskOwners($element_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as owners';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.element_id = $element_id AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner') order by role ASC";

		$total = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($total[0][0]['owners']) && !empty($total[0][0]['owners']) ) ? $total[0][0]['owners'] : 0;
	}

	// wsp sharers
	function taskSharers($element_id = null, $count = false){
		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id";
		if($count) {
			$selection = 'Count(*) as sharers';
		}
		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE user_permissions.element_id = $element_id AND user_permissions.role IN ('Sharer', 'Group Sharer') order by role ASC";

		$total = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($total[0][0]['sharers']) && !empty($total[0][0]['sharers']) ) ? $total[0][0]['sharers'] : 0;
	}


	// Get Project Elements
	public function wsp_element_cost($wspTasks = null, $cost_type = null) {

		App::import("Model", "ElementCost");
		$ec = new ElementCost();

		App::import("Model", "Element");
		$_elements = new Element();

		if(!isset($wspTasks) || empty($wspTasks)){
			return null;
		}

		$data = null;

		$workspaces = $elements = $area_ids = null;

			$query = '';

			$query .= 'SELECT element.id';
			$query .= ' FROM elements as element ';
			$query .= "WHERE element.id IN (" . implode(',', $wspTasks) . ") ";

			$data = $_elements->query($query);

			if (isset($data) && !empty($data)) {
				$totalelecostval = 0;
				foreach ($data as $elemid) {

					if ($cost_type == 'spend_cost') {

						$totalcost = $ec->find('first', array(
							'fields' => array('SUM(ElementCost.spend_cost) AS ctotal'),
							'conditions' => array('ElementCost.element_id' => $elemid['element']['id']),
						));

						if (isset($totalcost[0]['ctotal']) && $totalcost[0]['ctotal'] > 0) {
							$totalelecostval += $totalcost[0]['ctotal'];
						}

					} else {

						$totalcost = $ec->find('first', array(
							'fields' => array('SUM(ElementCost.estimated_cost) AS ctotal'),
							'conditions' => array('ElementCost.element_id' => $elemid['element']['id']),
						));

						if (isset($totalcost[0]['ctotal']) && $totalcost[0]['ctotal'] > 0) {
							$totalelecostval += $totalcost[0]['ctotal'];
						}
					}
				}
			}

			return (isset($totalelecostval) && $totalelecostval > 0) ? $totalelecostval : 0;


	}


	// Project all users
	function project_all_users($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_permissions.role";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer') order by fullname ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project owner users
	function project_owner_users($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_permissions.role";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator', 'Owner') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project group owner users
	function project_group_owner_users($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Group Owner') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project sharer users
	function project_sharer_users($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Sharer') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project group sharer users
	function project_group_sharer_users($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id, user_details.user_id";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Group Sharer') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function all_my_projects($level = 0, $rag = 0, $align = 0, $projectIDs = null){

		$user_id = $this->Session->read('Auth.User.id');
		$qry = " AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer')";
		if($level){
			$qry = " AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner') ";
		}

		$ragQry = "";
		if($rag) {
			$ragQry = " AND projects.rag_status = $rag ";
		}

		$alignQry = "";
		if($align) {
			$alignQry = " AND projects.aligned_id = $align ";
		}

		$prjQry = "";
		if(isset($projectIDs) && !empty($projectIDs)) {
			if(is_array($projectIDs)){
				$projectIDs = implode(',', $projectIDs);
			}
			$prjQry = " AND user_permissions.project_id IN ($projectIDs) ";
		}
		$fiveDays = date("Y-m-d", strtotime(date('Y-m-d') . " +5 Days"));

		$query = "SELECT projects.id, projects.title, projects.rag_current_status, projects.rag_status, projects.rag_current_status, projects.image_file, projects.color_code, projects.budget, projects.start_date, projects.end_date, projects.currency_id, projects.objective, projects.description, projects.aligned_id, currencies.sign, aligneds.title as atitle,
			sum(elements.sign_off=1) AS CMP,
			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null $prjQry and user_permissions.user_id = $user_id)) as stotal,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null $prjQry and user_permissions.user_id = $user_id)) as etotal,

			sum(date(elements.end_date)  BETWEEN '" . date('Y-m-d') . "' AND '" . $fiveDays . "' and elements.sign_off!=1 and elements.date_constraints=1  ) AS next_5_day_tasks,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_role,

			SUM( (res.impact = 1 AND (res.percentage = 3 OR res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 2 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 3 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 4 AND res.percentage= 1) ) AS low_risk,
			SUM( (res.impact = 1 AND (res.percentage = 5 OR res.percentage = 4)) OR (res.impact = 2 AND (res.percentage = 4 OR res.percentage= 3)) OR (res.impact = 3 AND res.percentage = 3) OR (res.impact = 4 AND (res.percentage = 3 OR res.percentage = 2)) OR (res.impact = 5 AND (res.percentage = 2 OR res.percentage = 1)) ) AS medium_risk,
			SUM( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AS high_risk,
			SUM( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) AS severe_risk,

			SUM(rm_details.status = 1) AS open_risk,
			SUM(rm_details.status = 2) AS review_risk,
			SUM(rm_details.status = 3) AS signoff_risk,
			SUM(rm_details.status = 4) AS overdue_risk

		FROM
				`user_permissions`
				INNER JOIN projects
				ON user_permissions.project_id = projects.id
				LEFT JOIN elements
				ON user_permissions.element_id = elements.id
				#LEFT JOIN element_costs
				#ON element_costs.element_id = elements.id
				LEFT JOIN rm_details
				ON rm_details.project_id = projects.id AND user_permissions.workspace_id is null
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id
				LEFT JOIN currencies
				ON currencies.id = projects.currency_id
				LEFT JOIN aligneds
				ON aligneds.id = projects.aligned_id

				Where user_permissions.user_id = $user_id $prjQry $qry $ragQry $alignQry group by projects.id order by projects.title ";

		$allmyproject = ClassRegistry::init('UserPermission')->query($query);
		// pr($allmyproject,1);

		$myprojectlist = array();
		if (isset($allmyproject) && !empty($allmyproject)) {
			foreach ($allmyproject as $valP) {
				// $title = strip_tags(str_replace("&nbsp;", " ", $valP['projects']['title']));
				$myprojectlist[$valP['projects']['id']] = ['project' => $valP['projects'], 'task' => $valP[0], 'currencies' => $valP['currencies'], 'aligneds' => $valP['aligneds']];
				// pr($valP);
			}
		}
		return $myprojectlist;

	}

	// wsp task counters
	function wspAndTasks($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
			#elements.title task_title, elements.date_constraints,  elements.start_date as task_start_date,  elements.end_date,  elements.sign_off,  elements.modified,
			sum(elements.sign_off=1) AS CMP,
			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,

			#areas.title as area_title,

			workspaces.id, workspaces.title as wsp_title, workspaces.start_date, workspaces.end_date, workspaces.created


		FROM
			user_permissions
		INNER JOIN elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0
		#INNER JOIN areas
			#ON user_permissions.area_id = areas.id and areas.studio_status = 0
		INNER JOIN workspaces
			ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0

		WHERE
			user_permissions.user_id IN ($user_id) AND
			user_permissions.project_id IN (SELECT project_id from user_permissions where user_permissions.user_id = $user_id and user_permissions.workspace_id is null) AND user_permissions.project_id = $project_id

		group by user_permissions.workspace_id
		";


		return $data = ClassRegistry::init('UserPermission')->query($query);
		// pr($data, 1);
	}


	function board_project_users($project_id = null){
		$query = "select users.id, users.email, user_details.user_id, user_details.first_name, user_details.last_name, CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname

			from users
			LEFT JOIN user_details
			ON user_details.user_id = users.id
			LEFT JOIN user_permissions
			ON user_permissions.user_id = users.id
			where users.id NOT IN (select user_permissions.user_id
									from user_permissions
									where user_permissions.project_id = $project_id and user_permissions.workspace_id IS NULL
									group by user_permissions.user_id)

			and user_details.first_name IS NOT NULL
			and users.role_id = 2
            GROUP BY users.id
            ";

		return $board_users = ClassRegistry::init('UserPermission')->query($query);
		// pr($board_users, 1);
	}

	function users_on_project($project_id = null){
		$query = "select user_permissions.user_id
					from user_permissions
					where user_permissions.project_id = $project_id and user_permissions.workspace_id IS NULL
					group by user_permissions.user_id
            ";

		return $board_users = ClassRegistry::init('UserPermission')->query($query);
		// pr($board_users, 1);
	}

	function project_progress($projectID = null ){


		$user_id = $this->Session->read('Auth.User.id');


		$data_query = "SELECT
					user_permissions.role as prj_role,
					wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,
					wlevel.wlevel_id,

					efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.none_remaining_hours,
					#efforts.remaining_hours_color,
					efforts.change_hours,
					#efforts.remaining_hours,
			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date,  projects.sign_off_date,  projects.sign_off, currencies.sign,

			(SELECT COUNT(*) FROM user_permissions up WHERE up.role IN ('Creator', 'Owner', 'Group Owner') AND up.project_id = $projectID AND up.workspace_id IS NULL) AS owners_count,
			(SELECT COUNT(*) FROM user_permissions up WHERE up.role IN ('Sharer', 'Group Sharer') AND up.project_id = $projectID AND up.workspace_id IS NULL) AS sharer_count,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,


			(SELECT count(*) as counts FROM project_skills  where  project_skills.project_id = $projectID GROUP by project_skills.project_id) as total_skills,

			(SELECT count(*) as counts FROM project_subjects  where  project_subjects.project_id = $projectID GROUP by project_subjects.project_id) as total_subjects,

			(SELECT count(*) as counts FROM project_domains  where  project_domains.project_id = $projectID GROUP by project_domains.project_id) as total_domains,



			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.project_id = projects.id and user_permissions.user_id = $user_id AND user_permissions.project_id = $projectID)) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.project_id = projects.id and user_permissions.user_id = $user_id AND user_permissions.project_id = $projectID)) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,

			count(distinct(rm_details.id)) AS total_risk,

			SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.status IN(1,2,4) and rm_details.id = res.rm_detail_id ) AS phigh_risk,
			SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4))) AND rm_details.status IN(1,2,4) and rm_details.id = res.rm_detail_id) AS psevere_risk,


			SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.status IN(3) and rm_details.id = res.rm_detail_id ) AS shigh_risk,
			SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4))) AND rm_details.status IN(3) and rm_details.id = res.rm_detail_id) AS ssevere_risk,

			(SELECT SUM( (wsp.start_date IS NULL OR wsp.start_date = '') AND (wsp.end_date IS NULL OR wsp.end_date = '') ) FROM workspaces wsp WHERE wsp.id IN (select user_permissions.workspace_id from user_permissions WHERE user_permissions.workspace_id IS NOT NULL AND user_permissions.project_id = $projectID )) AS WNON,
			(SELECT SUM(DATE(NOW())<DATE(wsp.start_date) AND wsp.sign_off != 1 ) FROM workspaces wsp WHERE wsp.id IN (select user_permissions.workspace_id from user_permissions WHERE user_permissions.workspace_id IS NOT NULL AND user_permissions.project_id = $projectID )) AS WPND,
			(SELECT SUM(DATE(NOW()) BETWEEN DATE(wsp.start_date) AND DATE(wsp.end_date) and wsp.sign_off!=1) FROM workspaces wsp WHERE wsp.id IN (select user_permissions.workspace_id from user_permissions WHERE user_permissions.workspace_id IS NOT NULL AND user_permissions.project_id = $projectID )) AS WPRG,
			(SELECT SUM(DATE(wsp.end_date)<DATE(NOW()) AND wsp.sign_off!=1) FROM workspaces wsp WHERE wsp.id IN (select user_permissions.workspace_id from user_permissions WHERE user_permissions.workspace_id IS NOT NULL AND user_permissions.project_id = $projectID )) AS WOVD,
			(SELECT SUM(wsp.sign_off = 1) FROM workspaces wsp WHERE wsp.id IN (select user_permissions.workspace_id from user_permissions WHERE user_permissions.workspace_id IS NOT NULL AND user_permissions.project_id = $projectID )) AS WCMP,


			(select sum(cast(reward_assignments.allocated_rewards as unsigned)) from reward_assignments where reward_assignments.user_id = $user_id AND reward_assignments.project_id = $projectID) as total_rewards,

			(select sum(cast(reward_user_accelerations.accelerated_amount as unsigned)) from reward_user_accelerations where reward_user_accelerations.user_id = $user_id AND reward_user_accelerations.project_id = $projectID) as total_acc,

			board.pb_count

		FROM
				`user_permissions`
				INNER JOIN projects
				ON user_permissions.project_id = projects.id
				LEFT JOIN elements
				ON user_permissions.element_id = elements.id
				LEFT JOIN rm_details
				ON rm_details.project_id = user_permissions.project_id AND user_permissions.workspace_id is null
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id
				LEFT JOIN currencies
				ON currencies.id = projects.currency_id

				LEFT JOIN (
					SELECT count(pb.id) AS pb_count, pb.project_id
					FROM project_boards pb
					LEFT JOIN user_projects p
						ON p.project_id = pb.project_id
					WHERE

						(pb.project_status = 0 OR pb.project_status IS NULL) AND
						p.id = $projectID AND
						p.user_id = $user_id

				) AS board
				ON board.project_id = user_permissions.project_id


				LEFT JOIN
				(
				    #--------------------------------------------
				    #get level counts
				    SELECT
				        wfl.ws_id as wlevel_id,
				        wfl.level_current as level,
				        wfl.level_count as level_count,
						wfl.confidence_class as confidence_class,
						wfl.confidence_level as confidence_level,
						wfl.confidence_arrow as confidence_arrow,
						wfl.confidence_order as confidence_order,
						wfl.confidence_order_asc as confidence_order_asc

				    FROM
				    (
				        SELECT
				            levels.project_id AS ws_id,
				            levels.element_id AS tid,
				            round(AVG(levels.level)) as level_current,
				            COUNT(levels.level) as level_count,
					(CASE
						WHEN ( round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'Low'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'Medium Low'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'Medium High'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'red'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'orange'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'yellow'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'lowgrey'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'mediumlowgrey'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'mediumhighgrey'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow,

					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 1
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 2
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 3
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 4
						ELSE 0 # not set
					END) AS confidence_order,
                    (CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 4
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 3
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 2
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 1
						ELSE 5 # not set
					END) AS confidence_order_asc


				        FROM
				            element_levels levels
							inner join elements on elements.id = levels.element_id


				        WHERE
							levels.is_active = 1
					group by ws_id
				    ) AS wfl #workspace

				    ) AS wlevel ON #workspace
				    	user_permissions.project_id = wlevel.wlevel_id

				left join(
				    SELECT
					pr.project_id,
					#...
					#additional columns for top level query:
					ef.total_hours,
					ef.blue_completed_hours,
					ef.green_remaining_hours,
					ef.amber_remaining_hours,
					ef.red_remaining_hours,
					ef.none_remaining_hours,
					#ef.remaining_hours_color,
					ef.change_hours
					#ef.remaining_hours


				FROM #mock top level query
				(
					SELECT $projectID AS project_id
				) AS pr
				#section to add to top level progress bar query:
				LEFT JOIN #get project effort data
				(
					SELECT
						ue.project_id,
						#ue.remaining_hours_color,
						SUM(ue.change_hours) change_hours,
						SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
						SUM(ue.completed_hours) AS blue_completed_hours,
						SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours
						#SUM((ue.remaining_hours)) AS remaining_hours
					FROM
					(
						SELECT
							ee.project_id,
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
					GROUP BY ue.project_id
				) AS ef ON
					pr.project_id = ef.project_id

				) as efforts ON #project
				    	user_permissions.project_id = efforts.project_id



				Where user_permissions.user_id = $user_id  AND projects.id = $projectID group by projects.id order by projects.title ";
			$allmyproject = ClassRegistry::init('UserPermission')->query($data_query);
			// pr($allmyproject, 1);


		return $allmyproject;

	}

	function wsp_progress($projectID = null, $workspace_id = null){

		$user_id = $this->Session->read('Auth.User.id');


		$data_query = "SELECT
			user_permissions.role as wsp_role,

			projects.id, projects.title, projects.budget, currencies.sign, workspaces.start_date, workspaces.end_date, workspaces.sign_off, workspaces.sign_off_date,

			(SELECT COUNT(*) FROM user_permissions up WHERE up.role IN ('Creator', 'Owner', 'Group Owner') AND up.project_id = $projectID AND up.workspace_id = $workspace_id AND up.area_id IS NULL) AS owners_count,
			(SELECT COUNT(*) FROM user_permissions up WHERE up.role IN ('Sharer', 'Group Sharer') AND up.project_id = $projectID AND up.workspace_id = $workspace_id AND up.area_id IS NULL) AS sharer_count,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,

			SUM(rm_details.id) AS total_risk,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.workspace_id = workspaces.id and user_permissions.user_id = $user_id AND user_permissions.workspace_id = $workspace_id )) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.workspace_id = workspaces.id and user_permissions.user_id = $user_id AND user_permissions.workspace_id = $workspace_id )) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,

			SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.status IN(1,2,4) and rm_details.id = res.rm_detail_id ) AS phigh_risk,
			SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4))) AND rm_details.status IN(1,2,4) and rm_details.id = res.rm_detail_id) AS psevere_risk,


			SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.status IN(3) and rm_details.id = res.rm_detail_id ) AS shigh_risk,
			SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4))) AND rm_details.status IN(3) and rm_details.id = res.rm_detail_id) AS ssevere_risk

		FROM
				`user_permissions`
				INNER JOIN projects
				ON user_permissions.project_id = projects.id
				INNER JOIN workspaces
				ON user_permissions.workspace_id = workspaces.id
				LEFT JOIN elements
				ON user_permissions.element_id = elements.id
				LEFT JOIN rm_details
				ON rm_details.project_id = user_permissions.project_id AND user_permissions.workspace_id is null
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id
				LEFT JOIN currencies
				ON currencies.id = projects.currency_id

				Where user_permissions.user_id = $user_id AND projects.id = $projectID AND user_permissions.workspace_id = $workspace_id group by user_permissions.workspace_id ";

		$allmyproject = ClassRegistry::init('UserPermission')->query($data_query);


		return $allmyproject;

	}

	/*
	overdue tasks
	task completing today and tomorrow
	assigned tasks that are overdue and in progress
	today's reminders tasks
	Off Plan Budget projects
	SEVERE and HIGH level risks
	*/
	function assistant_data(){

		$user_id = $this->Session->read('Auth.User.id');

		$today = date('Y-m-d');
		$tomm = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
		  $data_query = "SELECT
			user_permissions.role as prj_role,
			user_details.first_name,

			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date, projects.rag_status, projects.rag_current_status, projects.sign_off_date,  projects.sign_off,


			SUM(DATE(elements.end_date)<DATE(NOW()) AND elements.sign_off != 1 AND elements.date_constraints = 1) AS OVD,

			#SUM((date_format(elements.end_date,'%Y-%m-%d') BETWEEN '$today' AND '$tomm') AND elements.sign_off != 1 AND elements.date_constraints = 1) AS today_tomm,

			SUM( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3)) AS high_risk,
			SUM( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) AS severe_risk,

			#SUM(element_costs.spend_cost) AS sp_cost_o,
			#SUM(element_costs.estimated_cost) AS est_cost_0,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.project_id = projects.id and user_permissions.user_id = $user_id)) as sp_cost,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in  (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.project_id = projects.id and user_permissions.user_id = $user_id)) as est_cost,

			SUM( UNIX_TIMESTAMP(reminders.reminder_date) >= UNIX_TIMESTAMP() AND DATE(reminders.reminder_date)<=DATE(NOW())) AS rem_elements



			FROM
				`user_permissions`
				INNER JOIN user_details
				ON user_permissions.user_id = user_details.user_id
				INNER JOIN projects
				ON user_permissions.project_id = projects.id
				LEFT JOIN elements
				ON user_permissions.element_id = elements.id
				LEFT JOIN rm_details
				ON rm_details.project_id = projects.id AND user_permissions.workspace_id is null
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id
				#LEFT JOIN element_costs
				#ON element_costs.element_id = user_permissions.element_id
				LEFT JOIN reminders
				ON reminders.element_id = user_permissions.element_id

				Where user_permissions.user_id = $user_id AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner') group by user_permissions.project_id order by projects.title ";

		$allmyproject = ClassRegistry::init('UserPermission')->query($data_query);

		/*$myprojectlist = array();
		if (isset($allmyproject) && !empty($allmyproject)) {
			foreach ($allmyproject as $valP) {
				$myprojectlist = $valP[0];
			}
		}*/


		return $allmyproject;

	}


	function user_selected_skills($user_id = null) {
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$query = "SELECT user_data.skill_id, user_data.id, lib.title, details.user_level, details.user_experience , count(pdf.pdf_name) pdf_count
			FROM user_skills user_data
			INNER JOIN skills lib ON lib.id = user_data.skill_id
			LEFT JOIN skill_details details ON details.skill_id = user_data.skill_id AND details.user_id = $user_id
			LEFT JOIN skill_pdfs pdf ON pdf.skill_id = user_data.skill_id AND pdf.user_id = $user_id AND pdf.upload_status = 1
			WHERE user_data.user_id = $user_id
			GROUP By user_data.skill_id
			ORDER BY lib.title ASC
		";
		// pr($query);
		return $data = ClassRegistry::init('UserSubject')->query($query);
	}

	function user_selected_subjects($user_id = null){
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$query = "SELECT user_data.subject_id, user_data.id, lib.title, details.user_level, details.user_experience , count(pdf.pdf_name) pdf_count
			FROM user_subjects user_data
			INNER JOIN subjects lib ON lib.id = user_data.subject_id
			LEFT JOIN subject_details details ON details.subject_id = user_data.subject_id AND details.user_id = $user_id
			LEFT JOIN subject_pdfs pdf ON pdf.subject_id = user_data.subject_id AND pdf.user_id = $user_id  AND pdf.upload_status = 1
			WHERE user_data.user_id = $user_id
			GROUP By user_data.subject_id
			ORDER BY lib.title ASC
		";
		// pr($query);
		return $data = ClassRegistry::init('UserSubject')->query($query);
	}

	function user_selected_domains($user_id = null){
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$query = "SELECT user_data.domain_id, user_data.id, lib.title, details.user_level, details.user_experience , count(pdf.pdf_name) pdf_count
			FROM user_domains user_data
			INNER JOIN knowledge_domains lib ON lib.id = user_data.domain_id
			LEFT JOIN domain_details details ON details.domain_id = user_data.domain_id AND details.user_id = $user_id
			LEFT JOIN domain_pdfs pdf ON pdf.domain_id = user_data.domain_id AND pdf.user_id = $user_id  AND pdf.upload_status = 1
			WHERE user_data.user_id = $user_id
			GROUP By user_data.domain_id
			ORDER BY lib.title ASC
		";
		return $data = ClassRegistry::init('UserSubject')->query($query);
	}

	function get_user_skills($user_id = null){
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$q = "SELECT user_data.skill_id, user_data.id, lib.title, user_data.created, details.user_level, details.user_experience ,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as pdf_names,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as tooltip_names

				FROM user_skills user_data

				INNER JOIN skills lib ON lib.id = user_data.skill_id

				LEFT JOIN skill_details details ON details.skill_id = user_data.skill_id AND details.user_id = $user_id

				LEFT JOIN skill_pdfs pdf ON pdf.skill_id = user_data.skill_id AND pdf.user_id = $user_id and upload_status = 1

				WHERE user_data.user_id = $user_id

				GROUP By user_data.skill_id

				ORDER BY lib.title ASC";
		return $data = ClassRegistry::init('UserSubject')->query($q);
	}

	function get_user_subjects($user_id = null){
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$q = "SELECT user_data.subject_id, user_data.created, user_data.id, lib.title, details.user_level, details.user_experience ,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as pdf_names,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as tooltip_names

				FROM user_subjects user_data

				INNER JOIN subjects lib ON lib.id = user_data.subject_id

				LEFT JOIN subject_details details ON details.subject_id = user_data.subject_id AND details.user_id = $user_id

				LEFT JOIN subject_pdfs pdf ON pdf.subject_id = user_data.subject_id AND pdf.user_id = $user_id and upload_status = 1

				WHERE user_data.user_id = $user_id

				GROUP By user_data.subject_id

				ORDER BY lib.title ASC";
		return $data = ClassRegistry::init('UserSubject')->query($q);
	}

	function get_user_domains($user_id = null){
		if(!isset($user_id) || empty($user_id)){
			$user_id = $this->Session->read('Auth.User.id');
		}

		$q = "SELECT user_data.domain_id, user_data.created, user_data.id, lib.title, details.user_level, details.user_experience ,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as pdf_names,
				GROUP_CONCAT(pdf.id,'~',pdf.tooltip_name) as tooltip_names

				FROM user_domains user_data

				INNER JOIN knowledge_domains lib ON lib.id = user_data.domain_id

				LEFT JOIN domain_details details ON details.domain_id = user_data.domain_id AND details.user_id = $user_id

				LEFT JOIN domain_pdfs pdf ON pdf.domain_id = user_data.domain_id AND pdf.user_id = $user_id and upload_status = 1

				WHERE user_data.user_id = $user_id

				GROUP By user_data.domain_id

				ORDER BY lib.title ASC";
		return $data = ClassRegistry::init('UserSubject')->query($q);
	}


	function gantt_data($project_id = null){

		$q = "SELECT
				user_permissions.role as prj_role,

				workspaces.id, workspaces.title, workspaces.start_date, workspaces.end_date, workspaces.sign_off_date, workspaces.sign_off,
				group_concat(distinct(areas.id), '~', areas.title) as area_data,
				group_concat( distinct(concat(areas.id, '^', elements.id, '^', elements.title)) SEPARATOR '~~') as area_task_data,

				(CASE
					WHEN (user_permissions.role='Owner' ) THEN 'r_project'
					WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
					WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
					WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
					ELSE 'm_project'
				END) AS prj_type


			FROM
			`user_permissions`
			INNER JOIN projects
				ON user_permissions.project_id = projects.id
			LEFT JOIN workspaces
				ON user_permissions.workspace_id = workspaces.id
			LEFT JOIN areas
				ON user_permissions.area_id = areas.id
			LEFT JOIN elements
				ON user_permissions.element_id = elements.id

			Where
				user_permissions.user_id = 20 AND
				user_permissions.workspace_id IS NOT NULL AND
				projects.id = 75

			group by workspaces.id
			order by workspaces.id ";
		return $data = ClassRegistry::init('UserSubject')->query($q);
		//pr($data);
	}

	function skill_detail($user_id = null, $skill_id = null) {

		$query = "SELECT user_level, user_experience FROM skill_details WHERE user_id = $user_id AND skill_id = $skill_id LIMIT 1";
		$details = ['level' => '', 'experience' => ''];
		$data = ClassRegistry::init('SkillDetail')->query($query);
		if(isset($data) && !empty($data)){
			$details['level'] = $data[0]['skill_details']['user_level'];
			$details['experience'] = $data[0]['skill_details']['user_experience'];
		}
		return $details;
	}

	function subject_detail($user_id = null, $subject_id = null) {

		$query = "SELECT user_level, user_experience FROM subject_details WHERE user_id = $user_id AND subject_id = $subject_id LIMIT 1";
		$details = ['level' => '', 'experience' => ''];
		// pr($query);
		$data = ClassRegistry::init('SubjectDetail')->query($query);
		if(isset($data) && !empty($data)){
			$details['level'] = $data[0]['subject_details']['user_level'];
			$details['experience'] = $data[0]['subject_details']['user_experience'];
		}
		return $details;
	}

	function domain_detail($user_id = null, $domain_id = null) {

		$query = "SELECT user_level, user_experience FROM domain_details WHERE user_id = $user_id AND domain_id = $domain_id LIMIT 1";
		$details = ['level' => '', 'experience' => ''];
		$data = ClassRegistry::init('DomainDetail')->query($query);
		if(isset($data) && !empty($data)){
			$details['level'] = $data[0]['domain_details']['user_level'];
			$details['experience'] = $data[0]['domain_details']['user_experience'];
		}
		return $details;
	}

	function level_exp_icon($value = null, $level = true) {
		$icon = "";

		if($level) {
			$icon = ($value == 'Beginner') ? 'beginner-icon' : ( ($value == 'Intermediate') ? 'intermediate-icon' : ( ($value == 'Advanced') ? 'advanced-icon' : 'expert-icon' ) );
		}
		else {
			switch ($value) {
	            case '2': $icon = 'twoyears-icon'; break;
	            case '3': $icon = 'threeyears-icon'; break;
	            case '4': $icon = 'fouryears-icon'; break;
	            case '5': $icon = 'fiveyears-icon'; break;
	            case '6-10': $icon = 'sixyears-icon'; break;
	            case '11-15': $icon = 'elevenyears-icon'; break;
	            case '16-20': $icon = 'sixteenyears-icon'; break;
	            case 'Over 20': $icon = 'twentyyears-icon'; break;
	            default: $icon = 'oneyears-icon'; break;
	        }
	    }
		return $icon;
	}
	
	function level_exp_img($value = null, $level = true) {
		$icon = "";

		if($level) {
			$icon = ($value == 'Beginner') ? 'BeginnerBlack8x18.png' : ( ($value == 'Intermediate') ? 'IntermediateBlack8x18.png' : ( ($value == 'Advanced') ? 'AdvancedBlack8x18.png' : 'ExpertBlack8x18.png' ) );
		}
		else {
			switch ($value) {
	            case '2': $icon = '2YearsBlack8x18.png'; break;
	            case '3': $icon = '3YearsBlack8x18.png'; break;
	            case '4': $icon = '4YearsBlack8x18.png'; break;
	            case '5': $icon = '5YearsBlack8x18.png'; break;
	            case '6-10': $icon = '6-10YearsBlack8x18.png'; break;
	            case '11-15': $icon = '11-15YearsBlack8x18.png'; break;
	            case '16-20': $icon = '16-20YearsBlack8x18.png'; break;
	            case 'Over 20': $icon = 'Over20YearsBlack8x18.png'; break;
	            default: $icon = '1YearBlack8x18.png'; break;
	        }
	    }
		return $icon;
	}

	function exp_number($value = null) {
		$number = "";
		// e($value);

		switch ($value) {
            case '2': $number = 2; break;
            case '3': $number = 3; break;
            case '4': $number = 4; break;
            case '5': $number = 5; break;
            case '6-10': $number = 6; break;
            case '11-15': $number = 11; break;
            case '16-20': $number = 16; break;
            case 'Over 20': $number = 20; break;
            default: $number = 1; break;
        }
		return $number;
	}


	function tasks_data($project_id = null, $workspace_id = null){

		$wspCond = "";
		if(isset($workspace_id) && !empty($workspace_id)){
			$wspCond = " AND user_permissions.workspace_id = $workspace_id ";
		}
		$otherWsp = "";
		if(isset($workspace_id) && !empty($workspace_id)){
			$otherWsp = " and user_permissions.workspace_id = workspaces.id ";
		}
		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			user_permissions.role,
			projects.id, projects.title, projects.budget, currencies.sign, projects.start_date, projects.end_date, projects.sign_off, projects.sign_off_date,

			GROUP_CONCAT(elements.id) as all_tasks,
			count(elements.id) as total_tasks,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null $otherWsp and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id $wspCond)) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null $otherWsp and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id $wspCond )) as estimate_total


		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0
		INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id $wspCond";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}

	function wsp_tasks_data($project_id = null, $user = null){

		$user_id = $this->Session->read('Auth.User.id');
		if(isset($user) && !empty($user)){
			$user_id = $user;
		}
		$query = "SELECT
			user_permissions.role,
			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date, projects.sign_off, projects.sign_off_date,
			workspaces.id, workspaces.title, workspaces.start_date, workspaces.end_date, workspaces.sign_off, workspaces.sign_off_date, workspaces.color_code,
			currencies.sign,

			GROUP_CONCAT(elements.id) as all_tasks,
			count(elements.id) as total_tasks,

			(CASE
				WHEN (DATE(NOW())<DATE(workspaces.start_date) and workspaces.sign_off!=1) THEN 'PND'
				WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) and workspaces.sign_off!=1) THEN 'PRG'
				WHEN (DATE(workspaces.end_date)<DATE(NOW()) and workspaces.sign_off!=1) THEN 'OVD'
				WHEN (workspaces.sign_off=1) THEN 'CMP'
				ELSE 'NON'
			END) AS wsp_status,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,

			#sum(element_dependencies.is_critical=1) as total_critical,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,


			(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) high_risk,

			(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) severe_risk,

			( select count(element_assignments.assigned_to) from element_assignments where element_assignments.project_id = user_permissions.project_id and element_assignments.reaction != 3) as assigned_to


		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id and elements.studio_status = 0
		INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id
		#LEFT JOIN element_dependencies
			#ON element_dependencies.element_id = elements.id



		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id
		GROUP BY user_permissions.workspace_id ORDER BY -workspaces.start_date DESC,workspaces.start_date ASC";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}


	function project_critical_count($project_id = null, $user = null){

		$user_id = $this->Session->read('Auth.User.id');
		if(isset($user) && !empty($user)){
			$user_id = $user;
		}
		$query = "SELECT

			sum(element_dependencies.is_critical=1) as total_critical

		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id and elements.studio_status = 0
		#INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN element_dependencies
			ON element_dependencies.element_id = elements.id

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id
		";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}



	function wsp_gantt_data($project_id = null, $workspace_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		if(isset($user) && !empty($user)){
			//$user_id = $user;
		}
		$query = "SELECT
			user_permissions.role,
			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date, projects.sign_off, projects.sign_off_date,
			workspaces.id, workspaces.title, workspaces.start_date, workspaces.end_date, workspaces.sign_off, workspaces.sign_off_date, workspaces.color_code,
			currencies.sign,

			GROUP_CONCAT(elements.id) as all_tasks,
			count(elements.id) as total_tasks,

			(CASE
				WHEN (DATE(NOW())<DATE(workspaces.start_date) and workspaces.sign_off!=1) THEN 'WPND'
				WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) and workspaces.sign_off!=1) THEN 'WPRG'
				WHEN (DATE(workspaces.end_date)<DATE(NOW()) and workspaces.sign_off!=1) THEN 'WOVD'
				WHEN (workspaces.sign_off=1) THEN 'WCMP'
				ELSE 'WNON'
			END) AS wsp_status,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,



			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,


			(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) high_risk,

			(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) severe_risk


		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id and elements.studio_status = 0
		INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id and user_permissions.workspace_id = $workspace_id
		GROUP BY user_permissions.workspace_id ";


		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}

	function task_detail($task_id = null){
		$query = "SELECT elements.id, elements.title, elements.start_date, elements.end_date, elements.date_constraints, elements.sign_off, elements.engaged_by, elements.color_code,

					( select ec.estimated_cost from element_costs ec where ec.element_id = elements.id and ec.estimate_spend_flag = 1 ) ele_estimated_cost,
					( select eca.spend_cost from element_costs eca where eca.element_id = elements.id and eca.estimate_spend_flag = 2 ) ele_actual_cost,

					(CASE
						WHEN (element_dependencies.is_critical=1) THEN '1' ELSE '0'
					END) AS dep_is_critical,

					GROUP_CONCAT( (edr.element_dependancy_id)) as dependencies,

					element_assignments.assigned_to, element_assignments.reaction, element_assignments.created_by,

					( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = element_assignments.assigned_to) as assigned_touser,

					( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = element_assignments.created_by) as assigned_createuser,

					( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = elements.engaged_by) as engaged_touser,

				(CASE
					WHEN (elements.date_constraints=0) THEN 'NON'
					WHEN (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'PND'
					WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'PRG'
					WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'OVD'
					WHEN (elements.sign_off=1) THEN 'CMP'
					ELSE 'NON'
				END) AS task_status,

				(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id

				where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id = elements.id)

				) high_risk,

				(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
				LEFT JOIN rm_expose_responses res
				ON rm_details.id = res.rm_detail_id

				where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id = elements.id)

				) severe_risk,

				( select JSON_ARRAYAGG(element_id) from element_dependancy_relationships where element_dependancy_id = element_dependencies.id) as ele_all_dependancy


				FROM elements

				LEFT JOIN element_assignments
					ON element_assignments.element_id = elements.id
				LEFT JOIN element_dependencies
					ON element_dependencies.element_id = elements.id
				LEFT JOIN element_dependancy_relationships edr
					ON edr.element_dependancy_id = element_dependencies.id

				WHERE
					elements.id IN($task_id)

				GROUP BY elements.id
				ORDER BY -elements.start_date DESC, elements.start_date ASC ";
		return ClassRegistry::init('UserPermission')->query($query);

	}




	function element_progress_days($start = null, $end = null, $status = 'NON') {
		$data = [];
		$end = date('d-m-Y', strtotime($end . ' +1 day'));
	    $curr_date = date("d-m-Y");
	    $total_days = round(abs(strtotime($end) - strtotime($start))/(60*60*24));
	    $remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
	    $left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24))-1;
	    if ($status == 'NON') {
	        $data = array("total_days"=>0,"remaining_days"=>0,"left_days"=>0, 'progress' => '0.0');
	    }
	    else if ($status == 'PND') {
	        $remaining = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
	        $data = array("total_days"=>$total_days,"remaining_days"=>$remaining,"left_days"=>0, 'progress' => '0.0');
	    }
	    else if ($status == 'PRG') {
	    	$curr_date = date("d-m-Y");
	        $remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
	        $left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
	        $progress = $left_days/$total_days*1;

	        $data = array("total_days"=>$total_days,"remaining_days"=>$remaining_days,"left_days"=>$left_days, 'progress' => $progress);
	    }
	    else if ($status == 'OVD') {
	        $left = round(abs(strtotime($curr_date) - strtotime($end))/(60*60*24));
	        $data = array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>$left, 'progress' => '1.0');
	    }
	    else if ($status == 'CMP') {
	        $data = array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>0, 'progress' => '1.0');
	    }
	    else {
	        $data = array("total_days"=>0,"remaining_days"=>0,"left_days"=>0, 'progress' => '0.0');
	    }

		return $data;
	}

	function project_data($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			user_permissions.role,
			projects.*,
			currencies.sign,
			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS project_type

		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id GROUP BY projects.id";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function project_detail($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
			projects.*

		FROM
			projects

		WHERE projects.id = $project_id ";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function mongo_user_status( ){

		$collection = new ComponentCollection();
        $acl = new UsersComponent($collection);
        $user_id = $this->Session->read('Auth.User.id');
		$mongo_user_status = $acl->user_status($user_id);
		return $mongo_user_status;
	}

	//get gantt filter with project_id and workspace_id pawan updated 23 sep
	function wsp_tasks_data_filter($project_id = null, $workspace_id = null,$assignmentStatus= null){

		$user_id = $this->Session->read('Auth.User.id');
		if(isset($user) && !empty($user)){
			$user_id = $user;
		}

		$workspace_cnd = '';
		if( !empty($workspace_id)  ){
			$workspace_cnd = " and user_permissions.workspace_id =".$workspace_id;
		}

		$element_ids = '';
		if( isset($assignmentStatus) && $assignmentStatus == 'assignfilter' ){

			App::import("Model", "ElementAssignment");
			$ElementAssignment = new ElementAssignment();
			$assignElement = $ElementAssignment->find('all',
				array(
					'conditions' => array('ElementAssignment.project_id' => $project_id),
					'fields' => array('ElementAssignment.element_id'),
				)
			);

			$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
			$element_ids = implode(',', $elementids);

		}
		$notAssign = '';
		if( !empty($element_ids)  ){
			$notAssign = " and elements.id NOT IN (" . $element_ids . ") ";
		}

		$query = "SELECT
			user_permissions.role,
			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date, projects.sign_off, projects.sign_off_date,
			workspaces.id, workspaces.title, workspaces.start_date, workspaces.end_date, workspaces.sign_off, workspaces.sign_off_date, workspaces.color_code,
			currencies.sign,


			GROUP_CONCAT( elements.id ) as all_tasks,
			count(elements.id) as total_tasks,

			(CASE
				WHEN (DATE(NOW())< DATE(workspaces.start_date) and workspaces.sign_off!=1) THEN 'PND'
				WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) and workspaces.sign_off!=1) THEN 'PRG'
				WHEN (DATE(workspaces.end_date)< DATE(NOW()) and workspaces.sign_off!=1) THEN 'OVD'
				WHEN (workspaces.sign_off=1) THEN 'CMP'
				ELSE 'NON'
			END) AS wsp_status,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,

			sum(element_dependencies.is_critical=1) as total_critical,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,


			(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) high_risk,

			(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) severe_risk,


			( select count(element_assignments.assigned_to) from element_assignments where element_assignments.project_id = user_permissions.project_id and element_assignments.reaction != 3) as assigned_to



		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id and elements.studio_status = 0 $notAssign
		LEFT JOIN element_dependencies
			ON element_dependencies.element_id = elements.id
		INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id $workspace_cnd
		GROUP BY user_permissions.workspace_id  ORDER BY -workspaces.start_date DESC,-workspaces.start_date ASC ";

		//echo $query;

		return ClassRegistry::init('UserPermission')->query($query);
	}


	// pawan updated 23 sep below function using for gantt task filter
	function task_detail_filter($task_id = null, $ele_status = null, $is_critical = null, $assigned_user = null, $assignmentStatus = null ){

		//element_dependencies.is_critical=1
		$critial_cnd = '';
		if( isset($is_critical) && $is_critical == 1 ){
			$critial_cnd = 	" AND element_dependencies.is_critical=1 ";
		}

		// assignment user selected===========================
		$assign_data = " LEFT JOIN element_assignments ON element_assignments.element_id=elements.id ";
		$assign_user_data = '';

		if( isset($assigned_user) && !empty($assigned_user) ){

			$assign_user_data = " AND element_assignments.assigned_to = $assigned_user ";
		}


		$ele_sts_cond = '';
		if( isset($ele_status) && !empty($ele_status) ){

			if( $ele_status == 'CMP' ){

				$ele_sts_cond = ' AND (elements.sign_off=1 and elements.date_constraints=1 ) ';
			}

			if( $ele_status == 'PRG' ){

				$ele_sts_cond = ' AND (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

			}

			if( $ele_status == 'PND' ){

				$ele_sts_cond = ' AND (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

			}


			if( $ele_status == 'OVD' ){
				$ele_sts_cond = ' AND (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

			}

			if( $ele_status == 'NON' ){
				$ele_sts_cond = ' AND ( elements.date_constraints=0 ) ';

			}
		}


		$query = "SELECT elements.id, elements.title, elements.start_date, elements.end_date, elements.date_constraints, elements.sign_off, elements.engaged_by, elements.color_code,

			( select ec.estimated_cost from element_costs ec where ec.element_id = elements.id and ec.estimate_spend_flag = 1 ) ele_estimated_cost,
			( select eca.spend_cost from element_costs eca where eca.element_id = elements.id and eca.estimate_spend_flag = 2 ) ele_actual_cost,

			(CASE
				WHEN (element_dependencies.is_critical=1) THEN '1' ELSE '0'
			END) AS dep_is_critical,

			GROUP_CONCAT( DISTINCT(edr.element_dependancy_id) ) as dependencies,

			element_assignments.assigned_to,
			element_assignments.reaction,
			element_assignments.created_by,

			( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = element_assignments.assigned_to) as assigned_touser,

			( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = element_assignments.created_by) as assigned_createuser,

			( select JSON_ARRAYAGG(element_id) from element_dependancy_relationships where element_dependancy_id = element_dependencies.id) as ele_all_dependancy,


			#( select CONCAT(user_details.first_name,' ',user_details.last_name) from user_details where user_id = elements.engaged_by) as engaged_touser,

		(CASE
			WHEN (elements.date_constraints=0) THEN 'NON'
			WHEN (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'PND'
			WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'PRG'
			WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'OVD'
			WHEN (elements.sign_off=1) THEN 'CMP'
			ELSE 'NON'
		END) AS task_status,

		(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
		LEFT JOIN rm_expose_responses res
		ON rm_details.id = res.rm_detail_id

		where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id = elements.id)

		) high_risk,

		(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
		LEFT JOIN rm_expose_responses res
		ON rm_details.id = res.rm_detail_id

		where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id = elements.id)

		) severe_risk


		FROM elements

		LEFT JOIN element_dependencies
			ON element_dependencies.element_id = elements.id
		LEFT JOIN element_dependancy_relationships edr
			ON edr.element_dependancy_id = element_dependencies.id

		$assign_data

		WHERE
			elements.id IN($task_id) $ele_sts_cond $critial_cnd $assign_user_data

		GROUP BY elements.id ORDER BY -elements.start_date DESC,elements.start_date ASC ";
		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);

	}

	function set_reward_opt(){
		$sql = "SELECT u.id FROM users WHERE u.id NOT IN (SELECT DISTINCT(user_id) as uid FROM reward_opted_settings)";
		$query = "INSERT INTO reward_opted_settings (user_id, reward_opt_status, reward_table_opt_status, created, modified)   ((SELECT id FROM users WHERE id NOT IN (SELECT DISTINCT(user_id), 1, 1, now(), now() FROM reward_opted_settings))) ";
		$query = "SELECT DISTINCT(user_id) as uid FROM reward_opted_settings";
		$data = ClassRegistry::init('UserPermission')->query($query);
		pr($data,1);
	}


	// Project all users
	function project_feedback_users($project_id = null, $exclude_users = null){

		$exclude_user_qry = "";
		if(isset($exclude_users) && !empty($exclude_users)){
			$exclude_users = implode(',', $exclude_users);
			$exclude_user_qry = " AND user_details.user_id NOT IN($exclude_users)";
		}

		$query = "SELECT
			CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.first_name, user_details.last_name, user_details.user_id, user_permissions.role

			FROM `user_permissions`
			INNER JOIN user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE
			user_permissions.project_id = $project_id AND
			workspace_id is null
			#AND	user_permissions.role IN ('Creator', 'Owner', 'Sharer')
			$exclude_user_qry

			ORDER BY user_details.first_name ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}


    function grp_feedback_users($user_id = null, $project_id = null, $exclude_users = null){
    	$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
    	$exclude_user_qry = "";
		if(isset($exclude_users) && !empty($exclude_users)){
			$exclude_users = implode(',', $exclude_users);
			$exclude_user_qry = " AND ud.user_id NOT IN($exclude_users)";
		}
    	$query = "SELECT
    			pg.id, pg.title, ud.user_id, ud.first_name, ud.last_name, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name
    			FROM project_group_users pgu
    			LEFT JOIN user_details ud On pgu.user_id = ud.user_id
    			LEFT JOIN user_projects up On pgu.user_project_id = up.id
    			LEFT JOIN project_groups pg ON pg.id = pgu.project_group_id
    			WHERE
    			pgu.approved = 1
    			AND pg.group_owner_id = $user_id
    			AND up.project_id = $project_id
    			$exclude_user_qry

    			ORDER BY ud.first_name ASC";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }


    function is_element_permit($user_id = null, $element_id = null){

    	$query = "SELECT *
    			FROM user_permissions up
    			WHERE
    				up.user_id = $user_id
    				AND up.element_id = $element_id
    				AND up.element_id IS NOT NULL";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }

    /************************ organization ******************************/
    function location_detail($relation_id = null) {

    	$query = "SELECT
				    locations.id,
				    locations.name,
				    locations.image,
				    locations.city,
				    locations.state_id,
				    locations.country_id,
				    locations.type_id,
				    GROUP_CONCAT(location_skills.skill_id) AS all_skills,
				    GROUP_CONCAT(location_subjects.subject_id) AS all_subjects,
				    GROUP_CONCAT(location_domains.domain_id) AS all_domains,
				    GROUP_CONCAT( distinct(concat(location_links.id, '^', location_links.link, '^', location_links.title)) SEPARATOR '~') AS all_links,
				    GROUP_CONCAT( distinct(concat(location_files.id, '^', location_files.filename, '^', location_files.title)) SEPARATOR '~') AS all_files

				FROM `locations`

				#total skills
				INNER JOIN location_skills ON location_skills.location_id = locations.id
				#total subjects
				INNER JOIN location_subjects ON location_subjects.location_id = locations.id
				#total domains
				INNER JOIN location_domains ON location_domains.location_id = locations.id
				#link total
				INNER JOIN location_links ON location_links.location_id = locations.id
				#file total
				INNER JOIN location_files ON location_files.location_id = locations.id
				WHERE locations.id = $relation_id";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }

    function get_links($type = null, $relation_id = null){

    	$query = "SELECT *
    			FROM location_links up
    			WHERE
    				up.location_id = $relation_id
				ORDER BY up.id DESC";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }

    function get_files($type = null, $relation_id = null){

    	$query = "SELECT *
    			FROM location_files up
    			WHERE
    				up.location_id = $relation_id
				ORDER BY up.id DESC";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }

    function get_org_links( $relation_id = null){

    	$query = "SELECT *
    			FROM organization_links up
    			WHERE
    				up.organization_id = $relation_id
				ORDER BY up.id DESC";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }

    function get_org_files( $relation_id = null){

    	$query = "SELECT *
    			FROM organization_files up
    			WHERE
    				up.organization_id = $relation_id
				ORDER BY up.id DESC";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }
    function get_dept_competencies( $relation_id = null ){

    	$data = [];
    	if(isset($relation_id) && !empty($relation_id)){
    		$qry =  "SELECT departments.id,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT( skills.id, skills.title)) AS JSON FROM skills inner join department_skills on department_skills.skill_id = skills.id  WHERE department_skills.department_id = $relation_id ) as all_skills,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT(subjects.id, subjects.title)) AS JSON FROM subjects inner join department_subjects on department_subjects.subject_id = subjects.id  WHERE department_subjects.department_id = $relation_id ) as all_subjects,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT(knowledge_domains.id, knowledge_domains.title)) AS JSON FROM knowledge_domains inner join department_domains on department_domains.domain_id = knowledge_domains.id  WHERE department_domains.department_id = $relation_id ) as all_domains
    		FROM departments
    		WHERE departments.id = $relation_id";

    		// e($qry);
    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function get_loc_competencies( $relation_id = null ){

    	$data = [];
    	if(isset($relation_id) && !empty($relation_id)){
    		$qry =  "SELECT locations.id,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT( skills.id, skills.title)) AS JSON FROM skills inner join location_skills on location_skills.skill_id = skills.id  WHERE location_skills.location_id = $relation_id ) as all_skills,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT(subjects.id, subjects.title)) AS JSON FROM subjects inner join location_subjects on location_subjects.subject_id = subjects.id  WHERE location_subjects.location_id = $relation_id ) as all_subjects,
	    		( SELECT JSON_ARRAYAGG(JSON_OBJECT(knowledge_domains.id, knowledge_domains.title)) AS JSON FROM knowledge_domains inner join location_domains on location_domains.domain_id = knowledge_domains.id  WHERE location_domains.location_id = $relation_id ) as all_domains
    		FROM locations
    		WHERE locations.id = $relation_id";

    		// e($qry);
    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function get_loc_people( $relation_id = null ) {

    	$data = [];
    	if(isset($relation_id) && !empty($relation_id)) {
    		$qry =  "SELECT ol.id,
					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name', CONCAT_WS(' ', user_details.first_name, user_details.last_name), 'user_id',user_details.user_id, 'profile_pic', user_details.profile_pic, 'job_title', user_details.job_title )) AS JSON FROM user_details LEFT JOIN locations on locations.id = user_details.location_id  WHERE locations.id = $relation_id  ) as all_users
			    	FROM locations ol
    				WHERE ol.id = $relation_id
                    GROUP BY ol.id";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function get_loc_org( $relation_id = null ){

    	$data = [];
    	if(isset($relation_id) && !empty($relation_id)) {
    		$qry =  "SELECT ol.location_id, org.id, org.name, org.image, ot.type as org_type
			    	FROM organization_locations ol
			    	LEFT JOIN organizations org ON org.id = ol.organization_id
			    	INNER JOIN organization_types ot on ot.id = org.type_id
    				WHERE ol.location_id = $relation_id
                    GROUP BY ol.organization_id
                    ORDER BY org.name ASC";

    		// e($qry);
    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function competency_locations( $location_id = null ){

    	$data = [];
    	if(isset($location_id) && !empty($location_id)){
    		$location_id = implode(',', $location_id);
    		$qry =  "SELECT
			    locations.id,
			    locations.name,
			    locations.image,
			    locations.city,
			    countries.countryName as countryName

    		FROM locations

    		INNER JOIN countries on countries.id = locations.country_id

    		WHERE locations.id IN ($location_id)

    		GROUP BY locations.id

    		ORDER BY locations.name ASC";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function org_location_used( $location_id = null ){

    	$data = [];
    	if(isset($location_id) && !empty($location_id)) {
    		$location_id = implode(',', $location_id);
    		$qry =  "SELECT count(ud.user_id) as used_user FROM user_details ud LEFT JOIN users u ON u.id = ud.user_id LEFT JOIN locations ol ON ol.id = ud.location_id WHERE ol.id IN ($location_id) AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function user_location_used($orgid = null, $location_id = null ){

    	$data = [];
    	if(isset($location_id) && !empty($location_id)) {
    		$location_id = implode(',', $location_id);
    		$qry =  "SELECT ol.id, count(ud.user_id) as used_user FROM user_details ud LEFT JOIN users u ON u.id = ud.user_id LEFT JOIN locations ol ON ol.id = ud.location_id WHERE ol.id IN ($location_id) AND ud.organization_id = $orgid AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 GROUP BY ol.id";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function reports_from( $user_id = null ){

    	$data = [];
    	if(isset($user_id) && !empty($user_id)) {

    		$qry =  "SELECT ud.user_id, CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name, ud.job_title, ud.profile_pic,ud.organization_id FROM user_details ud WHERE ud.reports_to_id = $user_id";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    	}
    	return $data;
    }

    function dotted_from( $user_id = null ){

    	$dotted_from = [];
    	if(isset($user_id) && !empty($user_id)) {

    		$qry =  "SELECT
					    JSON_ARRAYAGG(
					        JSON_OBJECT(
					            'full_name',
					            CONCAT_WS(
					                ' ',
					                user_details.first_name,
					                user_details.last_name
					            ),
					            'job_title',
					            user_details.job_title,
					            'user_id',
					            user_details.user_id,
					            'profile_pic',
					            user_details.profile_pic,
								'organization_id',
					            user_details.organization_id
					        )
					    ) AS dotted_from
					FROM
					    `users`
					INNER JOIN user_details ON user_details.user_id = users.id
					WHERE
					    users.id IN(
					    SELECT
					        user_dotted_lines.user_id
					    FROM
					        user_dotted_lines
					    WHERE
					        user_details.user_id = user_dotted_lines.user_id AND user_dotted_lines.dotted_user_id = $user_id
					)";

    		$data = ClassRegistry::init('ProjectGroup')->query($qry);
    		$dotted_from = (isset($data[0][0]['dotted_from']) && !empty($data[0][0]['dotted_from'])) ? json_decode($data[0][0]['dotted_from'], true) : false;
    		if($dotted_from){
				usort($dotted_from, function($a, $b) {
				    return $a['full_name'] > $b['full_name'];
				});
			}
    	}
    	return $dotted_from;
    }


    function current_org($user_id = null){
    	$current_user_id = (!isset($user_id) || empty($user_id)) ? $this->Session->read('Auth.User.id') : $user_id;

    	$loggedUser = ClassRegistry::init('User')->query("SELECT organization_id FROM user_details WHERE user_id = '$current_user_id'");
		$loggedUser = $loggedUser[0]['user_details'];
    	return $loggedUser;
    }

	// Project all users
	function project_ssd($project_id = null) {
		$data = [];
		$query = "select user_id from user_permissions where project_id = $project_id  and workspace_id is null";

		$users = ClassRegistry::init('UserPermission')->query($query);
		if(isset($users) && !empty($users)) {
			$users = Set::extract($users, '{n}.user_permissions.user_id');
			$users = implode(',', $users);

			$ssd_qry = "SELECT
						round(( (SELECT COUNT(DISTINCT(a.skill_id)) skill_match
						FROM project_skills a, user_skills b
						WHERE a.skill_id = b.skill_id AND a.project_id = user_permissions.project_id AND b.user_id IN($users)) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = user_permissions.project_id ) * 100 )) AS skill_match_percent,


						round(( (SELECT COUNT(DISTINCT(ass.subject_id)) subject_matchs
						FROM project_subjects ass, user_subjects us
						WHERE ass.subject_id = us.subject_id AND ass.project_id = user_permissions.project_id AND us.user_id IN($users))/  (select   count(pss.subject_id) from project_subjects pss where pss.project_id = user_permissions.project_id) * 100 )) AS subject_match_percent,

						round(( (SELECT COUNT(DISTINCT(pas.domain_id)) domain_matchs
						FROM project_domains pas, user_domains pus
						WHERE pas.domain_id = pus.domain_id AND pas.project_id = user_permissions.project_id AND pus.user_id IN($users)) /  (select count(pds.domain_id) from project_domains pds where pds.project_id = user_permissions.project_id  ) * 100 )) AS domain_match_percent,

						(SELECT COUNT(DISTINCT(a.skill_id)) FROM project_skills a, user_skills b WHERE a.skill_id = b.skill_id AND a.project_id = user_permissions.project_id AND b.user_id IN($users) ) AS skill_total,

						(SELECT COUNT(DISTINCT(ass.subject_id)) subject_matchs FROM project_subjects ass, user_subjects us WHERE ass.subject_id = us.subject_id AND ass.project_id = user_permissions.project_id AND us.user_id IN($users)) AS sub_total,

						(SELECT COUNT(DISTINCT(pas.domain_id)) domain_matchs
						FROM project_domains pas, user_domains pus
						WHERE pas.domain_id = pus.domain_id AND pas.project_id = user_permissions.project_id AND pus.user_id IN($users)) AS domain_total

						FROM user_permissions

						INNER JOIN projects
						ON user_permissions.project_id = projects.id

						Where projects.id = $project_id
						GROUP BY projects.id
					";
					// pr($ssd_qry, 1);
			$data = ClassRegistry::init('UserPermission')->query($ssd_qry);
		}
		return $data;
	}

	// Project signoff wsp counters
	function WspSOCount($project_id = null ){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT

				sum(Workspace.sign_off != 1) AS PRG

			FROM user_permissions
			INNER JOIN workspaces Workspace on
				user_permissions.workspace_id = Workspace.id
			WHERE user_permissions.project_id = $project_id
				AND user_permissions.area_id is NULL
				and user_permissions.workspace_id is not null";


		$result =  ClassRegistry::init('UserPermission')->query($query);

		return (isset($result) && !empty($result)) ? $result : array();
	}
	// Project signoff comments entered or not
	function projectSignoffComments($project_id = null ){

		$user_id = $this->Session->read('Auth.User.id');

		return ClassRegistry::init('SignoffProject')->find('count', array('conditions'=>array('SignoffProject.project_id'=>$project_id) ));
	}


	// Project summary data
	function project_summary($project_id = null, $page = 0, $limit = 50){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT

					projects.id AS pid,
					workspaces.id AS wsid,
					workspaces.title,
					workspaces.start_date,
					workspaces.end_date,
					workspaces.sign_off,
					workspaces.sign_off_date,
					workspaces.color_code,

					owners_count.owner_count,
                    sharers_count.sharer_count,

                    currencies.sign,

                    cost_count.spcost,
                    cost_count.escost,

                    els.elids,

				    (CASE
						WHEN (workspaces.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) AND workspaces.sign_off!=1  ) THEN 'progressing'
						WHEN ((DATE(NOW()) < DATE(workspaces.start_date)) AND workspaces.sign_off!=1 ) THEN 'not_started'
						WHEN ((DATE(workspaces.end_date) < DATE(NOW())) AND workspaces.sign_off!=1 ) THEN 'overdue'
						ELSE 'not_set'
					END) AS ws_status,
					user_permissions.role AS project_role,

					sum(elements.date_constraints=0) AS NON,
					sum(DATE(NOW())<DATE(elements.start_date) AND elements.sign_off!=1 AND elements.date_constraints=1) AS PND,
					sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) AND elements.sign_off!=1 AND elements.date_constraints=1  ) AS PRG,
					sum(DATE(elements.end_date)<DATE(NOW()) AND elements.sign_off!=1 AND elements.date_constraints=1) AS OVD,
					sum(elements.sign_off=1) AS CMP,

					(CASE
						WHEN (user_permissions.role='Owner' ) THEN 'r_project'
						WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
						WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
						WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
						ELSE 'm_project'
					END) AS prj_type


				FROM
					user_permissions
					LEFT JOIN projects
						ON user_permissions.project_id = projects.id
					LEFT JOIN workspaces
						ON user_permissions.workspace_id = workspaces.id
					LEFT JOIN project_workspaces
						ON project_workspaces.workspace_id = workspaces.id
					LEFT JOIN elements
						ON user_permissions.element_id = elements.id

					LEFT JOIN currencies
						ON currencies.id = projects.currency_id

					LEFT JOIN (
                    	SELECT COUNT(*) AS owner_count, up.project_id, up.workspace_id FROM user_permissions up WHERE up.role IN ('Creator', 'Owner', 'Group Owner') AND up.workspace_id IS NOT null AND up.area_id IS NULL GROUP BY up.workspace_id
                    )
                     AS owners_count
                    ON owners_count.project_id = user_permissions.project_id AND owners_count.workspace_id = user_permissions.workspace_id


                    LEFT JOIN (
                    	SELECT COUNT(*) AS sharer_count, up.project_id, up.workspace_id FROM user_permissions up WHERE up.role IN ('Sharer', 'Group Sharer') AND up.workspace_id IS NOT null AND up.area_id IS NULL GROUP BY up.workspace_id
                    )
                     AS sharers_count
                    ON sharers_count.project_id = user_permissions.project_id AND sharers_count.workspace_id = user_permissions.workspace_id

                    LEFT JOIN (
                    	SELECT SUM(ec.spend_cost) AS spcost, SUM(ec.estimated_cost) AS escost,up.user_id,up.workspace_id
                    	FROM element_costs ec
                    	LEFT JOIN user_permissions up
                        ON up.element_id = ec.element_id

                        WHERE up.element_id IS NOT NULL AND
                        	up.area_id IS NOT NULL AND
                        	up.project_id = $project_id AND
                        	up.user_id = $user_id
                        GROUP BY up.workspace_id
                    )
                    AS cost_count
                    ON cost_count.workspace_id = user_permissions.workspace_id AND cost_count.user_id = user_permissions.user_id

                    LEFT JOIN (
                    	SELECT GROUP_CONCAT(up.element_id) AS elids, up.user_id,up.workspace_id
                    	FROM user_permissions up

                        WHERE up.element_id IS NOT NULL AND
                        	up.area_id IS NOT NULL AND
                        	up.project_id = $project_id AND
                        	up.user_id = $user_id
                        GROUP BY up.workspace_id
                    )
                    AS els
                    ON els.workspace_id = user_permissions.workspace_id AND els.user_id = user_permissions.user_id

				WHERE
					projects.id = $project_id AND
					user_permissions.user_id = $user_id AND
					user_permissions.workspace_id IS NOT NULL

				GROUP BY user_permissions.workspace_id

				ORDER BY project_workspaces.sort_order ASC
				LIMIT $page, $limit";


		$result =  ClassRegistry::init('Project')->query($query);
		// pr($result,1);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	// Project summary count rows
	function project_summary_count($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT count(user_permissions.workspace_id) AS counter
					FROM
						user_permissions

					WHERE
						user_permissions.project_id = $project_id AND
						user_permissions.user_id = $user_id AND
	                    user_permissions.workspace_id IS NOT NULL AND
	                    user_permissions.area_id IS NULL ";


		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	// Project summary count rows
	function summary_options($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					up.role as prj_role,
					(CASE
						WHEN (up.role = 'Creator' OR up.role = 'Owner' OR up.role = 'Group Owner') THEN 1
						ELSE 0
					END) AS user_role,
					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'

						ELSE 'not_spacified'
					END) AS prj_status,

		            wsps.prg_wsp_count,
		            all_wsp.wsp_count,
		            rsk.risk_count,

					count(signoff_projects.id) AS signoff_comments,

					up.permit_edit,

					projects.id, projects.start_date, projects.end_date, projects.sign_off_date, projects.sign_off


				FROM user_permissions up

				INNER JOIN projects
					ON up.project_id = projects.id

				LEFT JOIN signoff_projects
					ON up.project_id = signoff_projects.project_id

				LEFT JOIN (
                	SELECT count(workspaces.id) AS prg_wsp_count, user_permissions.project_id AS pid
                    FROM user_permissions
                    INNER JOIN workspaces on
                        user_permissions.workspace_id = workspaces.id
                    WHERE
                    	user_permissions.project_id = $project_id AND
                        user_permissions.area_id is NULL AND
                        user_permissions.workspace_id is not null AND
                    	workspaces.sign_off != 1 AND
                		user_permissions.user_id = $user_id

                ) AS wsps
					ON up.project_id = wsps.pid


				LEFT JOIN (
                	SELECT count(workspaces.id) AS wsp_count, user_permissions.project_id AS pid
                    FROM user_permissions
                    INNER JOIN workspaces on
                        user_permissions.workspace_id = workspaces.id
                    WHERE
                    	user_permissions.project_id = $project_id AND
                        user_permissions.area_id is NULL AND
                        user_permissions.workspace_id is not null AND
                		user_permissions.user_id = $user_id

                ) AS all_wsp
					ON up.project_id = all_wsp.pid


               LEFT JOIN (
                	SELECT count(rm_details.id) AS risk_count, user_permissions.project_id AS pid
                    FROM user_permissions
                    INNER JOIN rm_details on
                        user_permissions.project_id = rm_details.project_id
                    WHERE
                	 	user_permissions.project_id = $project_id AND
                     	user_permissions.area_id is NULL AND
                    	user_permissions.workspace_id is not null AND
                		rm_details.status != 3 AND
                		user_permissions.user_id = $user_id

                ) AS rsk
					ON up.project_id = rsk.pid

				LEFT JOIN rm_details
					ON up.project_id = rm_details.project_id

				WHERE
					projects.id = $project_id AND
					up.user_id = $user_id AND
					up.workspace_id is null /*AND
					up.role = 'Creator'*/

				GROUP BY up.project_id";


		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}
	// Project summary count rows
	function summary_risks($project_id = null, $element_id = null, $type = 'high'){

		$user_id = $this->Session->read('Auth.User.id');

		if($type == 'high'){
			$res_cond = "( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND ";
		}
		else{
			$res_cond = "( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) AND ";
		}

		$query = "SELECT count(DISTINCT(rd.id)) risk_total
					FROM rm_details rd
					LEFT JOIN rm_expose_responses res
						ON rd.id = res.rm_detail_id
					LEFT JOIN rm_users ru
						ON rd.id = ru.rm_detail_id
					LEFT JOIN rm_elements re
						ON rd.id = re.rm_detail_id
					WHERE
						rd.user_id = $user_id AND
						rd.project_id = $project_id AND
					    rd.status IN(1,2,4) AND
					    $res_cond
						re.element_id IN ($element_id)";


		$result =  ClassRegistry::init('Project')->query($query);
		// pr($result);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	// for project summary page
	function summary_data($project_id = null, $page = 0, $limit = 50){
		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					#details
				    pwd.pid,
				    pwd.ws_id,
				    pwd.role,
				    pwd.title,
				    pwd.start_date,
				    pwd.end_date,
				    pwd.ws_status,
				    pwd.sign_off,
				    pwd.sign_off_date,
				    pwd.color_code,
				    pwd.prj_type,
				    #team
				    wtc.owner_count,
				    wtc.sharer_count,
				    #work
				    wec.NON,
				    wec.PND,
				    wec.PRG,
				    wec.OVD,
				    wec.CMP,
				    #assets
				    (wdx.el_tot + wdx.en_tot + wdx.ed_tot + wdx.em_tot + wdc.dc_prg + wdc.dc_cmp + wfc.fb_nst + wfc.fb_prg + wfc.fb_ovd + wfc.fb_cmp + wvc.vt_nst + wvc.vt_prg + wvc.vt_ovd + wvc.vt_cmp) AS as_tot,
				    wdx.el_tot,
				    wdx.en_tot,
				    wdx.ed_tot,
				    wdx.em_tot,
				    (wdc.dc_prg + wdc.dc_cmp) AS dc_tot,
				    wdc.dc_prg,
				    wdc.dc_cmp,
				    (wfc.fb_nst + wfc.fb_prg + wfc.fb_ovd + wfc.fb_cmp) AS fb_tot,
				    wfc.fb_nst,
				    wfc.fb_prg,
				    wfc.fb_ovd,
				    wfc.fb_cmp,
				    (wvc.vt_nst + wvc.vt_prg + wvc.vt_ovd + wvc.vt_cmp) AS vt_tot,
				    wvc.vt_nst,
				    wvc.vt_prg,
				   	wvc.vt_ovd,
				    wvc.vt_cmp,
				    #costs
					pwd.sign,
				    wse.spcost,
				    wse.escost,
				    #risks
				    wrh.high_risk_total,
				    wrs.severe_risk_total,
					wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,
					wlevel.wlevel_id,
				    efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.none_remaining_hours,
					#ef.remaining_hours_color,
					efforts.change_hours,
					risk_counts.risk_count
				FROM
				(
				    #--------------------------------------------
				    #get project workspace details
				    SELECT
				        up.project_id AS pid,
				        up.workspace_id AS ws_id,
				    	ws.title,
				    	ws.start_date,
				    	ws.end_date,
				    	up.role,
				    	CASE
				    		WHEN ws.sign_off = 1 THEN 'completed'
				    		WHEN Date(NOW()) > Date(ws.end_date) AND ws.end_date IS NOT NULL AND ws.sign_off = 0 THEN 'overdue'
				    		WHEN Date(NOW()) BETWEEN Date(ws.start_date) AND Date(ws.end_date) AND ws.sign_off = 0 THEN 'progressing'
				    		WHEN Date(NOW()) < Date(ws.start_date) AND ws.sign_off = 0 THEN 'not_started'
				    		ELSE 'not_set'
				    	END AS ws_status,
				    	ws.sign_off,
				    	ws.sign_off_date,
				    	ws.color_code,
				    	pw.sort_order,
				    	c.sign,
				    	CASE
				             WHEN (up.role='Owner' OR up.role='Sharer') THEN 'r_project'
				             WHEN (up.role='Group Owner' OR up.role='Group Sharer' ) THEN 'g_project'
				             ELSE 'm_project'
				     	END AS prj_type
				    FROM
				        user_permissions up
				    LEFT JOIN projects p ON
				    	up.project_id = p.id
				    LEFT JOIN currencies c ON
				    	p.currency_id = c.id
				    LEFT JOIN workspaces ws ON
						up.workspace_id = ws.id
				    LEFT JOIN project_workspaces pw ON
				        up.workspace_id = pw.workspace_id
				    WHERE
				        up.project_id = $project_id AND
				        up.user_id = $user_id AND
				        up.workspace_id IS NOT NULL AND
				        up.area_id IS NULL
				    #-------------------------------------------
				) AS pwd #project workspace details
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get team counts
				    SELECT
				    	wur.ws_id,
				        SUM(if(wur.role = 'Creator' OR wur.role = 'Owner' OR wur.role = 'Group Owner',1,0)) AS owner_count,
				        SUM(if(wur.role = 'Sharer' OR wur.role ='Group Sharer',1,0)) AS sharer_count
				    FROM
				    (
				        SELECT
				            up.workspace_id AS ws_id,
				            up.user_id,
				            up.role
				        FROM
				            user_permissions up
				        WHERE
				            up.project_id = $project_id AND # $project_id
				            up.workspace_id IS NOT NULL AND
				            up.area_id IS NULL
				        GROUP BY up.workspace_id, up.user_id
				    ) AS wur #workspace user roles
				    GROUP BY wur.ws_id
				    #-------------------------------------------
				) AS wtc ON #workspace team counts
					pwd.ws_id = wtc.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get task counts
					SELECT
				        up.workspace_id AS ws_id,
				        sum(e.date_constraints=0) AS NON,
				        sum(DATE(NOW())<DATE(e.start_date) AND e.sign_off!=1 AND e.date_constraints=1) AS PND,
				        sum(DATE(NOW()) BETWEEN DATE(e.start_date) AND DATE(e.end_date) AND e.sign_off!=1 AND e.date_constraints=1  ) AS PRG,
				        sum(DATE(e.end_date)<DATE(NOW()) AND e.sign_off!=1 AND e.date_constraints=1) AS OVD,
				        sum(e.sign_off=1) AS CMP
				    FROM
				        user_permissions up
				    LEFT JOIN elements e ON
				        up.element_id = e.id
				    WHERE
				        up.user_id = $user_id # $user_id
				        AND up.project_id = $project_id # $project_id
				        AND up.element_id IS NOT NULL
				    GROUP BY up.workspace_id
				) AS wec ON #workspace element counts
					pwd.ws_id = wec.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get links, notes, documents, mind map status counts
				    SELECT
				        up.workspace_id AS ws_id,
				        COUNT(DISTINCT el.id) AS el_tot,
				        COUNT(DISTINCT en.id) AS en_tot,
				        COUNT(DISTINCT ed.id) AS ed_tot,
				        COUNT(DISTINCT em.id) AS em_tot
				    FROM
				    	user_permissions up
				    LEFT JOIN element_links el ON
				    	up.element_id = el.element_id
				    	AND el.status = 1
				    LEFT JOIN element_notes en ON
				    	up.element_id = en.element_id
				    	AND en.status = 1
				    LEFT JOIN element_documents ed ON
				    	up.element_id = ed.element_id
				    	AND ed.status = 1
				    LEFT JOIN element_mindmaps em ON
				    	up.element_id = em.element_id
				    	AND em.status = 1
				    WHERE
				    	up.user_id = $user_id # $user_id
				    	AND up.area_id IS NOT NULL # elements only
				    GROUP BY up.workspace_id
				) AS wdx ON #workspace x counts
					pwd.ws_id = wdx.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get decision counts
				    SELECT
				        wd.ws_id,
				        COUNT(IF(wd.dc_status = 'In Progress', 1, NULL)) AS dc_prg,
				        COUNT(IF(wd.dc_status = 'Completed', 1, NULL)) AS dc_cmp
				    FROM
				    (
				        SELECT
				            up.workspace_id AS ws_id,
				            up.element_id AS tid,
				            CASE
				                WHEN ed.sign_off = 0 THEN 'In Progress'
				                WHEN ed.sign_off = 1 THEN 'Completed'
				                ELSE 'None'
				            END AS dc_status
				        FROM
				            user_permissions up
				        LEFT JOIN element_decisions ed ON
				            up.element_id = ed.element_id
				        WHERE
				            up.user_id = $user_id # $user_id
				            AND up.area_id IS NOT NULL # elements only
				    ) AS wd #workspace decisions
				    GROUP BY wd.ws_id
				) AS wdc ON #workspace decision counts
					pwd.ws_id = wdc.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get feedback counts
				    SELECT
				        wf.ws_id,
				        COUNT(IF(wf.fb_status = 'Not Started', 1, NULL)) AS fb_nst,
				        COUNT(IF(wf.fb_status = 'In Progress', 1, NULL)) AS fb_prg,
				        COUNT(IF(wf.fb_status = 'Overdue', 1, NULL)) AS fb_ovd,
				        COUNT(IF(wf.fb_status = 'Completed', 1, NULL)) AS fb_cmp
				    FROM
				    (
				        SELECT
				            up.workspace_id AS ws_id,
				            up.element_id AS tid,
				            CASE
				                WHEN fb.sign_off = 1 THEN 'Completed'
				                WHEN Date(NOW()) > Date(fb.end_date) AND fb.end_date IS NOT NULL AND fb.sign_off = 0 THEN 'Overdue'
				                WHEN Date(NOW()) BETWEEN Date(fb.start_date) AND Date(fb.end_date) AND fb.sign_off = 0 THEN 'In Progress'
				                WHEN Date(NOW()) < Date(fb.start_date) AND fb.sign_off = 0 THEN 'Not Started'
				                ELSE 'None'
				            END AS fb_status
				        FROM
				            user_permissions up
				        LEFT JOIN feedback fb ON
				            up.element_id = fb.element_id
				            AND fb.status = 1
				        WHERE
				            up.user_id = $user_id # $user_id
				            AND up.area_id IS NOT NULL # elements only
				    ) AS wf #workspace feedback
				    GROUP BY wf.ws_id
				    ) AS wfc ON #workspace feedback counts
				    	pwd.ws_id = wfc.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get vote counts
				    SELECT
				        wv.ws_id,
				        COUNT(IF(wv.vt_status = 'Not Started', 1, NULL)) AS vt_nst,
				        COUNT(IF(wv.vt_status = 'In Progress', 1, NULL)) AS vt_prg,
				        COUNT(IF(wv.vt_status = 'Overdue', 1, NULL)) AS vt_ovd,
				        COUNT(IF(wv.vt_status = 'Completed', 1, NULL)) AS vt_cmp
				    FROM
				    (
				        SELECT
				            up.workspace_id AS ws_id,
				            up.element_id AS tid,
				            CASE
				                WHEN vt.is_completed = 1 THEN 'Completed'
				                WHEN Date(NOW()) > Date(vt.end_date) AND vt.end_date IS NOT NULL AND vt.is_completed = 0 THEN 'Overdue'
				                WHEN Date(NOW()) BETWEEN Date(vt.start_date) AND Date(vt.end_date) AND vt.is_completed = 0 THEN 'In Progress'
				                WHEN Date(NOW()) < Date(vt.start_date) AND vt.is_completed = 0 THEN 'Not Started'
				                ELSE 'None'
				            END AS vt_status
				        FROM
				            user_permissions up
				        LEFT JOIN votes vt ON
				            up.element_id = vt.element_id
				        WHERE
				            up.user_id = $user_id # user_id
				            AND up.area_id IS NOT NULL # elements only
				    ) AS wv #workspace votes
				    GROUP BY wv.ws_id
				) AS wvc ON #workspace vote counts
					pwd.ws_id = wvc.ws_id

				LEFT JOIN
				(
				    #--------------------------------------------
				    #get level counts
				    SELECT
				        wfl.ws_id as wlevel_id,
				        wfl.level_current as level,
				        wfl.level_count as level_count,
						wfl.confidence_class as confidence_class,
						wfl.confidence_level as confidence_level,
						wfl.confidence_arrow as confidence_arrow

				    FROM
				    (
				        SELECT
				            levels.workspace_id AS ws_id,
				            levels.element_id AS tid,
				            round(AVG(levels.level)) as level_current,
							COUNT(levels.level) as level_count,

					(CASE
						WHEN ( round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'Low'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'Medium Low'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'Medium High'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'red'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'orange'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'yellow'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'lowgrey'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'mediumlowgrey'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'mediumhighgrey'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow

				        FROM
				            element_levels levels
							inner join elements on elements.id = levels.element_id

				        WHERE
				              levels.project_id = $project_id
							   and levels.is_active = 1
					group by ws_id
				    ) AS wfl #workspace

				    ) AS wlevel ON #workspace
				    	pwd.ws_id = wlevel.wlevel_id


				left join (
					SELECT
						ws.workspace_id as workspace_id,
						#...
						#additional columns for top level query:
						ef.total_hours,
						ef.blue_completed_hours,
						ef.green_remaining_hours,
						ef.amber_remaining_hours,
						ef.red_remaining_hours,
						ef.none_remaining_hours,
						#ef.remaining_hours_color,
						ef.change_hours
						#ef.remaining_hours
					FROM #mock top level query
					(
						SELECT id  as workspace_id from workspaces
					) AS ws
					#section to add to top level progress bar query:
					LEFT JOIN #get workspace effort data
					(
						SELECT
							ue.workspace_id,
							#ue.remaining_hours_color,
							SUM(ue.change_hours) change_hours,
							SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
							SUM(ue.completed_hours) AS blue_completed_hours,
							SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours
							#SUM((ue.remaining_hours)) AS remaining_hours
						FROM
						(
							SELECT
								ee.workspace_id,
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
						GROUP BY ue.workspace_id
					) AS ef ON
						ws.workspace_id = ef.workspace_id
				)	as efforts on #wsp
				pwd.ws_id = efforts.workspace_id


				LEFT JOIN
				(
				    #--------------------------------------------
				    #get workspace costs
				    SELECT
				    	up.workspace_id AS ws_id,
				        SUM(ec.spend_cost) AS spcost,
				        SUM(ec.estimated_cost) AS escost
				    FROM
						user_permissions up
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id
					WHERE
				    	up.user_id = $user_id AND # user_id
				        up.project_id = $project_id AND # project_id
				        up.element_id IS NOT NULL
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wse ON #workspace spent estimated
					pwd.ws_id = wse.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get high pending risk counts
				    SELECT
				        up.workspace_id AS ws_id,
				        count(DISTINCT(rd.id)) AS high_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id # user_id
				        AND up.project_id = $project_id # project_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wrh ON #workspace risk high
					pwd.ws_id = wrh.ws_id

				LEFT JOIN (
					SELECT
						up.project_id,
						up.workspace_id AS ws_id,
						COUNT(DISTINCT(rd.id)) AS risk_count
					FROM user_permissions up
					INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
					LEFT JOIN rm_details rd ON
						re.rm_detail_id = rd.id
					WHERE
					    up.user_id = $user_id and
					    up.project_id = $project_id
					    AND up.element_id IS NOT NULL
					    AND rd.id IS NOT NULL


                    GROUP BY up.workspace_id

				) AS risk_counts
				ON risk_counts.ws_id = pwd.ws_id


				LEFT JOIN
				(
				    #--------------------------------------------
				    #get severe pending risk counts
				    SELECT
				        up.workspace_id AS ws_id,
				        COUNT(DISTINCT(rd.id)) AS severe_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id
				        AND up.project_id = $project_id # project_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				            OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wrs ON #workspace risk severe
					pwd.ws_id = wrs.ws_id
				ORDER BY pwd.sort_order ASC
				LIMIT $page, $limit";


		$result =  ClassRegistry::init('Project')->query($query);
		// pr($result, 1);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	function list_count($filter_query = '', $filters = []){
		// mpr($filters, $filter_query);
		$user_id = $this->Session->read('Auth.User.id');

		$filter_status = $filter_role = '';
		$filter_type_join = $filter_type_str = '';
		$filter_member_join = $filter_member_str = $filter_projects_str = $filter_rag = '';
		if(isset($filters) && !empty($filters)) {
			if(isset($filters['status']) && !empty($filters['status'])) {
				$val = implode(',', $filters['status']);
				$filter_status = "AND prj_status IN($val)";
			}
			if(isset($filters['roles']) && !empty($filters['roles'])) {
				$val = $filters['roles'];
				$all_roles = [];
				foreach ($val as $key => $role) {
					if($role == 'Owner'){
						$all_roles[] = 'Owner';
						$all_roles[] = 'Group Owner';
					}
					else if($role == 'Sharer'){
						$all_roles[] = 'Sharer';
						$all_roles[] = 'Group Sharer';
					}
					else{
						$all_roles[] = $role;
					}
				}
				$filter_role = "AND upr.role IN('".implode("','",$all_roles)."')";
			}
			if(isset($filters['task_types']) && !empty($filters['task_types'])) {
				$filter_type_join = 'LEFT JOIN aligneds pet ON pet.id = p.aligned_id';
				$val = implode(',', $filters['task_types']);
				$filter_type_str = "AND p.aligned_id IN ($val)";
			}
			if(isset($filters['members']) && !empty($filters['members'])) {
				$filter_member_join = 'LEFT JOIN user_permissions upr1 ON upr.project_id = upr1.project_id';
				$val = implode(",",$filters['members']);
				$filter_member_str = "AND upr1.user_id IN ($val)";
			}

			// filter programs and projects
			$project_ids = [];
			if(isset($filters['programs']) && !empty($filters['programs'])) {
				$val = implode(",",$filters['programs']);
				$prjs = ClassRegistry::init('Project')->query("SELECT DISTINCT(project_id) AS project_id FROM program_projects WHERE program_id IN($val)");
				if(isset($prjs) && !empty($prjs)){
					$project_ids = Set::extract($prjs, '{n}.program_projects.project_id');
				}
			}
			if(isset($filters['projects']) && !empty($filters['projects'])) {
				$val = $filters['projects'];
				$project_ids = array_merge($project_ids, $val);
			}

			if(isset($project_ids) && !empty($project_ids)) {
				$project_ids = implode(",",$project_ids);
				$filter_projects_str = "AND p.id IN ($project_ids)";
			}

			// filter by rag status
			if(isset($filters['rag']) && !empty($filters['rag'])) {
				$val = $filters['rag'];
				$filter_rag = "AND p_rag IN('".implode("','",$val)."')";
			}
		}

			$query = "SELECT
					prj_detail.prj_role,
					prj_detail.pid,
					prj_detail.ptitle,
					prj_detail.pbudget,
					prj_detail.psdate,
					prj_detail.pedate,
					prj_detail.color_code,
					prj_detail.psodate,
					prj_detail.psign_off,
					prj_detail.prj_status,
					prj_detail.csign,
					prj_detail.permit_edit,
					prag.p_rag

				FROM
				(
					# PROJECT DETAIL
				 	SELECT
				 	upr.role as prj_role,
				 	upr.user_id AS uid,
					p.id AS pid,
					p.title AS ptitle,
					p.budget AS pbudget,
					p.start_date AS psdate,
					p.end_date AS pedate,
					p.color_code AS color_code,
					p.sign_off_date AS psodate,
					p.sign_off AS psign_off,
					currencies.sign AS csign,
					upr.permit_edit,
					(CASE
						WHEN (DATE(NOW())<DATE(p.start_date) AND p.sign_off != 1) THEN '3' # not started
						WHEN (DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date) AND p.sign_off != 1) THEN '2' # progressing
						WHEN (DATE(p.end_date)<DATE(NOW()) AND p.sign_off != 1) THEN '1' # overdue
						WHEN (p.sign_off = 1) THEN '4' # completed
						ELSE '5' # not set
					END) AS prj_status

					FROM
				        user_permissions upr
				    $filter_member_join
				    LEFT JOIN projects p
				    	ON upr.project_id = p.id
					LEFT JOIN currencies
						ON currencies.id = p.currency_id
					$filter_type_join

					WHERE
						upr.user_id = $user_id
						$filter_type_str
						$filter_member_str
						$filter_role
						$filter_projects_str

					GROUP BY upr.project_id

				) AS prj_detail

					# PROJECT RAG STATUS
				LEFT JOIN (
		           	SELECT
			           	p.rag_status,
						up.project_id,
						SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1) AS elovd,
						COUNT(el.id) AS elall,
						ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100) AS ovd_percent,
						(CASE
							WHEN (
								(p.rag_status IS NOT NULL AND p.rag_status = 3) AND
						      	(prg.amber_value > 0 OR prg.red_value > 0)
							) THEN 'Rules'
							ELSE 'Manual'
						END) AS rag_type,

						(CASE
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 1 ) THEN 'red'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 2 ) THEN 'yellow'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 3 )
							THEN
								CASE
						            WHEN (
						            	(prg.amber_value > 0 AND prg.red_value > 0) AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) < prg.red_value
					            	)
					                THEN 'yellow'
					                WHEN (
					                	(prg.amber_value > 0 AND prg.red_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                		( prg.amber_value IS NULL OR prg.amber_value <= 0 OR prg.amber_value = '' ) AND prg.red_value > 0) AND
					                		((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                	(prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'yellow'
					                ELSE 'green-bg'
						        END
							ELSE 'green-bg'
						END) AS p_rag

					FROM user_projects up
				 	LEFT JOIN projects p
						ON up.project_id = p.id
				 	LEFT JOIN user_permissions ups
						ON ups.project_id = up.project_id
					LEFT JOIN elements el
						ON ups.element_id = el.id AND ups.element_id IS NOT NULL

				 	LEFT JOIN project_rags prg
						ON up.project_id = prg.project_id

					WHERE ups.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS prag
				ON prag.project_id = prj_detail.pid

				WHERE pid != ''
				$filter_query
				$filter_status
				$filter_rag
			";
					// pr($query, 1);
		$result =  ClassRegistry::init('Project')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function project_listing($page = 0, $limit = 50, $order = 'ORDER BY prj_status, ptitle ASC', $filter_query = ' 1', $filters = []){
		// mpr( $filters);die;
		$user_id = $this->Session->read('Auth.User.id');

		  //pr($order );
		if(!empty($order) && $order == "ORDER BY p_rag desc" ||  $order == "ORDER BY p_rag DESC"){
				$order = ' order by CASE
						WHEN p_rag="green-bg" THEN 3
						WHEN p_rag="yellow" THEN 2
						WHEN p_rag="red" THEN 1

					END desc';
		    }else if(!empty($order) && $order == "ORDER BY p_rag ASC" ||  $order == "ORDER BY p_rag asc"){
					$order = ' order by CASE
						WHEN p_rag="green-bg" THEN 3
						WHEN p_rag="yellow" THEN 2
						WHEN p_rag="red" THEN 1

					END asc';
			}else{
				$order = $order;
			}

		    if(!empty($order) && $order == "ORDER BY confidence_level DESC" ||  $order == "ORDER BY confidence_level desc"){
				$order = 'ORDER BY -confidence_order desc,confidence_order desc';
		    }else if(!empty($order) && $order == "ORDER BY confidence_level ASC" ||  $order == "ORDER BY confidence_level asc"){
				$order = 'ORDER BY -confidence_order_asc  DESC , confidence_order_asc DESC';
			}else{
				$order = $order;
			}

		$filter_status = $filter_role = '';
		$filter_type_join = $filter_type_str = '';
		$filter_member_join = $filter_member_str = $filter_projects_str = $filter_rag = '';
		if(isset($filters) && !empty($filters)) {
			if(isset($filters['status']) && !empty($filters['status'])) {
				$val = implode(',', $filters['status']);
				$filter_status = "AND prj_status IN($val)";
			}
			if(isset($filters['roles']) && !empty($filters['roles'])) {
				$val = $filters['roles'];
				$all_roles = [];
				foreach ($val as $key => $role) {
					if($role == 'Owner'){
						$all_roles[] = 'Owner';
						$all_roles[] = 'Group Owner';
					}
					else if($role == 'Sharer'){
						$all_roles[] = 'Sharer';
						$all_roles[] = 'Group Sharer';
					}
					else{
						$all_roles[] = $role;
					}
				}
				$filter_role = "AND upr.role IN('".implode("','",$all_roles)."')";
			}
			if(isset($filters['task_types']) && !empty($filters['task_types'])) {
				$filter_type_join = 'LEFT JOIN aligneds pet ON pet.id = p.aligned_id';
				$val = implode(',', $filters['task_types']);
				$filter_type_str = "AND p.aligned_id IN ($val)";
			}
			if(isset($filters['members']) && !empty($filters['members'])) {
				$filter_member_join = 'LEFT JOIN user_permissions upr1 ON upr.project_id = upr1.project_id';
				$val = implode(",",$filters['members']);
				$filter_member_str = "AND upr1.user_id IN ($val)";
			}

			// filter programs and projects
			$project_ids = [];
			if(isset($filters['programs']) && !empty($filters['programs'])) {
				$val = implode(",",$filters['programs']);
				$prjs = ClassRegistry::init('Project')->query("SELECT DISTINCT(project_id) AS project_id FROM program_projects WHERE program_id IN($val)");
				if(isset($prjs) && !empty($prjs)){
					$project_ids = Set::extract($prjs, '{n}.program_projects.project_id');
				}
			}
			if(isset($filters['projects']) && !empty($filters['projects'])) {
				$val = $filters['projects'];
				$project_ids = array_merge($project_ids, $val);
			}

			if(isset($project_ids) && !empty($project_ids)) {
				$project_ids = implode(",",$project_ids);
				$filter_projects_str = "AND p.id IN ($project_ids)";
			}

			// filter by rag status
			if(isset($filters['rag']) && !empty($filters['rag'])) {
				$val = $filters['rag'];
				$filter_rag = "AND p_rag IN('".implode("','",$val)."')";
			}
			// pr($project_ids);
		}

		 $query = "SELECT
					prj_detail.prj_role,
					prj_detail.pid,
					prj_detail.ptitle,
					prj_detail.pbudget,
					prj_detail.psdate,
					prj_detail.pedate,
					prj_detail.color_code,
					prj_detail.psodate,
					prj_detail.psign_off,
					prj_detail.prj_status,
					prj_detail.csign,
					prj_detail.permit_edit,
					oc.owner_count,
					sc.sharer_count,
					ec.enon,
					ec.epnd,
					ec.eprg,
					ec.eovd,
					ec.ecmp,
					ec.total_tasks,
					ew.wnon,
					ew.wpnd,
					ew.wprg,
					ew.wovd,
					ew.wcmp,
					ew.total_workspaces,
					pse.spcost,
					pse.escost,
					cost_status.c_status,
					prh.high_risk_total,
					prs.severe_risk_total,
					if(prh.high_risk_total > 0 , prh.high_risk_total , 0) + if(prs.severe_risk_total > 0 , prs.severe_risk_total , 0) AS ps_risk_total,
					pcc.comments_count,
					prag.rag_status,
					prag.p_rag,
					prag.rag_type,
					prag.ovd_percent,
					prag.elovd,
					prag.elall,
					board.pb_count,
					wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,
					wlevel.wlevel_id,
					wlevel.confidence_order as confidence_order,
					wlevel.confidence_order_asc as confidence_order_asc ,
					efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.none_remaining_hours,
					efforts.change_hours,
					efforts.remaining_hours,
					efforts.completed_hours,
					porgss.porgs_count,
					risk_counts.risk_count

				FROM
				(
					# PROJECT DETAIL
				 	SELECT
				 	upr.role as prj_role,
				 	upr.user_id AS uid,
					p.id AS pid,
					p.title AS ptitle,
					p.budget AS pbudget,
					p.start_date AS psdate,
					p.end_date AS pedate,
					p.color_code AS color_code,
					p.sign_off_date AS psodate,
					p.sign_off AS psign_off,
					currencies.sign AS csign,
					upr.permit_edit,
					(CASE
						WHEN (DATE(NOW())<DATE(p.start_date) AND p.sign_off != 1) THEN '3' # not started
						WHEN (DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date) AND p.sign_off != 1) THEN '2' # progressing
						WHEN (DATE(p.end_date)<DATE(NOW()) AND p.sign_off != 1) THEN '1' # overdue
						WHEN (p.sign_off = 1) THEN '4' # completed
						ELSE '5' # not set
					END) AS prj_status

					FROM
				        user_permissions upr
				    $filter_member_join
				    LEFT JOIN projects p
				    	ON upr.project_id = p.id
					LEFT JOIN currencies
						ON currencies.id = p.currency_id
					$filter_type_join

					WHERE
						upr.user_id = $user_id
						$filter_type_str
						$filter_member_str
						$filter_role
						$filter_projects_str

					GROUP BY upr.project_id

				) AS prj_detail

			    #get level counts
				LEFT JOIN
				(
				    #--------------------------------------------
				    SELECT
				        wfl.ws_id as wlevel_id,
				        wfl.level_current as level,
				        wfl.level_count as level_count,
						wfl.confidence_class as confidence_class,
						wfl.confidence_level as confidence_level,
						wfl.confidence_arrow as confidence_arrow,
						wfl.confidence_order as confidence_order,
						wfl.confidence_order_asc as confidence_order_asc

				    FROM
				    (
				        SELECT
				            levels.project_id AS ws_id,
				            levels.element_id AS tid,
				            round(AVG(levels.level)) as level_current,
							COUNT(levels.level) as level_count,
					(CASE
						WHEN ( round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'Low'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'Medium Low'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'Medium High'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'red'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'orange'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'yellow'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'lowgrey'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'mediumlowgrey'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'mediumhighgrey'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow,

					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 1
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 2
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 3
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 4
						ELSE 0 # not set
					END) AS confidence_order,
                    (CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 4
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 3
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 2
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 1
						ELSE 5 # not set
					END) AS confidence_order_asc


				        FROM
				            element_levels levels
							INNER JOIN elements on elements.id = levels.element_id

				        WHERE
							levels.is_active = 1
					group by ws_id
				    ) AS wfl #workspace

				    ) AS wlevel ON #workspace
				    	prj_detail.pid = wlevel.wlevel_id

		    	#efforts
				LEFT JOIN(
				    SELECT
					pr.project_id project_id,
					#...
					#additional columns for top level query:
					ef.total_hours,
					ef.blue_completed_hours,
					ef.green_remaining_hours,
					ef.amber_remaining_hours,
					ef.red_remaining_hours,
					ef.none_remaining_hours,
					#ef.remaining_hours_color,
					ef.change_hours,
					ef.remaining_hours,
					ef.completed_hours


					FROM #mock top level query
					(
						SELECT id AS project_id from projects
					) AS pr
					#section to add to top level progress bar query:
					LEFT JOIN #get project effort data
					(
						SELECT
							ue.project_id,
							#ue.remaining_hours_color,
							SUM(ue.change_hours) change_hours,
							SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
							SUM(ue.completed_hours) AS blue_completed_hours,
							SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
							SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours,
							SUM(ue.remaining_hours) AS remaining_hours,
							SUM(ue.completed_hours) AS completed_hours
						FROM
						(
							SELECT
								ee.project_id,
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
						GROUP BY ue.project_id
					) AS ef ON
						pr.project_id = ef.project_id

				) as efforts ON #project
				    	prj_detail.pid = efforts.project_id

		    	# OWNER COUNT
				LEFT JOIN (
		           SELECT COUNT(*) AS owner_count, up.project_id FROM user_permissions up WHERE up.role IN ('Creator', 'Owner', 'Group Owner') AND up.workspace_id IS NULL GROUP BY up.project_id
		       	) AS oc
				ON oc.project_id = prj_detail.pid

				# SHARER COUNT
				LEFT JOIN (

		           SELECT COUNT(*) AS sharer_count, up.project_id FROM user_permissions up WHERE up.role IN ('Sharer', 'Group Sharer') AND up.workspace_id IS NULL GROUP BY up.project_id
		       	) AS sc
				ON sc.project_id = prj_detail.pid

				# TASKS COUNT
				LEFT JOIN (
		           	SELECT
			           	SUM(elements.date_constraints = 0 AND elements.sign_off != 1) AS enon,
						SUM(DATE(NOW())<DATE(elements.start_date) AND elements.sign_off!=1 AND elements.date_constraints=1) AS epnd,
						SUM(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) AND elements.sign_off!=1 AND elements.date_constraints=1  ) AS eprg,
						SUM(DATE(elements.end_date)<DATE(NOW()) AND elements.sign_off!=1 AND elements.date_constraints=1) AS eovd,
						SUM(elements.sign_off=1) AS ecmp,
						up.project_id,
						COUNT(DISTINCT(up.element_id)) AS total_tasks
				 	FROM user_permissions up
				 	LEFT JOIN elements
						ON up.element_id = elements.id
					WHERE
						#up.role = 'Creator' AND
						up.element_id IS NOT NULL AND
						up.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS ec
				ON ec.project_id = prj_detail.pid

				# WORKSPACES COUNT
				LEFT JOIN (
		           	SELECT
			           	SUM( (workspaces.start_date IS NULL OR workspaces.start_date = '') AND (workspaces.end_date IS NULL OR workspaces.end_date = '') AND workspaces.sign_off!=1) AS wnon,
						SUM(DATE(NOW())<DATE(workspaces.start_date) AND workspaces.sign_off!=1) AS wpnd,
						SUM(DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) AND workspaces.sign_off!=1) AS wprg,
						SUM(DATE(workspaces.end_date)<DATE(NOW()) AND workspaces.sign_off!=1) AS wovd,
						SUM(workspaces.sign_off=1) AS wcmp,
						up.project_id,
						COUNT(DISTINCT(up.workspace_id)) AS total_workspaces
				 	FROM user_permissions up
				 	LEFT JOIN workspaces
						ON up.workspace_id = workspaces.id
					WHERE
						up.workspace_id IS NOT NULL AND
						up.area_id IS NULL AND
						up.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS ew
				ON ew.project_id = prj_detail.pid

				#est/spend cost
				LEFT JOIN (
					# COSTS
				    SELECT
				    	up.project_id,
				        SUM(ec.spend_cost) AS spcost,
				        SUM(ec.estimated_cost) AS escost
				    FROM
						user_permissions up
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id
					WHERE
				    	up.user_id = $user_id AND
				        up.element_id IS NOT NULL
				    GROUP BY up.project_id
				) AS pse
				ON pse.project_id = prj_detail.pid


				# HIGHT PENDING RISK COUNT
				LEFT JOIN (
				    SELECT
				        up.project_id,
				        count(DISTINCT(rd.id)) AS high_risk_total
				    FROM
				        user_permissions up

				    LEFT JOIN rm_details rd ON
				        up.project_id = rd.project_id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				    	#up.role IN ('Creator','Owner','Group Owner') OR
				        up.user_id = $user_id
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.project_id
				) AS prh
					ON prh.project_id = prj_detail.pid

				# SEVERE PENDING RISK COUNT
				LEFT JOIN (
				    SELECT
				        up.project_id,
				        count(DISTINCT(rd.id)) AS severe_risk_total
				    FROM
				        user_permissions up

				    LEFT JOIN rm_details rd ON
				        up.project_id = rd.project_id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        #up.role IN ('Creator','Owner','Group Owner') OR
				        up.user_id = $user_id
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5) OR
				            (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.project_id
				) AS prs
					ON prs.project_id = prj_detail.pid

				# PROJECT COMMENTS COUNT
				LEFT JOIN (
		           	SELECT
			           	count(pc.project_id) AS comments_count,
						up.id AS project_id
				 	FROM projects up
				 	LEFT JOIN project_comments pc
						ON up.id = pc.project_id

				 	GROUP BY up.id
		       	) AS pcc
				ON pcc.project_id = prj_detail.pid

				# PROJECT COST STATUS
				LEFT JOIN (
				    SELECT
				    	up.project_id,
						(CASE
							WHEN (
								(
									SUM(ec.estimated_cost) = '' OR
									SUM(ec.estimated_cost) = 0 OR
									SUM(ec.estimated_cost) IS NULL) AND
								(
									SUM(ec.spend_cost) = '' OR
									SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'Not Set'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(
									SUM(ec.spend_cost) = '' OR SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'Budget Set'

							WHEN (
								(SUM(ec.estimated_cost) = '' OR SUM(ec.estimated_cost) = 0 OR SUM(ec.estimated_cost) IS NULL) AND
								(SUM(ec.spend_cost) > 0)
							) THEN 'Costs Incurred'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(SUM(ec.spend_cost) > 0 AND (SUM(ec.spend_cost) > SUM(ec.estimated_cost)))
							) THEN 'Over Budget'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND (SUM(ec.spend_cost) > 0)  AND
								(SUM(ec.spend_cost) <= SUM(ec.estimated_cost))
							) THEN 'On Budget'

							ELSE 'Not Set'
						END) AS c_status

				    FROM
						user_permissions up
				    LEFT JOIN projects p ON
				    	up.project_id = p.id
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id AND up.element_id IS NOT NULL
					WHERE
				    	up.user_id = $user_id
				    GROUP BY up.project_id
				) AS cost_status
				ON cost_status.project_id = prj_detail.pid


				LEFT JOIN (
					SELECT count(porgs.id) AS porgs_count, porgs.project_id
					FROM project_opp_orgs porgs
					LEFT JOIN projects p
						ON p.id = porgs.project_id
					GROUP BY p.id
				) AS porgss
				ON porgss.project_id = prj_detail.pid


				LEFT JOIN (
					SELECT count(pb.id) AS pb_count, pb.project_id
					FROM project_boards pb
					LEFT JOIN projects p
						ON p.id = pb.project_id
					WHERE
						(pb.project_status = 0 OR pb.project_status IS NULL)

				) AS board
				ON board.project_id = prj_detail.pid


				LEFT JOIN (
					SELECT
						up.project_id,
						COUNT(DISTINCT(rd.id)) AS risk_count
					FROM user_permissions up
					LEFT JOIN projects p ON
						up.project_id = p.id
					LEFT JOIN rm_details rd ON
						up.project_id = rd.project_id
					LEFT JOIN rm_users ru ON
						rd.id = ru.rm_detail_id

					WHERE
					    up.user_id = $user_id
					    AND up.workspace_id IS NULL
					    AND rd.id IS NOT NULL
					    AND (
	                        up.role IN('Owner', 'Creator', 'Group Owner') OR
					        rd.created_by = $user_id
					        OR ru.user_id = $user_id
						)

                    GROUP BY up.project_id

				) AS risk_counts
				ON risk_counts.project_id = prj_detail.pid


					# PROJECT RAG STATUS
				LEFT JOIN (
		           	SELECT
			           	p.rag_status,
						up.project_id,
						SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1) AS elovd,
						COUNT(el.id) AS elall,
						ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100) AS ovd_percent,
						(CASE
							WHEN (
								(p.rag_status IS NOT NULL AND p.rag_status = 3) AND
						      	(prg.amber_value > 0 OR prg.red_value > 0)
							) THEN 'Rules'
							ELSE 'Manual'
						END) AS rag_type,

						(CASE
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 1 ) THEN 'red'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 2 ) THEN 'yellow'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 3 )
							THEN
								CASE
						            WHEN (
						            	(prg.amber_value > 0 AND prg.red_value > 0) AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) < prg.red_value
					            	)
					                THEN 'yellow'
					                WHEN (
					                	(prg.amber_value > 0 AND prg.red_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                		( prg.amber_value IS NULL OR prg.amber_value <= 0 OR prg.amber_value = '' ) AND prg.red_value > 0) AND
					                		((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                	(prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'yellow'
					                ELSE 'green-bg'
						        END
							ELSE 'green-bg'
						END) AS p_rag

					FROM user_projects up
				 	LEFT JOIN projects p
						ON up.project_id = p.id
				 	LEFT JOIN user_permissions ups
						ON ups.project_id = up.project_id
					LEFT JOIN elements el
						ON ups.element_id = el.id AND ups.element_id IS NOT NULL

				 	LEFT JOIN project_rags prg
						ON up.project_id = prg.project_id

					WHERE ups.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS prag
				ON prag.project_id = prj_detail.pid


				WHERE $filter_query
				$filter_status
				$filter_rag

				$order

				LIMIT $page, $limit

			";



		// pr($query);
		$result =  ClassRegistry::init('Project')->query($query);


		return (isset($result) && !empty($result)) ? $result : [];
	}

	public function wsp_cost_status_text( $estimatcost = null, $spendcost = null) {

		$costStatus = '';
		if ( (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0) ) {

			$costStatus = 'Not Set';

		} else if ( (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = 'Budget Set';

		} else if ( (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0)) {

			$costStatus = 'Costs Incurred';

		}else if ( (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0) && $spendcost > $estimatcost) {

			$costStatus = 'Over Budget';

		}else if ( (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0) && $spendcost <= $estimatcost ) {

			$costStatus = 'On Budget';

		}

		return $costStatus;
	}

	function listing_row($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					prj_detail.prj_role,
					prj_detail.pid,
					prj_detail.ptitle,
					prj_detail.pbudget,
					prj_detail.psdate,
					prj_detail.pedate,
					prj_detail.color_code,
					prj_detail.psodate,
					prj_detail.psign_off,
					prj_detail.prj_status,
					prj_detail.csign,
					prj_detail.permit_edit,
					oc.owner_count,
					sc.sharer_count,
					ec.enon,
					ec.epnd,
					ec.eprg,
					ec.eovd,
					ec.ecmp,
					ec.total_tasks,
					ew.wnon,
					ew.wpnd,
					ew.wprg,
					ew.wovd,
					ew.wcmp,
					ew.total_workspaces,
					pse.spcost,
					pse.escost,
					cost_status.c_status,
					prh.high_risk_total,
					prs.severe_risk_total,
					if(prh.high_risk_total > 0 , prh.high_risk_total , 0) + if(prs.severe_risk_total > 0 , prs.severe_risk_total , 0) AS ps_risk_total,
					pcc.comments_count,
					prag.rag_status,
					prag.p_rag,
					prag.rag_type,
					prag.ovd_percent,
					board.pb_count,
					wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,
					wlevel.wlevel_id,
					efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.none_remaining_hours,
					efforts.change_hours,
					porgss.porgs_count

				FROM
				(
					# PROJECT DETAIL
				 	SELECT
				 	upr.role as prj_role,
				 	upr.user_id AS uid,
					p.id AS pid,
					p.title AS ptitle,
					p.budget AS pbudget,
					p.start_date AS psdate,
					p.end_date AS pedate,
					p.color_code AS color_code,
					p.sign_off_date AS psodate,
					p.sign_off AS psign_off,
					currencies.sign AS csign,
					upr.permit_edit,
					(CASE
						WHEN (DATE(NOW())<DATE(p.start_date) AND p.sign_off != 1) THEN '3' # not started
						WHEN (DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date) AND p.sign_off != 1) THEN '2' # progressing
						WHEN (DATE(p.end_date)<DATE(NOW()) AND p.sign_off != 1) THEN '1' # overdue
						WHEN (p.sign_off = 1) THEN '4' # completed
						ELSE '5' # not set
					END) AS prj_status

					FROM
				        user_permissions upr
				    LEFT JOIN projects p
				    	ON upr.project_id = p.id
					LEFT JOIN currencies
						ON currencies.id = p.currency_id

					WHERE
						upr.user_id = $user_id

					GROUP BY upr.project_id

				) AS prj_detail

				LEFT JOIN
				(
				    #--------------------------------------------
				    #get level counts
				    SELECT
				        wfl.ws_id as wlevel_id,
				        wfl.level_current as level,
				        wfl.level_count as level_count,
						wfl.confidence_class as confidence_class,
						wfl.confidence_level as confidence_level,
						wfl.confidence_arrow as confidence_arrow

				    FROM
				    (
				        SELECT
				            levels.project_id AS ws_id,
				            levels.element_id AS tid,
				            round(AVG(levels.level)) as level_current,
							COUNT(levels.level) as level_count,

					(CASE
						WHEN ( round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'Low'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'Medium Low'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'Medium High'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'red'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'orange'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'yellow'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (round(AVG((levels.level))) >= 1 && round(AVG((levels.level))) <= 24) THEN 'lowgrey'
						WHEN (round(AVG((levels.level))) >= 25 && round(AVG((levels.level))) <= 49) THEN 'mediumlowgrey'
						WHEN (round(AVG((levels.level))) >= 50 && round(AVG((levels.level))) <= 74) THEN 'mediumhighgrey'
						WHEN (round(AVG((levels.level))) >= 75 && round(AVG((levels.level))) <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow

				        FROM
				            element_levels levels

				        WHERE
							levels.is_active = 1
					group by ws_id
				    ) AS wfl #workspace

				    ) AS wlevel ON #workspace
				    	prj_detail.pid = wlevel.wlevel_id

				LEFT JOIN(
				    SELECT
					pr.project_id,
					#...
					#additional columns for top level query:
					ef.total_hours,
					ef.blue_completed_hours,
					ef.green_remaining_hours,
					ef.amber_remaining_hours,
					ef.red_remaining_hours,
					ef.none_remaining_hours,
					#ef.remaining_hours_color,
					ef.change_hours
					#ef.remaining_hours


				FROM #mock top level query
				(
					SELECT $project_id AS project_id
				) AS pr
				#section to add to top level progress bar query:
				LEFT JOIN #get project effort data
				(
					SELECT
						ue.project_id,
						#ue.remaining_hours_color,
						SUM(ue.change_hours) change_hours,
						SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
						SUM(ue.completed_hours) AS blue_completed_hours,
						SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
						SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours
						#SUM((ue.remaining_hours)) AS remaining_hours
					FROM
					(
						SELECT
							ee.project_id,
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
					GROUP BY ue.project_id
				) AS ef ON
					pr.project_id = ef.project_id

				) as efforts ON #project
				    	prj_detail.pid = efforts.project_id

				LEFT JOIN (
					# OWNER COUNT
		           SELECT COUNT(*) AS owner_count, up.project_id FROM user_permissions up WHERE up.role IN ('Creator', 'Owner', 'Group Owner') AND up.workspace_id IS NULL GROUP BY up.project_id
		       	) AS oc
				ON oc.project_id = prj_detail.pid

				LEFT JOIN (
					# SHARER COUNT
		           SELECT COUNT(*) AS sharer_count, up.project_id FROM user_permissions up WHERE up.role IN ('Sharer', 'Group Sharer') AND up.workspace_id IS NULL GROUP BY up.project_id
		       	) AS sc
				ON sc.project_id = prj_detail.pid

				LEFT JOIN (
					# TASKS COUNT
		           	SELECT
			           	SUM(elements.date_constraints = 0 AND elements.sign_off != 1) AS enon,
						SUM(DATE(NOW())<DATE(elements.start_date) AND elements.sign_off!=1 AND elements.date_constraints=1) AS epnd,
						SUM(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) AND elements.sign_off!=1 AND elements.date_constraints=1  ) AS eprg,
						SUM(DATE(elements.end_date)<DATE(NOW()) AND elements.sign_off!=1 AND elements.date_constraints=1) AS eovd,
						SUM(elements.sign_off=1) AS ecmp,
						up.project_id,
						COUNT(DISTINCT(up.element_id)) AS total_tasks
				 	FROM user_permissions up
				 	LEFT JOIN elements
						ON up.element_id = elements.id
					WHERE
						#up.role = 'Creator' AND
						up.element_id IS NOT NULL AND
						up.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS ec
				ON ec.project_id = prj_detail.pid

				LEFT JOIN (
					# WORKSPACES COUNT
		           	SELECT
			           	SUM( (workspaces.start_date IS NULL OR workspaces.start_date = '') AND (workspaces.end_date IS NULL OR workspaces.end_date = '') AND workspaces.sign_off!=1) AS wnon,
						SUM(DATE(NOW())<DATE(workspaces.start_date) AND workspaces.sign_off!=1) AS wpnd,
						SUM(DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) AND workspaces.sign_off!=1) AS wprg,
						SUM(DATE(workspaces.end_date)<DATE(NOW()) AND workspaces.sign_off!=1) AS wovd,
						SUM(workspaces.sign_off=1) AS wcmp,
						up.project_id,
						COUNT(DISTINCT(up.workspace_id)) AS total_workspaces
				 	FROM user_permissions up
				 	LEFT JOIN workspaces
						ON up.workspace_id = workspaces.id
					WHERE
						up.workspace_id IS NOT NULL AND
						up.area_id IS NULL AND
						up.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS ew
				ON ew.project_id = prj_detail.pid

				LEFT JOIN (
					# COSTS
				    SELECT
				    	up.project_id,
				        SUM(ec.spend_cost) AS spcost,
				        SUM(ec.estimated_cost) AS escost
				    FROM
						user_permissions up
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id
					WHERE
				    	up.user_id = $user_id AND
				        up.element_id IS NOT NULL
				    GROUP BY up.project_id
				) AS pse
				ON pse.project_id = prj_detail.pid


				LEFT JOIN (
					# HIGHT PENDING RISK COUNT
				    SELECT
				        up.project_id,
				        count(DISTINCT(rd.id)) AS high_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.project_id
				) AS prh
					ON prh.project_id = prj_detail.pid

				LEFT JOIN (
					# SEVERE PENDING RISK COUNT
				    SELECT
				        up.project_id,
				        count(DISTINCT(rd.id)) AS severe_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5) OR
				            (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.project_id
				) AS prs
					ON prs.project_id = prj_detail.pid

				LEFT JOIN (
					# PROJECT COMMENTS COUNT
		           	SELECT
			           	count(pc.project_id) AS comments_count,
						up.id AS project_id
				 	FROM projects up
				 	LEFT JOIN project_comments pc
						ON up.id = pc.project_id

				 	GROUP BY up.id
		       	) AS pcc
				ON pcc.project_id = prj_detail.pid

				LEFT JOIN (
					# PROJECT COST STATUS
				    SELECT
				    	up.project_id,
						(CASE
							WHEN (
								(
									SUM(ec.estimated_cost) = '' OR
									SUM(ec.estimated_cost) = 0 OR
									SUM(ec.estimated_cost) IS NULL) AND
								(
									SUM(ec.spend_cost) = '' OR
									SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'None Set'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(
									SUM(ec.spend_cost) = '' OR SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'Budget Set'

							WHEN (
								(SUM(ec.estimated_cost) = '' OR SUM(ec.estimated_cost) = 0 OR SUM(ec.estimated_cost) IS NULL) AND
								(SUM(ec.spend_cost) > 0)
							) THEN 'Costs Incurred'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(SUM(ec.spend_cost) > 0 AND (SUM(ec.spend_cost) > SUM(ec.estimated_cost)))
							) THEN 'Over Budget'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND (SUM(ec.spend_cost) > 0)  AND
								(SUM(ec.spend_cost) <= SUM(ec.estimated_cost))
							) THEN 'On Budget'

							ELSE 'Not Set'
						END) AS c_status

				    FROM
						user_permissions up
				    LEFT JOIN projects p ON
				    	up.project_id = p.id
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id AND up.element_id IS NOT NULL
					WHERE
				    	up.user_id = $user_id
				    GROUP BY up.project_id
				) AS cost_status
				ON cost_status.project_id = prj_detail.pid

				LEFT JOIN (
					# PROJECT RAG STATUS
		           	SELECT
			           	p.rag_status,
						up.project_id,
						ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100) AS ovd_percent,
						(CASE
							WHEN (
								(p.rag_status IS NOT NULL AND p.rag_status = 3) AND
						      	(prg.amber_value > 0 OR prg.red_value > 0)
							) THEN 'Rules'
							ELSE 'Manual'
						END) AS rag_type,

						(CASE
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 1 ) THEN 'red'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 2 ) THEN 'yellow'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 3 )
							THEN
								CASE
						            WHEN (
						            	(prg.amber_value > 0 AND prg.red_value > 0) AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND
						            	(ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) < prg.red_value
					            	)
					                THEN 'yellow'
					                WHEN (
					                	(prg.amber_value > 0 AND prg.red_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                		( prg.amber_value IS NULL OR prg.amber_value <= 0 OR prg.amber_value = '' ) AND prg.red_value > 0) AND
					                		((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.red_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'red'
					                WHEN ((
					                	(prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0) AND
					                	((ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) >= prg.amber_value AND (ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100)) <= 100))
					                THEN 'yellow'
					                ELSE 'green-bg'
						        END
							ELSE 'green-bg'
						END) AS p_rag

					FROM user_projects up
				 	LEFT JOIN projects p
						ON up.project_id = p.id
				 	LEFT JOIN user_permissions ups
						ON ups.project_id = up.project_id
					LEFT JOIN elements el
						ON ups.element_id = el.id AND ups.element_id IS NOT NULL

				 	LEFT JOIN project_rags prg
						ON up.project_id = prg.project_id

					WHERE ups.user_id = $user_id

				 	GROUP BY up.project_id
		       	) AS prag
				ON prag.project_id = prj_detail.pid

				LEFT JOIN (
					SELECT count(porgs.id) AS porgs_count, porgs.project_id
					FROM project_opp_orgs porgs
					LEFT JOIN projects p
						ON p.id = porgs.project_id
					GROUP BY p.id
				) AS porgss
				ON porgss.project_id = prj_detail.pid


				LEFT JOIN (
					SELECT count(pb.id) AS pb_count, pb.project_id
					FROM project_boards pb
					LEFT JOIN projects p
						ON p.id = pb.project_id
					WHERE
						(pb.project_status = 0 OR pb.project_status IS NULL)
						#AND pb.receiver = $user_id

					GROUP BY p.id

				) AS board
				ON board.project_id = prj_detail.pid

				WHERE prj_detail.pid = $project_id
				";
				// pr($query, 1);
		$result =  ClassRegistry::init('Project')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function listing_task_types() {

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
				 	JSON_ARRAYAGG(JSON_OBJECT( 'id', ptypes.ptid, 'title', ptypes.pt_title)) AS types
				 	FROM (
				 		SELECT
						 	DISTINCT(pt.id) AS ptid, pt.title AS pt_title

							FROM
						        user_permissions upr
							LEFT JOIN projects p
								ON p.id = upr.project_id
							LEFT JOIN aligneds pt
								ON p.aligned_id = pt.id

							WHERE
								upr.user_id = $user_id
							ORDER BY pt.sort_order ASC
				 	) AS ptypes";

		$result =  ClassRegistry::init('Project')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function listing_members() {

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', txt.uid, 'full_name', txt.full_name)) AS users

					FROM (SELECT
							DISTINCT(user_permissions.user_id) as uid,
							CONCAT_WS(' ',ud.first_name , ud.last_name) as full_name
					 	FROM user_permissions
					 	INNER JOIN user_details ud
					 		ON ud.user_id = user_permissions.user_id AND ud.first_name IS NOT NULL
					 	WHERE project_id IN (SELECT upr.project_id FROM user_permissions upr where upr.user_id = $user_id and upr.workspace_id is null)
					 	AND user_permissions.user_id NOT IN ($user_id) and user_permissions.workspace_id is null ORDER by full_name
				 	) AS txt
			 	";

		$result =  ClassRegistry::init('Project')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function confidence_level($element_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		// GET CURRENT CONFIDENCE DATA
		$query = "SELECT
					DISTINCT(el.id),
					el.user_id,
					el.level,
					el.comment,
					(CASE
						WHEN (el.level >= 1 && el.level <= 24) THEN 'Low'
						WHEN (el.level >= 25 && el.level <= 49) THEN 'Medium Low'
						WHEN (el.level >= 50 && el.level <= 74) THEN 'Medium High'
						WHEN (el.level >= 75 && el.level <= 100) THEN 'High'
						ELSE '' # not set
					END) AS confidence_level,
					(CASE
						WHEN (el.level >= 1 && el.level <= 24) THEN 'red'
						WHEN (el.level >= 25 && el.level <= 49) THEN 'orange'
						WHEN (el.level >= 50 && el.level <= 74) THEN 'yellow'
						WHEN (el.level >= 75 && el.level <= 100) THEN 'green-bg'
						ELSE '' # not set
					END) AS confidence_class,
					(CASE
						WHEN (el.level >= 1 && el.level <= 24) THEN 'lowgrey'
						WHEN (el.level >= 25 && el.level <= 49) THEN 'mediumlowgrey'
						WHEN (el.level >= 50 && el.level <= 74) THEN 'mediumhighgrey'
						WHEN (el.level >= 75 && el.level <= 100) THEN 'highgrey'
						ELSE '' # not set
					END) AS confidence_arrow
				FROM element_levels el

				WHERE
					el.element_id = $element_id AND
					el.is_active = 1

				#ORDER BY el.created DESC
				#LIMIT 1 ";
		$result =  ClassRegistry::init('Project')->query($query);
		// pr($result);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function wsp_activities_tasks($project_id = null, $workspace_id = null, $page = 0, $limit = 50, $order = 'ORDER BY el_status ASC', $search = ' 1', $filters = [], $element_id = null){
		// mpr($page, $limit);

		$status_filter = $type_filter = $assign_filter = $assign_exclude = '';
		if(isset($filters) && !empty($filters)) {
			if(isset($filters['status']) && !empty($filters['status'])){
				$statuses = [];
				foreach ($filters['status'] as $key => $value) {
					if($value == 'not_spacified') $statuses[] = 5;
					else if($value == 'not_started') $statuses[] = 3;
					else if($value == 'progress') $statuses[] = 2;
					else if($value == 'overdue') $statuses[] = 1;
					else if($value == 'completed') $statuses[] = 4;
				}
				$val = implode(',', $statuses);
				$status_filter = " AND el_status IN($val)";
			}

			if(isset($filters['types']) && !empty($filters['types'])){
				$val = implode(',', $filters['types']);
				// pr($val);
				$type_filter = " AND element_types.type_id IN($val)";

				$general_flag = false;
				$g_query = "SELECT id, title FROM project_element_types WHERE id IN($val)";
				$task_types = ClassRegistry::init('UserPermission')->query($g_query);
				if( isset($task_types) && !empty($task_types) ){
					foreach ($task_types as $key => $value) {
						if($value['project_element_types']['title'] == 'General'){
							$general_flag = true;
						}
					}
				}
				if($general_flag) {
					$type_filter = " AND ( element_types.type_id IN($val) OR element_types.type_id IS NULL)";
				}

			}

			if(isset($filters['assign']) && !empty($filters['assign'])){
				$val = implode(',', $filters['assign']);
				$assign_filter = "";
				$assign_filter .= "  AND assigned.assigned_to IN($val)";
				$assign_filter .= "  AND assigned.reaction <> 3";
			}

		}
		// pr($filters);

		$element_sql = "";
		if(isset($element_id) && !empty($element_id)) {
			$element_sql = " AND up.element_id = $element_id";
		}

		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
					element_data.el_role,
					element_data.role,
					element_data.permit_edit,
					element_data.permit_copy,
					element_data.permit_delete,
					element_data.project_id,
					element_data.workspace_id,
					element_data.ele_id,
					element_data.ele_title,
					element_data.el_status,
					element_data.start_date,
					element_data.end_date,
					element_data.sign_off,
					element_data.sign_off_date,
					element_data.color_code,
					element_data.area_id,
					element_data.area_title,
					element_data.csign,

					etc.owner_count,
					etc.sharer_count,

					assigned.assigned_to,
					assigned.reaction,
					assigned.created_by,
					assigned.assign_status,
					assigned.assigned_user,
					assigned.organization_id,
					assigned.profile_pic,
					assigned.job_title,

					cost_count.spcost,
					cost_count.escost,
					cost_status.c_status,

					(if( hrisk_count.high_risk_total > 0, hrisk_count.high_risk_total ,0)) AS high_risk_total,
					(if( srisk_count.severe_risk_total > 0, srisk_count.severe_risk_total ,0)) AS severe_risk_total,
					((if( hrisk_count.high_risk_total > 0, hrisk_count.high_risk_total ,0)) + (if( srisk_count.severe_risk_total > 0, srisk_count.severe_risk_total ,0))) AS all_risk_total,

					el_level.level,
					el_level.confidence_level,
					el_level.confidence_class,
					el_level.confidence_arrow,

					asset_count.el_tot,
					asset_count.en_tot,
					asset_count.ed_tot,
					asset_count.em_tot,
					decision_count.dc_prg,
					decision_count.dc_cmp,
					feedback_count.fb_nst,
					feedback_count.fb_prg,
					feedback_count.fb_ovd,
					feedback_count.fb_cmp,
					vote_count.vt_nst,
					vote_count.vt_prg,
					vote_count.vt_ovd,
					vote_count.vt_cmp,

					efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.remaining_hours_color,
					efforts.change_hours,
					efforts.remaining_hours,
					efforts.completed_hours,

					(asset_count.el_tot + asset_count.en_tot + asset_count.ed_tot + asset_count.em_tot + decision_count.dc_prg + decision_count.dc_cmp + feedback_count.fb_nst + feedback_count.fb_prg + feedback_count.fb_ovd + feedback_count.fb_cmp + vote_count.vt_nst + vote_count.vt_prg + vote_count.vt_ovd + vote_count.vt_cmp) AS as_tot,
					(decision_count.dc_prg + decision_count.dc_cmp) AS dc_tot,
					(feedback_count.fb_nst + feedback_count.fb_prg + feedback_count.fb_ovd + feedback_count.fb_cmp) AS fb_tot,
					(vote_count.vt_nst + vote_count.vt_prg + vote_count.vt_ovd + vote_count.vt_cmp) AS vt_tot,

					reminder.reminder_count,
					risk_counts.risk_count

				FROM
				(
					SELECT
						up.role,
						up.permit_edit,
						up.permit_copy,
						up.permit_delete,
						CASE
							WHEN (up.role = 'Owner' OR up.role = 'Group Owner') THEN 'Owner'
							WHEN (up.role = 'Sharer' OR up.role = 'Group Sharer') THEN 'Sharer'
							ELSE 'Creator'
						END AS el_role,
					    up.project_id,
					    up.workspace_id,
						el.id AS ele_id, el.title AS ele_title, el.start_date, el.end_date, el.sign_off, el.sign_off_date, el.color_code,
					    CASE
							WHEN (el.sign_off=1) THEN '4' #completed
							WHEN ( DATE(NOW()) BETWEEN DATE(el.start_date) AND DATE(el.end_date) AND el.sign_off!=1 AND el.date_constraints=1 ) THEN '2' #progress
							WHEN ( DATE(NOW()) < DATE(el.start_date) AND el.sign_off!=1 AND el.date_constraints=1 ) THEN '3' #not_started
							WHEN (DATE(el.end_date) < DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1) THEN '1' #overdue
							ELSE '5' #not_spacified
						END AS el_status,
						up.area_id,
						areas.title AS area_title,
						currencies.sign AS csign
					FROM
					    user_permissions up
					LEFT JOIN projects p ON
						up.project_id = p.id
					LEFT JOIN elements el ON
					    up.element_id = el.id
					LEFT JOIN areas ON
					    up.area_id = areas.id
				    LEFT JOIN currencies
						ON currencies.id = p.currency_id
					LEFT JOIN element_types
						ON element_types.element_id = up.element_id
					WHERE
					    up.project_id = $project_id AND
					    up.user_id = $user_id AND
					    up.workspace_id = $workspace_id AND
					    up.element_id IS NOT NULL
					    $type_filter
					    $element_sql
					GROUP BY up.element_id
				) AS element_data
				# TEAM COUNT
				LEFT JOIN
				(
				    SELECT
				    	team_count.element_id,
				        SUM(if(team_count.role = 'Creator' OR team_count.role = 'Owner' OR team_count.role = 'Group Owner',1,0)) AS owner_count,
				        SUM(if(team_count.role = 'Sharer' OR team_count.role ='Group Sharer', 1, 0)) AS sharer_count
				    FROM
				    (
				        SELECT
				            up.element_id,
				            up.user_id,
				            up.role
				        FROM
				            user_permissions up
				        WHERE
				            up.project_id = $project_id AND
				            up.workspace_id = $workspace_id AND
				            up.element_id IS NOT NULL
				        GROUP BY up.element_id, up.user_id
				    ) AS team_count
				    GROUP BY team_count.element_id
				) AS etc
				ON element_data.ele_id = etc.element_id
				# ASSETS COUNT
				LEFT JOIN
				(
				    SELECT
				        up.element_id,
				        COUNT(DISTINCT el.id) AS el_tot,
				        COUNT(DISTINCT en.id) AS en_tot,
				        COUNT(DISTINCT ed.id) AS ed_tot,
				        COUNT(DISTINCT em.id) AS em_tot
				    FROM
				    	user_permissions up
				    LEFT JOIN element_links el
				    	ON up.element_id = el.element_id AND el.status = 1
				    LEFT JOIN element_notes en
				    	ON up.element_id = en.element_id AND en.status = 1
				    LEFT JOIN element_documents ed
				    	ON up.element_id = ed.element_id AND ed.status = 1
				    LEFT JOIN element_mindmaps em
				    	ON up.element_id = em.element_id AND em.status = 1
				    WHERE
				    	up.user_id = $user_id
				    	AND up.element_id IS NOT NULL
				    GROUP BY up.element_id
				) AS asset_count
				ON asset_count.element_id = element_data.ele_id
				# DECISION COUNTS
				LEFT JOIN
				(
				    SELECT
				        ed.eid,
				        COUNT(IF(ed.dc_status = 'In Progress', 1, NULL)) AS dc_prg,
				        COUNT(IF(ed.dc_status = 'Completed', 1, NULL)) AS dc_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS eid,
				            CASE
				                WHEN ed.sign_off = 0 THEN 'In Progress'
				                WHEN ed.sign_off = 1 THEN 'Completed'
				                ELSE 'None'
				            END AS dc_status
				        FROM
				            user_permissions up
				        LEFT JOIN element_decisions ed ON
				            up.element_id = ed.element_id
				        WHERE
				            up.user_id = $user_id
				            AND up.element_id IS NOT NULL
				    ) AS ed
				    GROUP BY ed.eid
				) AS decision_count
				ON decision_count.eid = element_data.ele_id
				# FEEDBACK COUNT
				LEFT JOIN
				(
				    SELECT
				        ef.eid,
				        COUNT(IF(ef.fb_status = 'Not Started', 1, NULL)) AS fb_nst,
				        COUNT(IF(ef.fb_status = 'In Progress', 1, NULL)) AS fb_prg,
				        COUNT(IF(ef.fb_status = 'Overdue', 1, NULL)) AS fb_ovd,
				        COUNT(IF(ef.fb_status = 'Completed', 1, NULL)) AS fb_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS eid,
				            CASE
				                WHEN fb.sign_off = 1 THEN 'Completed'
				                WHEN Date(NOW()) > Date(fb.end_date) AND fb.end_date IS NOT NULL AND fb.sign_off = 0 THEN 'Overdue'
				                WHEN Date(NOW()) BETWEEN Date(fb.start_date) AND Date(fb.end_date) AND fb.sign_off = 0 THEN 'In Progress'
				                WHEN Date(NOW()) < Date(fb.start_date) AND fb.sign_off = 0 THEN 'Not Started'
				                ELSE 'None'
				            END AS fb_status
				        FROM
				            user_permissions up
				        LEFT JOIN feedback fb ON
				            up.element_id = fb.element_id
				            AND fb.status = 1
				        WHERE
				            up.user_id = $user_id
				            AND up.element_id IS NOT NULL
				    ) AS ef
				    GROUP BY ef.eid
			    ) AS feedback_count
			    ON feedback_count.eid = element_data.ele_id
			    # VOTE COUNT
			    LEFT JOIN
				(
				    SELECT
				        ev.eid,
				        COUNT(IF(ev.vt_status = 'Not Started', 1, NULL)) AS vt_nst,
				        COUNT(IF(ev.vt_status = 'In Progress', 1, NULL)) AS vt_prg,
				        COUNT(IF(ev.vt_status = 'Overdue', 1, NULL)) AS vt_ovd,
				        COUNT(IF(ev.vt_status = 'Completed', 1, NULL)) AS vt_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS eid,
				            CASE
				                WHEN vt.is_completed = 1 THEN 'Completed'
				                WHEN Date(NOW()) > Date(vt.end_date) AND vt.end_date IS NOT NULL AND vt.is_completed = 0 THEN 'Overdue'
				                WHEN Date(NOW()) BETWEEN Date(vt.start_date) AND Date(vt.end_date) AND vt.is_completed = 0 THEN 'In Progress'
				                WHEN Date(NOW()) < Date(vt.start_date) AND vt.is_completed = 0 THEN 'Not Started'
				                ELSE 'None'
				            END AS vt_status
				        FROM
				            user_permissions up
				        LEFT JOIN votes vt ON
				            up.element_id = vt.element_id
				        WHERE
				            up.user_id = $user_id
				            AND up.element_id IS NOT NULL
				    ) AS ev #workspace votes
				    GROUP BY ev.eid
				) AS vote_count
				ON vote_count.eid = element_data.ele_id
				LEFT JOIN
				(
				    SELECT
				    	up.element_id AS eid,
				    	SUM(if( ec.spend_cost >0, ec.spend_cost ,0)) AS spcost,
				    	SUM(if( ec.estimated_cost >0, ec.estimated_cost ,0)) AS escost
				    FROM
						user_permissions up
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id
					WHERE
				    	up.user_id = $user_id AND
				        up.project_id = $project_id AND
				        up.element_id IS NOT NULL
				    GROUP BY up.element_id
				) AS cost_count
				ON cost_count.eid = element_data.ele_id

				# COST STATUS
				LEFT JOIN (
				    SELECT
				    	up.element_id AS eid,
				    	up.project_id,
						(CASE
							WHEN (
								(
									SUM(ec.estimated_cost) = '' OR
									SUM(ec.estimated_cost) = 0 OR
									SUM(ec.estimated_cost) IS NULL) AND
								(
									SUM(ec.spend_cost) = '' OR
									SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'Not Set'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(
									SUM(ec.spend_cost) = '' OR SUM(ec.spend_cost) = 0 OR SUM(ec.spend_cost) IS NULL
								)
							) THEN 'Budget Set'

							WHEN (
								(SUM(ec.estimated_cost) = '' OR SUM(ec.estimated_cost) = 0 OR SUM(ec.estimated_cost) IS NULL) AND
								(SUM(ec.spend_cost) > 0)
							) THEN 'Costs Incurred'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND
								(SUM(ec.spend_cost) > 0 AND (SUM(ec.spend_cost) > SUM(ec.estimated_cost)))
							) THEN 'Over Budget'

							WHEN (
								(SUM(ec.estimated_cost) > 0) AND (SUM(ec.spend_cost) > 0)  AND
								(SUM(ec.spend_cost) <= SUM(ec.estimated_cost))
							) THEN 'On Budget'

							ELSE 'Not Set'
						END) AS c_status

				    FROM
						user_permissions up
				    LEFT JOIN projects p ON
				    	up.project_id = p.id
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id AND up.element_id IS NOT NULL
					WHERE
				    	up.user_id = $user_id AND
				    	up.project_id = $project_id AND
				    	up.element_id IS NOT NULL
				    GROUP BY up.element_id
				) AS cost_status
				ON cost_status.eid = element_data.ele_id

				# HIGH RISK COUNT
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS eid,
				        COUNT(DISTINCT(rd.id)) AS high_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id
				        AND up.project_id = $project_id
				        AND up.workspace_id = $workspace_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.element_id
				) AS hrisk_count
				ON hrisk_count.eid = element_data.ele_id
				# SEVERE RISK COUNT
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS eid,
				        COUNT(DISTINCT(rd.id)) AS severe_risk_total
				    FROM
				        user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    LEFT JOIN rm_expose_responses rr ON
				        rd.id = rr.rm_detail_id
				    WHERE
				        up.user_id = $user_id
				        AND up.project_id = $project_id
				        AND up.workspace_id = $workspace_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4)
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				            OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.element_id
				) AS srisk_count
				ON srisk_count.eid = element_data.ele_id
				# ELEMENT ASSIGNMENT
				LEFT JOIN
				(
					SELECT
						up.element_id AS eid,
						ea.assigned_to,
						ea.created_by,
						ea.reaction,
						CASE
							WHEN (ea.reaction = 1) THEN 'accepted'
							WHEN (ea.reaction = 2) THEN 'not-accept-start'
							WHEN (ea.reaction = 3) THEN 'disengage'
							WHEN (ea.assigned_to IS NULL) THEN 'not-avail'
							ELSE 'not-react'
						END AS assign_status,
						CONCAT_WS(' ',ud.first_name , ud.last_name) AS assigned_user,
						ud.organization_id,
						ud.profile_pic,
						ud.job_title
					FROM
						user_permissions up
					LEFT JOIN
						element_assignments ea
						ON ea.element_id = up.element_id
					LEFT JOIN
						user_details ud
						ON ud.user_id = ea.assigned_to
					WHERE
				        up.project_id = $project_id AND
				        up.workspace_id = $workspace_id AND
				        up.element_id IS NOT NULL
				        $assign_exclude
			        GROUP BY up.element_id
				) AS assigned
				ON assigned.eid = element_data.ele_id
				# CONFIDENCE LEVEL
				LEFT JOIN
				(
				    SELECT
						DISTINCT(el.id),
						el.user_id,
						el.level,
						el.element_id AS eid,
						(CASE
							WHEN (el.level >= 1 && el.level <= 24) THEN 'Low'
							WHEN (el.level >= 25 && el.level <= 49) THEN 'Medium Low'
							WHEN (el.level >= 50 && el.level <= 74) THEN 'Medium High'
							WHEN (el.level >= 75 && el.level <= 100) THEN 'High'
							ELSE '' # not set
						END) AS confidence_level,
						(CASE
							WHEN (el.level >= 1 && el.level <= 24) THEN 'red'
							WHEN (el.level >= 25 && el.level <= 49) THEN 'orange'
							WHEN (el.level >= 50 && el.level <= 74) THEN 'yellow'
							WHEN (el.level >= 75 && el.level <= 100) THEN 'green-bg'
							ELSE '' # not set
						END) AS confidence_class,
						(CASE
							WHEN (el.level >= 1 && el.level <= 24) THEN 'lowgrey'
							WHEN (el.level >= 25 && el.level <= 49) THEN 'mediumlowgrey'
							WHEN (el.level >= 50 && el.level <= 74) THEN 'mediumhighgrey'
							WHEN (el.level >= 75 && el.level <= 100) THEN 'highgrey'
							ELSE '' # not set
						END) AS confidence_arrow

					FROM element_levels el
					LEFT JOIN user_permissions up
						ON up.element_id = el.element_id

					WHERE
						up.element_id IS NOT NULL AND
						el.is_active = 1

			    ) AS el_level
			    ON el_level.eid = element_data.ele_id

				LEFT JOIN (
					SELECT
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
						ef.remaining_hours,
						ef.completed_hours
					FROM #mock top level query
					(
						SELECT id AS element_id from elements
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
							SUM((ue.remaining_hours)) AS remaining_hours,
							SUM((ue.completed_hours)) AS completed_hours
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
					el.element_id = ef.element_id
				) as efforts
				on element_data.ele_id = efforts.element_id

				# ELEMENT ASSIGNMENT
				LEFT JOIN
				(
					SELECT
						up.element_id AS eid,
						count(ea.element_id) AS reminder_count
					FROM
						user_permissions up
					LEFT JOIN
						reminders ea
						ON ea.element_id = up.element_id
					WHERE
				        up.project_id = $project_id AND
				        up.element_id IS NOT NULL
			        GROUP BY up.element_id
				) AS reminder
				ON reminder.eid = element_data.ele_id

				LEFT JOIN (
					SELECT
						up.project_id,
						up.element_id AS eid,
						COUNT(DISTINCT(rd.id)) AS risk_count
					FROM user_permissions up
					INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
					LEFT JOIN rm_details rd ON
						re.rm_detail_id = rd.id
					WHERE
					    up.user_id = $user_id and
					    up.project_id = $project_id
					    AND up.element_id IS NOT NULL
					    AND rd.id IS NOT NULL


                    GROUP BY up.element_id

				) AS risk_counts
				ON risk_counts.eid = element_data.ele_id


			#ORDER BY element_data.el_status, element_data.area_id, element_data.ele_title ASC
		    WHERE $search $status_filter $assign_filter
		    $order
			LIMIT $page, $limit";


		// pr($query, 1);
		return ClassRegistry::init('UserPermission')->query($query);
	}

	function wsp_activities_count($project_id = null, $workspace_id = null, $search = ' 1', $filters = []){


		$status_filter = $type_filter = '';
		if(isset($filters) && !empty($filters)) {
			if(isset($filters['status']) && !empty($filters['status'])){
				$statuses = [];
				foreach ($filters['status'] as $key => $value) {
					if($value == 'not_spacified') $statuses[] = 5;
					else if($value == 'not_started') $statuses[] = 3;
					else if($value == 'progress') $statuses[] = 2;
					else if($value == 'overdue') $statuses[] = 1;
					else if($value == 'completed') $statuses[] = 4;
				}
				$val = implode(',', $statuses);
				$status_filter = " AND el_status IN($val)";
			}

			if(isset($filters['types']) && !empty($filters['types'])){
				$val = implode(',', $filters['types']);
				// pr($val);
				$type_filter = " AND element_types.type_id IN($val)";

				$general_flag = false;
				$g_query = "SELECT id, title FROM project_element_types WHERE id IN($val)";
				$task_types = ClassRegistry::init('UserPermission')->query($g_query);
				if( isset($task_types) && !empty($task_types) ){
					foreach ($task_types as $key => $value) {
						if($value['project_element_types']['title'] == 'General'){
							$general_flag = true;
						}
					}
				}
				if($general_flag) {
					$type_filter = " AND ( element_types.type_id IN($val) OR element_types.type_id IS NULL)";
				}

			}
		}


		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
					element_data.ele_id,
					element_data.ele_title,
					element_data.el_status

				FROM
				(
					SELECT
						el.id AS ele_id, el.title AS ele_title,
					    CASE
							WHEN (el.sign_off=1) THEN '4' #completed
							WHEN ( DATE(NOW()) BETWEEN DATE(el.start_date) AND DATE(el.end_date) AND el.sign_off!=1 AND el.date_constraints=1 ) THEN '2' #progress
							WHEN ( DATE(NOW()) < DATE(el.start_date) AND el.sign_off!=1 AND el.date_constraints=1 ) THEN '3' #not_started
							WHEN (DATE(el.end_date) < DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1) THEN '1' #overdue
							ELSE '5' #not_spacified
						END AS el_status
					FROM
					    user_permissions up
					LEFT JOIN elements el ON
					    up.element_id = el.id
					LEFT JOIN element_types
						ON element_types.element_id = up.element_id
					WHERE
					    up.project_id = $project_id AND
					    up.user_id = $user_id AND
					    up.workspace_id = $workspace_id AND
					    up.element_id IS NOT NULL
					    $type_filter
					GROUP BY up.element_id
				) AS element_data

			#ORDER BY element_data.el_status, element_data.area_id, element_data.ele_title ASC
		    WHERE $search $status_filter ";


		// pr($query, 1);
		return ClassRegistry::init('UserPermission')->query($query);
	}

	function summary_details($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					up.role as prj_role,
					(CASE
						WHEN (up.role = 'Creator' OR up.role = 'Owner' OR up.role = 'Group Owner') THEN 1
						ELSE 0
					END) AS user_role,
					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'

						ELSE 'not_spacified'
					END) AS prj_status,

					up.permit_edit,

					projects.id, projects.start_date, projects.end_date, projects.sign_off_date, projects.sign_off,projects.objective, projects.description, projects.image_file,
					CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name, up.user_id as user_id,projects.aligned_id , ud.job_title , ad.title, ud.profile_pic, projects.created


				FROM user_permissions up

				INNER JOIN projects
					ON up.project_id = projects.id
    			LEFT JOIN user_details ud On up.user_id = ud.user_id
    			LEFT JOIN aligneds ad On projects.aligned_id = ad.id


				WHERE
					projects.id = $project_id AND
					#up.user_id = $user_id AND
					up.workspace_id is null AND
					up.role = 'Creator'

				GROUP BY up.project_id";


		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	function confidence_graph($project_id = null,$user_id= null){

		$start_Date = date('Y-m-d');
		$end_Date = date("Y-m-d", strtotime(date('Y-m-d') . " +90 Days"));

		$query = "select (SELECT
					JSON_ARRAYAGG(
						JSON_OBJECT(
							'cl_date', cl_date,
							'cl_count', cl_count,
							'cl_value', cl_value
						)
					)
					FROM
					(
						SELECT thedate AS cl_date, COUNT(el1.level) AS cl_count, ROUND(AVG(el1.level),0) AS cl_value
						FROM
						(
							SELECT startdate + INTERVAL num DAY AS thedate
							FROM
							(
								SELECT digit_1.d + 10 * digit_2.d AS num
								FROM
									(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
								CROSS JOIN
									 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							) digits
							CROSS JOIN (SELECT '$start_Date' AS startdate, '$end_Date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
							WHERE startdate + INTERVAL num DAY <= enddate
						) ds
						LEFT JOIN element_levels el1 ON
							DATE(el1.created) <= thedate
							AND el1.project_id=$project_id # 2 - CHANGE AT RUNTIME
						LEFT JOIN element_levels el2 ON
							DATE(el2.created) <= thedate
							AND el1.element_id = el2.element_id
							AND (el1.created < el2.created)
						WHERE el2.created IS NULL
						GROUP BY thedate
						ORDER BY thedate
					) AS cl_daily_averages ) as data";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}


	function task_count_breakdown_graph($project_id = null,$user_id= null){

		$start_Date = date('Y-m-d');
		$end_Date = date("Y-m-d", strtotime(date('Y-m-d') . " +180 Days"));

		$query = "select (SELECT
						JSON_ARRAYAGG(
							JSON_OBJECT(
								'tb_date', tb_date,
								'tb_value', tb_value
							)
						)
					FROM
					(
						SELECT thedate AS tb_date, COUNT(el2.id) AS tb_value
						FROM
						(
							SELECT startdate + INTERVAL num DAY AS thedate
							FROM
							(
								SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d AS num
								FROM
									(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
								CROSS JOIN
									 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
								CROSS JOIN
									 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							) digits
							CROSS JOIN (SELECT '$start_Date' AS startdate, '$end_Date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
							WHERE startdate + INTERVAL num DAY <= enddate
						) ds
						LEFT JOIN
						(
							SELECT
								up.element_id AS id,
								el.created,
								el.sign_off,
								el.sign_off_date
							FROM
								user_permissions up
							LEFT JOIN elements el ON
								up.element_id = el.id
							WHERE
								up.user_id = $user_id # 1 - CHANGE AT RUNTIME
								AND up.project_id = $project_id # 2 - CHANGE AT RUNTIME
								AND up.element_id IS NOT NULL
						) el2 ON
							DATE(el2.created) <= thedate
							AND (
								el2.sign_off = 0
								OR (el2.sign_off = 1 AND el2.sign_off_date > thedate)
							)
						GROUP BY thedate
						ORDER BY thedate
					) AS tb_daily_counts) as data";


		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}


	function project_docs($project_id = null){

		$query = "SELECT * FROM project_documents WHERE project_id = $project_id ORDER BY sort_order ASC";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}

	function project_links($project_id = null){

		$query = "SELECT * FROM project_links WHERE project_id = $project_id ORDER BY sort_order ASC";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}

	function project_notes($project_id = null) {

		$query = "SELECT pn.*,
					CONCAT_WS(' ',ud.first_name , ud.last_name) AS notes_user,
					ud.organization_id,
					ud.profile_pic,
					ud.job_title
				FROM project_notes pn
				LEFT JOIN user_details ud
					ON ud.user_id = pn.user_id
				WHERE pn.project_id = $project_id
				ORDER BY modified DESC";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}

	function project_competency($project_id = null){

		$query = "SELECT (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', all_data.id, 'title', all_data.title, 'users_skill',all_data.users_skill)) AS skills
					FROM (
					SELECT
						skills.id as id,
						skills.title as title,
						GROUP_CONCAT( DISTINCT(user_skills.user_id)) as users_skill

					FROM project_skills
					inner join skills on project_skills.skill_id = skills.id
					left join user_permissions on user_permissions.project_id = project_skills.project_id and user_permissions.workspace_id is null

					left join user_skills on project_skills.skill_id = user_skills.skill_id and user_skills.user_id in (user_permissions.user_id)

					WHERE project_skills.project_id = $project_id group by project_skills.skill_id ORDER by skills.title) all_data) skills,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', all_sub.id, 'title', all_sub.title, 'users_subject',all_sub.users_subject)) AS subjects
					FROM (

						SELECT
							subjects.id as id,
							subjects.title as title,
							GROUP_CONCAT( DISTINCT(user_subjects.user_id)) as users_subject

						FROM project_subjects
						inner join subjects on project_subjects.subject_id = subjects.id
						left join user_permissions on user_permissions.project_id = project_subjects.project_id and user_permissions.workspace_id is null

						left join user_subjects on project_subjects.subject_id = user_subjects.subject_id and user_subjects.user_id in (user_permissions.user_id)

						WHERE project_subjects.project_id = $project_id group by project_subjects.subject_id ORDER by subjects.title) all_sub  ) subjects,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', all_dom.id, 'title', all_dom.title, 'users_domain',all_dom.users_domain)) AS domains

					FROM (

						SELECT
							knowledge_domains.id as id,
							knowledge_domains.title as title,
							GROUP_CONCAT( DISTINCT(user_domains.user_id)) as users_domain

						FROM project_domains
						inner join knowledge_domains on project_domains.domain_id = knowledge_domains.id
						left join user_permissions on user_permissions.project_id = project_domains.project_id and user_permissions.workspace_id is null

						left join user_domains on project_domains.domain_id = user_domains.domain_id and user_domains.user_id in (user_permissions.user_id)

						WHERE project_domains.project_id = $project_id group by project_domains.domain_id ORDER by knowledge_domains.title) all_dom) domains";
		$result =  ClassRegistry::init('Element')->query($query);
			// pr($result);
		return (isset($result) && !empty($result)) ? $result : [];

	}


	function project_sksudm($project_id = null) {

		$query = "SELECT p.id,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id)) AS JSON FROM skills inner join project_skills on project_skills.skill_id = skills.id  WHERE project_skills.project_id = $project_id ) as pskills,

				    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id)) AS JSON FROM subjects inner join project_subjects on project_subjects.subject_id = subjects.id  WHERE project_subjects.project_id = $project_id ) as psubjects,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id)) AS JSON FROM knowledge_domains inner join project_domains on project_domains.domain_id = knowledge_domains.id  WHERE project_domains.project_id = $project_id ) as pdomains,


					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'title', skills.title)) AS JSON FROM skills ) as skills,

				    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'title', subjects.title)) AS JSON FROM subjects ) as subjects,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'title', knowledge_domains.title)) AS JSON FROM knowledge_domains ) as domains

				FROM projects p

				WHERE p.id = $project_id";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}


	function task_detals($element_id = null) {

		$query = "SELECT e.id, e.title, e.description, e.comments, e.created,
					CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator,
					ud.organization_id,
					ud.user_id,
					ud.profile_pic,
					ud.job_title,
					types.type_title
				FROM elements e
				LEFT JOIN user_details ud
					ON ud.user_id = e.created_by
				LEFT JOIN (
						SELECT pet.title AS type_title, et.element_id
						FROM project_element_types pet
						LEFT JOIN element_types et
							ON et.type_id = pet.id AND et.element_id = $element_id
						WHERE et.element_id = $element_id
						GROUP BY et.element_id
					) AS types
				ON e.id = types.element_id
				WHERE e.id = $element_id";

		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];
	}

	function team_task_efforts_listing($user_id = null, $task_id = null, $page = 0, $offset = null, $order = null, $col = null, $dir = null) {

		$where = '';
		if(isset($user_id) && !empty($user_id)){
			$where = "WHERE ed.user_id = $user_id";
		}

		if(!isset($order) || empty($order)){
			$order = 'ORDER BY first_name, last_name ASC';
		}
		if( (isset($col) && !empty($col)) && (isset($dir) && !empty($dir)) ){
			if($col == 'first_name'){
				$order = "ORDER BY first_name $dir, last_name $dir";
			}
			if($col == 'last_name'){
				$order = "ORDER BY last_name $dir, first_name $dir";
			}
		}

		$today = date('Y-m-d');

		// pr($order);
		$query = "SELECT
			ed.element_id,
			ed.user_id,
			ed.first_name,
			ed.last_name,
			ed.full_name,
			ed.job_title,
			ed.profile_pic,
			ed.organization_id,
			ed.role,
			ed.group_id,

			ef.completed_hours,
			ef.remaining_hours,
			IFNULL(ef.remaining_hours_color,'Not Set') AS remaining_hours_color,
			ef.change_hours,
			cd.cost_estimate,
			cd.cost_spend,
			(CASE
				WHEN ISNULL(cost_estimate) AND ISNULL(cost_spend) THEN 'Not Set'
				WHEN ISNULL(cost_spend) THEN 'Budget Set'
				WHEN ISNULL(cost_estimate) THEN 'Costs Incurred'
				WHEN cost_spend > cost_estimate THEN 'Over Budget'
				WHEN cost_spend <= cost_estimate THEN 'On Budget'
				ELSE 'None' #never
			 END) AS cost_status,
			IFNULL(rh.high_pending_risks,0) AS high_pending_risks,
			IFNULL(rh.severe_pending_risks,0) AS severe_pending_risks,
			IFNULL(rh.total_risks,0) AS total_risks,
			ed.project_skills,
			ed.project_subjects,
			ed.project_domains,
			IFNULL(usk.user_skills,0) AS user_skills,
			IFNULL(usb.user_subjects,0) AS user_subjects,
			IFNULL(usd.user_domains,0) AS user_domains,
			ac.message,
			ac.updated,

			pg.grp_id,
			pg.grp_title,
			pg.grp_owner,
			pg.grp_share_type,
			pg.grp_user_name,

			unavail.tdays
		FROM #get user data
		(
			SELECT DISTINCT
				up.project_id,
				up.element_id,
				up.user_id,
				CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name,
				ud.first_name,
				ud.last_name,
				ud.job_title,
				ud.profile_pic,
				ud.organization_id,
				up.role,
				up.group_id,
				COUNT(DISTINCT ps.skill_id) AS project_skills,
				COUNT(DISTINCT pb.subject_id) AS project_subjects,
				COUNT(DISTINCT pd.domain_id) AS project_domains
			FROM
				user_permissions up
			LEFT JOIN user_details ud ON
				up.user_id = ud.user_id
			LEFT JOIN project_skills ps ON
				up.project_id = ps.project_id
			LEFT JOIN project_subjects pb ON
				up.project_id = pb.project_id
			LEFT JOIN project_domains pd ON
				up.project_id = pd.project_id
			WHERE
				up.area_id IS NOT NULL
				AND up.element_id = $task_id #change at runtime
			GROUP BY up.element_id, up.user_id
		) AS ed
		LEFT JOIN (
			SELECT
				pg.id AS grp_id,
				pg.title AS grp_title,
				pg.group_owner_id AS grp_owner,
				(CASE
					WHEN pg.share_permission = 1 THEN 'owner'
					WHEN pg.share_permission = 2 THEN 'sharer'
					ELSE ''
				END) AS grp_share_type,
				pgu.user_id AS grp_user,
				CONCAT_WS(' ',ud.first_name , ud.last_name) AS grp_user_name
			FROM project_groups pg
			LEFT JOIN project_group_users pgu
				ON pgu.project_group_id = pg.id
			LEFT JOIN user_details ud
				ON ud.user_id = pg.group_owner_id
		)
		AS pg ON
			pg.grp_id = ed.group_id AND
			(pg.grp_user = ed.user_id OR pg.grp_owner = ed.user_id)

		LEFT JOIN #get effort
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
				ee.element_id = $task_id #change at runtime
				AND ee.is_active = 1
		) AS ef ON
			ed.element_id = ef.element_id
			AND ed.user_id = ef.user_id

		LEFT JOIN #get costs
		(
			SELECT
				uec.element_id,
				uec.user_id,
				SUM(IF(uec.estimate_spend_flag = 1, (uec.quantity * uec.work_rate), NULL)) AS cost_estimate,
				SUM(IF(uec.estimate_spend_flag = 2, (uec.quantity * uec.work_rate), NULL)) AS cost_spend
			FROM
				user_element_costs uec
			WHERE
				uec.element_id = $task_id #change at runtime
			GROUP BY uec.element_id, uec.user_id
		) AS cd ON
			ed.element_id = cd.element_id
			AND ed.user_id = cd.user_id
		LEFT JOIN #get high, severe and total risks
		(
			SELECT
				element_id,
				user_id,
				SUM(
					IF(
						status IN (1, 2, 4) #open, review, overdue
						AND (
							(impact = 2 AND percentage = 5)
							OR (impact = 3 AND (percentage = 5 OR percentage = 4))
							OR (impact = 4 AND percentage = 4)
							OR (impact = 5 AND percentage = 3)
							)
					, 1, 0)
				) AS high_pending_risks,
				SUM(
					IF(
						status IN (1, 2, 4) #open, review, overdue
						AND (
							(impact = 4 AND percentage = 5)
							OR (impact = 5 AND (percentage = 5 OR percentage = 4))
							)
					, 1, 0)
				) AS severe_pending_risks,
				COUNT( DISTINCT id) AS total_risks
			FROM
			(
				(#created risks
					SELECT
						re.element_id,
						rd.user_id, #created by
						rd.id,
						rd.status,
						rr.impact,
						rr.percentage
					FROM
						rm_elements re
					LEFT JOIN rm_details rd ON
						re.rm_detail_id = rd.id
					LEFT JOIN rm_expose_responses rr ON
						rd.id = rr.rm_detail_id
					WHERE re.element_id = $task_id #change at runtime
				)
				UNION ALL
				(#assigned risks
					SELECT
						re.element_id,
						ru.user_id, #assigned user
						rd.id,
						rd.status,
						rr.impact,
						rr.percentage
					FROM
						rm_elements re
					LEFT JOIN rm_details rd ON
						re.rm_detail_id = rd.id
					LEFT JOIN rm_expose_responses rr ON
						rd.id = rr.rm_detail_id
					LEFT JOIN rm_users ru ON
						rd.id = ru.rm_detail_id
					WHERE
						re.element_id = $task_id #change at runtime
						AND ru.user_id IS NOT NULL
				)
			) AS rk
			GROUP BY element_id, user_id
		) AS rh ON
			ed.element_id = rh.element_id
			AND ed.user_id = rh.user_id
		LEFT JOIN #get user skills
		(
			SELECT
				ps.project_id,
				us.user_id,
				COUNT(DISTINCT us.skill_id) AS user_skills
			FROM
				project_skills ps
			INNER JOIN user_skills us ON
				ps.skill_id = us.skill_id
			GROUP BY ps.project_id, us.user_id
		) AS usk ON
			ed.project_id = usk.project_id
			AND ed.user_id = usk.user_id
		LEFT JOIN #get user subjects
		(
			SELECT
				ps.project_id,
				us.user_id,
				COUNT(DISTINCT us.subject_id) AS user_subjects
			FROM
				project_subjects ps
			INNER JOIN user_subjects us ON
				ps.subject_id = us.subject_id
			GROUP BY ps.project_id, us.user_id
		) AS usb ON
			ed.project_id = usb.project_id
			AND ed.user_id = usb.user_id
		LEFT JOIN #get user domains
		(
			SELECT
				pd.project_id,
				ud.user_id,
				COUNT(DISTINCT ud.domain_id) AS user_domains
			FROM
				project_domains pd
			INNER JOIN user_domains ud ON
				pd.domain_id = ud.domain_id
			GROUP BY pd.project_id, ud.user_id
		) AS usd ON
			ed.project_id = usd.project_id
			AND ed.user_id = usd.user_id
		LEFT JOIN #get activity
		(
			SELECT
				ac.element_id,
				ac.user_id,
				ac.message,
				ac.updated
			FROM
				(SELECT * FROM activities WHERE element_id = $task_id AND message <> 'Task viewed') AS ac
			LEFT JOIN
		    	(SELECT * FROM activities WHERE element_id = $task_id AND message <> 'Task viewed') AS ac2 ON
		            ac.user_id = ac2.user_id
		            AND ac.updated < ac2.updated
			WHERE
		    	ac2.user_id IS NULL
		) AS ac ON
			ed.user_id = ac.user_id
			AND ed.element_id = ac.element_id

		LEFT JOIN #get unavailability
		(
			SELECT count(*) tdays , user_id, STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d') avail_start_date,
					STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') avail_end_date
                    FROM `availabilities` WHERE
                     DATE(NOW()) BETWEEN DATE(STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d')) AND DATE(STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d'))
                     GROUP by user_id
		) AS unavail ON
			ed.user_id = unavail.user_id

		$where

		GROUP BY ed.element_id, ed.user_id
		$order
		LIMIT $page, $offset
		";
		// pr($query, 1);
		$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}


	function team_task_efforts_progressbar($task_id = null){

		$query = "SELECT
				el.element_id,
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
				SELECT $task_id AS element_id
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

			$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}

	function effort_history($task_id = null, $user_id = null){

		$query = "SELECT
					ef.*,
					CONCAT_WS(' ',ud.first_name , ud.last_name) AS user_name,
					ud.user_id,
					ud.organization_id,
					ud.profile_pic,
					ud.job_title

				FROM elements el
				LEFT JOIN element_efforts ef
					ON ef.element_id = el.id
				LEFT JOIN user_details ud
					ON ud.user_id = ef.user_id
				WHERE
					ef.element_id = $task_id AND
					ef.user_id = $user_id
				ORDER BY ef.created DESC
				";

			$result =  ClassRegistry::init('Element')->query($query);
		return (isset($result) && !empty($result)) ? $result : [];

	}

	function updatepbudget(){

		ClassRegistry::init('Element')->query("UPDATE `projects` SET budget=0");
		$query = "UPDATE projects as p
					LEFT JOIN (
						SELECT
				          	ecc.escost as ecctotal,
				            up.project_id
				        FROM
				        user_permissions up
				        LEFT JOIN (
				        	SELECT
				        		SUM(if( ec.estimated_cost >0, ec.estimated_cost, 0)) AS escost,
				          		SUM(if( ec.spend_cost >0, ec.spend_cost, 0)) AS spcost,
				        		up.project_id,
				        		up.user_id
				    		FROM user_permissions up
				    		LEFT JOIN element_costs ec
				    			ON up.element_id = ec.element_id
							WHERE
								up.element_id IS NOT NULL
								AND up.role = 'Creator'

				    		GROUP BY up.project_id
				        ) AS ecc
				        ON up.project_id = ecc.project_id

				        WHERE
							up.element_id IS NOT NULL
							AND up.role = 'Creator'
				        GROUP BY up.project_id
				    )
				     AS m ON m.project_id = p.id
				SET p.budget = m.ecctotal
				";

		$result =  ClassRegistry::init('Element')->query($query);

		pr($result, 1);

	}

	function project_user_list($project_id = null){

		$selection = "CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id";

		$query = "SELECT
			$selection

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer') order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function project_user_rates($project_id = null){

		$selection = "";

		$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname,
				user_details.user_id,
				user_details.first_name,
				user_details.last_name,
				user_details.profile_pic,
				user_details.job_title,
				user_details.organization_id,
				user_permissions.role,
				upc.day_rate,
				upc.hour_rate

			FROM `user_permissions`
			LEFT JOIN user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN user_project_costs upc on upc.project_id = user_permissions.project_id AND user_details.user_id = upc.user_id
			WHERE  user_permissions.project_id = $project_id and workspace_id is null AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer') order by user_details.first_name ASC, user_details.last_name ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}
	function element_user_rates($project_id = null, $element_id = null, $exclude_users = null){

		$exclude = "";
		if(isset($exclude_users) && !empty($exclude_users)){
			$exclude_users = implode(',', $exclude_users);
			$exclude = " AND user_permissions.user_id NOT IN($exclude_users) ";
		}

		$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname,
				user_details.user_id,
				user_details.first_name,
				user_details.last_name,
				user_details.profile_pic,
				user_details.job_title,
				user_details.organization_id,
				user_permissions.role,
				upc.day_rate,
				upc.hour_rate

			FROM `user_permissions`
			LEFT JOIN user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN user_project_costs upc on upc.project_id = user_permissions.project_id AND user_details.user_id = upc.user_id
			WHERE user_permissions.project_id = $project_id and workspace_id is NOT null AND user_permissions.element_id = $element_id AND user_permissions.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer') $exclude order by user_details.first_name ASC, user_details.last_name ASC";
			// pr($query);
		return ClassRegistry::init('UserPermission')->query($query);
	}

	function user_risk_projects($user_id = null){

		$query = "SELECT
				    DISTINCT(rd.project_id),
				    p.title AS ptitle, p.id

				FROM
				    rm_details rd
			    LEFT JOIN projects p
					ON rd.project_id = p.id

				WHERE
				    rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id) OR rd.user_id = $user_id

			    ORDER BY p.title ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function program_projects($program_id = null){

		$query = "SELECT
				    DISTINCT(pp.project_id),
				    p.title AS ptitle, p.id

				FROM
				    project_programs pp
			    LEFT JOIN projects p
					ON pp.project_id = p.id

				WHERE
				    pp.program_id = $program_id

			    ORDER BY p.title ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function risk_list1($user_id = null, $query_params = null, $limit = null){
		// pr($query_params);
		$where = [];
		if(isset($query_params['project_id']) && !empty($query_params['project_id'])){
			$prjids = implode(',', $query_params['project_id']);
			$where[] = "project_id IN ($prjids)";
		}
		if(isset($query_params['exposure']) && !empty($query_params['exposure'])){
			$where[] = "rd_exposure = '".$query_params['exposure']."'";
		}
		if(isset($query_params['type_id']) && !empty($query_params['type_id'])){
			$typeVal = $query_params['type_id'];
			$where[] = "risk_type IN ('".$typeVal."')";
		}
		if(isset($query_params['status']) && !empty($query_params['status'])){
			$where[] = "status_num = '".$query_params['status']."'";
		}
		if(isset($query_params['impact']) && !empty($query_params['impact'])){
			if($query_params['impact'] == 'Not Set'){
				$where[] = "(rd_impact = '".$query_params['impact']."' OR rd_impact IS NULL)";
			}
			else{
				$where[] = "rd_impact = '".$query_params['impact']."'";
			}
		}
		if(isset($query_params['probability']) && !empty($query_params['probability'])){
			if($query_params['probability'] == 'Not Set'){
				$where[] = "(rd_percent = '".$query_params['probability']."' OR rd_percent IS NULL)";
			}
			else{
				$where[] = "rd_percent = '".$query_params['probability']."'";
			}
		}
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$where[] = "id IN($rids)";
		}

		$where_cond = '';
		if(isset($where) && !empty($where)){
			$where_cond = 'WHERE ';
			$where_cond .= implode(' AND ', $where);
		}

		$ele_cond = '';
		if(isset($query_params['element_id']) && !empty($query_params['element_id'])){
			$elids = implode(',', $query_params['element_id']);
			$ele_cond = "WHERE rel.element_id IN ($elids)";
		}

		$risk_cond = '';
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$risk_cond = "AND rd.id = '$rids'";
		}

		$others_risk = '';
		if(!isset($query_params['my_risks']) || empty($query_params['my_risks'])){
			$others_risk = " OR rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id)";
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($limit) && !empty($limit)){
			$limit_str = "LIMIT $page, $limit";
		}

		$order_by = "ORDER BY rdate DESC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$order = $query_params['order'];
			$coloumn = $query_params['coloumn'];
			$order_by = "ORDER BY $coloumn $order";

			if($query_params['coloumn'] == 'rd_exposure'){
				$order_by = 'ORDER BY CASE
						WHEN rd_exposure="Low" THEN 1
						WHEN rd_exposure="Medium" THEN 2
						WHEN rd_exposure="High" THEN 3
						WHEN rd_exposure="Severe" THEN 4
						WHEN rd_exposure="None" OR rd_exposure IS NULL THEN 5
					END ' . $query_params['order'];
			}
		}

		$query = "SELECT
					risk.id,
					risk.project_id,
					risk.title,
					risk.ptitle,
					risk.rdate,
					risk.rd_status,
					risk.status_num,
					risk.creator_id,
					risk.creator_name,
					risk.creator_pic,
					risk.creator_job,
					risk.creator_org,
					risk.risk_type,
					exp.contingency_exists,
					exp.mitigation_exists,
					exp.residual_exists,
					if(exp.rd_percent IS NULL , 'Not Set', exp.rd_percent) AS rd_prob,
					if(exp.rd_impact IS NULL , 'Not Set', exp.rd_impact) AS rd_impact,
					if(exp.rd_exposure IS NULL , 'None', exp.rd_exposure) AS rd_exposure,
					exp.prob_num,
					exp.impact_num,
					ruser.assignee,
					if(ruser_counts.ruser_count IS NULL, 0, ruser_counts.ruser_count) AS ruser_count,
					rleader.leaders,
					rtask.risk_tasks,
					if(rtask_counts.rtask_count IS NULL, 0, rtask_counts.rtask_count) AS rtask_count

				# RISK DETAILS
				FROM
				(
					SELECT
					    p.id AS project_id,
					    p.title AS ptitle,
					    rd.id,
					    rd.title,
					    rd.possible_occurrence AS rdate,
					    rpt.title AS risk_type,
					    (CASE
				            WHEN rd.status = 2 THEN 'Review'
				            WHEN rd.status = 3 THEN 'SignOff'
				            WHEN rd.status = 4 THEN 'Overdue'
				            ELSE 'Open'
				        END ) AS rd_status,
				        rd.status AS status_num,

				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator_name,
				        ud.user_id AS creator_id,
				        ud.profile_pic AS creator_pic,
						ud.job_title AS creator_job,
						ud.organization_id AS creator_org

					FROM
					    rm_details rd
					LEFT JOIN projects p
						ON rd.project_id = p.id
					LEFT JOIN user_details ud
						ON ud.user_id = rd.created_by
					LEFT JOIN rm_project_risk_types rpt
						ON rpt.id = rd.rm_project_risk_type_id

					WHERE
					    ((rd.user_id = $user_id) $others_risk) $risk_cond

				) AS risk
				# RISK EXPOSURE
				LEFT JOIN
				(
					SELECT
						res.rm_detail_id,
						if(res.contingency IS NULL , 0, 1) AS contingency_exists,
						if(res.mitigation IS NULL , 0, 1) AS mitigation_exists,
						if(res.residual IS NULL , 0, 1) AS residual_exists,
				        res.percentage AS prob_num,
				        res.impact AS impact_num,
						CASE
				            WHEN res.percentage = 1 THEN 'Rare'
				            WHEN res.percentage = 2 THEN 'Unlikely'
				            WHEN res.percentage = 3 THEN 'Possible'
				            WHEN res.percentage = 4 THEN 'Likely'
				            WHEN res.percentage = 5 THEN 'Almost Certain'
				            ELSE 'Not Set'
				        END AS rd_percent,
						CASE
				            WHEN res.impact = 1 THEN 'Negligible'
				            WHEN res.impact = 2 THEN 'Minor'
				            WHEN res.impact = 3 THEN 'Moderate'
				            WHEN res.impact = 4 THEN 'Major'
				            WHEN res.impact = 5 THEN 'Critical'
				            ELSE 'Not Set'
				        END AS rd_impact,
						CASE
				            WHEN ( (res.impact = 1 AND (res.percentage = 3 OR res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 2 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 3 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 4 AND res.percentage= 1) ) THEN 'Low'
				            WHEN ( (res.impact = 1 AND (res.percentage = 5 OR res.percentage = 4)) OR (res.impact = 2 AND (res.percentage = 4 OR res.percentage= 3)) OR (res.impact = 3 AND res.percentage = 3) OR (res.impact = 4 AND (res.percentage = 3 OR res.percentage = 2)) OR (res.impact = 5 AND (res.percentage = 2 OR res.percentage = 1)) ) THEN 'Medium'
				            WHEN ( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) THEN 'High'
				            WHEN ( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) THEN 'Severe'
				            ELSE 'None'
				        END AS rd_exposure
					FROM
					    rm_expose_responses res
					LEFT JOIN rm_details rd
						ON rd.id = res.rm_detail_id
					GROUP BY rd.id
				) AS exp
				ON exp.rm_detail_id = risk.id
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS assignee
				   	FROM rm_users ru
					INNER JOIN
						user_details ud
					ON ud.user_id = ru.user_id
				   	GROUP BY ru.rm_detail_id
				) AS ruser
				ON ruser.rmid = risk.id
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		COUNT(ud.user_id) AS ruser_count
				   	FROM rm_users ru
					INNER JOIN
						user_details ud
					ON ud.user_id = ru.user_id
				   	GROUP BY ru.rm_detail_id
				) AS ruser_counts
				ON ruser_counts.rmid = risk.id
				# RISK LEADERS COUNT
				LEFT JOIN (
				   	SELECT
				   		rld.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS leaders
				   	FROM rm_leaders rld
					INNER JOIN
						user_details ud
					ON ud.user_id = rld.user_id
				   	GROUP BY rld.rm_detail_id
				) AS rleader
				ON rleader.rmid = risk.id
				# RISK TASKS
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						JSON_ARRAYAGG(JSON_OBJECT( 'id', rel.element_id, 'title', el.title)) AS risk_tasks
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					$ele_cond
					GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = risk.id
				# RISK TASKS COUNT
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						COUNT(rel.element_id) AS rtask_count
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					$ele_cond
					GROUP BY rel.rm_detail_id
				) AS rtask_counts
				ON rtask_counts.rmid = risk.id

				$where_cond
				GROUP BY risk.id
				$order_by
				$limit_str";

		// pr($query,1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}


	function risk_list($user_id = null, $query_params = null, $limit = null){
		// pr($query_params);
		$allProjectCond = "";
		if(isset($query_params['project_id']) && $query_params['project_id'] != 'my'){
			$allProjectCond = "up.role IN ('Creator','Owner','Group Owner') OR";
		}
		$where = [];
		$project_cond = '';
		if(isset($query_params['project_id']) && !empty($query_params['project_id']) && ($query_params['project_id'] != 'my' && $query_params['project_id'] != 'all')){
			$prjids = implode(',', $query_params['project_id']);
			$where[] = "up.project_id IN ($prjids)";
			$project_cond = "up.project_id IN($prjids) AND";
		}
		if(isset($query_params['exposure']) && !empty($query_params['exposure'])){
			// pr($query_params['exposure'],1);
			$exposure = $query_params['exposure'];
			if($exposure == 'severe'){
				$where[] = "( (rx.impact = 4 AND rx.percentage = 5) OR (rx.impact = 5 AND (rx.percentage = 5 OR rx.percentage= 4)) )";
			}
			else if($exposure == 'high'){
				$where[] = "( (rx.impact = 2 AND rx.percentage = 5) OR (rx.impact = 3 AND (rx.percentage = 5 OR rx.percentage= 4)) OR (rx.impact = 4 AND rx.percentage = 4) OR (rx.impact = 5 AND rx.percentage = 3) )";
			}
			else if($exposure == 'medium'){
				$where[] = "( (rx.impact = 1 AND (rx.percentage = 5 OR rx.percentage = 4)) OR (rx.impact = 2 AND (rx.percentage = 4 OR rx.percentage= 3)) OR (rx.impact = 3 AND rx.percentage = 3) OR (rx.impact = 4 AND (rx.percentage = 3 OR rx.percentage = 2)) OR (rx.impact = 5 AND (rx.percentage = 2 OR rx.percentage = 1)) )";
			}
			else if($exposure == 'low'){
				$where[] = "( (rx.impact = 1 AND (rx.percentage = 3 OR rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 2 AND (rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 3 AND (rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 4 AND rx.percentage= 1) )";
			}
		}
		if(isset($query_params['type_id']) && !empty($query_params['type_id'])){
			$typeVal = $query_params['type_id'];
			$where[] = "rt.title = '".$typeVal."'";
		}
		if(isset($query_params['status']) && !empty($query_params['status'])){
			$status = $query_params['status'];
			if($status == 'Open'){
				$where[] = "(rd.status = 1 AND (rd.status <> 3 AND Date(rd.possible_occurrence) > Date(now())))";
			}
			else if($status == 'Review'){
				$where[] = "(rd.status = 2 AND (rd.status <> 3 AND Date(rd.possible_occurrence) > Date(now())))";
			}
			else if($status == 'Overdue'){
				$where[] = "(rd.status <> 3 AND Date(rd.possible_occurrence) < Date(now()))";
			}
			else if($status == 'Completed'){
				$where[] = "(rd.status = 3 )";
			}
		}
		if(isset($query_params['impact']) && !empty($query_params['impact'])){
			if($query_params['impact'] == 'Not Set'){
				$where[] = "(rx.impact = '".$query_params['impact']."' OR rx.impact IS NULL)";
			}
			else{
				$where[] = "rx.impact = '".$query_params['impact']."'";
			}
		}
		if(isset($query_params['probability']) && !empty($query_params['probability'])){
			if($query_params['probability'] == 'Not Set'){
				$where[] = "(rx.percentage = '".$query_params['probability']."' OR rx.percentage IS NULL)";
			}
			else{
				$where[] = "rx.percentage = '".$query_params['probability']."'";
			}
		}
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$where[] = "rd.id IN($rids)";
		}

		$where_cond = '';
		if(isset($where) && !empty($where)){
			$where_cond = 'AND ';
			$where_cond .= implode(' AND ', $where);
		}
		// pr($where_cond);

		$ele_cond = '';
		if(isset($query_params['element_id']) && !empty($query_params['element_id'])){
			$elids = implode(',', $query_params['element_id']);
			$ele_cond = "WHERE rel.element_id IN ($elids)";
		}

		$risk_cond = '';
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$risk_cond = "AND rd.id = '$rids'";
		}

		$others_risk = '';
		if(!isset($query_params['my_risks']) || empty($query_params['my_risks'])){
			$others_risk = " OR rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id)";
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($limit) && !empty($limit)){
			$limit_str = "LIMIT $page, $limit";
		}

		$order_by = "ORDER BY rdate DESC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$order = $query_params['order'];
			$coloumn = $query_params['coloumn'];
			$order_by = "ORDER BY $coloumn $order";

			if($query_params['coloumn'] == 'rd_exposure'){
				$order_by = 'ORDER BY CASE
						WHEN rd_exposure="Low" THEN 1
						WHEN rd_exposure="Medium" THEN 2
						WHEN rd_exposure="High" THEN 3
						WHEN rd_exposure="Severe" THEN 4
						WHEN rd_exposure="None" OR rd_exposure IS NULL THEN 5
					END ' . $query_params['order'];
			}
		}

		$query = "SELECT
					rd.id,
				    up.project_id,
				    rd.title,
				    p.title AS ptitle,
				    rd.possible_occurrence AS rdate,
				    (CASE
				        WHEN (rd.status <> 3 AND Date(rd.possible_occurrence) < Date(now())) THEN 'Overdue'
				    	WHEN (rd.status = 2) THEN 'Review'
				        WHEN rd.status = 3 THEN 'Completed'
				        WHEN rd.status = 1 THEN 'Open'
				        ELSE 'Open'
				    END) AS rd_status,
				    rd.status AS status_num,
				    rd.created_by AS creator_id,
				    CONCAT_WS(' ',ud.first_name,ud.last_name) AS creator_name,
				    ud.profile_pic AS creator_pic,
				    ud.job_title AS creator_job,
				    ud.organization_id AS creator_org,
				    rt.title AS risk_type,
				    IF(rx.contingency IS NULL,0,1) AS contingency_exists,
				    IF(rx.mitigation IS NULL,0,1) AS mitigation_exists,
				    IF(rx.residual IS NULL,0,1) AS residual_exists,
				    (CASE
				    	WHEN rx.percentage = 1 THEN 'Rare'
				        WHEN rx.percentage = 2 THEN 'Unlikely'
				        WHEN rx.percentage = 3 THEN 'Possible'
				        WHEN rx.percentage = 4 THEN 'Likely'
				        WHEN rx.percentage = 5 THEN 'Almost Certain'
				        ELSE 'Not Set'
				    END) AS rd_percent,
				    (CASE
				     	WHEN rx.impact = 1 THEN 'Negligible'
				     	WHEN rx.impact = 2 THEN 'Minor'
				    	WHEN rx.impact = 3 THEN 'Moderate'
				     	WHEN rx.impact = 4 THEN 'Major'
				     	WHEN rx.impact = 5 THEN 'Critical'
				     	ELSE 'Not Set'
				    END) AS rd_impact,
				    (CASE
				     	WHEN ( (rx.impact = 1 AND (rx.percentage = 3 OR rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 2 AND (rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 3 AND (rx.percentage = 2 OR rx.percentage= 1)) OR (rx.impact = 4 AND rx.percentage= 1) ) THEN 'Low'
				     	WHEN ( (rx.impact = 1 AND (rx.percentage = 5 OR rx.percentage = 4)) OR (rx.impact = 2 AND (rx.percentage = 4 OR rx.percentage= 3)) OR (rx.impact = 3 AND rx.percentage = 3) OR (rx.impact = 4 AND (rx.percentage = 3 OR rx.percentage = 2)) OR (rx.impact = 5 AND (rx.percentage = 2 OR rx.percentage = 1)) ) THEN 'Medium'
				     	WHEN ( (rx.impact = 2 AND rx.percentage = 5) OR (rx.impact = 3 AND (rx.percentage = 5 OR rx.percentage= 4)) OR (rx.impact = 4 AND rx.percentage = 4) OR (rx.impact = 5 AND rx.percentage = 3) ) THEN 'High'
				     	WHEN ( (rx.impact = 4 AND rx.percentage = 5) OR (rx.impact = 5 AND (rx.percentage = 5 OR rx.percentage= 4)) ) THEN 'Severe'
				     	ELSE 'None'
				    END) AS rd_exposure,
				    rx.percentage AS prob_num,
				    rx.impact AS impact_num,
				    #JSON_ARRAYAGG(JSON_OBJECT('id', ru.user_id,'title',CONCAT_WS(' ',ud2.first_name,ud2.last_name))) AS assignee,
				    ruser.assignee,
				    ruser.ruser_count,
				    #COUNT(ru.user_id) AS ruser_count,
				    #JSON_ARRAYAGG(JSON_OBJECT('id', rl.user_id,'title',CONCAT_WS(' ',ud3.first_name,ud3.last_name))) AS leaders,
				    rleader.leaders,
				    #JSON_ARRAYAGG(JSON_OBJECT('id', re.element_id,'title',el.title)) AS risk_tasks,
				    rtask.risk_tasks,
				    rtask.rtask_count
				    -- COUNT(el.id) AS rtask_count
				FROM user_permissions up
				LEFT JOIN projects p ON
					up.project_id = p.id
				LEFT JOIN rm_details rd ON
					up.project_id = rd.project_id
				LEFT JOIN rm_project_risk_types rt ON
					rd.rm_project_risk_type_id = rt.id
				LEFT JOIN rm_expose_responses rx ON
					rd.id = rx.rm_detail_id
				LEFT JOIN user_details ud ON
					rd.created_by = ud.user_id
				LEFT JOIN rm_users ru ON
					rd.id = ru.rm_detail_id
				LEFT JOIN user_details ud2 ON
					ru.user_id = ud2.user_id
				LEFT JOIN rm_leaders rl ON
					rd.id = rl.rm_detail_id
				LEFT JOIN user_details ud3 ON
					rl.user_id = ud3.user_id
				LEFT JOIN rm_elements re ON
					rd.id = re.rm_detail_id
				LEFT JOIN elements el ON
					re.element_id = el.id

				LEFT JOIN (
				    SELECT
				        ru.rm_detail_id AS rmid,
				        JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS assignee,
				        count(DISTINCT(ud.user_id)) AS ruser_count
				    FROM rm_users ru
				    INNER JOIN
				        user_details ud
				    ON ud.user_id = ru.user_id
				    GROUP BY ru.rm_detail_id
				) AS ruser
				ON ruser.rmid = rd.id
				LEFT JOIN (
				    SELECT
				        rld.rm_detail_id AS rmid,
				        JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS leaders
				    FROM rm_leaders rld
				    INNER JOIN
				        user_details ud
				    ON ud.user_id = rld.user_id
				    GROUP BY rld.rm_detail_id
				) AS rleader
				ON rleader.rmid = rd.id
				LEFT JOIN (
				    SELECT
				        rel.rm_detail_id AS rmid,
				        JSON_ARRAYAGG(JSON_OBJECT( 'id', rel.element_id, 'title', el.title)) AS risk_tasks,
				        count(DISTINCT(rel.element_id)) AS rtask_count
				    FROM rm_elements rel
				    INNER JOIN elements el ON rel.element_id = el.id
				    $ele_cond
				    GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = rd.id

				WHERE
					#up.project_id = 2 AND #comment out for All Projects and My Risks, uncomment for Specific Project
					$project_cond
				    up.user_id = $user_id
				    AND up.workspace_id IS NULL
				    AND rd.id IS NOT NULL
				    AND (
				        #up.role IN ('Creator','Owner','Group Owner') OR #comment out for My Risks, uncomment for All Projects and Specific Project
				    	$allProjectCond
				        rd.created_by = $user_id
				        OR ru.user_id = $user_id
						)
					$where_cond
				GROUP BY rd.id
				$order_by
				$limit_str
			";

		// pr($query);
		// pr($query_params);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result);
		return $result;
	}

	function risk_list_new($user_id = null, $query_params = null, $limit = 50){
		// pr($query_params);
		$where = [];
		if(isset($query_params['project_id']) && !empty($query_params['project_id'])){
			$prjids = implode(',', $query_params['project_id']);
			$where[] = "project_id IN ($prjids)";
		}
		if(isset($query_params['exposure']) && !empty($query_params['exposure'])){
			$where[] = "rd_exposure = '".$query_params['exposure']."'";
		}
		if(isset($query_params['type_id']) && !empty($query_params['type_id'])){
			$typeVal = $query_params['type_id'];
			/*$types =  ClassRegistry::init('Project')->query("SELECT GROUP_CONCAT(id) AS types FROM `rm_project_risk_types` where title like '%$typeVal%'");
			pr($types);
			$types = $types[0][0]['types'];*/
			$where[] = "risk_type IN ('".$typeVal."')";
		}
		if(isset($query_params['status']) && !empty($query_params['status'])){
			$where[] = "status_num = '".$query_params['status']."'";
		}
		if(isset($query_params['impact']) && !empty($query_params['impact'])){
			if($query_params['impact'] == 'Not Set'){
				$where[] = "(rd_impact = '".$query_params['impact']."' OR rd_impact IS NULL)";
			}
			else{
				$where[] = "rd_impact = '".$query_params['impact']."'";
			}
		}
		if(isset($query_params['probability']) && !empty($query_params['probability'])){
			if($query_params['probability'] == 'Not Set'){
				$where[] = "(rd_percent = '".$query_params['probability']."' OR rd_percent IS NULL)";
			}
			else{
				$where[] = "rd_percent = '".$query_params['probability']."'";
			}
		}
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$where[] = "id IN($rids)";
		}

		$where_cond = '';
		if(isset($where) && !empty($where)){
			$where_cond = 'WHERE ';
			$where_cond .= implode(' AND ', $where);
		}

		$ele_cond = '';
		if(isset($query_params['element_id']) && !empty($query_params['element_id'])){
			$elids = implode(',', $query_params['element_id']);
			$ele_cond = "WHERE rel.element_id IN ($elids)";
		}

		$risk_cond = '';
		if(isset($query_params['risk_id']) && !empty($query_params['risk_id'])){
			$rids = $query_params['risk_id'];
			$risk_cond = "AND rd.id = '$rids'";
		}

		$others_risk = '';
		if(!isset($query_params['my_risks']) || empty($query_params['my_risks'])){
			$others_risk = " OR rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id)";
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($limit) && !empty($limit)){
			$limit_str = "LIMIT $page, $limit";
		}

		$order_by = "ORDER BY rdate DESC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$order = $query_params['order'];
			$coloumn = $query_params['coloumn'];
			$order_by = "ORDER BY $coloumn $order";

			if($query_params['coloumn'] == 'rd_exposure'){
				$order_by = 'ORDER BY CASE
						WHEN rd_exposure="Low" THEN 1
						WHEN rd_exposure="Medium" THEN 2
						WHEN rd_exposure="High" THEN 3
						WHEN rd_exposure="Severe" THEN 4
						WHEN rd_exposure="None" OR rd_exposure IS NULL THEN 5
					END ' . $query_params['order'];
			}
		}

		$query = "SELECT
					risk.id,
					risk.project_id,
					risk.title,
					risk.ptitle,
					risk.rdate,
					risk.rd_status,
					risk.status_num,
					risk.creator_id,
					risk.creator_name,
					risk.creator_pic,
					risk.creator_job,
					risk.creator_org,
					risk.risk_type,
					exp.contingency_exists,
					exp.mitigation_exists,
					exp.residual_exists,
					if(exp.rd_percent IS NULL , 'Not Set', exp.rd_percent) AS rd_prob,
					if(exp.rd_impact IS NULL , 'Not Set', exp.rd_impact) AS rd_impact,
					if(exp.rd_exposure IS NULL , 'None', exp.rd_exposure) AS rd_exposure,
					exp.prob_num,
					exp.impact_num,
					ruser.assignee,
					if(ruser_counts.ruser_count IS NULL, 0, ruser_counts.ruser_count) AS ruser_count,
					rleader.leaders,
					rtask.risk_tasks,
					if(rtask_counts.rtask_count IS NULL, 0, rtask_counts.rtask_count) AS rtask_count

				# RISK DETAILS
				FROM
				(
					SELECT
					    p.id AS project_id,
					    p.title AS ptitle,
					    rd.id,
					    rd.title,
					    rd.possible_occurrence AS rdate,
					    rpt.title AS risk_type,
					    (CASE
				            WHEN rd.status = 2 THEN 'Review'
				            WHEN rd.status = 3 THEN 'SignOff'
				            WHEN rd.status = 4 THEN 'Overdue'
				            ELSE 'Open'
				        END ) AS rd_status,
				        rd.status AS status_num,

				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator_name,
				        ud.user_id AS creator_id,
				        ud.profile_pic AS creator_pic,
						ud.job_title AS creator_job,
						ud.organization_id AS creator_org

					FROM
					    rm_details rd
					LEFT JOIN projects p
						ON rd.project_id = p.id
					LEFT JOIN user_details ud
						ON ud.user_id = rd.created_by
					LEFT JOIN rm_project_risk_types rpt
						ON rpt.id = rd.rm_project_risk_type_id

					WHERE
					    ((rd.user_id = $user_id) $others_risk) $risk_cond

				) AS risk
				# RISK EXPOSURE
				LEFT JOIN
				(
					SELECT
						res.rm_detail_id,
						if(res.contingency IS NULL , 0, 1) AS contingency_exists,
						if(res.mitigation IS NULL , 0, 1) AS mitigation_exists,
						if(res.residual IS NULL , 0, 1) AS residual_exists,
				        res.percentage AS prob_num,
				        res.impact AS impact_num,
						CASE
				            WHEN res.percentage = 1 THEN 'Rare'
				            WHEN res.percentage = 2 THEN 'Unlikely'
				            WHEN res.percentage = 3 THEN 'Possible'
				            WHEN res.percentage = 4 THEN 'Likely'
				            WHEN res.percentage = 5 THEN 'Almost Certain'
				            ELSE 'Not Set'
				        END AS rd_percent,
						CASE
				            WHEN res.impact = 1 THEN 'Negligible'
				            WHEN res.impact = 2 THEN 'Minor'
				            WHEN res.impact = 3 THEN 'Moderate'
				            WHEN res.impact = 4 THEN 'Major'
				            WHEN res.impact = 5 THEN 'Critical'
				            ELSE 'Not Set'
				        END AS rd_impact,
						CASE
				            WHEN ( (res.impact = 1 AND (res.percentage = 3 OR res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 2 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 3 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 4 AND res.percentage= 1) ) THEN 'Low'
				            WHEN ( (res.impact = 1 AND (res.percentage = 5 OR res.percentage = 4)) OR (res.impact = 2 AND (res.percentage = 4 OR res.percentage= 3)) OR (res.impact = 3 AND res.percentage = 3) OR (res.impact = 4 AND (res.percentage = 3 OR res.percentage = 2)) OR (res.impact = 5 AND (res.percentage = 2 OR res.percentage = 1)) ) THEN 'Medium'
				            WHEN ( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) THEN 'High'
				            WHEN ( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) THEN 'Severe'
				            ELSE 'None'
				        END AS rd_exposure
					FROM
					    rm_expose_responses res
					LEFT JOIN rm_details rd
						ON rd.id = res.rm_detail_id
					GROUP BY rd.id
				) AS exp
				ON exp.rm_detail_id = risk.id
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS assignee
				   	FROM rm_users ru
					INNER JOIN
						user_details ud
					ON ud.user_id = ru.user_id
				   	GROUP BY ru.rm_detail_id
				) AS ruser
				ON ruser.rmid = risk.id
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		COUNT(ud.user_id) AS ruser_count
				   	FROM rm_users ru
					INNER JOIN
						user_details ud
					ON ud.user_id = ru.user_id
				   	GROUP BY ru.rm_detail_id
				) AS ruser_counts
				ON ruser_counts.rmid = risk.id
				# RISK LEADERS COUNT
				LEFT JOIN (
				   	SELECT
				   		rld.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name))) AS leaders
				   	FROM rm_leaders rld
					INNER JOIN
						user_details ud
					ON ud.user_id = rld.user_id
				   	GROUP BY rld.rm_detail_id
				) AS rleader
				ON rleader.rmid = risk.id
				# RISK TASKS
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						JSON_ARRAYAGG(JSON_OBJECT( 'id', rel.element_id, 'title', el.title)) AS risk_tasks
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					$ele_cond
					GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = risk.id
				# RISK TASKS COUNT
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						COUNT(rel.element_id) AS rtask_count
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					$ele_cond
					GROUP BY rel.rm_detail_id
				) AS rtask_counts
				ON rtask_counts.rmid = risk.id

				$where_cond
				GROUP BY risk.id
				$order_by

				";

		// pr($query);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function type_by_project($user_id = null, $project_id = null){

		$query = "SELECT
					prt.id,
					prt.title
				FROM rm_project_risk_types prt
				WHERE prt.project_id = $project_id
			";

		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}


	function risk_detail($user_id = null, $risk_id = null ){


		$risk_cond = '';
		if(isset($risk_id) && !empty($risk_id)){
			$risk_cond = "AND rd.id = '$risk_id'";
		}
		$query = "SELECT
					risk.id,
					risk.project_id,
					risk.title,
					risk.ptitle,
					risk.rdate,
					risk.rd_status,
					risk.status_num,
					risk.creator_id,
					risk.creator_name,
					risk.creator_pic,
					risk.creator_job,
					risk.creator_org,
					risk.risk_type,
					risk.description,
					exp.mitigation,
					exp.contingency,
					exp.residual,
					exp.contingency_exists,
					exp.mitigation_exists,
					exp.residual_exists,
					if(exp.rd_percent IS NULL , 'Not Set', exp.rd_percent) AS rd_prob,
					if(exp.rd_impact IS NULL , 'Not Set', exp.rd_impact) AS rd_impact,
					if(exp.rd_exposure IS NULL , 'None', exp.rd_exposure) AS rd_exposure,
					exp.prob_num,
					exp.impact_num,
					ruser.assignee,
					rleader.leaders,
					rtask.risk_tasks

				# RISK DETAILS
				FROM
				(
					SELECT
						up.role,
					    up.project_id,
					    p.title AS ptitle,
					    rd.id,
					    rd.title,
					    rd.description,
					    rd.possible_occurrence AS rdate,
					    rpt.title AS risk_type,
					    CASE
				            WHEN rd.status = 2 THEN 'Review'
				            WHEN rd.status = 3 THEN 'SignOff'
				            WHEN rd.status = 4 THEN 'Overdue'
				            ELSE 'Open'
				        END AS rd_status,
				        rd.status AS status_num,

				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator_name,
				        ud.user_id AS creator_id,
				        ud.profile_pic AS creator_pic,
						ud.job_title AS creator_job,
						ud.organization_id AS creator_org

					FROM
					    user_permissions up
					LEFT JOIN projects p
						ON up.project_id = p.id
					LEFT JOIN rm_details rd
						ON up.project_id = rd.project_id
					LEFT JOIN user_details ud
						ON ud.user_id = rd.created_by
					LEFT JOIN rm_project_risk_types rpt
						ON rpt.id = rd.rm_project_risk_type_id

					WHERE
					    (rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id) OR rd.user_id = $user_id) $risk_cond

				) AS risk
				# RISK EXPOSURE
				LEFT JOIN
				(
					SELECT
						res.rm_detail_id,
						res.mitigation,
						res.contingency,
						res.residual,
						if(res.contingency IS NULL , 0, 1) AS contingency_exists,
						if(res.mitigation IS NULL , 0, 1) AS mitigation_exists,
						if(res.residual IS NULL , 0, 1) AS residual_exists,
				        res.percentage AS prob_num,
				        res.impact AS impact_num,
						CASE
				            WHEN res.percentage = 1 THEN 'Rare'
				            WHEN res.percentage = 2 THEN 'Unlikely'
				            WHEN res.percentage = 3 THEN 'Possible'
				            WHEN res.percentage = 4 THEN 'Likely'
				            WHEN res.percentage = 5 THEN 'Almost Certain'
				            ELSE 'Not Set'
				        END AS rd_percent,
						CASE
				            WHEN res.impact = 1 THEN 'Negligible'
				            WHEN res.impact = 2 THEN 'Minor'
				            WHEN res.impact = 3 THEN 'Moderate'
				            WHEN res.impact = 4 THEN 'Major'
				            WHEN res.impact = 5 THEN 'Critical'
				            ELSE 'Not Set'
				        END AS rd_impact,
						CASE
				            WHEN ( (res.impact = 1 AND (res.percentage = 3 OR res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 2 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 3 AND (res.percentage = 2 OR res.percentage= 1)) OR (res.impact = 4 AND res.percentage= 1) ) THEN 'Low'
				            WHEN ( (res.impact = 1 AND (res.percentage = 5 OR res.percentage = 4)) OR (res.impact = 2 AND (res.percentage = 4 OR res.percentage= 3)) OR (res.impact = 3 AND res.percentage = 3) OR (res.impact = 4 AND (res.percentage = 3 OR res.percentage = 2)) OR (res.impact = 5 AND (res.percentage = 2 OR res.percentage = 1)) ) THEN 'Medium'
				            WHEN ( (res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) THEN 'High'
				            WHEN ( (res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)) ) THEN 'Severe'
				            ELSE 'None'
				        END AS rd_exposure
					FROM
					    rm_expose_responses res
					LEFT JOIN rm_details rd
						ON rd.id = res.rm_detail_id
					GROUP BY rd.id
				) AS exp
				ON exp.rm_detail_id = risk.id
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name), 'profile_pic', ud.profile_pic, 'job_title', ud.job_title, 'organization_id', ud.organization_id)) AS assignee
				   	FROM rm_users ru
					INNER JOIN
						user_details ud
					ON ud.user_id = ru.user_id
				   	GROUP BY ru.rm_detail_id
				) AS ruser
				ON ruser.rmid = risk.id
				# RISK LEADERS
				LEFT JOIN (
				   	SELECT
				   		rld.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name), 'profile_pic', ud.profile_pic, 'job_title', ud.job_title, 'organization_id', ud.organization_id)) AS leaders
				   	FROM rm_leaders rld
					INNER JOIN
						user_details ud
					ON ud.user_id = rld.user_id
				   	GROUP BY rld.rm_detail_id
				) AS rleader
				ON rleader.rmid = risk.id
				# RISK TASKS
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						JSON_ARRAYAGG(JSON_OBJECT( 'id', rel.element_id, 'title', el.title)) AS risk_tasks
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = risk.id

				GROUP BY risk.id ";

		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function project_breakdown($user_id = null, $project_id = null ){

		$current_org = $this->current_org();
		$current_org = $current_org['organization_id'];

		$query = "SELECT
				    JSON_OBJECT(
				        'project_id', projectsList.project_id,
				        'created_id', projectsList.created_id,
				        'created_name', projectsList.created_name,
				        'created_image', projectsList.created_image,
				        'created_niyo', projectsList.created_niyo,
				        'name', projectsList.name,
				        'type', projectsList.type,
				        'status', projectsList.status,
				        'start_date', projectsList.start_date,
				        'end_date', projectsList.end_date,
				        'level', projectsList.level,
				        'level_count', projectsList.level_count,
				        'owner_count', projectsList.owner_count,
				        'sharer_count', projectsList.sharer_count,
				        'symbol', projectsList.symbol,
				        'budget', projectsList.budget,
				        'actual', projectsList.actual,
				        'total_hours', projectsList.total_hours,
				        'blue_completed_hours', projectsList.blue_completed_hours,
				        'green_remaining_hours', projectsList.green_remaining_hours,
				        'amber_remaining_hours', projectsList.amber_remaining_hours,
				        'red_remaining_hours', projectsList.red_remaining_hours,
				        'noschedule_remaining_hours', projectsList.noschedule_remaining_hours,
				        'complete_risks', projectsList.complete_risks,
				        'incomplete_low', projectsList.incomplete_low,
				        'incomplete_medium', projectsList.incomplete_medium,
				        'incomplete_high', projectsList.incomplete_high,
				        'incomplete_severe', projectsList.incomplete_severe,
				        'incomplete_none', projectsList.incomplete_none,
				        'children', IFNULL(projectsList.workspaces, JSON_ARRAY())
				    ) AS projectRoots

				FROM
				( #get projects
				    SELECT
				    	pd.project_id,
				    	pd.created_id,
				        pd.created_name,
				        pd.created_image,
				        pd.created_niyo,
				        pd.name,
				        'Project' AS type,
				    	pd.status AS status,
				        pd.start_date,
				        pd.end_date,
				        SUM(workspacesList.level) AS level,
				        SUM(workspacesList.level_count) AS level_count,
				    	pd.owner_count,
				    	pd.sharer_count,
				    	pd.symbol,
				    	SUM(workspacesList.budget) AS budget,
				    	SUM(workspacesList.actual) AS actual,
				    	SUM(workspacesList.total_hours) AS total_hours,
				        SUM(workspacesList.blue_completed_hours) AS blue_completed_hours,
				        SUM(workspacesList.green_remaining_hours) AS green_remaining_hours,
				        SUM(workspacesList.amber_remaining_hours) AS amber_remaining_hours,
				        SUM(workspacesList.red_remaining_hours) AS red_remaining_hours,
				        SUM(workspacesList.noschedule_remaining_hours) AS noschedule_remaining_hours,
				        pr.complete_risks,
				        pr.incomplete_low,
				        pr.incomplete_medium,
				        pr.incomplete_high,
				        pr.incomplete_severe,
				        pr.incomplete_none,
				    	IF(workspacesList.workspace_id IS NULL, JSON_ARRAY(),
				           JSON_ARRAYAGG(
				            JSON_OBJECT(
				                'project_id', workspacesList.project_id,
				                'workspace_id', workspacesList.workspace_id,
				                'created_id', workspacesList.created_id,
				                'created_name', workspacesList.created_name,
				                'created_image', workspacesList.created_image,
				                'created_niyo', workspacesList.created_niyo,
				                'name', workspacesList.name,
				                'type', workspacesList.type,
				                'status', workspacesList.status,
				                'start_date', workspacesList.start_date,
				                'end_date', workspacesList.end_date,
				                'level', workspacesList.level,
				                'level_count', workspacesList.level_count,
				                'owner_count', workspacesList.owner_count,
				                'sharer_count', workspacesList.sharer_count,
				                'budget', workspacesList.budget,
				                'actual', workspacesList.actual,
				                'total_hours', workspacesList.total_hours,
				                'blue_completed_hours', workspacesList.blue_completed_hours,
				                'green_remaining_hours', workspacesList.green_remaining_hours,
				                'amber_remaining_hours', workspacesList.amber_remaining_hours,
				                'red_remaining_hours', workspacesList.red_remaining_hours,
				                'noschedule_remaining_hours', workspacesList.noschedule_remaining_hours,
				                'complete_risks', workspacesList.complete_risks,
				                'incomplete_medium', workspacesList.incomplete_medium,
				                'incomplete_high', workspacesList.incomplete_high,
				                'incomplete_severe', workspacesList.incomplete_severe,
				                'incomplete_none', workspacesList.incomplete_none,
				                'children', IFNULL(workspacesList.areas, JSON_ARRAY())
				            )
				        )) AS workspaces
				    FROM #get project details
				    (
				    	SELECT
				    	p.id AS project_id,
				        ud.user_id AS created_id,
				        CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				        ud.profile_pic AS created_image,
				        IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				        p.title AS name,
				        #IF(c.symbol = 'Â£', '£', c.symbol) AS symbol,
				       	CONCAT(c.sign, ' ') AS symbol,
				       	#c.symbol,
				        p.sign_off,
				        (CASE
				   			WHEN p.sign_off = 1 THEN 'Completed'
				    		WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
				    		WHEN Date(NOW()) BETWEEN Date(p.start_date) AND Date(p.end_date) THEN 'In Progress'
				         	WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started'
				    	    ELSE 'Not Set'
				        END) AS status,
				        DATE_FORMAT(DATE(p.start_date), '%e %b, %Y') AS start_date,
				        DATE_FORMAT(DATE(p.end_date), '%e %b, %Y') AS end_date,
				        SUM(IF(up.role IN ('Creator', 'Owner', 'Group Owner'),1,0)) AS owner_count,
				        SUM(IF(up.role IN ('Sharer', 'Group Sharer'),1,0)) AS sharer_count
				        FROM
				            projects p
				        LEFT JOIN user_permissions up ON
				            up.workspace_id IS NULL
				            AND p.id = up.project_id #change at runtime
				        LEFT JOIN user_details ud ON
				        	p.created_by = ud.user_id
				        LEFT JOIN currencies c ON
				        	p.currency_id = c.id
				        WHERE
				            p.id = $project_id #change at runtime
				    ) AS pd
				    LEFT JOIN #get project risks
				    (
				        SELECT
				            er.project_id,
				            SUM(complete_risks) AS complete_risks,
				            SUM(incomplete_low) AS incomplete_low,
				            SUM(incomplete_medium) AS incomplete_medium,
				            SUM(incomplete_high) AS incomplete_high,
				            SUM(incomplete_severe) AS incomplete_severe,
				            SUM(incomplete_none) AS incomplete_none
				        FROM
				        (
				            SELECT
				                rd.project_id,
				                IF(rd.status = 3,1,0) AS complete_risks,
				                IF(((percentage = 3 AND impact = 1) OR (percentage = 2 AND (impact = 1 OR impact = 2 OR impact = 3)) OR (percentage = 1 AND (impact = 1 OR impact = 2 OR impact = 3 OR impact = 4))) #low
				                   AND (rd.status <> 3) #not complete
				                   ,1,0) AS incomplete_low,
				                IF(((percentage = 5 OR percentage = 4) AND impact = 1) OR ((percentage = 4 OR percentage = 3) AND impact = 2) OR (percentage = 3 AND impact = 3) OR ((percentage = 3 OR percentage = 2) AND impact = 4) OR ((percentage = 2 OR percentage = 1) AND impact = 5) #medium
				                   AND (rd.status <> 3) #not complete
				                   ,1,0) AS incomplete_medium,
				                IF(((percentage = 5 AND(impact = 2 OR impact = 3)) OR (percentage = 4 AND(impact = 3 OR impact = 4)) OR (percentage = 3 AND impact = 5)) #high
				                   AND (rd.status <> 3) #not complete
				                   ,1,0) AS incomplete_high,
				                IF(((percentage = 5 AND(impact = 4 OR impact = 5)) OR (percentage = 4 AND impact = 5)) #severe
				                   AND (rd.status <> 3) #not complete
				                   ,1,0) AS incomplete_severe,
				                IF(percentage IS NULL AND impact IS NULL AND rd.status <> 3
				                   ,1,0) AS incomplete_none
				            FROM
				            	rm_details rd
				            LEFT JOIN rm_expose_responses rr ON
				            	rd.id = rr.rm_detail_id
				            WHERE
				            	rd.project_id = $project_id #change at runtime
				        ) AS er
				        GROUP BY er.project_id
				    ) AS pr ON
				    	pd.project_id = pr.project_id
				    LEFT JOIN #get workspaces
				    (
				        SELECT DISTINCT
				            up.project_id,
				            up.workspace_id,
				            ud.user_id AS created_id,
				            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				            ud.profile_pic AS created_image,
				            IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				            ws.title AS name,
				            'Workspace' AS type,
				            (CASE
				                WHEN ws.sign_off = 1 THEN 'Completed'
				                WHEN Date(NOW()) > Date(ws.end_date) THEN 'Overdue'
				                WHEN Date(NOW()) BETWEEN Date(ws.start_date) AND Date(ws.end_date) THEN 'In Progress'
				                WHEN Date(NOW()) < Date(ws.start_date) THEN 'Not Started'
				                ELSE 'Not Set'
				            END) AS status,
				            DATE_FORMAT(DATE(ws.start_date), '%e %b, %Y') AS start_date,
				            DATE_FORMAT(DATE(ws.end_date), '%e %b, %Y') AS end_date,
				            SUM(areasList.level) AS level,
				        	SUM(areasList.level_count) AS level_count,
				        	wd.owner_count,
				        	wd.sharer_count,
				        	SUM(areasList.budget) AS budget,
				        	SUM(areasList.actual) AS actual,
				        	SUM(areasList.total_hours) AS total_hours,
				            SUM(areasList.blue_completed_hours) AS blue_completed_hours,
				            SUM(areasList.green_remaining_hours) AS green_remaining_hours,
				            SUM(areasList.amber_remaining_hours) AS amber_remaining_hours,
				            SUM(areasList.red_remaining_hours) AS red_remaining_hours,
				            SUM(areasList.noschedule_remaining_hours) AS noschedule_remaining_hours,
							wr.complete_risks,
				            wr.incomplete_medium,
				            wr.incomplete_high,
				            wr.incomplete_severe,
				            wr.incomplete_none,
				            IF(areasList.area_id IS NULL, JSON_ARRAY(),
				               JSON_ARRAYAGG(
				                JSON_OBJECT(
				                    'project_id', up.project_id,
				                    'workspace_id', up.workspace_id,
				                    'area_id', areasList.area_id,
				                    'name', areasList.name,
				                    'type', 'Area',
				                    'level', areasList.level,
				                    'level_count', areasList.level_count,
				                    'owner_count', areasList.owner_count,
				                    'sharer_count', areasList.sharer_count,
				                    'budget', areasList.budget,
				                    'actual', areasList.actual,
				                    'total_hours', areasList.total_hours,
				                    'blue_completed_hours', areasList.blue_completed_hours,
				                    'green_remaining_hours', areasList.green_remaining_hours,
				                    'amber_remaining_hours', areasList.amber_remaining_hours,
				                    'red_remaining_hours', areasList.red_remaining_hours,
				                    'noschedule_remaining_hours', areasList.noschedule_remaining_hours,
				                    'complete_risks', areasList.complete_risks,
				                    'incomplete_medium', areasList.incomplete_medium,
				                    'incomplete_high', areasList.incomplete_high,
				                    'incomplete_severe', areasList.incomplete_severe,
				                    'incomplete_none', areasList.incomplete_none,
				                    'children', IFNULL(areasList.tasks, JSON_ARRAY())
				                )
				            )) AS areas
				        FROM
				            user_permissions up
				        LEFT JOIN workspaces ws ON
				        	up.workspace_id = ws.id
				        LEFT JOIN project_workspaces pw ON
				        	up.project_id = pw.project_id
				        	AND up.workspace_id = pw.workspace_id
				        LEFT JOIN user_details ud ON
				        	ws.created_by = ud.user_id
				        LEFT JOIN #get workspace team
				        (
				            SELECT
				                up.workspace_id,
				                SUM(IF(up.role IN ('Creator', 'Owner', 'Group Owner'),1,0)) AS owner_count,
				                SUM(IF(up.role IN ('Sharer', 'Group Sharer'),1,0)) AS sharer_count
				            FROM
				                user_permissions up
				            WHERE
				                up.area_id IS NULL
				            	AND up.workspace_id IS NOT NULL
				                AND up.project_id = $project_id #change at runtime
				            GROUP BY up.workspace_id
				        ) AS wd ON
				        	up.workspace_id = wd.workspace_id
				        LEFT JOIN #get workspace risks
				        (
				            SELECT
				                wr2.workspace_id,
				                SUM(wr2.complete_risks) AS complete_risks,
				                SUM(wr2.incomplete_medium) AS incomplete_medium,
				                SUM(wr2.incomplete_high) AS incomplete_high,
				                SUM(wr2.incomplete_severe) AS incomplete_severe,
				                SUM(wr2.incomplete_none) AS incomplete_none
				            FROM
				            (
				                SELECT
				                    wr.workspace_id,
				                    IF(rd.status = 3,1,0) AS complete_risks,
				                    IF(((percentage = 3 AND impact = 1) OR (percentage = 2 AND (impact = 1 OR impact = 2 OR impact = 3)) OR (percentage = 1 AND (impact = 1 OR impact = 2 OR impact = 3 OR impact = 4))) #low
				                       AND (rd.status <> 3) #not complete
				                       ,1,0) AS incomplete_low,
				                    IF(((percentage = 5 OR percentage = 4) AND impact = 1) OR ((percentage = 4 OR percentage = 3) AND impact = 2) OR (percentage = 3 AND impact = 3) OR ((percentage = 3 OR percentage = 2) AND impact = 4) OR ((percentage = 2 OR percentage = 1) AND impact = 5) #medium
				                       AND (rd.status <> 3) #not complete
				                       ,1,0) AS incomplete_medium,
				                    IF(((percentage = 5 AND(impact = 2 OR impact = 3)) OR (percentage = 4 AND(impact = 3 OR impact = 4)) OR (percentage = 3 AND impact = 5)) #high
				                       AND (rd.status <> 3) #not complete
				                       ,1,0) AS incomplete_high,
				                    IF(((percentage = 5 AND(impact = 4 OR impact = 5)) OR (percentage = 4 AND impact = 5)) #severe
				                       AND (rd.status <> 3) #not complete
				                       ,1,0) AS incomplete_severe,
				                    IF(percentage IS NULL AND impact IS NULL AND rd.status <> 3
				                   ,1,0) AS incomplete_none
				                FROM
				                ( #get workspace risks
				                    SELECT DISTINCT
				                        up.workspace_id,
				                        re.rm_detail_id
				                    FROM
				                    	user_permissions up
				                    LEFT JOIN rm_elements re ON
				                    	up.element_id = re.element_id
				                    WHERE
				                    	up.element_id IS NOT NULL
				                        AND up.project_id = $project_id #change at runtime
				                        AND up.user_id = $user_id #change at runtime
				                        AND re.rm_detail_id IS NOT NULL
				                ) AS wr
				                LEFT JOIN rm_details rd ON
				                	wr.rm_detail_id = rd.id
				                LEFT JOIN rm_expose_responses rr ON
				                	wr.rm_detail_id = rr.rm_detail_id
				            ) AS wr2
				            GROUP BY wr2.workspace_id
				        ) AS wr ON
				        	up.workspace_id = wr.workspace_id
				        LEFT JOIN #get areas
				        (
				            SELECT DISTINCT
				                    ar.workspace_id,
				                    ar.area_id,
				                    ar.title AS name,
				                    'Area' AS type,
				            		SUM(tasksList.level) AS level,
				                	SUM(tasksList.level_count) AS level_count,
				            		at.owner_count,
				                   	at.sharer_count,
				            		SUM(tasksList.budget) AS budget,
				            		SUM(tasksList.actual) AS actual,
				            		SUM(tasksList.total_hours) AS total_hours,
				                    SUM(tasksList.blue_completed_hours) AS blue_completed_hours,
				                    SUM(tasksList.green_remaining_hours) AS green_remaining_hours,
				                    SUM(tasksList.amber_remaining_hours) AS amber_remaining_hours,
				                    SUM(tasksList.red_remaining_hours) AS red_remaining_hours,
				                    SUM(tasksList.noschedule_remaining_hours) AS noschedule_remaining_hours,
				            		ak.complete_risks,
				                    ak.incomplete_medium,
				                    ak.incomplete_high,
				                    ak.incomplete_severe,
				                    ak.incomplete_none,
				                    IF(tasksList.element_id IS NULL, JSON_ARRAY(),
				                       JSON_ARRAYAGG(
				                            JSON_OBJECT(
				                                'project_id', tasksList.project_id,
				                                'workspace_id', tasksList.workspace_id,
				                                'area_id', tasksList.area_id,
				                                'element_id', tasksList.element_id,
				                                'created_id', tasksList.created_id,
				                                'created_name', tasksList.created_name,
				                                'created_image', tasksList.created_image,
				                                'created_niyo', tasksList.created_niyo,
				                                'name', tasksList.name,
				                                'type', 'Task',
				                                'status', tasksList.status,
				                                'start_date', tasksList.start_date,
				                                'end_date', tasksList.end_date,
				                                'level', tasksList.level,
				                                'level_count', IF(tasksList.level IS NULL, 0, 1),
				                                'owner_count', tasksList.owner_count,
				                                'sharer_count', tasksList.sharer_count,
				                                'leader_status', tasksList.leader_status,
				                                'assigned_id', tasksList.assigned_id,
				                                'assigned_name', tasksList.assigned_name,
				                                'assigned_image', tasksList.assigned_image,
				                                'assigned_niyo', tasksList.assigned_niyo,
				                                'budget', tasksList.budget,
				                                'actual', tasksList.actual,
				                                'total_hours', tasksList.total_hours,
				                                'blue_completed_hours', tasksList.blue_completed_hours,
				                                'green_remaining_hours', tasksList.green_remaining_hours,
				                                'amber_remaining_hours', tasksList.amber_remaining_hours,
				                                'red_remaining_hours', tasksList.red_remaining_hours,
				                                'noschedule_remaining_hours', tasksList.noschedule_remaining_hours,
				                                'complete_risks', tasksList.complete_risks,
				                                'incomplete_low', tasksList.incomplete_low,
				                                'incomplete_medium', tasksList.incomplete_medium,
				                                'incomplete_high', tasksList.incomplete_high,
				                                'incomplete_severe', tasksList.incomplete_severe,
				                                'incomplete_none', tasksList.incomplete_none,
				                                'children', IFNULL(tasksList.assets, JSON_ARRAY())
				                            )
				                        )
				                    ) AS tasks
				            FROM
				            	(#get workspace areas
				                    SELECT
				                        workspace_id,
				                        id AS area_id,
				                        title,
				                        sort_order
				                    FROM
				                        areas
				                    WHERE
				                        status = 1
				                    ORDER BY sort_order
				                ) AS ar
				            LEFT JOIN #get area team
				            (
				            	SELECT
				                au.area_id,
				                SUM(IF(au.role IN ('Creator', 'Owner', 'Group Owner'),1,0)) AS owner_count,
				                SUM(IF(au.role IN ('Sharer', 'Group Sharer'),1,0)) AS sharer_count
				                FROM
				                ( #get area users
				                    SELECT DISTINCT
				                        up.area_id,
				                        up.user_id,
				                        up.role
				                    FROM
				                        user_permissions up
				                    WHERE
				                        up.element_id IS NOT NULL
				                        AND up.project_id = $project_id #change at runtime
				                ) AS au
				           		GROUP BY au.area_id
				            ) AS at ON
				            	ar.area_id = at.area_id
				            LEFT JOIN #get area risks
				            (
				                SELECT
				                    ar2.area_id,
				                    SUM(ar2.complete_risks) AS complete_risks,
				                    SUM(ar2.incomplete_medium) AS incomplete_medium,
				                    SUM(ar2.incomplete_high) AS incomplete_high,
				                    SUM(ar2.incomplete_severe) AS incomplete_severe,
				                    SUM(ar2.incomplete_none) AS incomplete_none
				                FROM
				                (
				                    SELECT
				                        ar.area_id,
				                        IF(rd.status = 3,1,0) AS complete_risks,
				                        IF(((percentage = 3 AND impact = 1) OR (percentage = 2 AND (impact = 1 OR impact = 2 OR impact = 3)) OR (percentage = 1 AND (impact = 1 OR impact = 2 OR impact = 3 OR impact = 4))) #low
				                           AND (rd.status <> 3) #not complete
				                           ,1,0) AS incomplete_low,
				                        IF(((percentage = 5 OR percentage = 4) AND impact = 1) OR ((percentage = 4 OR percentage = 3) AND impact = 2) OR (percentage = 3 AND impact = 3) OR ((percentage = 3 OR percentage = 2) AND impact = 4) OR ((percentage = 2 OR percentage = 1) AND impact = 5) #medium
				                           AND (rd.status <> 3) #not complete
				                           ,1,0) AS incomplete_medium,
				                        IF(((percentage = 5 AND(impact = 2 OR impact = 3)) OR (percentage = 4 AND(impact = 3 OR impact = 4)) OR (percentage = 3 AND impact = 5)) #high
				                           AND (rd.status <> 3) #not complete
				                           ,1,0) AS incomplete_high,
				                        IF(((percentage = 5 AND(impact = 4 OR impact = 5)) OR (percentage = 4 AND impact = 5)) #severe
				                           AND (rd.status <> 3) #not complete
				                           ,1,0) AS incomplete_severe,
				                        IF(percentage IS NULL AND impact IS NULL AND rd.status <> 3
				                       ,1,0) AS incomplete_none
				                    FROM
				                    ( #get area risks
				                        SELECT DISTINCT
				                            up.area_id,
				                            re.rm_detail_id
				                        FROM
				                            user_permissions up
				                        LEFT JOIN rm_elements re ON
				                            up.element_id = re.element_id
				                        WHERE
				                            up.element_id IS NOT NULL
				                            AND up.project_id = $project_id #change at runtime
				                            AND up.user_id = $user_id #change at runtime
				                            AND re.rm_detail_id IS NOT NULL
				                    ) AS ar
				                    LEFT JOIN rm_details rd ON
				                        ar.rm_detail_id = rd.id
				                    LEFT JOIN rm_expose_responses rr ON
				                        ar.rm_detail_id = rr.rm_detail_id
				                ) AS ar2
				                GROUP BY ar2.area_id
				            ) AS ak ON
				            	ar.area_id = ak.area_id
				            LEFT JOIN #get tasks
				            (
				                SELECT DISTINCT
				                    up.project_id,
				                    up.workspace_id,
				                    up.area_id,
				                    up.element_id,
				                    ud.user_id AS created_id,
				                    CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                    ud.profile_pic AS created_image,
				                	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                    es.title AS name,
				                    'Task' AS type,
				                    (CASE
				                        WHEN es.sign_off = 1 THEN 'Completed'
				                        WHEN Date(NOW()) > Date(es.end_date) THEN 'Overdue'
				                        WHEN Date(NOW()) BETWEEN Date(es.start_date) AND Date(es.end_date) THEN 'In Progress'
				                        WHEN Date(NOW()) < Date(es.start_date) THEN 'Not Started'
				                        ELSE 'Not Set'
				                    END) AS status,
				                    DATE_FORMAT(DATE(es.start_date), '%e %b, %Y') AS start_date,
				                    DATE_FORMAT(DATE(es.end_date), '%e %b, %Y') AS end_date,
				                    el.level,
				                	IF(el.level IS NULL, 0,1) AS level_count,
				                	et.owner_count,
				                	et.sharer_count,
				                	et.leader_status,
				                	et.assigned_id,
				                	CONCAT(ud2.first_name, ' ',ud2.last_name) AS assigned_name,
				                	ud2.profile_pic AS assigned_image,
				                	IF(IF(ISNULL(ud2.organization_id), '', ud2.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS assigned_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                	#---------------------------------
				                	#REPLACE IF CURRENT USER IS SHARER
				                	ec.budget, # -1 AS budget,
				                	ec.actual,	# -1 AS actual,
				                	#---------------------------------
				                	ee.total_hours,
				                	ee.blue_completed_hours,
				                	ee.green_remaining_hours,
				                	ee.amber_remaining_hours,
				                	ee.red_remaining_hours,
				                	ee.noschedule_remaining_hours,
				                	er.complete_risks,
				                    er.incomplete_low,
				                    er.incomplete_medium,
				                    er.incomplete_high,
				                    er.incomplete_severe,
				                	er.incomplete_none,
				                	IF(assetsList.asset_id IS NULL, JSON_ARRAY(),
				                       JSON_ARRAYAGG(
				                            JSON_OBJECT(
				                                'project_id', assetsList.project_id,
				                                'workspace_id', assetsList.workspace_id,
				                                'area_id', assetsList.area_id,
				                                'task_id', assetsList.element_id,
				                                'asset_id', assetsList.asset_id,
				                                'created_id', assetsList.created_id,
				                                'created_name', assetsList.created_name,
				                                'created_image', assetsList.created_image,
				                                'created_niyo', assetsList.created_niyo,
				                                'name', assetsList.name,
				                                'type', assetsList.type,
				                                'status', assetsList.status,
				                                'start_date', assetsList.start_date,
				                    			'end_date', assetsList.end_date
				                            )
				                        )
				                    ) AS assets
				                FROM
				                    user_permissions up
				                LEFT JOIN elements es ON
				                	up.element_id = es.id
				                LEFT JOIN element_levels el ON
				                	up.element_id = el.element_id
				                	AND el.is_active = 1
				                LEFT JOIN user_details ud ON
				                	es.created_by = ud.user_id
				                LEFT JOIN user_details ud3 ON
				                	es.updated_user_id = ud3.user_id
				                LEFT JOIN #get task team
				                (
				                     SELECT
				                        up.element_id,
				                        SUM(IF(up.role IN ('Creator', 'Owner', 'Group Owner'),1,0)) AS owner_count,
				                        SUM(IF(up.role IN ('Sharer', 'Group Sharer'),1,0)) AS sharer_count,
				                        SUM(ea.reaction) AS leader_status,
				                    	SUM(ea.assigned_to) AS assigned_id
				                    FROM
				                        user_permissions up
				                    LEFT JOIN element_assignments ea ON
				                        up.project_id = ea.project_id
				                        AND up.element_id = ea.element_id
				                        AND up.user_id = ea.assigned_to
				                    WHERE
				                        up.element_id IS NOT NULL
				                        AND up.project_id = $project_id #change at runtime
				                    GROUP BY up.element_id
				                ) AS et ON
				                	up.element_id = et.element_id
				                LEFT JOIN user_details ud2 ON
				                	et.assigned_id = ud2.user_id
				                LEFT JOIN #get effort
				                (
				                    SELECT
				                        eh.element_id,
				                        SUM(eh.completed_hours + eh.remaining_hours) AS total_hours,
				                        SUM(eh.completed_hours) AS blue_completed_hours,
				                        SUM(IF(eh.remaining_hours_color = 'Green', eh.remaining_hours, 0)) AS green_remaining_hours,
				                        SUM(IF(eh.remaining_hours_color = 'Amber', eh.remaining_hours, 0)) AS amber_remaining_hours,
				                        SUM(IF(eh.remaining_hours_color = 'Red', eh.remaining_hours, 0)) AS red_remaining_hours,
				                        SUM(IF(eh.remaining_hours_color = 'No Schedule', eh.remaining_hours, 0)) AS noschedule_remaining_hours
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
				                            THEN 'No Schedule'
				                            WHEN
				                            CEIL(ee.remaining_hours/8)
				                            > DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1
				                            THEN 'Red'
				                            WHEN
				                            CEIL(GREATEST(CEIL(ee.remaining_hours/8),2)*1.5)
				                            > DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1
				                            THEN 'Amber'
				                            ELSE 'Green'
				                            END AS remaining_hours_color,
				                            ee.change_hours
				                        FROM
				                        	element_efforts ee
				                        LEFT JOIN elements el ON
				                        	ee.element_id = el.id
				                        WHERE
				                            ee.project_id = $project_id #change at runtime
				                            AND ee.is_active = 1
				                        GROUP BY ee.element_id, ee.user_id
				                    ) AS eh
				                    GROUP BY eh.element_id
				                ) AS ee ON
				                	up.element_id = ee.element_id
				                LEFT JOIN #get task costs
				                (
				                    SELECT
				                        ec.element_id,
				                        ROUND(SUM(ec.estimated_cost),2) AS budget,
				                        ROUND(SUM(ec.spend_cost),2) AS actual
				                    FROM
				                        element_costs ec
				                    GROUP BY ec.element_id
				                ) AS ec ON
				                	up.element_id = ec.element_id
				                LEFT JOIN #get risks
				                (
				                    SELECT
				                        er.element_id,
				                        SUM(complete_risks) AS complete_risks,
				                        SUM(incomplete_low) AS incomplete_low,
				                        SUM(incomplete_medium) AS incomplete_medium,
				                        SUM(incomplete_high) AS incomplete_high,
				                        SUM(incomplete_severe) AS incomplete_severe,
				                        SUM(incomplete_none) AS incomplete_none
				                    FROM
				                    (
				                        SELECT
				                            re.element_id,
				                            IF(rd.status = 3,1,0) AS complete_risks,
				                            IF(((percentage = 3 AND impact = 1) OR (percentage = 2 AND (impact = 1 OR impact = 2 OR impact = 3)) OR (percentage = 1 AND (impact = 1 OR impact = 2 OR impact = 3 OR impact = 4))) #low
				                               AND (rd.status <> 3) #not complete
				                               ,1,0) AS incomplete_low,
				                            IF(((percentage = 5 OR percentage = 4) AND impact = 1) OR ((percentage = 4 OR percentage = 3) AND impact = 2) OR (percentage = 3 AND impact = 3) OR ((percentage = 3 OR percentage = 2) AND impact = 4) OR ((percentage = 2 OR percentage = 1) AND impact = 5) #medium
				                               AND (rd.status <> 3) #not complete
				                               ,1,0) AS incomplete_medium,
				                            IF(((percentage = 5 AND(impact = 2 OR impact = 3)) OR (percentage = 4 AND(impact = 3 OR impact = 4)) OR (percentage = 3 AND impact = 5)) #high
				                               AND (rd.status <> 3) #not complete
				                               ,1,0) AS incomplete_high,
				                            IF(((percentage = 5 AND(impact = 4 OR impact = 5)) OR (percentage = 4 AND impact = 5)) #severe
				                               AND (rd.status <> 3) #not complete
				                               ,1,0) AS incomplete_severe,
				                        	IF(percentage IS NULL AND impact IS NULL AND rd.status <> 3
				                   				,1,0) AS incomplete_none
				                        FROM
				                        	rm_elements re
				                        LEFT JOIN rm_details rd ON
				                        	re.rm_detail_id = rd.id
				                        LEFT JOIN rm_expose_responses rr ON
				                        	rd.id = rr.rm_detail_id
				                        WHERE
				                        	re.project_id = $project_id #change at runtime
				                    ) AS er
				                    GROUP BY er.element_id
				                ) AS er ON
				                up.element_id = er.element_id
				                LEFT JOIN #get assets
				                (
				                    SELECT DISTINCT
				                        assetsUnion.project_id,
				                        assetsUnion.workspace_id,
				                        assetsUnion.area_id,
				                        assetsUnion.element_id,
				                    	assetsUnion.asset_id,
				                        assetsUnion.created_id,
				                    	assetsUnion.created_name,
				                    	assetsUnion.created_image,
				                    	assetsUnion.created_niyo,
				                        assetsUnion.name,
				                        assetsUnion.type AS type,
				                    	assetsUnion.status,
				                    	assetsUnion.start_date,
				                    	assetsUnion.end_date
				                    FROM
				                    (
										#links
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	el.id AS asset_id,
				                         	ud.user_id AS created_id,
				                            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            el.title AS name,
				                            'Link' AS type,
				                        	NULL AS status,
				                         	NULL AS start_date,
				                    		NULL AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN element_links el ON
				                            up.element_id = el.element_id
				                            AND el.status = 1
				                        LEFT JOIN user_details ud ON
				                         	el.creater_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND el.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #notes
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	en.id AS asset_id,
				                         	ud.user_id AS created_id,
				                         	CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            en.title AS name,
				                            'Note' AS type,
				                        	NULL AS status,
				                         	NULL AS start_date,
				                    		NULL AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN element_notes en ON
				                            up.element_id = en.element_id
				                            AND en.status = 1
				                        LEFT JOIN user_details ud ON
				                         	en.creater_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND en.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #documents
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	ed.id AS asset_id,
				                         	ud.user_id AS created_id,
				                        	CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                        	ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            ed.title AS name,
				                            'Document' AS type,
				                        	NULL AS status,
				                         	NULL AS start_date,
				                    		NULL AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN element_documents ed ON
				                            up.element_id = ed.element_id
				                            AND ed.status = 1
				                        LEFT JOIN user_details ud ON
				                         	ed.creater_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND ed.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #mind maps
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	em.id AS asset_id,
				                         	ud.user_id AS created_id,
				                            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            em.title AS name,
				                            'Mind Map' AS type,
				                        	NULL AS status,
				                         	NULL AS start_date,
				                    		NULL AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN element_mindmaps em ON
				                            up.element_id = em.element_id
				                            AND em.status = 1
				                        LEFT JOIN user_details ud ON
				                         	em.creater_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND em.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #decisions
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	ed.id AS asset_id,
				                         	ud.user_id AS created_id,
				                            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            ed.title AS name,
				                            'Decision' AS type,
				                        	CASE
				                                WHEN ed.sign_off = 0 THEN 'In Progress'
				                                WHEN ed.sign_off = 1 THEN 'Completed'
				                                ELSE 'None'
				                            END AS status,
				                         	NULL AS start_date,
				                    		NULL AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN element_decisions ed ON
											up.element_id = ed.element_id
				                        LEFT JOIN user_details ud ON
				                         	ed.creater_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND ed.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #feedback
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	fb.id AS asset_id,
				                         	ud.user_id AS created_id,
				                            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            fb.title AS name,
				                            'Feedback' AS type,
				                        	CASE
				                                WHEN fb.sign_off = 1 THEN 'Completed'
				                                WHEN Date(NOW()) > Date(fb.end_date) AND fb.end_date IS NOT NULL AND fb.sign_off = 0 THEN 'Overdue'
				                                WHEN Date(NOW()) BETWEEN Date(fb.start_date) AND Date(fb.end_date) AND fb.sign_off = 0 THEN 'In Progress'
				                                WHEN Date(NOW()) < Date(fb.start_date) AND fb.sign_off = 0 THEN 'Not Started'
				                                ELSE 'None'
				                            END AS status,
				                         	DATE_FORMAT(DATE(fb.start_date), '%e %b, %Y') AS start_date,
				                    		DATE_FORMAT(DATE(fb.end_date), '%e %b, %Y') AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN feedback fb ON
				                            up.element_id = fb.element_id
				                            AND fb.status = 1
				                        LEFT JOIN user_details ud ON
				                         	fb.user_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND fb.id IS NOT NULL
				                        )
				                        UNION ALL
				                        #vote
				                        (SELECT
				                            up.project_id,
				                            up.workspace_id,
				                            up.area_id,
				                            up.element_id,
				                        	vt.id AS asset_id,
				                         	ud.user_id AS created_id,
				                            CONCAT(ud.first_name, ' ',ud.last_name) AS created_name,
				                            ud.profile_pic AS created_image,
				                         	IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($current_org),'',$current_org), '1','0') AS created_niyo, #where 2 is the current user organization_id and needs to be replaced at runtime
				                            vt.title AS name,
				                            'Vote' AS type,
				                        	CASE
				                                WHEN vt.is_completed = 1 THEN 'Completed'
				                                WHEN Date(NOW()) > Date(vt.end_date) AND vt.end_date IS NOT NULL AND vt.is_completed = 0 THEN 'Overdue'
				                                WHEN Date(NOW()) BETWEEN Date(vt.start_date) AND Date(vt.end_date) AND vt.is_completed = 0 THEN 'In Progress'
				                                WHEN Date(NOW()) < Date(vt.start_date) AND vt.is_completed = 0 THEN 'Not Started'
				                                ELSE 'None'
				                            END AS status,
				                         	DATE_FORMAT(DATE(vt.start_date), '%e %b, %Y') AS start_date,
				                    		DATE_FORMAT(DATE(vt.end_date), '%e %b, %Y') AS end_date
				                        FROM
				                            user_permissions up
				                        LEFT JOIN votes vt ON
											up.element_id = vt.element_id
				                        LEFT JOIN user_details ud ON
				                         	vt.user_id = ud.user_id
				                        WHERE
				                            up.project_id = $project_id #change at runtime
				                            AND up.element_id IS NOT NULL
				                         	AND up.user_id = $user_id #change at runtime
				                        	AND vt.id IS NOT NULL
				                        )
				                	) AS assetsUnion
				                    ORDER BY assetsUnion.type, assetsUnion.name
				                ) AS assetsList ON
				                	up.element_id = assetsList.element_id
				                WHERE
				                    up.project_id = $project_id #change at runtime
				                	AND up.element_id IS NOT NULL
				                	AND up.user_id = $user_id #change at runtime
				                GROUP BY up.project_id, up.workspace_id, up.area_id, up.element_id
				            ) AS tasksList
				            ON ar.area_id = tasksList.area_id
				            GROUP BY ar.workspace_id, ar.area_id
				            ORDER BY ar.sort_order
				        ) AS areasList
				        ON up.workspace_id = areasList.workspace_id
				        WHERE
				            up.project_id = $project_id #change at runtime
				            AND up.area_id IS NULL
				            AND up.workspace_id IS NOT NULL
				        	AND up.user_id = $user_id #change at runtime
				        GROUP BY up.project_id, up.workspace_id
				        ORDER BY pw.sort_order
				    ) AS workspacesList
				    ON pd.project_id = workspacesList.project_id
				    GROUP BY pd.project_id
				) AS projectsList";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function org_chart($user_id = null, $org_id = null, $people_from = null, $specific_items = null, $dotted_lines = null){

		$select_people = $select_people_root = "";
		if(isset($people_from) && !empty($people_from)){
			if($people_from == "community"){
				$select_people = "#Select People From: All Community
									SELECT DISTINCT
									    u.id
									FROM
									    users u
									LEFT JOIN user_details ud ON
									    u.id = ud.user_id
									WHERE
									    u.role_id = 2 AND
									    u.status = 1 AND
									    u.is_deleted = 0";
				$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'All Community' AS name, 'All Community.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
			}
			else if($people_from == "all_projects"){
				$select_people = "#Select People From: All My Projects
									SELECT DISTINCT
									    up1.user_id AS id
									FROM
									    user_permissions up1
									INNER JOIN user_permissions up2 ON
									    up1.project_id = up2.project_id AND
									    up2.workspace_id IS NULL
									LEFT JOIN user_details ud ON
									    up1.user_id = ud.user_id
									WHERE
									    up1.workspace_id IS NULL AND
									    up2.user_id = $user_id";
				$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'All My Projects' AS name, 'All My Projects.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
			}
			else if($people_from == "created_projects"){
				$select_people = "#Select People From: Projects I Created
									SELECT DISTINCT
									    up1.user_id AS id
									FROM
									    user_permissions up1
									INNER JOIN user_permissions up2 ON
									    up1.project_id = up2.project_id AND
									    up2.workspace_id IS NULL
									LEFT JOIN user_details ud ON
									    up1.user_id = ud.user_id
									WHERE
									    up1.workspace_id IS NULL AND
									    up2.user_id = $user_id AND
									    up2.role = 'Creator'";
				$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Projects I Created' AS name, 'Projects I Created.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
			}
			else if($people_from == "owner_projects"){
				$select_people = "#Select People From: Projects I Own
									SELECT DISTINCT
									    up1.user_id AS id
									FROM
									    user_permissions up1
									INNER JOIN user_permissions up2 ON
									    up1.project_id = up2.project_id AND
									    up2.workspace_id IS NULL
									LEFT JOIN user_details ud ON
									    up1.user_id = ud.user_id
									WHERE
									    up1.workspace_id IS NULL AND
									    up2.user_id = $user_id AND
									    up2.role IN ('Creator', 'Owner', 'Group Owner')";
				$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Projects I Own' AS name, 'Projects I Own.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
			}
			else if($people_from == "shared_projects"){
				$select_people = "#Select People From: Projects Shared with Me
									SELECT DISTINCT
									    up1.user_id AS id
									FROM
									    user_permissions up1
									INNER JOIN user_permissions up2 ON
									    up1.project_id = up2.project_id AND
									    up2.workspace_id IS NULL
									LEFT JOIN user_details ud ON
									    up1.user_id = ud.user_id
									WHERE
									    up1.workspace_id IS NULL AND
									    up2.user_id = $user_id AND
									    up2.role IN ('Sharer', 'Group Sharer')";
				$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Projects Shared With Me' AS name, 'Projects Shared With Me.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
			}
			else if($people_from == "organizations" || $people_from == "locations" || $people_from == "departments" || $people_from == "users" || $people_from == "skills" || $people_from == "subjects" || $people_from == "domains" || $people_from == "project"){
				if(isset($specific_items) && !empty($specific_items)){
					$items = implode(',', $specific_items);
					if($people_from == "organizations"){
						$select_people = "#Select People From: Specific Organizations
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    ud.organization_id IN($items)";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Organizations' AS name, 'Specific Organizations.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "locations"){
						$select_people = "#Select People From: Specific Locations
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    ud.location_id IN
												(
											        $items
											    )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Locations' AS name, 'Specific Locations.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "departments"){
						$select_people = "#Select People From: Specific Departments
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    ud.department_id IN
										    	(
										            $items
										        )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Departments' AS name, 'Specific Departments.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "users"){
						$select_people = "#Select People From: Specific People
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    u.id IN
												(
											        $items
											    )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific People' AS name, 'Specific People.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "skills"){
						$select_people = "#Select People From: Specific Skills
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_skills us ON
											    u.id = us.user_id
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    us.skill_id IN
												(
											        $items
											    )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Skills' AS name, 'Specific Skills.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "subjects"){
						$select_people = "#Select People From: Specific Subjects
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_subjects us ON
											    u.id = us.user_id
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    us.subject_id IN
												(
											        $items
											    )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Subjects' AS name, 'Specific Subjects.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "domains"){
						$select_people = "#Select People From: Specific Domains
											SELECT DISTINCT
											    u.id
											FROM
											    users u
											LEFT JOIN user_domains us ON
											    u.id = us.user_id
											LEFT JOIN user_details ud ON
											    u.id = ud.user_id
											WHERE
											    u.role_id = 2 AND
											    u.status = 1 AND
											    u.is_deleted = 0 AND
											    us.domain_id IN
												(
											        $items
											    )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Domains' AS name, 'Specific Domains.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
					else if($people_from == "project"){
						$select_people = "#Select People From: Specific Projects
											SELECT DISTINCT
											    up1.user_id AS id
											FROM
											    user_permissions up1
											INNER JOIN user_permissions up2 ON
											    up1.project_id = up2.project_id AND
											    up2.workspace_id IS NULL
											LEFT JOIN user_details ud ON
											    up1.user_id = ud.user_id
											WHERE
											    up1.workspace_id IS NULL AND
											    up2.user_id = $user_id AND
											    up1.project_id IN
										        (
										            $items
										        )";
						$select_people_root = "(SELECT '0' AS id, 'TR' AS code, 'Specific Projects' AS name, 'Specific Projects.png' AS imageURL, 'Show People From' AS jobTitle,'' AS organization, '0' AS notInYourOrganization, '' AS location, '' AS department, null AS parentID)";
					}
				}
			}
		}

		$dotted_query = "";
		if(isset($dotted_lines) && !empty($dotted_lines) && $dotted_lines == 'direct_dotted'){
			$dotted_query = "#------------------------------------
				            # 4 - ONLY INCLUDE THIS SECTION IF SELECTION IS 'DIRECT AND DOTTED LINES'
				            UNION ALL
				            #add dotted reports
				            (
				                SELECT DISTINCT
				                    CONCAT(ul2.id, '-', ul.id) AS id, #parent and user id to avoid duplicate ids
				                    'DL' AS code,
				                    CONCAT(ud.first_name, ' ', ud.last_name) AS name,
				                    CASE WHEN ud.profile_pic IS NULL OR ud.profile_pic = ''
										THEN CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
										ELSE CONCAT('".SITEURL.USER_PIC_PATH."', ud.profile_pic)
									END  AS imageURL,
				                    #ud.profile_pic AS imageURL,
				                    IFNULL(ud.job_title, 'None') AS jobTitle,
				                    IFNULL(o.name, 'None') AS organization,
				                    IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($org_id),'',1), '$org_id','0') AS notInYourOrganization, #IF(ISNULL(1),'',1) has 1 as the current user organization_id and both need to be replaced at runtime
				                    IFNULL(l.name, 'None') AS location,
				                    IFNULL(d.name,'None') As department,
				                    CONCAT(ul2.id) AS parentID
				                FROM
				                (
				                    #------------------------------------
				                    # 5 - INSERT FROM 'SELECT PEOPLE'
				                    $select_people
				                    #------------------------------------
				                    ORDER BY ud.first_name, ud.last_name
				                ) AS ul #user list
				                INNER JOIN user_dotted_lines udl ON
				                	ul.id = udl.user_id
				                LEFT JOIN user_details ud ON
				                	ul.id = ud.user_id
				                LEFT JOIN organizations o ON
				               	 ud.organization_id = o.id
				                LEFT JOIN locations l ON
				                	ud.location_id = l.id
				                LEFT JOIN departments d ON
				                	ud.department_id = d.id
				                INNER JOIN
				                (
				                    #------------------------------------
				                    # 6 - INSERT FROM 'SELECT PEOPLE'
				                    $select_people
				                    #------------------------------------
				                ) AS ul2 ON udl.dotted_user_id = ul2.id
				            )
				            #------------------------------------";
		}
		$query1 = "SET SESSION group_concat_max_len = 65536; #override 1024 default to allow for 10,000 six figure ids
		SELECT @select_people := GROUP_CONCAT(id) FROM
            (
                #------------------------------------
                # 1 - INSERT FROM 'SELECT PEOPLE'
            	$select_people
                #------------------------------------
            ) AS sp;
		";



		$query2 = "


            SELECT
            		JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', CONCAT(cd.id),
                        'code', IF (cd.parentID = 0, 'RT', cd.code),
                        'name', cd.name,
                        'imageUrl', cd.imageURL,
                        'jobTitle', cd.jobTitle,
                        'organization', cd.organization,
                        'notInYourOrganization', cd.notInYourOrganization,
                        'location', cd.location,
                        'department', cd.department,
                        'parentId', cd.parentID
                    )
                ) AS data
            FROM
            (
            #add tree root
            #------------------------------------
            # 2 - INSERT FROM 'SELECT PEOPLE ROOT'
            $select_people_root
            #------------------------------------
            UNION ALL
            #add direct reports
            (
                SELECT DISTINCT
                    CONCAT(ul.id) AS id,
                	CASE
                		WHEN ud.reports_to_id IS NULL THEN 'RT'
                		WHEN FIND_IN_SET(ud.reports_to_id,@select_people)>0 THEN 'RT'
                		ELSE 'IR'
                	END AS code, #direct report RT, indirect report IR
                	CONCAT(ud.first_name, ' ', ud.last_name) AS name,
                	CASE WHEN ud.profile_pic IS NULL OR ud.profile_pic = ''
						THEN CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						ELSE CONCAT('".SITEURL.USER_PIC_PATH."', ud.profile_pic)
					END  AS imageURL,
                    #ud.profile_pic AS imageURL,
                    IFNULL(ud.job_title, 'None') AS jobTitle,
                    IFNULL(o.name, 'None') AS organization,
                    IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL($org_id),'',$org_id), '1','0') AS notInYourOrganization, #IF(ISNULL(2),'',2) has 2 as the current user organization_id and both need to be replaced at runtime
                    IFNULL(l.name, 'None') AS location,
                    IFNULL(d.name,'None') As department,
                    CASE
                    	WHEN FIND_IN_SET(ul2.t1r,@select_people)>0 THEN CONCAT(ul2.t1r)
                		WHEN FIND_IN_SET(ul2.t2r,@select_people)>0 THEN CONCAT(ul2.t2r)
                		WHEN FIND_IN_SET(ul2.t3r,@select_people)>0 THEN CONCAT(ul2.t3r)
                		WHEN FIND_IN_SET(ul2.t4r,@select_people)>0 THEN CONCAT(ul2.t4r)
                		WHEN FIND_IN_SET(ul2.t5r,@select_people)>0 THEN CONCAT(ul2.t5r)
                		WHEN FIND_IN_SET(ul2.t6r,@select_people)>0 THEN CONCAT(ul2.t6r)
                		WHEN FIND_IN_SET(ul2.t7r,@select_people)>0 THEN CONCAT(ul2.t7r)
                		WHEN FIND_IN_SET(ul2.t8r,@select_people)>0 THEN CONCAT(ul2.t8r)
                		WHEN FIND_IN_SET(ul2.t9r,@select_people)>0 THEN CONCAT(ul2.t9r)
                		WHEN FIND_IN_SET(ul2.t10r,@select_people)>0 THEN CONCAT(ul2.t10r)
                		WHEN FIND_IN_SET(ul2.t11r,@select_people)>0 THEN CONCAT(ul2.t11r)
                		WHEN FIND_IN_SET(ul2.t12r,@select_people)>0 THEN CONCAT(ul2.t12r)
                		WHEN FIND_IN_SET(ul2.t13r,@select_people)>0 THEN CONCAT(ul2.t13r)
                		WHEN FIND_IN_SET(ul2.t14r,@select_people)>0 THEN CONCAT(ul2.t14r)
                		WHEN FIND_IN_SET(ul2.t15r,@select_people)>0 THEN CONCAT(ul2.t15r)
                		WHEN FIND_IN_SET(ul2.t16r,@select_people)>0 THEN CONCAT(ul2.t16r)
                        ELSE '0' #root
                    END AS parentID
                FROM
                (
                    #------------------------------------
                    # 3 - INSERT FROM 'SELECT PEOPLE'
                	$select_people
                    #------------------------------------
                    ORDER BY ud.first_name, ud.last_name
                ) AS ul
                LEFT JOIN user_details ud ON
                    	ul.id = ud.user_id
                LEFT JOIN organizations o ON
                	ud.organization_id = o.id
                LEFT JOIN locations l ON
                	ud.location_id = l.id
                LEFT JOIN departments d ON
                	ud.department_id = d.id
                LEFT JOIN
                (
                    #get up to 15 ancestors
                    SELECT
                    	t1.user_id,
                    	t1.reports_to_id AS t1r, t2.reports_to_id AS t2r, t3.reports_to_id AS t3r, t4.reports_to_id AS t4r, t5.reports_to_id AS t5r, t6.reports_to_id AS t6r, t7.reports_to_id AS t7r, t8.reports_to_id AS t8r,
                    	t9.reports_to_id AS t9r, t10.reports_to_id AS t10r, t11.reports_to_id AS t11r, t12.reports_to_id AS t12r, t13.reports_to_id AS t13r, t14.reports_to_id AS t14r, t15.reports_to_id AS t15r, t16.reports_to_id AS t16r
                    FROM
                    user_details AS t1
                    LEFT JOIN user_details AS t2 ON t2.user_id = t1.reports_to_id
                    LEFT JOIN user_details AS t3 ON t3.user_id = t2.reports_to_id
                    LEFT JOIN user_details AS t4 ON t4.user_id = t3.reports_to_id
                    LEFT JOIN user_details AS t5 ON t5.user_id = t4.reports_to_id
                    LEFT JOIN user_details AS t6 ON t6.user_id = t5.reports_to_id
                    LEFT JOIN user_details AS t7 ON t7.user_id = t6.reports_to_id
                    LEFT JOIN user_details AS t8 ON t8.user_id = t7.reports_to_id
                    LEFT JOIN user_details AS t9 ON t9.user_id = t8.reports_to_id
                    LEFT JOIN user_details AS t10 ON t10.user_id = t9.reports_to_id
                    LEFT JOIN user_details AS t11 ON t11.user_id = t10.reports_to_id
                    LEFT JOIN user_details AS t12 ON t12.user_id = t11.reports_to_id
                    LEFT JOIN user_details AS t13 ON t13.user_id = t12.reports_to_id
                    LEFT JOIN user_details AS t14 ON t14.user_id = t13.reports_to_id
                    LEFT JOIN user_details AS t15 ON t15.user_id = t14.reports_to_id
                    LEFT JOIN user_details AS t16 ON t16.user_id = t15.reports_to_id
                ) AS ul2 ON ul.id = ul2.user_id
            )

	        $dotted_query

        ) AS cd
        #chart data";

		// pr($query, 1);
		 ClassRegistry::init('Project')->query($query1);
		$result =  ClassRegistry::init('UserPermission')->query($query2);
		 // pr($result, 1);
		return $result;
	}


	function opportunity_project_detail($project_id = null){

		$query = "SELECT prj.*, sks.skills, sbj.subjects,dmn.domains
		FROM
		(
			SELECT  proj.prj_status,

			proj.id,proj.title, proj.start_date, proj.end_date, proj.sign_off_date, proj.sign_off,proj.objective, proj.description, proj.image_file,
			proj.created,
			proj.type,
			JSON_ARRAYAGG(JSON_OBJECT( 'role', proj.prj_role,  'full_name',proj.full_name,'job_title',proj.job_title,  'profile_pic',proj.profile_pic,'user_id',proj.user_id,'org_id',proj.organization_id)) AS users
		    from
			(select up.role as prj_role,

					(CASE
						WHEN (up.role = 'Creator' OR up.role = 'Owner' OR up.role = 'Group Owner') THEN 1
						ELSE 0
					END) AS user_role,
					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'

						ELSE 'not_spacified'
					END) AS prj_status,

					up.permit_edit,

					projects.id, projects.start_date, projects.end_date,projects.title, projects.sign_off_date, projects.sign_off,projects.objective, projects.description, projects.image_file,
					CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name, up.user_id as user_id,ud.organization_id,projects.aligned_id , ud.job_title , ad.title as type, ud.profile_pic, projects.created


				FROM user_permissions up

				INNER JOIN projects
					ON up.project_id = projects.id
    			LEFT JOIN user_details ud On up.user_id = ud.user_id
    			LEFT JOIN aligneds ad On projects.aligned_id = ad.id
				where up.workspace_id is null
				and up.project_id = $project_id
				order by ud.first_name
				) proj
		) as prj,
		(

				SELECT
						all_data.pid,
						JSON_ARRAYAGG(
							JSON_OBJECT(
								'id',
								all_data.id,
								'title',
								all_data.title,
								'users_skill',
								all_data.users_skill,
								'team_skill',
								all_data.team_skill,
								'team_count',
								all_data.team_count
							)
						) AS skills

					FROM
						(
						SELECT
							project_skills.project_id AS pid,
							skills.id AS id,
							skills.title AS title,
							GROUP_CONCAT(DISTINCT(user_skills.user_id)) AS users_skill,
							GROUP_CONCAT(DISTINCT(user_permissions.user_id)) AS team_skill,
							COUNT(user_permissions.user_id) AS team_count
						FROM
							project_skills
						INNER JOIN skills ON
							project_skills.skill_id = skills.id
						LEFT JOIN user_skills ON
							project_skills.skill_id = user_skills.skill_id
						LEFT JOIN user_permissions ON
							user_permissions.project_id = project_skills.project_id
							AND user_permissions.workspace_id IS NULL
							AND user_skills.user_id = user_permissions.user_id
						WHERE
							project_skills.project_id = $project_id
						GROUP BY
							project_skills.skill_id
						ORDER BY
							skills.title
					) AS all_data

		) AS sks,
		(

				SELECT

						all_sub.spid,
							JSON_ARRAYAGG(
								JSON_OBJECT(
									'id',
									all_sub.id,
									'title',
									all_sub.title,
									'users_subject',
									all_sub.users_subject,
									'team_subject',
									all_sub.team_subject,
									'team_count',
									all_sub.team_count
									)
								) AS subjects

					FROM (

						SELECT
							project_subjects.project_id as spid,
							subjects.id as id,
							subjects.title as title,
							GROUP_CONCAT( DISTINCT(user_subjects.user_id)) as users_subject,
							GROUP_CONCAT(DISTINCT(user_permissions.user_id)) AS team_subject,
							COUNT(user_permissions.user_id) AS team_count
						FROM
							project_subjects
						INNER JOIN subjects ON
							project_subjects.subject_id = subjects.id
						LEFT JOIN user_subjects ON
							project_subjects.subject_id = user_subjects.subject_id
						LEFT JOIN user_permissions ON
							user_permissions.project_id = project_subjects.project_id
							AND user_permissions.workspace_id IS NULL
							AND user_subjects.user_id = user_permissions.user_id
						WHERE
							project_subjects.project_id = $project_id
						GROUP BY
							project_subjects.subject_id
						ORDER BY
							subjects.title

					) all_sub

		) AS sbj,
		(
				  SELECT
						all_dom.dpid,
						JSON_ARRAYAGG(
							JSON_OBJECT(
								'id',
								all_dom.id,
								'title',
								all_dom.title,
								'users_domain',
								all_dom.users_domain,
								'team_domain',
								all_dom.team_domain,
								'team_count',
								all_dom.team_count
								)
							) AS domains
					FROM (
						SELECT
							project_domains.project_id as dpid,
							knowledge_domains.id as id,
							knowledge_domains.title as title,
							GROUP_CONCAT( DISTINCT(user_domains.user_id)) as users_domain,
							GROUP_CONCAT(DISTINCT(user_permissions.user_id)) AS team_domain,
							COUNT(user_permissions.user_id) AS team_count

						FROM
							project_domains
						INNER JOIN knowledge_domains ON
							project_domains.domain_id = knowledge_domains.id
						LEFT JOIN user_domains ON
							project_domains.domain_id = user_domains.domain_id
						LEFT JOIN user_permissions ON
							user_permissions.project_id = project_domains.project_id
							AND user_permissions.workspace_id IS NULL
							AND user_domains.user_id = user_permissions.user_id
						WHERE
							project_domains.project_id = $project_id
						GROUP BY
							project_domains.domain_id
						ORDER BY
							knowledge_domains.title

					) all_dom

		) as dmn
		";

		$result =  ClassRegistry::init('Element')->query($query);
			// pr($result);
		return (isset($result) && !empty($result)) ? $result : [];

	}


	function project_popup_detail($project_id = null){

		$query = "SELECT prj.*
		FROM
		(
			SELECT  proj.prj_status,

			proj.id,proj.title, proj.start_date, proj.end_date, proj.sign_off_date, proj.sign_off,proj.objective, proj.description, proj.image_file,
			proj.created,
			proj.type,
			JSON_ARRAYAGG(JSON_OBJECT( 'role', proj.prj_role,  'full_name',proj.full_name,'job_title',proj.job_title,  'profile_pic',proj.profile_pic,'user_id',proj.user_id,'org_id',proj.organization_id)) AS users
		    from
			(select up.role as prj_role,

					(CASE
						WHEN (up.role = 'Creator' OR up.role = 'Owner' OR up.role = 'Group Owner') THEN 1
						ELSE 0
					END) AS user_role,
					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'

						ELSE 'not_spacified'
					END) AS prj_status,

					up.permit_edit,

					projects.id, projects.start_date, projects.end_date,projects.title, projects.sign_off_date, projects.sign_off,projects.objective, projects.description, projects.image_file,
					CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name, up.user_id as user_id,ud.organization_id,projects.aligned_id , ud.job_title , ad.title as type, ud.profile_pic, projects.created


				FROM user_permissions up

				INNER JOIN projects
					ON up.project_id = projects.id
    			LEFT JOIN user_details ud On up.user_id = ud.user_id
    			LEFT JOIN aligneds ad On projects.aligned_id = ad.id
				where up.workspace_id is null
				and up.project_id = $project_id
				order by ud.first_name
				) proj
		) as prj
		";

		$result =  ClassRegistry::init('Element')->query($query);
			// pr($result);
		return (isset($result) && !empty($result)) ? $result : [];

	}





}