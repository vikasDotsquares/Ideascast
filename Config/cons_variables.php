<?php
/*********************************** SITE CONSTANTS **************************************************/

/*if($_SERVER['HTTP_HOST'] == '192.168.7.20' ){
	if($_SERVER['REMOTE_ADDR'] != '192.168.4.218'){
		define("PROTOCOL", 'https://'); // website protocol
	}
	else{
		define("PROTOCOL", 'http://'); // website protocol
	}
}
else{
}*/
define("PROTOCOL", 'http://'); // website protocol
$hostname = $_SERVER['HTTP_HOST'];

if ($hostname == 'localhost' || $hostname == '192.168.7.20' ) {
	$whatINeed = explode('/', $_SERVER['REQUEST_URI']);
	$whatINeed = $whatINeed[1];
	define('SITEURL', PROTOCOL . $hostname . '/' . $whatINeed . '/');
	define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . "/ideascast/app/webroot/");

	//date_default_timezone_set('UTC');
	//echo date('Y-m-d H:i');
	define('MMURL', PROTOCOL . $hostname . '' . ':3008');
} else {
	define('SITEURL', 'https://jeera.ideascast.com/');

	define('DOC_ROOT', "/app/webroot/");
	define('MMURL', 'https://' . $hostname . ':8888');

	date_default_timezone_set("Etc/GMT-1");
}

// from constant setting
if ($hostname == '192.168.7.20' ) {
	define("SEARCH_SERVERNAME", "localhost");
	define("SEARCH_USERNAME", "root");
	define("SEARCH_PASSWORD", "dT9Qa@$2ET");
	define("SEARCH_DB", "ideascast");

	define("MONGO_CONNECT", "mongodb://ideascast:4XXkybCF7r@127.0.0.1:27017");
	// define("MONGO_CONNECT", "mongodb://127.0.0.1:27017");
	define("MONGO_DATABASE", 'ideascast');

	define('DOC_CHAT_URL', "/home/ideascast/prod/chatMM/ideascastchat_jeera/");
	// define('CHATURL', 'http://192.168.4.207:3007');
	// define('SOCKETURL', 'http://192.168.4.207:3001');
	define('CHATURL', PROTOCOL.'192.168.7.20:3001');
	define('SOCKETURL', CHATURL);

} else {
	define("SEARCH_SERVERNAME", "localhost");
	define("SEARCH_USERNAME", "ideascas_jeeraN");
	define("SEARCH_PASSWORD", "tiVO5([?3xDN");
	define("SEARCH_DB", "ideascas_jeera");

	define("MONGO_CONNECT", "mongodb://ideascast:#OpIh!t~6@127.0.0.1:27019");
	define("MONGO_DATABASE", 'ideascast');

	define('DOC_CHAT_URL', "/home/ideascast/prod/chatMM/ideascastchat_jeera/");
	define('CHATURL', '');
}

define("CHAT_ENABLED", true); // Chat On/Off (this will not stop notifications)
define("SOCKET_MESSAGES", true); // To stop notification

/* End Chat and Notifications */

/*Gate enabled and and disabled for Task center and Update element page*/
define("GATE_ENABLED", true);



define("UA", true);

// from constant setting

define("WEBDOMAIN", '.opusview.com');
define("SERVER_NAME", 'server1.opusview.com');
define("WEBDOMAIN_HOST", 'localhost');

define("ORG_SETUP", false);
define("LOCALIP", '192.168.7.20');
define("LOCAL_DIR", 'ideascast');

define('MAINTENANCE', 0);
define('PHP_VERSIONS', 7);
define('PROCEDURE_MODE', 1);
define('CHAT_CLOUD', 'no');
define('DOMAIN_LAST_PORT', 41001);
define('MMPORT_VALUE_LAST', 45001);

define('OPUSVIEW', '172.31.25.66');
define('OPUSVIEW_DEV', '172.31.26.121');
define('OPUSVIEW_CLOUD', '172.31.15.21');
define('FUTURE_DATE', 'on');

define('DOC_VENDOR', "/app/vendor/");
define('ADMIN_EMAIL', 'info@ideascast.com');
define('ADMIN_FROM_EMAIL', 'message-noreply@ideascast.com');
define('DOMAINPREFIX', 'ideascas_');
define('DOMAIN_ALISE', 'ideascas_');
define('DOMAIN_PREFIX', '.ideascast.com');

define('MINDMAP_SERVER', 'http://111.93.41.194:8888/');

define("ADMIN_DATE_FORMAT", 'M jS, Y');
define("FRONT_DATE_FORMAT", '%d-%m-%Y');
define("TIME_ZONE", 'Asia/Kolkata');

define('PER_PAGE', '1');
define('ADMIN_PAGING', '10');
define('FRONT_PAGING', '10');
define('PROJECT_STATUS', serialize(array("proposed" => "Proposed", "active" => "Active", "completed" => "Completed")));
define('SHOW_PER_PAGE', serialize(array("10" => "10", "20" => "20", "30" => "30", "40" => "40", "50" => "50", "100" => "100", "150" => "150", "200" => "200")));
define("COUNTRY_CODE", '2');
define("ORG_CHART", serialize(array("parent" => "Parent", "subsidiary" => "Subsidiary")));
define("OWNERSHIP", serialize(array("private" => "Private", "public" => "Public")));

define("ACTIVE", '1');
define("DEACTIVE", '0');
define("SITENAME", "OpusView");
define("MAIL_SITENAME", "OpusView");
define("SITE_ADMIN", "1");
define("SITE_USER", "2");

/*********************************** IMAGE UPLOAD CONSTANTS **************************************************/
define("UPLOAD", 'uploads/');
define("DATA_FILEPATH", UPLOAD . 'datafiles/');
define("USER_PIC_PATH", UPLOAD . 'user_images/');
define("THIRD_PARTY_USER_PATH", UPLOAD . 'thirdy_party_user/');
define("POST_PIC_PATH", UPLOAD . 'postimages/');
define("ANNOUNCEMENT_FILE_PATH", UPLOAD . 'announcement/');
define("POST_RESIZE_PIC_PATH", UPLOAD . 'postimages/resize/');
define("POST_RESIZE_SHOW_PATH", SITEURL . UPLOAD . 'postimages/resize/');
define("TODO", UPLOAD . 'dolist_uploads/');
define("TODOCOMMENT", UPLOAD . 'dolist_comments/');
define("SKETCH_DOCUMENT", UPLOAD . 'sketch_document/');

define("ELEMENT_DOCUMENT_PATH", UPLOAD . 'element_documents/');
define("SKILL_IMAGE_PATH", UPLOAD . 'skill_images/');
define("SKILL_FILE_PATH", UPLOAD . 'skill_images/files/');
define("SUBJECT_IMAGE_PATH", UPLOAD . 'subject_images/');
define("SUBJECT_FILE_PATH", UPLOAD . 'subject_images/files/');
define("DOMAIN_IMAGE_PATH", UPLOAD . 'domain_images/');
define("DOMAIN_FILE_PATH", UPLOAD . 'domain_images/files/');
define("IMAGE_TEMP_PATH", UPLOAD . 'competency_temp_files/');

define("COMM_IMAGE_PATH", UPLOAD . 'dept_images/');

define("LOC_IMAGE_PATH", UPLOAD . 'loc_images/');
define("LOC_FILE_PATH", UPLOAD . 'loc_images/files/');
define("LOC_TEMP_PATH", UPLOAD . 'dept_temp_files/');

define("ORG_IMAGE_PATH", UPLOAD . 'org_images/');
define("ORG_FILE_PATH", UPLOAD . 'org_images/files/');

define("STORY_TEMP_PATH", UPLOAD . 'story_temp_files/');
define("STORY_IMAGE_PATH", UPLOAD . 'story_images/');
define("STORY_FILE_PATH", UPLOAD . 'story_images/files/');

define("PROJECT_DOC_PATH", UPLOAD . 'project_documents/');

define("ELEMENT_SIGNOFF_PATH", UPLOAD . 'element_documents/signoff/');
define("PROJECT_SIGNOFF_PATH", UPLOAD . 'project/signoff/');
define("WORKSPACE_SIGNOFF_PATH", UPLOAD . 'project/signoff/');


define("SKILL_PDF_PATH", UPLOAD . 'skill/');
define("TEMPLATES_MOVE_IMAGE", UPLOAD . 'template_move/');
define("ELEMENT_NOTES_TEMP_PATH", UPLOAD . 'element_notes_temp/');
define("ELEMENT_FEEDBACK_IMAGE_PATH", UPLOAD . 'element_feedback_images/');

define("PROJECT_IMAGE_PATH", UPLOAD . 'project/');
define("HOME_SLIDER_PATH", UPLOAD . 'home_slider/');

define("DO_LIST_UPLOAD", UPLOAD . 'dolist_uploads/');
define("DO_LIST_COMMENT", UPLOAD . 'dolist_comments/');
define("DO_LIST_BLOG_DOCUMENTS", UPLOAD . 'blogdocuments/');

define("TEMPLATE_DOCUMENTS", UPLOAD . 'template_element_document/');

define("WIKI_PAGE_DOCUMENT", UPLOAD . 'wiki_page_document/');


define("STATUS_NOT_SPACIFIED", "Not Specified");
define("STATUS_NOT_STARTED", "Not Started");
define("STATUS_PROGRESS", "In Progress");
define("STATUS_OVERDUE", "Overdue");
define("STATUS_COMPLETED", "Completed");


/*********************************** LIVE SITE CONSTANTS **************************************************/
define("LIVE_SETTING", true);

// template categories icon path
define("TEMP_CAT_ICON", 'images/template-categories/');

if( PHP_VERSIONS  == 7 ){
	$emptymsg = 'notBlank';
} else {
	$emptymsg = 'notEmpty';
}
define("EMPTY_MSG", $emptymsg);
define("HOST_DIR", '/public_html/');

define('CHAT_VERSION', 'new');

define('TASK_CENTERS', SITEURL."dashboards/task_centers/");
//define('TASK_CENTERS', SITEURL."dashboards/task_center/");


/* 20-8-2020 */
define("SUBJECT_PDF_PATH", UPLOAD . 'subjects/');
define("DOMAIN_PDF_PATH", UPLOAD . 'knowledge_domains/');
