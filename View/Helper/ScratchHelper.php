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
App::uses ( 'Helper', 'View' );

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package app.View.Helper
 */
class ScratchHelper extends AppHelper {
	var $helpers = array (
			'Html',
			'Session',
			'Thumbnail',
			'Common',
			"Group"
	);
	public function _displayDate_new($date = null, $format = 'd M, Y g:i A') {
		// date must be pass in formate of 'Y-m-d h:i:s A'....
		$timezone = ClassRegistry::init ( 'Timezone' )->findByUserId ( $this->Session->read ( "Auth.User.id" ) );


		if((!isset($timezone['Timezone']['name']) || empty($timezone['Timezone']['name'])) || ($timezone['Timezone']['name'] ==  'Etc/Unknown')){
			$timezone['Timezone']['name'] = 'Europe/London';
		}

		$target_time_zone = new DateTimeZone ( $timezone ['Timezone'] ['name'] );
		$kolkata_date_time = new DateTime ( 'now', $target_time_zone );
		$time = $kolkata_date_time->format ( 'P' );
		$exp = explode ( ':', $time );
		$minutes = (substr ( $exp [0], 1 ) * 60) + $exp [1];
		$sign = substr ( $exp [0], 0, 1 );
		$addsignandminutes = $sign . $minutes;

		$rfc_1123_date = date ( 'Y-m-d h:i:s A', strtotime ( $date ) );
		$sertimestamp = strtotime ( $rfc_1123_date . " $addsignandminutes minute" );
		$date = date ( $format, $sertimestamp );

		date_default_timezone_set ( 'UTC' );

		if (empty ( $date ))
			return;

		return $date;
	}
	public function _displayDate($date = null, $format = 'd M, Y g:i A') {
		if (empty ( $date ))
			return;

		return date ( $format, strtotime ( $date ) );
	}

	function people_list($query_params = null, $limit = null){
		// pr($query_params,1);
		$user_id = $this->Session->read('Auth.User.id');

		$tg_filter = $sk_filter = $sb_filter = $dom_filter = '';
		// search text
		$where = [];
		$ser = '^';
		if(isset($query_params['search_text']) && !empty($query_params['search_text'])){
			$search= Sanitize::escape(like($query_params['search_text'], $ser ));
			$where[] = "(CONCAT(ud.first_name,' ',ud.last_name) LIKE '%$search%' ESCAPE '$ser')";
			// $where[] = "(ud.first_name LIKE '%$search%' ESCAPE '$ser' OR ud.last_name LIKE '%$search%' ESCAPE '$ser')";
		}

		if(isset($query_params['user']) && !empty($query_params['user'])){
			$val = implode(',', $query_params['user']);
			$where[] = "u.id IN($val)";
		}

		if(isset($query_params['tag']) && !empty($query_params['tag'])){
			$where[] = "tg.tag IN('" . implode("', '", $query_params['tag']) . "')";
		}

		if(isset($query_params['skill']) && !empty($query_params['skill'])){
			$val = implode(',', $query_params['skill']);
			$where[] = "us.skill_id IN($val)";
		}
		if(isset($query_params['sub']) && !empty($query_params['sub'])){
			$val = implode(',', $query_params['sub']);
			$where[] = "usb.subject_id IN($val)";
		}
		if(isset($query_params['domain']) && !empty($query_params['domain'])){
			$val = implode(',', $query_params['domain']);
			$where[] = "usd.domain_id IN($val)";
		}

		if(isset($query_params['org']) && !empty($query_params['org'])){
			$val = implode(',', $query_params['org']);
			$where[] = "ud.organization_id IN($val)";
		}
		if(isset($query_params['loc']) && !empty($query_params['loc'])){
			$val = implode(',', $query_params['loc']);
			$where[] = "ud.location_id IN($val)";
		}
		if(isset($query_params['dept']) && !empty($query_params['dept'])){
			$val = implode(',', $query_params['dept']);
			$where[] = "ud.department_id IN($val)";
		}
		$story_join = "";
		if(isset($query_params['story']) && !empty($query_params['story'])){
			$val = implode(',', $query_params['story']);
			$where[] = "stu.story_id IN($val)";
			$story_join = "LEFT JOIN story_users stu ON stu.user_id = u.id";
		}

		$where_cond = '';
		if(isset($where) && !empty($where)){
			// $where_cond = 'WHERE ';
			$where_cond = implode(' AND ', $where);
		}
		if(isset($where_cond) && !empty($where_cond)){
			$where_cond = 'AND '.$where_cond;
		}

		$order_by = "ORDER BY full_name ASC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$coloumn = $query_params['coloumn'];
			$order = $query_params['order'];
			$order_by = "ORDER BY $coloumn $order";
			if($query_params['coloumn'] == 'first_name'){
				$order_by = "ORDER BY first_name $order, last_name $order";
			}
			if($query_params['coloumn'] == 'last_name'){
				$order_by = "ORDER BY last_name $order, first_name $order";
			}
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;

		$limit_str = '';
		if(isset($limit) && !empty($limit)){
			$limit_str = "LIMIT $page, $limit";
		}
		// pr($query_params, 1);

		$query = "SELECT
				    udata.user_id, udata.first_name, udata.last_name,  udata.full_name, udata.organization_id, udata.job_title,  udata.profile_pic, udata.organization, udata.location, udata.department,
				    rt_user.reports_to_user,
				    if(count_tags.count_tag IS NULL , 0, count_tags.count_tag) AS count_tag,
				    if(udline.count_dline IS NULL , 0, udline.count_dline) AS count_dline,
				    if(count_skills.count_skill IS NULL , 0, count_skills.count_skill) AS count_skill,
				    if(count_subjects.count_subject IS NULL , 0, count_subjects.count_subject) AS count_subject,
				    if(count_domains.count_domain IS NULL , 0, count_domains.count_domain) AS count_domain,
				    if(count_stories.count_story IS NULL , 0, count_stories.count_story) AS count_story,
				    if(count_reports_to.count_report_to IS NULL , 0, count_reports_to.count_report_to) AS count_report_to,
				    if(count_dotted_lines.count_dotted_line IS NULL , 0, count_dotted_lines.count_dotted_line) AS count_dotted_line

				FROM
				(
				    SELECT
				        u.id as user_id, ud.first_name, ud.last_name, CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name, ud.organization_id, ud.job_title, ud.profile_pic,
				        org.name as organization, org.id AS orgid, loc.name as location, loc.id AS locid, dept.name as department, dept.id AS deptid
				    FROM users u
				    LEFT JOIN user_details ud ON ud.user_id = u.id
				    LEFT JOIN organizations org ON org.id = ud.organization_id
				    LEFT JOIN locations loc ON loc.id = ud.location_id
				    LEFT JOIN departments dept ON dept.id = ud.department_id
				    LEFT JOIN user_skills us ON us.user_id = u.id
                    LEFT JOIN user_subjects usb ON usb.user_id = u.id
                    LEFT JOIN user_domains usd ON usd.user_id = u.id
                    $story_join
                    LEFT JOIN tags tg ON tg.user_id = $user_id AND tg.tagged_user_id = u.id
				    WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
				    $where_cond
				    GROUP BY u.id
				) AS udata

				LEFT JOIN (
				    SELECT
				        ud.user_id as uid,
				        CONCAT_WS(' ',udr.first_name , udr.last_name) AS reports_to_user
				    FROM user_details ud
				    INNER JOIN user_details udr ON udr.user_id = ud.reports_to_id
				    GROUP BY ud.user_id
				) AS rt_user
				ON rt_user.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        u.id as uid,
				        count(udl.dotted_user_id) as count_dline
				    FROM user_dotted_lines udl
				    INNER JOIN users u ON u.id = udl.user_id
				    GROUP BY u.id
				) AS udline
				ON udline.uid = udata.user_id

				LEFT JOIN (
				    SELECT
		                u.id as uid,
		                count(tg.tagged_user_id) as count_tag
		            FROM tags tg
		            INNER JOIN users u ON u.id = tg.tagged_user_id
		            WHERE tg.user_id = $user_id AND tg.tagged_user_id = u.id
		            GROUP BY tg.tagged_user_id
				) AS count_tags
				ON count_tags.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        usk.user_id as uid,
				        count(DISTINCT(usk.id)) as count_skill
				    FROM user_skills usk
				    INNER JOIN users u ON u.id = usk.user_id
				    GROUP BY usk.user_id
				) AS count_skills
				ON count_skills.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        usu.user_id as uid,
				        count(DISTINCT(usu.id)) as count_subject
				    FROM user_subjects usu
				    INNER JOIN users u ON u.id = usu.user_id
				    GROUP BY usu.user_id
				) AS count_subjects
				ON count_subjects.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        udm.user_id as uid,
				        count(DISTINCT(udm.id)) as count_domain
				    FROM user_domains udm
				    GROUP BY  udm.user_id
				) AS count_domains
				ON count_domains.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        su.user_id as uid,
				        count(DISTINCT(su.id)) as count_story
				    FROM story_users su
				    GROUP BY su.user_id
				) AS count_stories
				ON count_stories.uid = udata.user_id

				LEFT JOIN (
				    SELECT
				        u.id as uid,
				        ud.reports_to_id as urt,
				        count(ud.reports_to_id) as count_report_to
				    FROM users u
				    INNER JOIN user_details ud ON u.id = ud.user_id
				    GROUP BY ud.reports_to_id
				) AS count_reports_to
				ON count_reports_to.urt = udata.user_id

				LEFT JOIN (
				    SELECT
				        u.id as uid,
				        udl.dotted_user_id as udlu,
				        count(udl.dotted_user_id) as count_dotted_line
				    FROM users u
				    INNER JOIN user_dotted_lines udl ON u.id = udl.user_id
				    GROUP BY udl.dotted_user_id
				) AS count_dotted_lines
				ON count_dotted_lines.udlu = udata.user_id

				$order_by
				$limit_str
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function filter_list(){
		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name) )) AS JSON FROM users AS u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0) as user,

					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'title', organizations.name )) AS JSON FROM organizations ) as organization,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'title', locations.name )) AS JSON FROM locations ) as location,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', departments.id, 'title', departments.name )) AS JSON FROM departments ) as department,

					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', t.tag, 'title', t.tag )) AS JSON FROM tags AS t WHERE t.user_id = $user_id ) as tag,

					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'title', skills.title )) AS JSON FROM skills ) as skill,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'title', subjects.title )) AS JSON FROM subjects ) as subject,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'title', knowledge_domains.title )) AS JSON FROM knowledge_domains ) as domain,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'title', stories.name )) AS JSON FROM stories ) as story

				";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function people_engagement($params = null){
		$user_id = $this->Session->read('Auth.User.id');

		// pr($params);
		$tag_join = $skill_join = $sub_join = $dom_join = $story_join = "";
		$user_where = $org_where = $loc_where = $dept_where = $tag_where = $skill_where = $sub_where = $domain_where = $story_where = $search_where = "";
		if(isset($params['user']) && !empty($params['user'])){
			$val = implode(',', $params['user']);
			$user_where = "AND u.id IN ($val)";
		}
		if(isset($params['org']) && !empty($params['org'])){
			$val = implode(',', $params['org']);
			$org_where = "AND ud.organization_id IN ($val)";
		}
		if(isset($params['loc']) && !empty($params['loc'])){
			$val = implode(',', $params['loc']);
			$loc_where = "AND ud.location_id IN ($val)";
		}
		if(isset($params['dept']) && !empty($params['dept'])){
			$val = implode(',', $params['dept']);
			$dept_where = "AND ud.department_id IN ($val)";
		}
		if(isset($params['tag']) && !empty($params['tag'])){
			$val = implode(',', $params['tag']);
			$tag_where = "AND t.tag IN('" . implode("', '", $params['tag']) . "')";
			// include only if filter has Select Tags selections
			$tag_join = "LEFT JOIN tags t ON
				            	u.id = t.tagged_user_id
				            	AND t.user_id = $user_id";
		}
		if(isset($params['skill']) && !empty($params['skill'])){
			$val = implode(',', $params['skill']);
			$skill_where = "AND us.skill_id IN ($val)";
			// include only if filter has Select Skills selections
			$skill_join = "LEFT JOIN user_skills us ON
				            	u.id = us.user_id";
		}
		if(isset($params['sub']) && !empty($params['sub'])){
			$val = implode(',', $params['sub']);
			$sub_where = "AND ub.subject_id IN ($val)";
			// include only if filter has Select Subjects selections
			$sub_join = "LEFT JOIN user_subjects ub ON
				            	u.id = ub.user_id";
		}
		if(isset($params['domain']) && !empty($params['domain'])){
			$val = implode(',', $params['domain']);
			$domain_where = "AND um.domain_id IN ($val)";
			// include only if filter has Select Domains selections
			$dom_join = "LEFT JOIN user_domains um ON
				            	u.id = um.user_id";
		}
		if(isset($params['story']) && !empty($params['story'])){
			$val = implode(',', $params['story']);
			$story_where = "AND stu.story_id IN ($val)";
			// include only if filter has Select Domains selections
			$story_join = "LEFT JOIN story_users stu ON
				            	u.id = stu.user_id";
		}
		if(isset($params['search_text']) && !empty($params['search_text'])){
			$val = $params['search_text'];
			// include only if filter has Search For People... text
			$search_where = "AND CONCAT(ud.first_name,' ',ud.last_name) LIKE '%$val%'";
		}

		$start_date = date('Y-m-d', strtotime($params['start_date']));
		$end_date = date('Y-m-d', strtotime($params['end_date']));

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'user_id', userList.user_id,
				            'group', userList.full_name,
				            'data', IFNULL(userList.labels, JSON_ARRAY())
				        )
				    ) AS groups
				FROM
				(
				    SELECT
				        ul.user_id,
				        ul.full_name,
				        JSON_ARRAY(
				            JSON_OBJECT(
				                'user_id', ul.user_id,
				                'label', 'Projects',
				                'data', IFNULL(ul.projects, JSON_ARRAY())
				            ),
				            JSON_OBJECT(
				                'user_id', ul.user_id,
				                'label', 'Tasks',
				                'data', IFNULL(ul.elements, JSON_ARRAY())
				            ),
				            JSON_OBJECT(
				                'user_id', ul.user_id,
				                'label', 'Absences',
				                'data', IFNULL(ul.absences, JSON_ARRAY())
				            ),
				            JSON_OBJECT(
				                'user_id', ul.user_id,
				                'label', 'Work Blocks',
				                'data', IFNULL(ul.blocks, JSON_ARRAY())
				            )
				        ) AS labels
				    FROM
				    (
				        SELECT
				            u.id AS user_id,
				            u.full_name,
				            p.projects,
				        	e.elements,
				        	a.absences,
				        	b.blocks
				        #user data
				        FROM
				        (
				            SELECT
				            	u.id,
				            	CONCAT(ud.first_name,' ',ud.last_name) AS full_name
				            FROM
				            	users u
				            LEFT JOIN user_details ud ON
				            	u.id = ud.user_id
			            	$tag_join
			            	$skill_join
			            	$sub_join
			            	$dom_join
			            	$story_join
				            WHERE
				                u.role_id = 2
				                AND u.status = 1
				                AND u.is_deleted = 0
				                AND u.is_activated = 1
				                $user_where
				                $org_where
				                $loc_where
				                $dept_where
				                $tag_where
				                $skill_where
				                $sub_where
				                $domain_where
				                $story_where
				                $search_where
				            GROUP BY u.id
				        ) AS u
				        #project data
				        LEFT JOIN
				        (
				            SELECT
				            	up.user_id,
				            	IF(p.id IS NULL, JSON_ARRAY(),
				                    JSON_ARRAYAGG(
				                        JSON_OBJECT(
				                            'timeRange', JSON_ARRAY(
				                                CASE
				                                    WHEN p.start_date < '$start_date' THEN '$start_date 00:00:00' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(p.start_date), '%Y-%m-%d 00:00:00')
				                                END,
				                            	CASE
				                                    WHEN p.end_date > '$end_date' THEN '$end_date 23:59:59' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(p.end_date), '%Y-%m-%d 23:59:59')
				                                END
				                            ),
				                            'start_date', DATE_FORMAT(DATE(p.start_date), '%Y-%m-%d 00:00:00'),
				                            'end_date', DATE_FORMAT(DATE(p.end_date), '%Y-%m-%d 23:59:59'),
				                            'val', 'P',
				                            'name', IF(up2.user_id IS NULL, 'Project', p.title)
				                        )
				                    )
				                ) AS projects
				            FROM
				            	user_permissions up
				            LEFT JOIN projects p ON
				        		up.project_id = p.id
				            	AND (
				            		(p.start_date BETWEEN '$start_date' AND '$end_date' OR p.end_date BETWEEN '$start_date' AND '$end_date') #change dates at runtime
				            		OR (p.start_date < '$start_date' AND p.end_date > '$end_date') #change dates at runtime
				                )
				            LEFT JOIN user_permissions up2 ON
				            	up2.workspace_id IS NULL
				                AND up2.user_id = $user_id #change at runtime
				                AND up.project_id = up2.project_id
				            WHERE
				        		up.workspace_id IS NULL
				            	AND p.start_date IS NOT NULL
				            	AND p.end_date IS NOT NULL
				            GROUP BY up.user_id
				        ) AS p ON
				        	u.id = p.user_id
				        #task data
				        LEFT JOIN
				        (
				            SELECT
				            	up.user_id,
				            	IF(e.id IS NULL, JSON_ARRAY(),
				                    JSON_ARRAYAGG(
				                        JSON_OBJECT(
				                            'timeRange', JSON_ARRAY(
				                            	CASE
				                                    WHEN e.start_date < '$start_date' THEN '$start_date 00:00:00' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(e.start_date), '%Y-%m-%d 00:00:00')
				                                END,
				                            	CASE
				                                    WHEN e.end_date > '$end_date' THEN '$end_date 23:59:59' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(e.end_date), '%Y-%m-%d 23:59:59')
				                                END
				                            ),
				                            'start_date', DATE_FORMAT(DATE(e.start_date), '%Y-%m-%d 00:00:00'),
				                            'end_date', DATE_FORMAT(DATE(e.end_date), '%Y-%m-%d 23:59:59'),
				                            'val', 'T',
				                            'name', IF(up2.user_id IS NULL, 'Task', e.title)
				                        )
				                    )
				                ) AS elements
				            FROM
				            	user_permissions up
				            LEFT JOIN elements e ON
				        		up.element_id = e.id
				            	AND (
				            		(e.start_date BETWEEN '$start_date' AND '$end_date' OR e.end_date BETWEEN '$start_date' AND '$end_date') #change dates at runtime
				            		OR (e.start_date < '$start_date' AND e.end_date > '$end_date') #change dates at runtime
				                )
				            LEFT JOIN user_permissions up2 ON
				            	up2.element_id IS NOT NULL
				                AND up2.user_id = $user_id #change at runtime
				                AND up.element_id = up2.element_id
				            WHERE
				        		up.element_id IS NOT NULL
				            	AND e.start_date IS NOT NULL
				            	AND e.end_date IS NOT NULL
				            GROUP BY up.user_id
				        ) AS e ON
				        	u.id = e.user_id
				        #absence data
				        LEFT JOIN
				        (
				            SELECT
				            	a.user_id,
				            	IF(a.id IS NULL, JSON_ARRAY(),
				                    JSON_ARRAYAGG(
				                        JSON_OBJECT(
				                            'timeRange', JSON_ARRAY(
				                                CASE
				                                    WHEN a.avail_start_date < '$start_date' THEN '$start_date 00:00:00' #change date at runtime
				                                    ELSE a.avail_start_date
				                                END,
				                            	CASE
				                                    WHEN a.avail_end_date > '$end_date' THEN '$end_date 23:59:59' #change date at runtime
				                                    ELSE a.avail_end_date
				                                END
				                            ),
				                            'start_date', a.avail_start_date,
				                            'end_date', a.avail_end_date,
				                            'val', 'A',
				                            'name', 'Absence'
				                        )
				                    )
				                ) AS absences
				            FROM
				            	availabilities a
				            WHERE
				            	(a.avail_start_date BETWEEN '$start_date' AND '$end_date' OR a.avail_end_date BETWEEN '$start_date' AND '$end_date') #change dates at runtime
				            	OR (a.avail_start_date < '$start_date' AND a.avail_end_date > '$end_date') #change dates at runtime
				            GROUP BY a.user_id
						) AS a ON
				        	u.id = a.user_id
				        #block data
				        LEFT JOIN
				        (
				            SELECT
				            	ub.user_id,
				            	IF(ub.id IS NULL, JSON_ARRAY(),
				                    JSON_ARRAYAGG(
				                        JSON_OBJECT(
				                            'timeRange', JSON_ARRAY(
				                            	CASE
				                                    WHEN ub.work_start_date < '$start_date' THEN '$start_date 00:00:00' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(ub.work_start_date), '%Y-%m-%d 00:00:00')
				                                END,
				                            	CASE
				                                    WHEN ub.work_end_date > '$end_date' THEN '$end_date 23:59:59' #change date at runtime
				                                    ELSE DATE_FORMAT(DATE(ub.work_end_date), '%Y-%m-%d 23:59:59')
				                                END
				                            ),
				                            'start_date', DATE_FORMAT(DATE(ub.work_start_date), '%Y-%m-%d 00:00:00'),
				                            'end_date', DATE_FORMAT(DATE(ub.work_end_date), '%Y-%m-%d 23:59:59'),
				                            'val', 'W',
				                            #'name', 'Work Block'
				                            'name', IF(ub.comments = '' OR ub.comments IS NULL,'Work Block',CONCAT('Comment: ',ub.comments))
				                        )
				                    )
				                ) AS blocks
				            FROM
				            	user_blocks ub
				            WHERE
				            	(ub.work_start_date BETWEEN '$start_date' AND '$end_date' OR ub.work_end_date BETWEEN '$start_date' AND '$end_date') #change dates at runtime
				            	OR (ub.work_start_date < '$start_date' AND ub.work_end_date > '$end_date') #change dates at runtime
				            GROUP BY ub.user_id
						) AS b ON
				        	u.id = b.user_id
				    ) AS ul
				) AS userList
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result[0][0]['groups'], 1);
		return $result;
	}

	function project_data($project_id = null){
		$pquery = "SELECT projects.id, projects.title, projects.start_date, projects.end_date, currencies.sign, members.total_members,
					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))  and projects.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(projects.start_date)) and projects.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(projects.end_date) < DATE(NOW())) and projects.sign_off!=1) THEN 'overdue'
						ELSE 'not_spacified'
					END) AS prj_status
				FROM projects
				LEFT JOIN currencies ON currencies.id = projects.currency_id
				LEFT JOIN(
					SELECT COUNT(*) AS total_members, up.project_id
					FROM user_permissions up
					WHERE up.project_id = $project_id AND up.workspace_id IS NULL
				) AS members
				ON members.project_id = projects.id
				WHERE projects.id = $project_id
			";
		$presult =  ClassRegistry::init('UserPermission')->query($pquery);
		return $presult[0];
	}

	function task_data($task_id = null){
		$pquery = "SELECT elements.id, elements.title, elements.start_date, elements.end_date,
					(CASE
						WHEN (elements.date_constraints=0) THEN 'NON'
						WHEN (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'PND'
						WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) THEN 'progress'
						WHEN (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) THEN 'OVD'
						WHEN (elements.sign_off=1) THEN 'completed'
						ELSE 'NON'
					END) AS task_status
				FROM elements
				WHERE elements.id = $task_id
			";
		$presult =  ClassRegistry::init('UserPermission')->query($pquery);
		return $presult[0];
	}

	function performance_cost($user_id = null, $project_id = null){
		$presult = $this->project_data($project_id);
		$start_date = date('Y-m-d', strtotime($presult['projects']['start_date']));
		$end_date = ($presult[0]['prj_status'] == 'overdue') ? date('Y-m-d') : date('Y-m-d', strtotime($presult['projects']['end_date']));

		// pr($presult, 1);

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'ct_date', ct_date,
				            'ct_estimated', IF(DATE(ct_date) > CURDATE() OR ISNULL(ct_estimated), NULL, ct_estimated),
				            'ct_spent', IF(DATE(ct_date) > CURDATE() OR ISNULL(ct_spent), NULL, ct_spent)
				        )
				    ) AS perform_data
				FROM
				(
					SELECT thedate AS ct_date, ROUND(SUM(es1.estimated),2) AS ct_estimated, ROUND(SUM(es1.spent),2) AS ct_spent
				    FROM
				    (
				        SELECT startdate + INTERVAL num DAY AS thedate
				        FROM
				        (
				            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
				            FROM
				            	(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
				            CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
				        ) digits
				        CROSS JOIN (SELECT '$start_date' AS startdate, '$end_date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
				        WHERE startdate + INTERVAL num DAY <= enddate
				    ) ds
				    LEFT JOIN
				    (
				    	SELECT
				            ch.element_id,
				            ch.estimated,
				            ch.spent,
				        	ch.created
				        FROM
				        (
				            SELECT
				                up.element_id
				            FROM
				                user_permissions up
				            WHERE
				                up.user_id = $user_id # 2 - CHANGE AT RUNTIME
				                AND up.project_id = $project_id # 3 - CHANGE AT RUNTIME
				                AND up.element_id IS NOT NULL
				        ) AS el # elements
				        INNER JOIN
				        (
				            (
				                SELECT
				                    ec.element_id,
				                    ec.estimated_cost AS estimated,
				                    ec.spend_cost AS spent,
				                    ec.modified AS created
				                FROM
				                    element_costs ec
				            ) #AS cc # current costs
				            UNION ALL
				            (
				                SELECT
				                    ech.element_id,
				                    ech.estimated_cost AS estimated,
				                    ech.spend_cost AS spent,
				                    ech.modified AS created
				                FROM
				                    element_cost_history ech
				            )
				            ORDER BY
				                element_id,
				                created
				        ) AS ch ON # cost history
				            el.element_id = ch.element_id
				    ) AS es1 ON
				        DATE(es1.created) <= thedate
				        #AND es1.project_id=2 # 4 - CHANGE AT RUNTIME
				    LEFT JOIN
				    (
				        SELECT
				            ch.element_id,
				            ch.estimated,
				            ch.spent,
				        	ch.created
				        FROM
				        (
				            SELECT
				                up.element_id
				            FROM
				                user_permissions up
				            WHERE
				                up.user_id = $user_id # 5 - CHANGE AT RUNTIME
				                AND up.project_id = $project_id # 6 - CHANGE AT RUNTIME
				                AND up.element_id IS NOT NULL
				        ) AS el # elements
				        INNER JOIN
				        (
				            (
				                SELECT
				                    ec.element_id,
				                    ec.estimated_cost AS estimated,
				                    ec.spend_cost AS spent,
				                    ec.modified AS created
				                FROM
				                    element_costs ec
				            ) #AS cc # current costs
				            UNION ALL
				            (
				                SELECT
				                    ech.element_id,
				                    ech.estimated_cost AS estimated,
				                    ech.spend_cost AS spent,
				                    ech.modified AS created
				                FROM
				                    element_cost_history ech
				            )
				            ORDER BY
				                element_id,
				                created
				        ) AS ch ON # cost history
				            el.element_id = ch.element_id
				    ) AS es2 ON
				    	DATE(es2.created) <= thedate
				    	AND es1.element_id = es2.element_id
				    	AND (es1.created < es2.created)
				        AND (ISNULL(es1.estimated + es2.spent) OR ISNULL(es2.estimated + es1.spent))
				WHERE es2.created IS NULL
				    GROUP BY thedate
				    ORDER BY thedate
				) AS ct_daily_values
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function performance_efforts($user_id = null, $project_id = null){
		$presult = $this->project_data($project_id);
		$start_date = date('Y-m-d', strtotime($presult['projects']['start_date']));
		$end_date = ($presult[0]['prj_status'] == 'overdue') ? date('Y-m-d') : date('Y-m-d', strtotime($presult['projects']['end_date']));
		// pr($presult, 1);

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'ef_date', ef_date,
				            'ef_completed', IF(DATE(ef_date) > CURDATE() OR ISNULL(ef_completed), NULL, ef_completed),
				            'ef_remaining', IF(DATE(ef_date) > CURDATE() OR ISNULL(ef_remaining), NULL, ef_remaining)
				        )
				    ) AS perform_data
				FROM
				(
					SELECT thedate AS ef_date, ROUND(SUM(ee1.completed_hours)) AS ef_completed, ROUND(SUM(ee1.remaining_hours)) AS ef_remaining
				    FROM
				    (
				        SELECT startdate + INTERVAL num DAY AS thedate
				        FROM
				        (
				            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
				            FROM
				            	(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
				            CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
				        ) digits
				        CROSS JOIN (SELECT '$start_date' AS startdate, '$end_date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
				        WHERE startdate + INTERVAL num DAY <= enddate
				    ) ds
				    LEFT JOIN element_efforts ee1 ON
				        DATE(ee1.created) <= thedate
				        AND ee1.project_id=$project_id # 2 - CHANGE AT RUNTIME
				    LEFT JOIN element_efforts ee2 ON
				    	DATE(ee2.created) <= thedate
				    	AND ee1.element_id = ee2.element_id
				    	AND ee1.user_id = ee2.user_id
				    	AND (ee1.created < ee2.created)
					WHERE ee2.created IS NULL
				    GROUP BY thedate
				    ORDER BY thedate
				) AS ef_daily_averages
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function performance_confidence($user_id = null, $project_id = null){
		$presult = $this->project_data($project_id);
		$start_date = date('Y-m-d', strtotime($presult['projects']['start_date']));
		$end_date = ($presult[0]['prj_status'] == 'overdue') ? date('Y-m-d') : date('Y-m-d', strtotime($presult['projects']['end_date']));
		// pr($presult, 1);

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'cl_date', cl_date,
				            'cl_count', cl_count,
				            'cl_value', IF(DATE(cl_date) > CURDATE() OR ISNULL(cl_value), NULL, cl_value)
				        )
				    ) AS perform_data
				FROM
				(
					SELECT thedate AS cl_date, COUNT(el1.level) AS cl_count, ROUND(AVG(el1.level),0) AS cl_value
				    FROM
				    (
				        SELECT startdate + INTERVAL num DAY AS thedate
				        FROM
				        (
				            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
				            FROM
				            	(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
				            CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
				        ) digits
				        CROSS JOIN (SELECT '$start_date' AS startdate, '$end_date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
				        WHERE startdate + INTERVAL num DAY <= enddate
				    ) ds
				    LEFT JOIN element_levels el1 ON
				        DATE(el1.created) <= thedate
				        AND el1.project_id = $project_id # 2 - CHANGE AT RUNTIME
				    LEFT JOIN element_levels el2 ON
				    	DATE(el2.created) <= thedate
				    	AND el1.element_id = el2.element_id
				    	AND (el1.created < el2.created)
					WHERE el2.created IS NULL
				    GROUP BY thedate
				    ORDER BY thedate
				) AS cl_daily_averages
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function performance_status($user_id = null, $project_id = null){
		$presult = $this->project_data($project_id);
		$start_date = date('Y-m-d', strtotime($presult['projects']['start_date']));
		$end_date = ($presult[0]['prj_status'] == 'overdue') ? date('Y-m-d') : date('Y-m-d', strtotime($presult['projects']['end_date']));
		// pr($presult, 1);

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'ts_date', ts_date,
				            'ts_notset', IF(DATE(ts_date) > CURDATE() OR ISNULL(ts_notset), 0, ts_notset),
				            'ts_notstarted', IF(DATE(ts_date) > CURDATE() OR ISNULL(ts_notstarted), 0, ts_notstarted),
				            'ts_inprogress', IF(DATE(ts_date) > CURDATE() OR ISNULL(ts_inprogress), 0, ts_inprogress),
				            'ts_overdue', IF(DATE(ts_date) > CURDATE() OR ISNULL(ts_overdue), 0, ts_overdue),
				            'ts_complete', IF(DATE(ts_date) > CURDATE() OR ISNULL(ts_complete), 0, ts_complete)
				        )
				    ) AS perform_data
				FROM
				(
					SELECT
				        thedate AS ts_date,
				        SUM(
				                IF(
				                    ISNULL(el2.start_date)
				                ,1,0)
				        ) AS ts_notset,
				        SUM(
				                IF(
				                    el2.start_date IS NOT NULL AND DATE(el2.start_date) > DATE(thedate)
				                ,1,0)
				        ) AS ts_notstarted,
				        SUM(
				                IF(
				                    (el2.sign_off = 0 OR (el2.sign_off = 1 AND DATE(el2.sign_off_date) > DATE(thedate)))
				                    AND DATE(el2.start_date) <= DATE(thedate)
				                    AND DATE(el2.end_date) >= DATE(thedate)
				                ,1,0)
				        ) AS ts_inprogress,
				        SUM(
				                IF(
				                    (el2.sign_off = 0 OR (el2.sign_off = 1 AND DATE(el2.sign_off_date) > DATE(thedate)))
				                    AND DATE(el2.end_date) < DATE(thedate)
				                ,1,0)
				        ) AS ts_overdue,
				        SUM(
				                IF(
				                    el2.sign_off = 1 AND DATE(el2.sign_off_date) <= DATE(thedate)
				                ,1,0)
				        ) AS ts_complete

				    FROM
				    (
				        SELECT startdate + INTERVAL num DAY AS thedate
				        FROM
				        (
				            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
				            FROM
				            	(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
				            CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
				        ) digits
				        CROSS JOIN (SELECT '$start_date' AS startdate, '$end_date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
				        WHERE startdate + INTERVAL num DAY <= enddate
				    ) ds
				    LEFT JOIN
				    (
				        SELECT
				            up.element_id AS id,
				            el.created,
				        	el.start_date,
				        	el.end_date,
				            el.sign_off,
				            el.sign_off_date
				        FROM
				            user_permissions up
				        LEFT JOIN elements el ON
				            up.element_id = el.id
				        WHERE
				            up.user_id = $user_id # 2 - CHANGE AT RUNTIME
				            AND up.project_id = $project_id # 3 - CHANGE AT RUNTIME
				            AND up.element_id IS NOT NULL
				    ) el2 ON
				        DATE(el2.created) <= thedate
				    GROUP BY thedate
				    ORDER BY thedate
				) AS ts_daily_counts
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function performance_activity($user_id = null, $project_id = null){
		$presult = $this->project_data($project_id);
		$start_date = date('Y-m-d', strtotime($presult['projects']['start_date']));
		$end_date = ($presult[0]['prj_status'] == 'overdue') ? date('Y-m-d') : date('Y-m-d', strtotime($presult['projects']['end_date']));
		// pr($presult, 1);

		$query = "SELECT
					JSON_ARRAYAGG(
				        JSON_OBJECT(
				            'ua_date', ua_date,
				            'ua_value', IF(DATE(ua_date) > CURDATE(), NULL, ua_value)
				        )
				    ) AS perform_data
				FROM
				(
					SELECT thedate AS ua_date, COUNT(pal.user_id) AS ua_value
				    FROM
				    (
				        SELECT startdate + INTERVAL num DAY AS thedate
				        FROM
				        (
				            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
				            FROM
				            	(SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
				            CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
							CROSS JOIN
				                 (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
				        ) digits
				        CROSS JOIN (SELECT '$start_date' AS startdate, '$end_date' AS enddate) CONST # 1 - CHANGE AT RUNTIME
				        WHERE startdate + INTERVAL num DAY <= enddate
				    ) ds
				    LEFT JOIN
				    	(
				            SELECT DISTINCT
				            	pau.project_id,
				            	pau.user_id AS user_id,
				            	DATE(pau.created) AS created
				           	FROM
				            (
				                (
				                SELECT
				                	pa.project_id,
				                	pa.updated_user_id AS user_id,
				            		DATE(pa.updated) AS created
				               	FROM
				            		project_activities pa
				            	WHERE
				            		pa.project_id = $project_id # 2 - CHANGE AT RUNTIME
				                	AND DATE(pa.updated) BETWEEN '$start_date' AND '$end_date' # 3 - CHANGE AT RUNTIME
				            	)
				                UNION ALL
				                (
				                SELECT
				                	wa.project_id,
				                	wa.updated_user_id AS user_id,
				            		DATE(wa.updated) AS created
				               	FROM
				            		workspace_activities wa
				            	WHERE
				            		wa.project_id = $project_id # 4 - CHANGE AT RUNTIME
				                	AND DATE(wa.updated) BETWEEN '$start_date' AND '$end_date' # 5 - CHANGE AT RUNTIME
				            	)
				               	UNION ALL
				               	(
				                SELECT
				                	ea.project_id,
				                	ea.updated_user_id AS user_id,
				            		DATE(ea.updated) AS created
				               	FROM
				            		activities ea
				            	WHERE
				            		ea.project_id = $project_id # 6 - CHANGE AT RUNTIME
				                	AND DATE(ea.updated) BETWEEN '$start_date' AND '$end_date' # 7 - CHANGE AT RUNTIME
				            	)
				            ) pau
				            ORDER BY
				            	pau.created
				        ) pal ON
				        DATE(pal.created) = thedate
				        AND pal.project_id = $project_id # 8 - CHANGE AT RUNTIME
				    GROUP BY thedate
				    ORDER BY thedate
				) AS cl_daily_averages
			";
		// pr($query, 1);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function workspace_costing($workspace_id = null){
		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
					(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.workspace_id = $workspace_id and user_permissions.user_id = $user_id AND user_permissions.workspace_id = $workspace_id )) as budget_cost,

					(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null and user_permissions.workspace_id = $workspace_id and user_permissions.user_id = $user_id AND user_permissions.workspace_id = $workspace_id )) as actual_cost

			";
		$result =  ClassRegistry::init('Element')->query($query);
		return $result[0][0];
	}

	function cost_type_list($id = null, $exclude = false ){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}


		$query = " SELECT ctypes.id, ctypes.type, IF( (pcount.el_count IS NULL), 0, pcount.el_count ) AS el_count
				FROM
				    (
				    SELECT id, type
				    FROM cost_types
				) AS ctypes
				LEFT JOIN(
				    SELECT cost_type_id, COUNT(id) AS el_count
				    FROM element_costs
				    GROUP BY cost_type_id
				) AS pcount
				ON
				    pcount.cost_type_id = ctypes.id
			    $where
				ORDER BY ctypes.type";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function program_type_list($id = null, $exclude = false ){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}


		$query = "SELECT
					types.id, types.type,
					IF((pcount.prog_count IS NULL), 0, pcount.prog_count) AS prog_count
				FROM(
					SELECT id, type
					FROM program_types
				) AS types
				LEFT JOIN(
					SELECT type_id, COUNT(id) AS prog_count
					FROM programs
					GROUP BY type_id
				) AS pcount
					ON pcount.type_id = types.id
				$where
				ORDER BY types.type
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function project_type_list($id = null, $exclude = false, $params = null){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}

		$order_by = "ORDER BY sort_order ASC";
		if( (isset($params['order']) && !empty($params['order'])) && (isset($params['field']) && !empty($params['field'])) ){
			$field = $params['field'];
			$order = $params['order'];
			$order_by = "ORDER BY $field $order";
		}

		$query = "SELECT
					align.id, align.title, align.sort_order,
					prj_count
				FROM(
					SELECT id, title, sort_order
					FROM aligneds
				) AS align
				LEFT JOIN(
					SELECT aligned_id, COUNT(id) AS prj_count
					FROM projects
					GROUP BY aligned_id
				) AS pcount
					ON pcount.aligned_id = align.id
				$where
				$order_by
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function currency_list($id = null, $exclude = false, $params = null){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}

		$order_by = "ORDER BY title ASC";
		if( (isset($params['order']) && !empty($params['order'])) && (isset($params['field']) && !empty($params['field'])) ){
			$field = $params['field'];
			$order = $params['order'];
			$order_by = "ORDER BY $field $order";
		}

		if(isset($params['where']) && !empty($params['where'])) {
			if(isset($where) && !empty($where)) {
				$where .= " AND " . $params['where'];
			}
			else{
				$where .= "WHERE " . $params['where'];
			}
		}

		$query = "SELECT
					cur.id, cur.title, cur.sign, cur.status,
					prj_count
				FROM(
					SELECT id, name AS title, sign, status
					FROM currencies
				) AS cur
				LEFT JOIN(
					SELECT currency_id, COUNT(id) AS prj_count
					FROM projects
					GROUP BY currency_id
				) AS pcount
					ON pcount.currency_id = cur.id
				$where
				$order_by
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function active_currencies(){
		$query = "SELECT
					if(COUNT(id) IS NULL, 0, COUNT(id)) AS active_count
				FROM currencies
				WHERE status = 1
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result[0][0]['active_count'];
	}

	function task_type_list(){

		$query = "SELECT `id`, `title`, `status`
				FROM project_types
				ORDER BY title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function org_type_list($id = null, $exclude = false){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}

		$query = "SELECT
					ot.id, ot.title, counter
				FROM(
					SELECT id, type AS title
					FROM organization_types
				) AS ot
				LEFT JOIN(
					SELECT type_id, COUNT(id) AS counter
					FROM organizations
					GROUP BY type_id
				) AS counts
					ON counts.type_id = ot.id
				$where
				ORDER BY title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function loc_type_list($id = null, $exclude = false){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}

		$query = "SELECT
					ot.id, ot.title, counter
				FROM(
					SELECT id, type AS title
					FROM location_types
				) AS ot
				LEFT JOIN(
					SELECT type_id, COUNT(id) AS counter
					FROM locations
					GROUP BY type_id
				) AS counts
					ON counts.type_id = ot.id
				$where
				ORDER BY title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function story_type_list($id = null, $exclude = false){

		$where = '';
		if(isset($id) && !empty($id)) {
			if($exclude){
				$where = "WHERE id <> $id";
			}
			else{
				$where = "WHERE id = $id";
			}
		}

		$query = "SELECT
					ot.id, ot.title, counter
				FROM(
					SELECT id, type AS title
					FROM story_types
				) AS ot
				LEFT JOIN(
					SELECT type_id, COUNT(id) AS counter
					FROM stories
					GROUP BY type_id
				) AS counts
					ON counts.type_id = ot.id
				$where
				ORDER BY title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function risk_type_list(){

		$query = "SELECT `id`, `title`, `status`
				FROM rm_risk_types
				ORDER BY title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function manage_project($project_id = null, $params = null){
		$user_id = $this->Session->read('Auth.User.id');

		$where = "";
		if(isset($project_id) && !empty($project_id)) {

			$query = "SELECT
					    projects.id, projects.title, projects.objective, projects.description, projects.color_code, projects.budget, projects.start_date, projects.end_date,
					    upr.is_rewards, upr.pass_protected,
					    (CASE
					        WHEN (projects.sign_off=1) THEN 'CMP'
					        WHEN ((DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date)) AND projects.sign_off!=1  ) THEN 'PRG'
					        WHEN ((DATE(NOW()) < DATE(projects.start_date)) AND projects.sign_off!=1 ) THEN 'NOS'
					        WHEN ((DATE(projects.end_date) < DATE(NOW())) AND projects.sign_off!=1 ) THEN 'OVD'
					        ELSE 'NON'
					    END) AS prj_status,
					    currencies.id AS currency_id,
					    aligneds.id AS project_type_id,
					    prj_prog.progs,
					    prag.rag_status,
					    prag.p_rag,
					    prag.rag_type,
					    prag.amber_value,
					    prag.red_value,
					    all_types.all_type,
					    sel_types.sel_type,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', currencies.id, 'title', currencies.sign)) AS all_currency FROM currencies WHERE status = 1) AS all_currencies,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', aligneds.id, 'title', aligneds.title)) AS all_ptype FROM aligneds) AS all_ptypes,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', programs.id, 'title', programs.program_name)) AS all_prog FROM programs WHERE user_id = $user_id) AS all_progs

					FROM projects
					LEFT JOIN user_projects upr
					    ON upr.project_id = projects.id
					LEFT JOIN currencies
					    ON currencies.id = projects.currency_id
					LEFT JOIN aligneds
					    ON aligneds.id = projects.aligned_id

					LEFT JOIN(
					    SELECT
					        JSON_ARRAYAGG(JSON_OBJECT( 'id', programs.id, 'title', programs.program_name)) AS progs,
					        pp.project_id
					    FROM programs
					    LEFT JOIN project_programs pp
					        ON programs.id = pp.program_id
					    GROUP BY pp.project_id
					) AS prj_prog
					    ON prj_prog.project_id = projects.id

					LEFT JOIN(
					    SELECT
					        JSON_ARRAYAGG(JSON_OBJECT( 'id', pet.id, 'title', pet.title)) AS all_type,
					        pet.project_id
					    FROM project_element_types pet
					    GROUP BY pet.project_id
					) AS all_types
					    ON all_types.project_id = projects.id

					LEFT JOIN(
					    SELECT
					        #JSON_ARRAYAGG(JSON_OBJECT('id', pet.id)) AS sel_type,
					        GROUP_CONCAT(pet.id) AS sel_type,
					        pet.project_id
					    FROM project_element_types pet
					    WHERE pet.type_status = 1
					    GROUP BY pet.project_id
					) AS sel_types
					    ON sel_types.project_id = projects.id

					LEFT JOIN (
					    # PROJECT RAG STATUS
					    SELECT
					        p.rag_status,
					        up.project_id,
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
					        END) AS p_rag,
					        IF(prg.amber_value IS NULL, 0, prg.amber_value) AS amber_value,
					        IF(prg.red_value IS NULL, 0, prg.red_value) AS red_value

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
					ON prag.project_id = projects.id

					WHERE projects.id = $project_id
				";
		}
		else{
			$query = "SELECT
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', currencies.id, 'title', currencies.sign)) AS all_currency FROM currencies WHERE status = 1) AS all_currencies,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', aligneds.id, 'title', aligneds.title)) AS all_ptype FROM aligneds) AS all_ptypes,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', programs.id, 'title', programs.program_name)) AS all_prog FROM programs WHERE user_id = $user_id) AS all_progs,
					    (SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', pet.id, 'title', pet.title)) AS all_type FROM project_element_type_temps pet WHERE user_id = $user_id) AS all_types";
		}
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result, 1);
		return $result;
	}

	function wsp_teams($params = null){
		$user_id = $this->Session->read('Auth.User.id');

		$workspace_id = $params['workspace_id'];
		$project_id = $params['project_id'];

		$order = '';
		if(!isset($params['order']) || empty($params['order'])){
			$order = 'ORDER BY first_name, last_name ASC';
		}
		if( (isset($params['coloumn']) && !empty($params['coloumn'])) && (isset($params['order']) && !empty($params['order'])) ){
			$column = $params['coloumn'];
			$dir = $params['order'];
			$order = "ORDER BY $column $dir";
			if($params['coloumn'] == 'first_name'){
				$order = "ORDER BY first_name $dir, last_name $dir";
			}
			if($params['coloumn'] == 'last_name'){
				$order = "ORDER BY last_name $dir, first_name $dir";
			}
		}

		$limit_str = '';
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 0;
		if(isset($params['limit']) && !empty($params['limit'])){
			$limit = $params['limit'];
			$limit_str = "LIMIT $page, $limit";
		}
		// e($order);

		$query = "SELECT
				    ed.project_id,
				    ed.workspace_id,
				    ed.user_id,
				    ed.first_name,
				    ed.last_name,
				    ed.full_name,
				    ed.job_title,
				    ed.profile_pic,
				    ed.organization_id,
				    ed.role,
				    ed.project_skills,
				    ed.project_subjects,
				    ed.project_domains,
				    ed.sign,

				    unavail.unvailable_days,

				    cd.escost,
				    cd.spcost,
				    (CASE
						WHEN ISNULL(escost) AND ISNULL(spcost) THEN 'Not Set'
						WHEN ISNULL(spcost) THEN 'Budget Set'
						WHEN ISNULL(escost) THEN 'Costs Incurred'
						WHEN spcost > escost THEN 'Over Budget'
						WHEN spcost <= escost THEN 'On Budget'
						ELSE 'None' #never
					 END) AS cost_status,

				    efrt.total_hours,
				    efrt.blue_completed_hours,
				    efrt.green_remaining_hours,
				    efrt.amber_remaining_hours,
				    efrt.red_remaining_hours,
				    efrt.none_remaining_hours,
				    efrt.change_hours,
					efrt.remaining_hours,
					efrt.completed_hours,

				    IFNULL(rh.high_pending_risks, 0) AS high_risks,
					IFNULL(rh.severe_pending_risks, 0) AS severe_risks,
					IFNULL(rh.total_risks, 0) AS total_risks,

					IFNULL(usk.user_skills,0) AS user_skills,
					IFNULL(usb.user_subjects,0) AS user_subjects,
					IFNULL(usd.user_domains,0) AS user_domains,

				    ac.message,
				    ac.updated,

				    pg.grp_id,
					pg.grp_title,
					pg.grp_owner,
					pg.grp_share_type,
					pg.grp_user_name

				FROM #get user/wsp/project data
				(
				    SELECT DISTINCT
				        up.project_id,
				        up.workspace_id,
				        wsp.start_date,
				        wsp.end_date,
				        up.user_id,
				        up.group_id,
				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name,
				        ud.first_name,
				        ud.last_name,
				        ud.job_title,
				        ud.profile_pic,
				        ud.organization_id,
				        up.role,
				        COUNT(DISTINCT ps.skill_id) AS project_skills,
				        COUNT(DISTINCT pb.subject_id) AS project_subjects,
				        COUNT(DISTINCT pd.domain_id) AS project_domains,
				        currencies.sign
				    FROM
				        user_permissions up
				    LEFT JOIN user_details ud ON
				        up.user_id = ud.user_id
				    LEFT JOIN workspaces wsp ON
				        wsp.id = up.workspace_id
				    LEFT JOIN project_skills ps ON
				        up.project_id = ps.project_id
				    LEFT JOIN project_subjects pb ON
				        up.project_id = pb.project_id
				    LEFT JOIN project_domains pd ON
				        up.project_id = pd.project_id
			        INNER JOIN projects ON
			        	projects.id = up.project_id
			        LEFT JOIN currencies ON
			        	currencies.id = projects.currency_id
				    WHERE
				        up.area_id IS NULL
				        AND up.workspace_id = $workspace_id #change at runtime
				    GROUP BY up.workspace_id, up.user_id
				) AS ed

				LEFT JOIN #get unavailability
				(
				    SELECT count(*) unvailable_days, user_id, STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d') avail_start_date,
				            STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') avail_end_date
				            FROM `availabilities` WHERE
				             DATE(NOW()) BETWEEN DATE(STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d')) AND DATE(STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d'))
				             GROUP by user_id
				) AS unavail ON
				    ed.user_id = unavail.user_id

				LEFT JOIN #get effort
				(
				    SELECT
				        ws.workspace_id as workspace_id,
				        ef.total_hours,
				        ef.blue_completed_hours,
				        ef.green_remaining_hours,
				        ef.amber_remaining_hours,
				        ef.red_remaining_hours,
				        ef.none_remaining_hours,
				        ef.change_hours,
				        ef.user_id,
						ef.remaining_hours,
						ef.completed_hours
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
				            ue.user_id,
				            SUM(ue.change_hours) change_hours,
				            SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
				            SUM(ue.completed_hours) AS blue_completed_hours,
				            SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
				            SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
				            SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
				            SUM(IF(ue.remaining_hours_color = 'None', ue.remaining_hours, 0)) AS none_remaining_hours,
				            SUM((ue.remaining_hours)) AS remaining_hours,
							SUM(ue.completed_hours) AS completed_hours
				        FROM
				        (
				            SELECT
				                ee.workspace_id,
				                ee.completed_hours,
				                ee.remaining_hours,
				                ee.user_id,
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
				        WHERE ue.workspace_id = $workspace_id
				        GROUP BY  ue.user_id
				    ) AS ef ON
				        ws.workspace_id = ef.workspace_id
				) AS efrt ON
				    ed.workspace_id = efrt.workspace_id and
				    ed.user_id = efrt.user_id

			    LEFT JOIN #get high, severe and total risks
				(
				    SELECT
				        rk.user_id,
				        up.workspace_id,
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
				            WHERE re.element_id IN ( SELECT up.element_id FROM user_permissions up WHERE up.workspace_id = $workspace_id AND up.element_id IS NOT NULL AND up.role = 'Creator' )
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
				                re.element_id IN ( SELECT up.element_id FROM user_permissions up WHERE up.workspace_id = $workspace_id AND up.element_id IS NOT NULL AND up.role = 'Creator' )
				                AND ru.user_id IS NOT NULL
				        )
				    ) AS rk
				    LEFT JOIN user_permissions up
				        ON up.element_id = rk.element_id
				    WHERE up.workspace_id = $workspace_id AND up.role = 'Creator'
				    GROUP BY rk.user_id
				) AS rh ON
					ed.workspace_id = rh.workspace_id AND
				    ed.user_id = rh.user_id

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
				        ac.workspace_id,
				        ac.updated_user_id AS user_id,
				        ac.message,
				        ac.updated
				    FROM
				        (SELECT * FROM workspace_activities WHERE workspace_id = $workspace_id AND message <> 'Workspace viewed') AS ac
				    LEFT JOIN
				        (SELECT * FROM workspace_activities WHERE workspace_id = $workspace_id AND message <> 'Workspace viewed') AS ac2 ON
				            ac.updated_user_id = ac2.updated_user_id
				            AND ac.updated < ac2.updated
				    WHERE
				        ac2.updated_user_id IS NULL
				) AS ac ON
				    ed.user_id = ac.user_id
				    AND ed.workspace_id = ac.workspace_id

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

				LEFT JOIN #get costs
				(
				    SELECT ed.workspace_id, ed.user_id, cd.cost_estimate AS escost, cd.cost_spend AS spcost from user_permissions ed

				    LEFT JOIN
				    (
				        SELECT
				            uec.element_id,
				            uec.user_id,
				            SUM(IF(uec.estimate_spend_flag = 1, (uec.quantity * uec.work_rate), NULL)) AS cost_estimate,
				            SUM(IF(uec.estimate_spend_flag = 2, (uec.quantity * uec.work_rate), NULL)) AS cost_spend
				        FROM
				            user_element_costs uec
				        WHERE
				            uec.element_id IN (
				                    SELECT up.element_id FROM user_permissions up WHERE up.workspace_id = $workspace_id AND up.element_id IS NOT NULL AND up.role NOT IN('Sharer', 'Group Sharer')
				                )
				        GROUP BY uec.user_id

				    )
				    AS cd ON
				        ed.element_id = cd.element_id
				        AND ed.user_id = cd.user_id

				    WHERE ed.project_id = $project_id AND ed.workspace_id = $workspace_id AND ed.element_id IS NOT NULL
				    GROUP BY cd.element_id HAVING escost IS NOT NULL OR spcost IS NOT NULL
				) AS cd ON
				    ed.user_id = cd.user_id
				    AND ed.workspace_id = cd.workspace_id

			    -- WHERE ed.user_id = 20
			    $order
			    $limit_str
			";

		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($query );
		return $result;
	}

	function project_teams($params = null){
		$user_id = $this->Session->read('Auth.User.id');

		$project_id = $params['project_id'];

		$order = '';
		if(!isset($params['order']) || empty($params['order'])){
			$order = 'ORDER BY first_name, last_name ASC';
		}
		if( (isset($params['coloumn']) && !empty($params['coloumn'])) && (isset($params['order']) && !empty($params['order'])) ){
			$column = $params['coloumn'];
			$dir = $params['order'];
			$order = "ORDER BY $column $dir";
			if($params['coloumn'] == 'first_name'){
				$order = "ORDER BY first_name $dir, last_name $dir";
			}
			if($params['coloumn'] == 'last_name'){
				$order = "ORDER BY last_name $dir, first_name $dir";
			}
		}

		$limit_str = '';
		$page = (isset($params['page']) && !empty($params['page'])) ? $params['page'] : 0;
		if(isset($params['limit']) && !empty($params['limit'])){
			$limit = $params['limit'];
			$limit_str = "LIMIT $page, $limit";
		}
		// e($order);

		$query = "SELECT
				    ed.project_id,
				    ed.user_id,
				    ed.first_name,
				    ed.last_name,
				    ed.full_name,
				    ed.job_title,
				    ed.profile_pic,
				    ed.organization_id,
				    ed.role,
				    psk.project_skills,
				    psb.project_subjects,
				    psd.project_domains,
				    ed.sign,

				    unavail.unvailable_days,

				    cd.escost,
				    cd.spcost,
				    (CASE
				        WHEN ISNULL(escost) AND ISNULL(spcost) THEN 'Not Set'
				        WHEN ISNULL(spcost) THEN 'Budget Set'
				        WHEN ISNULL(escost) THEN 'Costs Incurred'
				        WHEN spcost > escost THEN 'Over Budget'
				        WHEN spcost <= escost THEN 'On Budget'
				        ELSE 'None' #never
				     END) AS cost_status,

				    efrt.total_hours,
				    efrt.blue_completed_hours,
				    efrt.green_remaining_hours,
				    efrt.amber_remaining_hours,
				    efrt.red_remaining_hours,
				    efrt.none_remaining_hours,
				    efrt.change_hours,

				    IFNULL(rh.high_pending_risks, 0) AS high_risks,
				    IFNULL(rh.severe_pending_risks, 0) AS severe_risks,
				    IFNULL(rh.total_risks, 0) AS total_risks,

				    /* usk.user_skills,
				    usb.user_subjects,
				    usd.user_domains, */

					IFNULL(usk.user_skills,0) AS user_skills,
					IFNULL(usb.user_subjects,0) AS user_subjects,
					IFNULL(usd.user_domains,0) AS user_domains,

				    ac.message,
				    ac.updated,

				    pg.grp_id,
				    pg.grp_title,
				    pg.grp_owner,
				    pg.grp_share_type,
				    pg.grp_user_name

				FROM #get user data
				(
				    SELECT
				        up.project_id,
				        prj.start_date,
				        prj.end_date,
				        up.user_id,
				        up.group_id,
				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name,
				        ud.first_name,
				        ud.last_name,
				        ud.job_title,
				        ud.profile_pic,
				        ud.organization_id,
				        up.role,
				        currencies.sign
				    FROM
				        user_permissions up
				    LEFT JOIN user_details ud ON
				        up.user_id = ud.user_id
				    LEFT JOIN projects prj ON
				        prj.id = up.project_id
			        LEFT JOIN currencies ON
			        	currencies.id = prj.currency_id
				    WHERE
				        up.workspace_id IS NULL
				        AND up.project_id = $project_id
				    GROUP BY up.project_id, up.user_id
				) AS ed

				LEFT JOIN(
					SELECT COUNT(DISTINCT ps.skill_id) AS project_skills, ps.project_id
					FROM project_skills ps
					GROUP BY ps.project_id
				) AS psk
					ON psk.project_id = ed.project_id

				LEFT JOIN(
					SELECT COUNT(DISTINCT ps.subject_id) AS project_subjects, ps.project_id
					FROM project_subjects ps
					GROUP BY ps.project_id
				) AS psb
					ON psb.project_id = ed.project_id

				LEFT JOIN(
					SELECT COUNT(DISTINCT ps.domain_id) AS project_domains, ps.project_id
					FROM project_domains ps
					GROUP BY ps.project_id
				) AS psd
					ON psd.project_id = ed.project_id

				LEFT JOIN #get unavailability
				(
				    SELECT count(*) unvailable_days, user_id, STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d') avail_start_date,
				            STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') avail_end_date
				            FROM `availabilities` WHERE
				             DATE(NOW()) BETWEEN DATE(STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d')) AND DATE(STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d'))
				             GROUP by user_id
				) AS unavail ON
				    ed.user_id = unavail.user_id

				LEFT JOIN #get effort
				(
				    SELECT
				        prj.project_id as project_id,
				        ef.total_hours,
				        ef.blue_completed_hours,
				        ef.green_remaining_hours,
				        ef.amber_remaining_hours,
				        ef.red_remaining_hours,
				        ef.none_remaining_hours,
				        ef.change_hours,
				        ef.user_id
				    FROM #mock top level query
				    (
				        SELECT id AS project_id FROM projects
				    ) AS prj
				    #section to add to top level progress bar query:
				    LEFT JOIN #get workspace effort data
				    (
				        SELECT
				            ue.project_id,
				            #ue.remaining_hours_color,
				            ue.user_id,
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
				                ee.user_id,
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
				        WHERE ue.project_id = $project_id
				        GROUP BY  ue.user_id
				    ) AS ef ON
				        prj.project_id = ef.project_id
				) AS efrt ON
				    ed.project_id = efrt.project_id and
				    ed.user_id = efrt.user_id

				LEFT JOIN #get high, severe and total risks
				(
				    SELECT
				        rk.user_id,
				        up.project_id,
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
				            WHERE re.element_id IN ( SELECT up.element_id FROM user_permissions up WHERE up.project_id = $project_id AND up.element_id IS NOT NULL AND up.role = 'Creator' )
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
				                re.element_id IN ( SELECT up.element_id FROM user_permissions up WHERE up.project_id = $project_id AND up.element_id IS NOT NULL AND up.role = 'Creator' )
				                AND ru.user_id IS NOT NULL
				        )
				    ) AS rk
				    LEFT JOIN user_permissions up
				        ON up.element_id = rk.element_id
				    WHERE up.project_id = $project_id AND up.workspace_id IS NULL AND up.role = 'Creator'
				    GROUP BY rk.user_id
				) AS rh ON
				    ed.project_id = rh.project_id AND
				    ed.user_id = rh.user_id

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
				        ac.project_id,
				        ac.updated_user_id AS user_id,
				        ac.message,
				        ac.updated
				    FROM
				        (SELECT * FROM project_activities WHERE project_id = $project_id AND message <> 'Project viewed') AS ac
				    LEFT JOIN
				        (SELECT * FROM project_activities WHERE project_id = $project_id AND message <> 'Project viewed') AS ac2 ON
				            ac.updated_user_id = ac2.updated_user_id
				            AND ac.updated < ac2.updated
				    WHERE
				        ac2.updated_user_id IS NULL
				) AS ac ON
				    ed.user_id = ac.user_id
				    AND ed.project_id = ac.project_id

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

				LEFT JOIN #get costs
				(
				    SELECT ed.project_id, ed.user_id, cd.cost_estimate AS escost, cd.cost_spend AS spcost from user_permissions ed

				    LEFT JOIN
				    (
				        SELECT
				            uec.element_id,
				            uec.user_id,
				            SUM(IF(uec.estimate_spend_flag = 1, (uec.quantity * uec.work_rate), NULL)) AS cost_estimate,
				            SUM(IF(uec.estimate_spend_flag = 2, (uec.quantity * uec.work_rate), NULL)) AS cost_spend
				        FROM
				            user_element_costs uec
				        WHERE
				            uec.element_id IN (
				                    SELECT up.element_id FROM user_permissions up WHERE up.project_id = $project_id AND up.element_id IS NOT NULL AND up.role NOT IN('Sharer', 'Group Sharer')
				                )
				        GROUP BY uec.user_id
				    )
				    AS cd ON
				        ed.element_id = cd.element_id
				        AND ed.user_id = cd.user_id

				    WHERE ed.project_id = $project_id AND ed.element_id IS NOT NULL
				    GROUP BY cd.element_id, cd.user_id
				    HAVING escost IS NOT NULL OR spcost IS NOT NULL
				) AS cd ON
				    ed.user_id = cd.user_id
				    AND ed.project_id = cd.project_id

			    $order
			    $limit_str
			";

		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result,1 );
		return $result;
	}

	function wsp_info($workspace_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					up.role,
					up.permit_edit,

					(CASE
						WHEN (up.role = 'Creator' OR up.role = 'Owner' OR up.role = 'Group Owner') THEN 1
						ELSE 0
					END) AS user_role,
					(CASE
						WHEN (workspaces.sign_off=1) THEN 'completed'
						WHEN ((DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date))  and workspaces.sign_off!=1 ) THEN 'progress'
						WHEN ((DATE(NOW()) < DATE(workspaces.start_date)) and workspaces.sign_off!=1) THEN 'not_started'
						WHEN ((DATE(workspaces.end_date) < DATE(NOW())) and workspaces.sign_off!=1) THEN 'overdue'
						ELSE 'not_spacified'
					END) AS wsp_status,

					workspaces.id, workspaces.start_date, workspaces.end_date, workspaces.sign_off_date, workspaces.sign_off, workspaces.description, workspaces.created, workspaces.outcome,
					CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name, ud.user_id, ud.job_title, ud.profile_pic, ud.organization_id

				FROM user_permissions up

				INNER JOIN workspaces
					ON up.workspace_id = workspaces.id

    			LEFT JOIN user_details ud On workspaces.created_by = ud.user_id

				WHERE
					workspaces.id = $workspace_id AND
					up.area_id IS null AND
					up.role = 'Creator'

				GROUP BY up.workspace_id";


		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	function risk_types($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$where = "";
		if(isset($project_id) && !empty($project_id)) {
			$where = " AND p.id = $project_id";
		}

		$query = "SELECT
				    DISTINCT(rpt.title)

				FROM
				    rm_details rd
				LEFT JOIN projects p
					ON rd.project_id = p.id
				INNER JOIN rm_project_risk_types rpt
					ON rpt.id = rd.rm_project_risk_type_id
				LEFT JOIN user_permissions up
					ON up.project_id = p.id

				WHERE
				    (((rd.user_id = $user_id)  OR rd.id IN(SELECT rm_users.rm_detail_id FROM rm_users WHERE rm_users.user_id = $user_id)) AND rpt.id IS NOT NULL OR up.role IN('Creator', 'Owner', 'Group Owner'))  $where

				GROUP BY rpt.title";

			// pr($query);
		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	function risk_details($risk_id = null){

		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					risk.id,
					risk.project_id,
					risk.title,
					risk.ptitle,
					risk.rdate,
					risk.creator_id,
					risk.creator_name,
					ruser.assignee,
					rtask.risk_tasks

				# RISK DETAILS
				FROM
				(
					SELECT
					    p.id AS project_id,
					    p.title AS ptitle,
					    rd.id,
					    rd.title,
					    rd.possible_occurrence AS rdate,
				        ud.user_id AS creator_id,
				        CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator_name

					FROM
					    rm_details rd
					LEFT JOIN projects p
						ON rd.project_id = p.id
					LEFT JOIN user_details ud
						ON ud.user_id = rd.created_by
					INNER JOIN rm_project_risk_types rpt
						ON rpt.id = rd.rm_project_risk_type_id

					WHERE
					    rd.id = $risk_id

				) AS risk
				# RISK USERS
				LEFT JOIN (
				   	SELECT
				   		ru.rm_detail_id AS rmid,
				   		JSON_ARRAYAGG(JSON_OBJECT('email', u.email, 'email_notification', u.email_notification, 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name), 'notifiation_web' ,en.web ,'notifiation_email' ,en.email)) AS assignee
				   	FROM rm_users ru
					left JOIN
						users u
					ON u.id = ru.user_id
					left JOIN
						user_details ud
				        on ud.user_id = ru.user_id
					LEFT JOIN
						email_notifications en
					ON en.user_id = ru.user_id and en.notification_type = 'riskcenter' and en.personlization = 'risk_deleted'
				    where ru.rm_detail_id = $risk_id
				) AS ruser
				ON ruser.rmid = risk.id
				#TASKS
				LEFT JOIN (
					SELECT
						rel.rm_detail_id AS rmid,
						JSON_ARRAYAGG(JSON_OBJECT( 'id', rel.element_id, 'title', el.title, 'wsp_id', w.id)) AS risk_tasks
					FROM rm_elements rel
					INNER JOIN elements el ON rel.element_id = el.id
					INNER JOIN areas ON el.area_id = areas.id
					INNER JOIN workspaces w ON w.id = areas.workspace_id
					GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = risk.id
			";
			// pr($query, 1);


		$result =  ClassRegistry::init('Project')->query($query);

		return (isset($result) && !empty($result)) ? $result : [];
	}

	function user_projects($user_id = null, $completed = false){
		$completed_qry = "";
		if($completed){
			$completed_qry = "AND p.sign_off <> 1";
		}
		$query = "SELECT p.id, p.title
				FROM user_permissions up
				INNER JOIN projects p ON p.id = up.project_id
				WHERE
					up.user_id = $user_id AND
					up.workspace_id IS NULL
					$completed_qry
				GROUP BY up.project_id
				ORDER BY p.title ASC
            ";

		return ClassRegistry::init('UserPermission')->query($query);
	}

	function myrisks($user_id = null){
		$query = "SELECT
					rd.id,
				    up.project_id,
				    rd.title,
				    p.title AS ptitle,
				    rd.possible_occurrence AS rdate,
				    (CASE
				    	WHEN rd.status = 2 THEN 'Review'
				        WHEN rd.status = 3 THEN 'SignOff'
				        WHEN rd.status = 4 THEN 'Overdue'
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
				    JSON_ARRAYAGG(JSON_OBJECT('id', ru.user_id,'title',CONCAT_WS(' ',ud2.first_name,ud2.last_name))) AS assignee,
				    COUNT(ru.user_id) AS ruser_count,
				    JSON_ARRAYAGG(JSON_OBJECT('id', rl.user_id,'title',CONCAT_WS(' ',ud3.first_name,ud3.last_name))) AS leaders,
				    JSON_ARRAYAGG(JSON_OBJECT('id', re.element_id,'title',el.title)) AS risk_tasks,
				    COUNT(el.id) AS rtask_count
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
				WHERE
					#up.project_id = 2 AND #comment out for All Projects and My Risks, uncomment for Specific Project
				    up.user_id = 20
				    AND up.workspace_id IS NULL
				    AND rd.id IS NOT NULL
				    AND (
				        #up.role IN ('Creator','Owner','Group Owner') OR #comment out for My Risks, uncomment for All Projects and Specific Project
				        rd.created_by = 20
				        OR ru.user_id = 20
						)
				GROUP BY rd.id
            ";

		$result = ClassRegistry::init('UserPermission')->query($query);
		pr($result, 1);
	}

	function project_risks( $query_params = null ){
		$user_id = $this->Session->read('Auth.User.id');


		$project_id = '';
		if( isset($query_params['project_id']) && !empty($query_params['project_id']) ){
			$project_id = $query_params['project_id'];
		}

		$where = [];

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

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($query_params['limit']) && !empty($query_params['limit'])){
			$limit = $query_params['limit'];
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
				    GROUP BY rel.rm_detail_id
				) AS rtask
				ON rtask.rmid = rd.id

				WHERE
					up.project_id = $project_id AND
				    up.user_id = $user_id
				    AND up.workspace_id IS NULL
				    AND rd.id IS NOT NULL
				    AND (
				    	up.role IN ('Creator','Owner','Group Owner') OR
				        rd.created_by = $user_id
				        OR ru.user_id = $user_id
						)
						$where_cond

				GROUP BY rd.id
				$order_by
				$limit_str
			";

		// pr($query,1);
		// pr($query_params);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result);
		return $result;
	}

	function users_detail($user_id = null){
		$query = "SELECT
					u.id,
					CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name
				FROM users u
				INNER JOIN user_details ud
					ON ud.user_id = u.id
				WHERE u.id IN($user_id);
				ORDER BY ud.first_name ASC, ud.last_name ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result);
		return $result;
	}

	function programs_list($query_params = null){
		$user_id = $this->Session->read('Auth.User.id');
		// pr($query_params);
		$order_by = "ORDER BY status ASC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$order = $query_params['order'];
			$coloumn = $query_params['coloumn'];
			$order_by = "ORDER BY $coloumn $order";
		}

		$where_str = "";
		$where = [];
		if( isset($query_params['search']) && !empty($query_params['search']) ){
			$where[] = $query_params['search'];
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($query_params['limit']) && !empty($query_params['limit'])){
			$limit = $query_params['limit'];
			$limit_str = "LIMIT $page, $limit";
		}

		$filter_status = '';
		$filter_type_str = '';
		if(isset($query_params['status']) && !empty($query_params['status'])) {
			$val = $query_params['status'];
			//
			$status_filters = [];
			foreach ($val as $key => $value) {
				if($value == 4){
					$status_filters[] = "(prjs.cmp_count = prjs.total_projects)";
				}
				if($value == 3){
					$status_filters[] = "(DATE(NOW()) < pdets.stdate AND prjs.cmp_count <> prjs.total_projects)";
				}
				if($value == 2){
					$status_filters[] = "(DATE(NOW()) BETWEEN pdets.stdate AND pdets.endate AND prjs.cmp_count <> prjs.total_projects)";
				}
				if($value == 1){
					$status_filters[] = "(pdets.endate < DATE(NOW()) AND prjs.cmp_count <> prjs.total_projects)";
				}
				if($value == 5){
					$status_filters[] = "((prjs.total_projects IS NULL OR prjs.total_projects <= 0) AND (prjs.non_count IS NULL OR prjs.non_count <= 0))";
				}
			}
			$filter_status = implode(" OR ", $status_filters);
			$where[] = "(" . $filter_status . ")";
		}

		$roleStr = "(prog.created_by = $user_id OR pu.user_id = $user_id)";
		if(isset($query_params['roles']) && !empty($query_params['roles'])) {
			$val = $query_params['roles'];

			$all_roles = [];
			foreach ($val as $key => $role) {
				if($role == 'creator'){
					$all_roles[] = "prog.created_by = $user_id";
				}
				if($role == 'stakeholder'){
					$all_roles[] = "pu.user_id = $user_id";
				}
			}
			$roleStr = implode(" OR ", $all_roles);
		}
		if(isset($query_params['types']) && !empty($query_params['types'])) {
			$val = implode(',', $query_params['types']);
			$filter_type_str = "AND prog.type_id IN ($val)";
		}

		if(isset($where) && !empty($where)){
			$where_str = "WHERE " . implode(" AND ", $where);
		}

		$query = "
			SELECT
				progs.id, progs.name, progs.color_code, progs.created_on, progs.created_by, progs.creator, progs.type, progs.role,
				if(stake.stakeholders IS NULL, 0, stake.stakeholders) AS stakeholders,
				pdets.stdate, pdets.endate, pdets.pbudget, pdets.cids AS ctot, pdets.csign,
				(CASE
			        WHEN prjs.cmp_count = prjs.total_projects THEN '4' # completed
					WHEN DATE(NOW()) < pdets.stdate THEN '3' # not started
			        WHEN DATE(NOW()) BETWEEN pdets.stdate AND pdets.endate THEN '2' # in progress
			        WHEN pdets.endate < DATE(NOW()) THEN '1' # overdue
			        ELSE '5' # not set, no projects
			    END) AS status,
				conf_levels.conf_level,
				conf_levels.level,
				team.team_count,
				efforts.total_hours,
				efforts.blue_completed_hours,
				efforts.green_remaining_hours,
				efforts.amber_remaining_hours,
				efforts.red_remaining_hours,
				efforts.none_remaining_hours,
				efforts.change_hours,
				efforts.remaining_hours,
				efforts.completed_hours,
				prjs.cmp_count, prjs.ovd_count, prjs.prg_count, prjs.nos_count, prjs.non_count, prjs.total_projects,
				rag_status.rag_totals,
				ws.prws_counts, ws.total_workspaces,
				task.prts_counts, task.total_tasks,
				cost_statuses.cost_totals,
				cost_total.cost_totals,
				srisk.severe_risk_total,
				hrisk.high_risk_total,
				/*risk_totals.risk_total,
				risk_statuses.risk_status,*/
				risk_statuses.ropen_count,
				risk_statuses.rprg_count,
				risk_statuses.rovd_count,
				risk_statuses.rcmp_count,
				risk_statuses.risk_total,
				stake.stakeholders

			-- PROGRAM DETAILS
			FROM (
			  	SELECT
			      	prog.id, prog.name, prog.color_code, prog.created_by, prog.created_on,
			      	CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator,
			      	pt.type,
					IF(prog.created_by = $user_id,'Creator','Stakeholder') AS role
			  	FROM
			        programs prog
			    LEFT JOIN user_details ud ON
			        ud.user_id = prog.created_by
			    LEFT JOIN program_types pt ON
			        pt.id = prog.type_id
			    LEFT JOIN program_users pu ON
			        pu.program_id = prog.id
			    WHERE
			    	($roleStr)
			    	$filter_type_str
				GROUP BY prog.id
			) AS progs

			-- STAKEHOLDERS
			LEFT JOIN (
			   	SELECT
			   		pu.program_id AS id,
			   		COUNT(pu.user_id) AS stakeholders
				FROM program_users pu
				GROUP BY pu.program_id
			)
			AS stake ON
				progs.id = stake.id

			-- PROJECTS EARLIEST START/ LATEST END DATES, TOTAL BUDGET,	 CURRENCIES
			LEFT JOIN (
			   	SELECT
			   		pp.program_id AS id,
			   		MIN(DATE(prj.start_date)) AS stdate,
			        MAX(DATE(prj.end_date)) AS endate,
			        SUM(prj.budget) as pbudget,
			        GROUP_CONCAT(DISTINCT(prj.currency_id)) AS cids,
			        GROUP_CONCAT(DISTINCT(cu.sign)) AS csign
				FROM program_projects pp
				LEFT JOIN projects prj ON
					pp.project_id = prj.id
				LEFT JOIN currencies cu ON
			    	prj.currency_id = cu.id
				GROUP BY pp.program_id
			)
			AS pdets ON
				progs.id = pdets.id

			-- CONFIDENCE LEVEL
			LEFT JOIN
			(
				SELECT
					plevel.id,
					plevel.level_current AS level,
					JSON_ARRAYAGG(
						JSON_OBJECT(
							'level', concat(plevel.level_current),
							'level_count', concat(plevel.level_count),
							'confidence_class', concat(plevel.confidence_class),
							'confidence_level', concat(plevel.confidence_level),
							'confidence_arrow', concat(plevel.confidence_arrow),
							'confidence_order', concat(plevel.confidence_order),
							'confidence_order_asc', concat(plevel.confidence_order_asc)
						)
					) AS conf_level

				FROM (
					SELECT
			            pp.program_id AS id,
			            ROUND(AVG(el.level)) AS level_current,
			            COUNT(el.level) AS level_count,
			            (CASE
							WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'Low'
							WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'Medium Low'
							WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'Medium High'
							WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'High'
							ELSE '' # not set
						END) AS confidence_level,
						(CASE
							WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'red'
							WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'orange'
							WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'yellow'
							WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'green-bg'
							ELSE '' # not set
						END) AS confidence_class,
						(CASE
							WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'lowgrey'
							WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'mediumlowgrey'
							WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'mediumhighgrey'
							WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'highgrey'
							ELSE '' # not set
						END) AS confidence_arrow,
			            (CASE
			                WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 1
			                WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 2
			                WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 3
			                WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 4
			                ELSE 0 # not set
			            END) AS confidence_order,
			            (CASE
			                WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 4
			                WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 3
			                WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 2
			                WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 1
			                ELSE 5 # not set
			            END) AS confidence_order_asc
			        FROM
			            program_projects pp
			        LEFT JOIN element_levels el ON
			            pp.project_id = el.project_id
			        	AND el.is_active = 1
			        GROUP BY pp.program_id
				) AS plevel
			    GROUP BY plevel.id
			) conf_levels ON
				progs.id = conf_levels.id

			-- TEAM COUNT
			LEFT JOIN (
			   	SELECT
			    	pp.program_id AS id,
			        COUNT(DISTINCT(up.user_id)) AS team_count
			    FROM
			    	program_projects pp
			    LEFT JOIN user_permissions up ON
			    	pp.project_id = up.project_id
			        AND up.workspace_id IS NULL
			    GROUP BY pp.program_id
			)
			AS team ON
				progs.id = team.id

			-- EFFORT
			LEFT JOIN (
			    SELECT
			        pp.program_id AS id,
			        SUM(ef.total_hours) AS total_hours,
			        SUM(ef.blue_completed_hours) AS blue_completed_hours,
			        SUM(ef.green_remaining_hours) AS green_remaining_hours,
			        SUM(ef.amber_remaining_hours) AS amber_remaining_hours,
			        SUM(ef.red_remaining_hours) AS red_remaining_hours ,
			        SUM(ef.none_remaining_hours) AS none_remaining_hours,
			        SUM(ef.change_hours) AS change_hours,
			        SUM(ef.remaining_hours) AS remaining_hours,
			        SUM(ef.completed_hours) AS completed_hours
			    FROM
			    	program_projects pp
			    LEFT JOIN
			    (
			        SELECT
			        ue.project_id,
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
			            (CASE
			            	WHEN el.sign_off = 1 OR el.start_date IS NULL THEN 'None' #signed off or no schedule
			            	WHEN CEIL(ee.remaining_hours/8) #remaining user 8 hour days
			             		> DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1 #remaining project days
			             		THEN 'Red' #remaining user effort days cannot be completed in remaining project days
			            	WHEN
			            		CEIL(GREATEST(CEIL(ee.remaining_hours/8),2)*1.5) #remaining user 8 hour days (force minimum 2 days) with 50% contingency days
			            		> DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1 #remaining project days
			            		THEN 'Amber' #remaining user days plus 50% contingency cannot be completed in remaining project days
			            	ELSE 'Green' #remaining user effort days can be completed in remaining project days
			            END) AS remaining_hours_color,
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
			    	pp.project_id = ef.project_id
			    GROUP by pp.program_id
			) AS efforts ON
				progs.id = efforts.id

			-- PROJECTS
			LEFT JOIN (
			   	SELECT
			   		pp.program_id AS id,
			   		SUM(IF(prj.sign_off = 1, 1, 0)) AS cmp_count,
			   		SUM(IF((DATE(NOW()) > DATE(prj.end_date) AND prj.end_date IS NOT NULL AND prj.sign_off = 0), 1, 0)) AS ovd_count,
			   		SUM(IF((DATE(NOW()) BETWEEN DATE(prj.start_date) AND DATE(prj.end_date) AND prj.sign_off = 0), 1, 0)) AS prg_count,
			   		SUM(IF((DATE(NOW()) < DATE(prj.start_date) AND prj.sign_off = 0), 1, 0)) AS nos_count,
			   		SUM(if((prj.start_date IS NULL OR prj.end_date IS NULL), 1, 0)) AS non_count,
			   		COUNT(DISTINCT(prj.id)) AS total_projects
				FROM program_projects pp
				LEFT JOIN projects prj ON
					prj.id = pp.project_id
				GROUP BY pp.program_id
			) AS prjs ON
				progs.id = prjs.id

			-- RAG STATUS
			LEFT JOIN (
				SELECT
				pr.program_id,
				JSON_ARRAYAGG(
					JSON_OBJECT(
						'red', concat(pr.red_count),
						'yellow', concat(pr.yellow_count),
						'green', concat(pr.green_count)
					)
				) as rag_totals

				FROM
				(
					SELECT pp.program_id,
						SUM(if(((p.rag_status IS NOT NULL AND p.rag_status = 1) OR ((p.rag_status IS NOT NULL AND p.rag_status = 3) AND (prg.amber_value > 0 AND prg.red_value > 0) AND (els.ovd_percent >= prg.red_value AND els.ovd_percent <= 100)) OR ((p.rag_status IS NOT NULL AND p.rag_status = 3) AND (( (prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0 ) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent <= 100)))), 1, 0)) AS red_count,

						SUM(if(((p.rag_status IS NOT NULL AND p.rag_status = 2) OR (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND (prg.amber_value > 0 AND prg.red_value > 0) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent < prg.red_value )) OR (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND ( (prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0 ) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent <= 100)) ), 1, 0)) AS yellow_count,

			          	SUM(if((
				        	((p.rag_status IS NOT NULL AND p.rag_status = 1) OR ((p.rag_status IS NOT NULL AND p.rag_status = 3) AND (prg.amber_value > 0 AND prg.red_value > 0) AND (els.ovd_percent >= prg.red_value AND els.ovd_percent <= 100)) OR ((p.rag_status IS NOT NULL AND p.rag_status = 3) AND (( (prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0 ) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent <= 100))))
							OR
					        ((p.rag_status IS NOT NULL AND p.rag_status = 2) OR (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND (prg.amber_value > 0 AND prg.red_value > 0) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent < prg.red_value )) OR (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND ( (prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0 ) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent <= 100)))
							), 0,1 ) ) green_count,

						(CASE
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 1 ) THEN 'red'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 2 ) THEN 'yellow'
							WHEN ( p.rag_status IS NOT NULL AND p.rag_status = 3 )
							THEN
								CASE
						            WHEN (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND (prg.amber_value > 0 AND prg.red_value > 0) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent < prg.red_value ))
					                THEN 'yellow'
					                WHEN ( (prg.amber_value > 0 AND prg.red_value > 0) AND (els.ovd_percent >= prg.red_value AND els.ovd_percent <= 100))
					                THEN 'red'
					                WHEN ((
					                		( prg.amber_value IS NULL OR prg.amber_value <= 0 OR prg.amber_value = '' ) AND prg.red_value > 0) AND
					                		(els.ovd_percent >= prg.red_value AND els.ovd_percent <= 100))
					                THEN 'red'
					                WHEN (( p.rag_status IS NOT NULL AND p.rag_status = 3 ) AND ( (prg.red_value IS NULL OR prg.red_value <= 0 OR prg.red_value = '') AND prg.amber_value > 0 ) AND ( els.ovd_percent >= prg.amber_value AND els.ovd_percent <= 100))
					                THEN 'yellow'
					                ELSE 'green-bg'
						        END
							ELSE 'green-bg'
						END) AS p_rag
					FROM(
						SELECT
							up.project_id,
							COUNT(up.element_id),
							SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1) AS elovd,
							ROUND((SUM(DATE(el.end_date)<DATE(NOW()) AND el.sign_off!=1 AND el.date_constraints=1)/COUNT(el.id))*100) AS ovd_percent
						FROM user_permissions up
						LEFT JOIN elements el ON up.element_id = el.id AND up.element_id IS NOT NULL
						WHERE up.role = 'Creator'
						GROUP BY up.project_id
					) AS els
					LEFT JOIN projects p
						ON els.project_id = p.id
					LEFT JOIN project_rags prg
						ON els.project_id = prg.project_id
					LEFT JOIN program_projects pp
						ON els.project_id = pp.project_id

					WHERE pp.program_id IS NOT NULL
					GROUP BY pp.program_id
				) pr

			    GROUP BY pr.program_id
			)
			AS rag_status ON rag_status.program_id = progs.id

			-- WORKSPACES
			LEFT JOIN (
			   	SELECT ws_count.program_id,
			   		(ws_count.total_workspaces) AS total_workspaces,
				   	JSON_ARRAYAGG(
						JSON_OBJECT(
							'non', concat(ws_count.wnon),
							'pnd', concat(ws_count.wpnd),
							'prg', concat(ws_count.wprg),
							'ovd', concat(ws_count.wovd),
							'cmp', concat(ws_count.wcmp),
							'total', concat(ws_count.total_workspaces)
						)
					) as prws_counts
				FROM(
					SELECT
						pp.program_id,
					   	SUM( (workspaces.start_date IS NULL OR workspaces.start_date = '') AND (workspaces.end_date IS NULL OR workspaces.end_date = '') AND workspaces.sign_off!=1) AS wnon,
						SUM(DATE(NOW())<DATE(workspaces.start_date) AND workspaces.sign_off!=1) AS wpnd,
						SUM(DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) AND workspaces.sign_off!=1) AS wprg,
						SUM(DATE(workspaces.end_date)<DATE(NOW()) AND workspaces.sign_off!=1) AS wovd,
						SUM(workspaces.sign_off=1) AS wcmp,
						COUNT(DISTINCT(up.workspace_id)) AS total_workspaces
					FROM user_permissions up
					LEFT JOIN workspaces ON up.workspace_id = workspaces.id
					LEFT JOIN program_projects pp ON pp.project_id = up.project_id
					WHERE
						up.workspace_id IS NOT NULL AND
						up.area_id IS NULL AND
						up.role = 'Creator' AND
						pp.program_id IS NOT NULL

					GROUP BY pp.program_id
				) ws_count
				GROUP BY ws_count.program_id
			)
			AS ws ON ws.program_id = progs.id

			-- TASKS
			LEFT JOIN (
			   	SELECT ts_count.program_id,
			   		(ts_count.total_tasks) AS total_tasks,
				   	JSON_ARRAYAGG(
						JSON_OBJECT(
							'non', concat(ts_count.enon),
							'pnd', concat(ts_count.epnd),
							'prg', concat(ts_count.eprg),
							'ovd', concat(ts_count.eovd),
							'cmp', concat(ts_count.ecmp),
							'total', concat(ts_count.total_tasks)
						)
					) as prts_counts
				FROM(
					SELECT
						pp.program_id,
				       	SUM(elements.date_constraints = 0 AND elements.sign_off != 1) AS enon,
						SUM(DATE(NOW())<DATE(elements.start_date) AND elements.sign_off!=1 AND elements.date_constraints=1) AS epnd,
						SUM(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) AND elements.sign_off!=1 AND elements.date_constraints=1  ) AS eprg,
						SUM(DATE(elements.end_date)<DATE(NOW()) AND elements.sign_off!=1 AND elements.date_constraints=1) AS eovd,
						SUM(elements.sign_off=1) AS ecmp,
						COUNT(DISTINCT(up.element_id)) AS total_tasks
				 	FROM user_permissions up
				 	LEFT JOIN elements ON up.element_id = elements.id
					LEFT JOIN program_projects pp ON pp.project_id = up.project_id
					WHERE
						up.element_id IS NOT NULL AND
						up.role = 'Creator' AND
						pp.program_id IS NOT NULL

				 	GROUP BY pp.program_id
				) ts_count
				GROUP BY ts_count.program_id
			)
			AS task ON task.program_id = progs.id

			-- COST STATUS
			LEFT JOIN (
				SELECT
					cost_status.program_id,
					JSON_ARRAYAGG(
						JSON_OBJECT(
							'cost_not_set', concat(cost_status.cost_not_set),
							'cost_budget_set', concat(cost_status.cost_budget_set),
							'cost_incurred', concat(cost_status.cost_incurred),
							'cost_over_budget', concat(cost_status.cost_over_budget),
							'cost_on_budget', concat(cost_status.cost_on_budget)
						)
					) as cost_totals
				FROM(
					SELECT
						pp.program_id,
						SUM(IF( ((sum_val.est_sum = '' OR sum_val.est_sum = 0 OR sum_val.est_sum IS NULL) AND
								(sum_val.sp_sum = '' OR sum_val.sp_sum = 0 OR sum_val.sp_sum IS NULL)
							),1,0)) AS cost_not_set,
						SUM(IF(((sum_val.est_sum > 0) AND ( sum_val.sp_sum = '' OR sum_val.sp_sum = 0 OR sum_val.sp_sum IS NULL )
							),1,0)) AS cost_budget_set,
						SUM(IF(( (sum_val.est_sum = '' OR sum_val.est_sum = 0 OR sum_val.est_sum IS NULL) AND (sum_val.sp_sum > 0)
							),1,0)) AS cost_incurred,
						SUM(if(( (sum_val.est_sum > 0) AND (sum_val.sp_sum > 0 AND (sum_val.sp_sum > sum_val.est_sum))
							),1,0)) AS cost_over_budget,
						SUM(IF(( (sum_val.est_sum > 0) AND (sum_val.sp_sum > 0) AND (sum_val.sp_sum <= sum_val.est_sum)
							),1,0)) AS cost_on_budget
					FROM(
					 	SELECT
							up.project_id,
							SUM(ec.estimated_cost) AS est_sum,
							SUM(ec.spend_cost) AS sp_sum
						FROM
							user_permissions up
						LEFT JOIN projects p ON
							up.project_id = p.id
						LEFT JOIN element_costs ec ON
							up.element_id = ec.element_id AND up.element_id IS NOT NULL
						WHERE
							up.role = 'Creator'
						GROUP BY up.project_id
					) AS sum_val
					LEFT JOIN program_projects pp ON sum_val.project_id = pp.project_id
					WHERE pp.program_id IS NOT NULL
					GROUP BY pp.program_id
				) cost_status
				GROUP BY cost_status.program_id
			)
			AS cost_statuses ON cost_statuses.program_id = progs.id

			-- EST/ACT TOTAL
			LEFT JOIN (
				SELECT
					cost_status.program_id,
					JSON_ARRAYAGG(
						JSON_OBJECT(
							'est_sum', concat(cost_status.est_sum),
				            'sp_sum', concat(cost_status.est_sp)
						)
					) as cost_totals
				FROM(
				    SELECT
						pp.program_id,
						SUM(ec.estimated_cost) AS est_sum,
						SUM(ec.spend_cost) AS est_sp

					FROM
						user_permissions up
					LEFT JOIN projects p ON
						up.project_id = p.id
					LEFT JOIN element_costs ec ON
						up.element_id = ec.element_id AND up.element_id IS NOT NULL
					LEFT JOIN program_projects pp ON pp.project_id = up.project_id
					WHERE
						up.role = 'Creator' AND pp.program_id IS NOT null
					GROUP BY pp.program_id
				) cost_status
				GROUP BY cost_status.program_id
			)
			AS cost_total ON cost_total.program_id = progs.id

			-- PENDING SEVERE RISK
			LEFT JOIN
			(
			    SELECT
			        pp.project_id,
			        pp.program_id,
			        count(DISTINCT(rd.id)) AS severe_risk_total
			    FROM
			        user_permissions up

			    LEFT JOIN rm_details rd ON
			        up.project_id = rd.project_id
			    LEFT JOIN rm_expose_responses rr ON
			        rd.id = rr.rm_detail_id
			    LEFT JOIN program_projects pp ON
			        pp.project_id = rd.project_id
			    WHERE
			        up.role = 'Creator'
			        AND up.element_id IS NOT NULL
			        AND rd.status IN (1, 2, 4) #open, review, overdue
			        AND ( (rr.impact = 4 AND rr.percentage = 5) OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4)) )
			        AND pp.program_id IS NOT NULL
			    GROUP BY pp.program_id
			) srisk ON srisk.program_id = progs.id

			-- PENDING HIGH RISK
			LEFT JOIN
			(
			    SELECT
			        pp.project_id,
			        pp.program_id,
			        count(DISTINCT(rd.id)) AS high_risk_total
			    FROM
			        user_permissions up

			    LEFT JOIN rm_details rd ON
			        up.project_id = rd.project_id
			    LEFT JOIN rm_expose_responses rr ON
			        rd.id = rr.rm_detail_id
			    LEFT JOIN program_projects pp ON
			        pp.project_id = rd.project_id
			    WHERE
			        up.role = 'Creator'
			        AND up.element_id IS NOT NULL
			        AND rd.status IN (1, 2, 4) #open, review, overdue
			        AND (
			            (rr.impact = 2 AND rr.percentage = 5)
			            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
			            OR (rr.impact = 4 AND rr.percentage = 4)
			            OR (rr.impact = 5 AND rr.percentage = 3)
			            )
			        AND pp.program_id IS NOT NULL
			    GROUP BY pp.program_id
			) hrisk ON hrisk.program_id = progs.id

			-- STATUS WISE RISKS
			LEFT JOIN
			(
				SELECT
					risk_st.program_id,
					concat(risk_st.opn_count) AS ropen_count,
					concat(risk_st.prg_count) AS rprg_count,
					concat(risk_st.ovd_count) AS rovd_count,
					concat(risk_st.cmp_count) AS rcmp_count,
					risk_st.total AS risk_total
				FROM(
				    SELECT
				    	pp.program_id,
				        SUM(IF((rd.status <> 3 AND Date(rd.possible_occurrence) < Date(now())), 1, 0)) AS ovd_count,
				        SUM(IF((rd.status <> 3 AND Date(rd.possible_occurrence) >= Date(now()) AND rd.status = 2), 1, 0)) AS prg_count,
				        SUM(IF((rd.status = 3), 1, 0)) AS cmp_count,
				        SUM(IF((rd.status = 1 AND Date(rd.possible_occurrence) >= Date(now())), 1, 0)) AS opn_count,
				        COUNT(DISTINCT(rd.id)) as total

				    FROM program_projects pp
					LEFT JOIN rm_details rd ON pp.project_id = rd.project_id
				    WHERE
				        pp.program_id IS NOT NULL #AND
				       #( rd.created_by = 2
				       #OR rd.id IN(SELECT DISTINCT(rm_users.rm_detail_id) FROM rm_users LEFT JOIN rm_details on rm_details.id = rm_users.rm_detail_id WHERE rm_users.user_id = $user_id GROUP BY rm_details.project_id)
				       #OR pp.project_id IN (SELECT project_id from user_permissions WHERE role in ('Owner','Group Owner','Creator') AND workspace_id IS NOT NULL AND user_id = $user_id GROUP BY project_id)
				       #)

				    GROUP BY pp.program_id
			    ) risk_st
			    GROUP BY risk_st.program_id
			) risk_statuses ON risk_statuses.program_id = progs.id

			$where_str
			$order_by
			$limit_str
		";
		// pr($query);
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_types(){
		$query = " SELECT pt.id, pt.type
			  	FROM program_types pt
			  	ORDER BY pt.type ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_projects_data($program_id = null){
		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT clevels.conf_level
			  	FROM program_projects pp
			  	LEFT JOIN(
			  		SELECT
						plevel.id,
						JSON_ARRAYAGG(
							JSON_OBJECT(
                                'project_id', plevel.id,
                                'project_title', plevel.title,
                                'project_status', plevel.prj_status,
                                'project_start_date', plevel.start_date,
                                'project_end_date', plevel.end_date,
                                'project_color', plevel.color_code,
                                'project_role', plevel.role,
								'level', concat(plevel.level_current),
								'level_count', concat(plevel.level_count),
								'confidence_class', concat(plevel.confidence_class),
								'confidence_level', concat(plevel.confidence_level),
								'confidence_arrow', concat(plevel.confidence_arrow),
								'confidence_order', concat(plevel.confidence_order),
								'confidence_order_asc', concat(plevel.confidence_order_asc)
							)
						) AS conf_level

					FROM (
						SELECT
				            p.id AS id,
				            p.title,
				            (CASE
								WHEN (DATE(NOW())<DATE(p.start_date) AND p.sign_off != 1) THEN '3' # not started
								WHEN (DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date) AND p.sign_off != 1) THEN '2' # progressing
								WHEN (DATE(p.end_date)<DATE(NOW()) AND p.sign_off != 1) THEN '1' # overdue
								WHEN (p.sign_off = 1) THEN '4' # completed
								ELSE '5' # not set
							END) AS prj_status,
							p.start_date,
							p.end_date,
							p.color_code,
							permit.role,
				            ROUND(AVG(el.level)) AS level_current,
				            COUNT(el.level) AS level_count,
				            (CASE
								WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'Low'
								WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'Medium Low'
								WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'Medium High'
								WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'High'
								ELSE '' # not set
							END) AS confidence_level,
							(CASE
								WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'red'
								WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'orange'
								WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'yellow'
								WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'green-bg'
								ELSE '' # not set
							END) AS confidence_class,
							(CASE
								WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 'lowgrey'
								WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 'mediumlowgrey'
								WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 'mediumhighgrey'
								WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 'highgrey'
								ELSE '' # not set
							END) AS confidence_arrow,
				            (CASE
				                WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 1
				                WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 2
				                WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 3
				                WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 4
				                ELSE 0 # not set
				            END) AS confidence_order,
				            (CASE
				                WHEN (ROUND(AVG((el.level))) >= 1 && ROUND(AVG((el.level))) <= 24) THEN 4
				                WHEN (ROUND(AVG((el.level))) >= 25 && ROUND(AVG((el.level))) <= 49) THEN 3
				                WHEN (ROUND(AVG((el.level))) >= 50 && ROUND(AVG((el.level))) <= 74) THEN 2
				                WHEN (ROUND(AVG((el.level))) >= 75 && ROUND(AVG((el.level))) <= 100) THEN 1
				                ELSE 5 # not set
				            END) AS confidence_order_asc
				        FROM
				            projects p
				        LEFT JOIN element_levels el ON
				            p.id = el.project_id AND el.is_active = 1
				        LEFT JOIN (
				        	SELECT up.role, up.project_id
				        	FROM user_permissions up
				        	WHERE up.user_id = $user_id AND
				        	up.workspace_id IS NULL
				        )
				        AS permit ON permit.project_id = p.id
				        GROUP BY p.id
					) AS plevel
				    GROUP BY plevel.id
			  	)
			  	AS clevels ON clevels.id = pp.project_id
			  	WHERE pp.program_id = $program_id
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_team($program_id = null){
		$query = "SELECT
			    	DISTINCT(user_permissions.user_id),
                    CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS fullname
			    FROM
			    	program_projects pp
			    LEFT JOIN user_permissions ON pp.project_id = user_permissions.project_id AND user_permissions.workspace_id IS NULL
			    LEFT JOIN user_details ON user_details.user_id = user_permissions.user_id
			    WHERE pp.program_id = $program_id
                ORDER BY user_details.first_name ASC, user_details.last_name ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_stakeholders($program_id = null){
		$query = "SELECT
			    	DISTINCT(user_details.user_id),
                    CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS fullname
			    FROM
			    	program_users pp
			    LEFT JOIN user_details ON user_details.user_id = pp.user_id
			    WHERE pp.program_id = $program_id
                ORDER BY user_details.first_name ASC, user_details.last_name ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_creator($program_id = null){
		$query = "SELECT
			    	DISTINCT(user_details.user_id),
                    CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS fullname
			    FROM
			    	programs pp
			    LEFT JOIN user_details ON user_details.user_id = pp.created_by
			    WHERE pp.id = $program_id
                ORDER BY user_details.first_name ASC, user_details.last_name ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function program_selected_types(){
		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT DISTINCT(pt.id), pt.type
			  	FROM programs p
			  	LEFT JOIN program_users pu ON p.id = pu.program_id
			  	LEFT JOIN program_types pt ON p.type_id = pt.id
			  	WHERE p.created_by = $user_id OR pu.user_id = $user_id
			  	ORDER BY pt.type ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function user_notify($user_id = null){
		$query = "SELECT
				   		u.id, u.email, u.email_notification, CONCAT_WS(' ',ud.first_name , ud.last_name) AS title, en.web AS notifiation_web, en.email AS notifiation_email
				   	FROM users u
					left JOIN
						user_details ud
				        on ud.user_id = u.id
					LEFT JOIN
						email_notifications en
					ON en.user_id = u.id and en.notification_type = 'riskcenter' and en.personlization = 'risk_deleted'
				    where u.id = $user_id
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		return $result;
	}

	function work_availability(){
		$user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
					IFNULL((
				        SELECT
				            (CASE
				             WHEN WEEKDAY(CURDATE()) = 0 THEN IFNULL(ua1.monday,0)
				             WHEN WEEKDAY(CURDATE()) = 1 THEN IFNULL(ua1.tuesday,0)
				             WHEN WEEKDAY(CURDATE()) = 2 THEN IFNULL(ua1.wednesday,0)
				             WHEN WEEKDAY(CURDATE()) = 3 THEN IFNULL(ua1.thursday,0)
				             WHEN WEEKDAY(CURDATE()) = 4 THEN IFNULL(ua1.friday,0)
				             WHEN WEEKDAY(CURDATE()) = 5 THEN IFNULL(ua1.saturday,0)
				             WHEN WEEKDAY(CURDATE()) = 6 THEN IFNULL(ua1.sunday,0)
				             ELSE 0 END ) AS user_hr_day
				        FROM
				        	user_availabilities ua1
				        LEFT JOIN user_availabilities ua2 ON
				        	DATE(ua2.effective) <= CURDATE()
				        	AND ua1.user_id = ua2.user_id
				        	AND (ua1.effective < ua2.effective)
				        WHERE
				        	ua1.user_id = $user_id
				        	AND DATE(ua1.effective) <= CURDATE()
				       		AND ua2.effective IS NULL
				    ), 0) AS user_hr_day

			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result);
		return $result[0][0]['user_hr_day'];
	}

	function wsp_progress_bar($project_id = null, $workspace_id = null){
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
				    (CASE
						WHEN ISNULL(wse.escost) AND ISNULL(wse.spcost) THEN 'Not Set'
						WHEN ISNULL(wse.spcost) AND (wse.escost > 0) THEN 'Budget Set'
						WHEN ISNULL(wse.escost) AND (wse.spcost > 0) THEN 'Costs Incurred'
						WHEN (wse.spcost > wse.escost) AND (wse.spcost > 0) AND (wse.escost > 0) THEN 'Over Budget'
						WHEN (wse.spcost <= wse.escost) AND (wse.spcost > 0) AND (wse.escost > 0) THEN 'On Budget'
						ELSE 'None' #never
					 END) AS cost_status,
				    #risks
				    wrh.high_risk_total,
				    wrs.severe_risk_total,
				    wrh_off.high_risk_total_off,
				    wrs_off.severe_risk_total_off,
				    risk_counts.risk_count,
					/*wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,*/
				    efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.none_remaining_hours,
					#ef.remaining_hours_color,
					efforts.change_hours,
					wlevel.confidence_level,
					wlevel.confidence_class,
					wlevel.confidence_arrow,
					wlevel.level,
					wlevel.level_count,
					wlevel.wlevel_id
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
				        up.workspace_id = $workspace_id AND
				        up.area_id IS NULL
					group by up.project_id
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
				        	up.project_id,
				            up.user_id,
				            up.role
				        FROM
				            user_permissions up
				        WHERE
				            up.project_id = $project_id AND
				            up.workspace_id  = $workspace_id AND
				            up.area_id IS NULL
				        GROUP BY  up.user_id
				    ) AS wur #workspace user roles
				    GROUP BY wur.project_id
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
				        up.user_id = $user_id
				        AND up.project_id = $project_id
				        AND up.workspace_id = $workspace_id
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
				    	up.user_id = $user_id
						AND up.workspace_id = $workspace_id
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
				            up.user_id = $user_id
							AND up.workspace_id = $workspace_id
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
				            up.user_id = $user_id
							AND up.workspace_id = $workspace_id
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
							AND up.workspace_id = $workspace_id
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
				pwd.ws_id = wlevel.wlevel_id



				left join (
					SELECT
						ws.workspace_id as workspace_id,
						ef.total_hours,
						ef.blue_completed_hours,
						ef.green_remaining_hours,
						ef.amber_remaining_hours,
						ef.red_remaining_hours,
						ef.none_remaining_hours,
						ef.change_hours
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
						 up.workspace_id = $workspace_id AND
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
						AND up.workspace_id = $workspace_id
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
						AND up.workspace_id = $workspace_id
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				            OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wrs ON #workspace risk severe
					pwd.ws_id = wrs.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get high pending risk counts
				    SELECT
				        up.workspace_id AS ws_id,
				        count(DISTINCT(rd.id)) AS high_risk_total_off
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
						AND up.workspace_id = $workspace_id
				        AND rd.status IN (3) #sign off
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wrh_off ON #workspace risk high sign_off
					pwd.ws_id = wrh_off.ws_id
				LEFT JOIN
				(
				    #--------------------------------------------
				    #get severe pending risk counts
				    SELECT
				        up.workspace_id AS ws_id,
				        COUNT(DISTINCT(rd.id)) AS severe_risk_total_off
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
						AND up.workspace_id = $workspace_id
				        AND rd.status IN (3) #sign off
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				            OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.workspace_id
					#--------------------------------------------
				) AS wrs_off ON #workspace risk severe sign_off
					pwd.ws_id = wrs_off.ws_id

				LEFT JOIN (
				    SELECT
				        up.workspace_id AS ws_id,
				        COUNT(DISTINCT(rd.id)) AS risk_count
				    FROM user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    WHERE
				        up.user_id = 20
				        AND up.project_id = 75
				        AND up.workspace_id = 93
				        AND up.element_id IS NOT NULL
				        AND rd.id IS NOT NULL
				    GROUP BY up.workspace_id

				) AS risk_counts
				ON risk_counts.ws_id = pwd.ws_id

				ORDER BY pwd.sort_order ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		// pr($result);
		return $result;
	}

	function task_progress_bar($project_id = null, $task_id = null){
		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					#details
				    edata.pid,
				    edata.ws_id,
				    edata.el_id,
				    edata.role,
				    edata.title,
				    edata.start_date,
				    edata.end_date,
				    edata.sign_off,
				    edata.sign_off_date,
				    edata.color_code,
				    edata.date_constraints,
				    edata.el_status,
			        edata.permit_edit,
			        edata.permit_read,
			        edata.permit_delete,
			        edata.permit_add,
				    #team
				    etc.owner_count,
				    etc.sharer_count,
				    #assets
				    (e_assets.links + e_assets.notes + e_assets.docs + e_assets.mms + e_decision.dc_prg + e_decision.dc_cmp + e_fb.fb_pnd + e_fb.fb_prg + e_fb.fb_ovd + e_fb.fb_cmp + e_vote.vt_pnd + e_vote.vt_prg + e_vote.vt_ovd + e_vote.vt_cmp) AS as_tot,
				    e_assets.links,
				    e_assets.notes,
				    e_assets.docs,
				    e_assets.mms,
				    (e_decision.dc_prg + e_decision.dc_cmp) AS dc_tot,
				    e_decision.dc_prg,
				    e_decision.dc_cmp,
				    (e_fb.fb_pnd + e_fb.fb_prg + e_fb.fb_ovd + e_fb.fb_cmp) AS fb_tot,
				    e_fb.fb_pnd,
				    e_fb.fb_prg,
				    e_fb.fb_ovd,
				    e_fb.fb_cmp,
				    (e_vote.vt_pnd + e_vote.vt_prg + e_vote.vt_ovd + e_vote.vt_cmp) AS vt_tot,
				    e_vote.vt_pnd,
				    e_vote.vt_prg,
				   	e_vote.vt_ovd,
				    e_vote.vt_cmp,
				    #costs
					edata.sign,
				    e_cost.spcost,
				    e_cost.escost,
				    (CASE
				        WHEN (e_cost.escost <= 0 OR ISNULL(e_cost.escost)) AND (e_cost.spcost <= 0 OR ISNULL(e_cost.spcost)) THEN 'Not Set'
				        WHEN e_cost.escost > 0 AND (e_cost.spcost <= 0 OR ISNULL(e_cost.spcost)) THEN 'Budget Set'
				        WHEN e_cost.spcost > 0 AND (e_cost.escost <= 0 OR ISNULL(e_cost.escost)) THEN 'Costs Incurred'
				        WHEN e_cost.escost > 0 AND e_cost.spcost > 0 AND e_cost.spcost > e_cost.escost THEN 'Over Budget'
				        WHEN e_cost.escost > 0 AND e_cost.spcost > 0 AND e_cost.spcost <= e_cost.escost THEN 'On Budget'
				        ELSE 'None'
				     END) AS cost_status,
				    #risks
				    e_hrisk.risk_total,
				    e_srisk.risk_total,
				    e_hrisk_off.risk_total,
				    e_srisk_off.risk_total,

					e_levels.confidence_level,
					e_levels.confidence_class,
					e_levels.confidence_arrow,
					e_levels.level,
					e_levels.level_count,

				    efforts.total_hours,
					efforts.blue_completed_hours,
					efforts.green_remaining_hours,
					efforts.amber_remaining_hours,
					efforts.red_remaining_hours,
					efforts.change_hours,
					efforts.remaining_hours,
					efforts.remaining_hours_color,

					risk_counts.risk_count,

					assigned.assigned_to,
					assigned.reaction,
					assigned.created_by,
					assigned.assign_status,
					assigned.assigned_user,
					assigned.asi_org,
					assigned.asi_profile,
					assigned.asi_job,
					assigned.created_user,
					assigned.cre_org,
					assigned.cre_profile,
					assigned.cre_job

				FROM
				(
				    SELECT
				        up.project_id AS pid,
				        up.workspace_id AS ws_id,
				        up.element_id AS el_id,
				        el.title,
				        el.start_date,
				        el.end_date,
				        el.date_constraints,
				        up.role,
				        CASE
				            WHEN (el.date_constraints=0) THEN 'NON'
				            WHEN (DATE(NOW()) < DATE(el.start_date) and el.sign_off!=1 and el.date_constraints=1) THEN 'PND'
				            WHEN (DATE(NOW()) BETWEEN DATE(el.start_date) AND DATE(el.end_date) and el.sign_off!=1 and el.date_constraints=1 ) THEN 'PRG'
				            WHEN (DATE(el.end_date) < DATE(NOW()) and el.sign_off!=1 and el.date_constraints=1) THEN 'OVD'
				            WHEN (el.sign_off=1) THEN 'CMP'
				            ELSE 'NON'
				        END AS el_status,
				        el.sign_off,
				        el.sign_off_date,
				        el.color_code,
				        c.sign,
				        CASE
				             WHEN (up.role='Creator' OR up.role='Owner' OR up.role='Group Owner') THEN 'owner'
				             WHEN (up.role='Sharer' OR up.role='Group Sharer' ) THEN 'sharer'
				             ELSE 'nonw'
				        END AS el_type,
				        up.permit_edit,
				        up.permit_read,
				        up.permit_delete,
				        up.permit_add
				    FROM
				        user_permissions up
				    LEFT JOIN projects p ON
				        up.project_id = p.id
				    LEFT JOIN currencies c ON
				        p.currency_id = c.id
				    LEFT JOIN elements el ON
				        up.element_id = el.id
				    WHERE
				        up.user_id = $user_id AND
				        up.element_id = $task_id
				    GROUP BY up.element_id
				) AS edata

				# TEAM COUNT
				LEFT JOIN
				(
				    SELECT
				        eteam.el_id,
				        SUM(if(eteam.role = 'Creator' OR eteam.role = 'Owner' OR eteam.role = 'Group Owner', 1, 0)) AS owner_count,
				        SUM(if(eteam.role = 'Sharer' OR eteam.role ='Group Sharer',1,0)) AS sharer_count
				    FROM
				    (
				        SELECT
				            up.element_id AS el_id,
				            up.project_id,
				            up.role
				        FROM
				            user_permissions up
				        WHERE
				            up.project_id = $project_id AND
				            up.element_id  = $task_id
				        GROUP BY  up.user_id
				    ) AS eteam
				    GROUP BY eteam.project_id
				    #-------------------------------------------
				) AS etc ON edata.el_id = etc.el_id

				#LINKS, NOTES, DOCUMENTS, MIND MAP COUNTS
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS el_id,
				        COUNT(DISTINCT el.id) AS links,
				        COUNT(DISTINCT en.id) AS notes,
				        COUNT(DISTINCT ed.id) AS docs,
				        COUNT(DISTINCT em.id) AS mms
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
				        up.user_id = $user_id
				        AND up.element_id = $task_id
				    GROUP BY up.element_id
				) AS e_assets ON edata.el_id = e_assets.el_id

				# DECISION COUNTS
				LEFT JOIN
				(
				    SELECT
				        decision.el_id,
				        COUNT(IF(decision.dc_status = 'In Progress', 1, NULL)) AS dc_prg,
				        COUNT(IF(decision.dc_status = 'Completed', 1, NULL)) AS dc_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS el_id,
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
				            up.user_id = $user_id
				            AND up.element_id = $task_id
				    ) AS decision
				    GROUP BY decision.el_id
				) AS e_decision ON
				    edata.el_id = e_decision.el_id

				# FEEDBACK COUNTS
				LEFT JOIN
				(
				    SELECT
				        efb.el_id,
				        COUNT(IF(efb.fb_status = 'Not Started', 1, NULL)) AS fb_pnd,
				        COUNT(IF(efb.fb_status = 'In Progress', 1, NULL)) AS fb_prg,
				        COUNT(IF(efb.fb_status = 'Overdue', 1, NULL)) AS fb_ovd,
				        COUNT(IF(efb.fb_status = 'Completed', 1, NULL)) AS fb_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS el_id,
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
				            up.user_id = $user_id
				            AND up.element_id = $task_id
				    ) AS efb
				    GROUP BY efb.el_id
				) AS e_fb ON edata.el_id = e_fb.el_id

				# VOTE COUNTS
				LEFT JOIN
				(
				    SELECT
				        ev.el_id,
				        COUNT(IF(ev.vt_status = 'Not Started', 1, NULL)) AS vt_pnd,
				        COUNT(IF(ev.vt_status = 'In Progress', 1, NULL)) AS vt_prg,
				        COUNT(IF(ev.vt_status = 'Overdue', 1, NULL)) AS vt_ovd,
				        COUNT(IF(ev.vt_status = 'Completed', 1, NULL)) AS vt_cmp
				    FROM
				    (
				        SELECT
				            up.element_id AS el_id,
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
				            up.user_id = $user_id
				            AND up.element_id = $task_id
				    ) AS ev
				    GROUP BY ev.el_id
				) AS e_vote ON edata.el_id = e_vote.el_id

				# LEVEL COUNTS
				LEFT JOIN(
				    SELECT
				        elvls.el_id as el_id,
				        elvls.level_current as level,
				        elvls.level_count as level_count,
				        elvls.confidence_class as confidence_class,
				        elvls.confidence_level as confidence_level,
				        elvls.confidence_arrow as confidence_arrow

				    FROM
				    (
				        SELECT
				            DISTINCT(el.element_id) AS el_id,
				            el.level AS level_current,
				            COUNT(el.level) as level_count,
				            el.comment,
				            (CASE
				                WHEN (el.level >= 1 && el.level <= 24) THEN 'Low'
				                WHEN (el.level >= 25 && el.level <= 49) THEN 'Medium Low'
				                WHEN (el.level >= 50 && el.level <= 74) THEN 'Medium High'
				                WHEN (el.level >= 75 && el.level <= 100) THEN 'High'
				                ELSE ''
				            END) AS confidence_level,
				            (CASE
				                WHEN (el.level >= 1 && el.level <= 24) THEN 'red'
				                WHEN (el.level >= 25 && el.level <= 49) THEN 'orange'
				                WHEN (el.level >= 50 && el.level <= 74) THEN 'yellow'
				                WHEN (el.level >= 75 && el.level <= 100) THEN 'green-bg'
				                ELSE ''
				            END) AS confidence_class,
				            (CASE
				                WHEN (el.level >= 1 && el.level <= 24) THEN 'lowgrey'
				                WHEN (el.level >= 25 && el.level <= 49) THEN 'mediumlowgrey'
				                WHEN (el.level >= 50 && el.level <= 74) THEN 'mediumhighgrey'
				                WHEN (el.level >= 75 && el.level <= 100) THEN 'highgrey'
				                ELSE ''
				            END) AS confidence_arrow
				        FROM element_levels el

				        WHERE
				            el.element_id = $task_id AND
				            el.is_active = 1
				        GROUP BY el.element_id
				    ) AS elvls
				) AS e_levels ON edata.el_id = e_levels.el_id

				# EFFORTS

				LEFT JOIN (
				    SELECT
				        es.id as el_id,
				        ef.total_hours,
				        ef.blue_completed_hours,
				        ef.green_remaining_hours,
				        ef.amber_remaining_hours,
				        ef.red_remaining_hours,
				        ef.change_hours,
				        ef.remaining_hours_color,
				        ef.remaining_hours
				    FROM
				    (
				        SELECT id FROM elements
				    ) AS es
				    LEFT JOIN
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
				        es.id = ef.element_id
				)   as efforts ON edata.el_id = efforts.el_id

				# ELEMENT COST
				LEFT JOIN
				(
				    SELECT
				    	up.element_id AS el_id,
				        SUM(ec.spend_cost) AS spcost,
				        SUM(ec.estimated_cost) AS escost
				    FROM
						user_permissions up
				    LEFT JOIN element_costs ec ON
				    	up.element_id = ec.element_id
					WHERE
				    	up.user_id = $user_id AND
				        up.project_id = $project_id AND
						up.element_id = $task_id
				    GROUP BY up.element_id
				) AS e_cost ON edata.el_id = e_cost.el_id

				# HiGH RiSK
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS el_id,
				        count(DISTINCT(rd.id)) AS risk_total
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
				        AND up.element_id IS NOT NULL
						AND up.element_id = $task_id
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				                OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				                OR (rr.impact = 4 AND rr.percentage = 4)
				                OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.element_id
				) AS e_hrisk ON edata.el_id = e_hrisk.el_id

				# SEVER RiSK
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS el_id,
				        COUNT(DISTINCT(rd.id)) AS risk_total
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
				        AND up.element_id = $task_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (1, 2, 4) #open, review, overdue
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				                OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.element_id
				) AS e_srisk ON edata.el_id = e_srisk.el_id

				# HiGH PENDiNG RiSK
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS el_id,
				        count(DISTINCT(rd.id)) AS risk_total
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
				        AND up.element_id = $task_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (3) #sign off
				        AND (
				            (rr.impact = 2 AND rr.percentage = 5)
				            OR (rr.impact = 3 AND (rr.percentage = 5 OR rr.percentage = 4))
				            OR (rr.impact = 4 AND rr.percentage = 4)
				            OR (rr.impact = 5 AND rr.percentage = 3)
				            )
				    GROUP BY up.element_id
				) AS e_hrisk_off ON	edata.el_id = e_hrisk_off.el_id

				# SEVER RiSK PENDiNG
				LEFT JOIN
				(
				    SELECT
				        up.element_id AS el_id,
				        COUNT(DISTINCT(rd.id)) AS risk_total
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
				        AND up.element_id = $task_id
				        AND up.element_id IS NOT NULL
				        AND rd.status IN (3) #sign off
				        AND (
				            (rr.impact = 4 AND rr.percentage = 5)
				            OR (rr.impact = 5 AND (rr.percentage = 5 OR rr.percentage = 4))
				            )
				    GROUP BY up.element_id
				) AS e_srisk_off ON edata.el_id = e_hrisk_off.el_id


				LEFT JOIN (
				    SELECT
				        up.element_id AS el_id,
				        COUNT(DISTINCT(rd.id)) AS risk_count
				    FROM user_permissions up
				    INNER JOIN rm_elements re ON
				        up.element_id = re.element_id
				    LEFT JOIN rm_details rd ON
				        re.rm_detail_id = rd.id
				    WHERE
				        up.user_id = $user_id
				        AND up.project_id = $project_id
				        AND up.element_id = $task_id
				        AND up.element_id IS NOT NULL
				        AND rd.id IS NOT NULL
				    GROUP BY up.element_id

				) AS risk_counts ON risk_counts.el_id = edata.el_id

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
						CONCAT_WS(' ',asi.first_name , asi.last_name) AS assigned_user,
						asi.organization_id AS asi_org,
						asi.profile_pic AS asi_profile,
						asi.job_title AS asi_job,
						CONCAT_WS(' ',cre.first_name , cre.last_name) AS created_user,
						cre.organization_id AS cre_org,
						cre.profile_pic AS cre_profile,
						cre.job_title AS cre_job
					FROM
						user_permissions up
					LEFT JOIN
						element_assignments ea
						ON ea.element_id = up.element_id
					LEFT JOIN
						user_details asi
						ON asi.user_id = ea.assigned_to
					LEFT JOIN
						user_details cre
						ON cre.user_id = ea.created_by
					WHERE
				        up.project_id = $project_id AND
				        up.element_id IS NOT NULL
			        GROUP BY up.element_id
				) AS assigned ON assigned.eid = edata.el_id

				ORDER BY edata.title ASC
			";
		$result =  ClassRegistry::init('UserPermission')->query($query);
		//
		return $result;
	}

}
