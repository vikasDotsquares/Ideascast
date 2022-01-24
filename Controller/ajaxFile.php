<?php 
include("includes/config.php");
include("includes/sessionCheck.php");
if(isset($_POST) && isset($_POST['doAction']) && $_POST['doAction'] == 'getCourseEventStartTime'){
	$get_course_events = $db->query("SELECT course_event_id, event_start_time FROM course_event_dates LEFT JOIN course_events ON course_events.id = course_event_dates.course_event_id WHERE course_event_dates.event_date = :0 AND course_events.location_id = :1 AND course_events.course_id = :2", array(date('Y-m-d', strtotime($_POST['selDate'])), $_POST['loc_id'], $_POST['course_id']));
	$resp['status'] = 0;
	$resp['data'] = array();
	if(!empty($get_course_events) && count($get_course_events) > 0){
		//$ce_id = $get_course_events['ce_id'];
		$courseEvents = $tempArr = array();
		foreach($get_course_events as $k => $v) {
			$courseEvents[] = $v['course_event_id'];
			$tempArr[$v['course_event_id']] = $v['event_start_time'];
		}
		$ce_id = implode(',', $courseEvents);
		if(count($courseEvents) == 1) {
			$resp['status'] = 1;
			$resp['data']['course_event_id'] = $get_course_events[0]['course_event_id'];
			$resp['data']['course_start_time'] = $get_course_events[0]['event_start_time'];
		} else {
			$attendee = rtrim(str_replace("'", "\'", $_POST['attendee']), '_0');
			$getAttendeeSQL = "SELECT user_id, CONCAT(first_name,' ',sur_name) as attendee_name, license_number, vehicle_type, course_event_id FROM bookings LEFT JOIN booking_attendees ON bookings.id = booking_attendees.booking_id WHERE bookings.course_event_id IN ($ce_id) AND bookings.status = 1 AND CONCAT(first_name, ' ', sur_name) = '".$attendee."' LIMIT 0,1";
			$getAttendee = $db->row($getAttendeeSQL);
			
			$resp['status'] = 1;
			$resp['data']['course_event_id'] = $getAttendee['course_event_id'];
			$resp['data']['course_start_time'] = $tempArr[$getAttendee['course_event_id']];
		}
		echo json_encode($resp);die;
	}
	echo json_encode($resp);die;
}
if(isset($_POST) && isset($_POST['doAction']) && $_POST['doAction'] == 'getAttendeeForDLReturn'){
	$get_course_events = $db->query("SELECT course_event_id, event_start_time FROM course_event_dates LEFT JOIN course_events ON course_events.id = course_event_dates.course_event_id WHERE course_event_dates.event_date = :0 AND course_events.location_id = :1 AND course_events.course_id = :2", array(date('Y-m-d', strtotime($_POST['selDate'])), $_POST['loc_id'], $_POST['course_id']));
	$resp['status'] = 0;
	$resp['data']['attendees'] = $resp['data']['course_events'] = array();
	if(!empty($get_course_events) && count($get_course_events) > 0){
		//$ce_id = $get_course_events['ce_id'];
		$courseEvents = array();
		foreach($get_course_events as $k => $v) {
			$courseEvents[] = $v['course_event_id'];
		}
		$ce_id = implode(',', $courseEvents);
		$getAttendeeSQL = "SELECT user_id, CONCAT(first_name,' ',sur_name) as attendee_name, license_number, vehicle_type, booking_attendees.id as attendee_id FROM bookings LEFT JOIN booking_attendees ON bookings.id = booking_attendees.booking_id WHERE bookings.course_event_id IN ($ce_id) AND bookings.status = 1";
		$getAttendee = $db->query($getAttendeeSQL);
		
		$resp['status'] = 1;
		$resp['data']['attendees'] = $getAttendee;
		$resp['data']['course_events'] = $get_course_events;
		
		echo json_encode($resp);die;
	}
	echo json_encode($resp);die;
}

if(isset($_POST) && isset($_POST['doAction']) && $_POST['doAction'] == 'getCourses'){
	$get_courses = $db->query("SELECT courses.id, course_name FROM courses LEFT JOIN course_events ON courses.id = course_events.course_id WHERE courses.is_cbt = 1 AND course_events.status = '1' AND courses.status IN ('1', '2') AND course_events.location_id = :0 GROUP BY courses.id", array($_POST['loc_id']));
	$resp['status'] = 0;
	$resp['data'] = array();
	if(!empty($get_courses) && count($get_courses) > 0){
		$resp['status'] = 1;
		$resp['data'] = $get_courses;
		
		echo json_encode($resp);die;
	}
	echo json_encode($resp);die;
}

if(isset($_POST) && isset($_POST['doAction']) && $_POST['doAction'] == 'checkBookNo'){
	if($_POST['mode'] == 'add') {
		$getBookdata = $db->row("SELECT COUNT(*) as cnt FROM dl_returns WHERE book_no = :0", array($_POST['book_no']));
	} else if($_POST['mode'] == 'edit') {
		$getBookdata = $db->row("SELECT COUNT(*) as cnt FROM dl_returns WHERE book_no = :0 AND id != :1", array($_POST['book_no'], $_POST['id']));
	}
	$resp['status'] = 0;
	$resp['data'] = array();
	if(!empty($getBookdata) && count($getBookdata) > 0){
		$resp['status'] = 1;
		$resp['data'] = $getBookdata;
		
		echo json_encode($resp);die;
	}
	echo json_encode($resp);die;
}

?>