<?php

App::uses('Helper', 'View');
App::import("Model", "User");
App::import("Model", "UserProject");
App::import("Model", "Project");
App::import("Model", "Element");
App::import("Model", "ElementPermission");
App::import("Model", 'Decision');
App::import("Model", 'ElementDecision');
App::import("Model", 'ElementDecisionDetail');
App::import("Model", 'ElementDocument');
App::import("Model", 'ElementFeedback');
App::import("Model", 'ElementFeedbackDetail');
App::import("Model", 'Feedback');
App::import("Model", 'FeedbackAttachment');
App::import("Model", 'FeedbackResult');
App::import("Model", 'ElementLink');
App::import("Model", 'ElementNote');
App::import("Model", 'Vote');
App::import("Model", 'TemplateRelation');

App::import("Model", 'DoList');
App::import("Model", 'DoListUser');
App::import("Model", 'DoListComment');
App::import("Model", 'DoListCommentUpload');
App::import("Model", 'Blog');
App::import("Model", 'BlogComment');
App::import("Model", 'BlogDocument');
App::import("Model", 'Wiki');
App::import("Model", 'WikiPage');
App::import("Model", 'WikiPageComment');
App::import("Model", 'WikiPageCommentDocument');

class SearchHelper extends Helper {

    var $helpers = array(
        'Html',
        'Session',
        'ViewModel',
        'Wiki',
        'Common',
        'Group'
    );
    protected $_user;
    public $exclude_fields;

    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);

        $this->_user = new User ();
        $this->_project = new Project ();
        $this->UserProject = new UserProject ();
        $this->elements = new Element ();
        $this->element_permissions = new ElementPermission ();
        $this->ElementDecision = new ElementDecision ();
        $this->ElementDecisionDetail = new ElementDecisionDetail ();
        $this->ElementDocument = new ElementDocument ();
        $this->Feedback = new Feedback ();
        $this->FeedbackAttachment = new FeedbackAttachment ();
        $this->FeedbackResult = new FeedbackResult ();
        $this->ElementLink = new ElementLink ();
        $this->ElementNote = new ElementNote ();
        $this->Vote = new Vote ();
        $this->TemplateRelation = new TemplateRelation ();

        $this->DoList = new DoList ();
        $this->DoListUser = new DoListUser ();
        $this->DoListComment = new DoListComment ();
        $this->DoListCommentUpload = new DoListCommentUpload ();
        $this->Blog = new Blog ();
        $this->BlogComment = new BlogComment ();
        $this->BlogDocument = new BlogDocument ();
        $this->Wiki = new Wiki ();
        $this->WikiPage = new WikiPage ();
        $this->WikiPageComment = new WikiPageComment ();
        $this->WikiPageCommentDocument = new WikiPageCommentDocument ();

        $this->exclude_fields = ['created', 'modified', 'start_date', 'end_date', 'status', 'archieved_on'];
    }

    public function users_list($id = null) {
        $this->_user->virtualFields = array(
            'name' => 'CONCAT(UserDetail.first_name, " ", UserDetail.last_name)'
        );

        $conditions ["User.role_id"] = 2;
        $conditions ["User.status"] = 1;
        if (isset($id) && !empty($id)) {
            $conditions ["User.id"] = $id;
        }

        $data = $this->_user->find("all", array(
            "conditions" => $conditions,
            "fields" => array(
                "User.id",
                "User.name"
            ),
            "recursive" => 0
        ));

        return isset($data) ? $data : false;
    }

    public function showResultAsLeft($resultArray, $keyword) {
        $response = [
            'tab_one' => null,
            'tab_two' => null,
            'tab_thired' => null,
            'html' => null
        ];
        $project_count = $teamtalk_count = $chat_count = 0;
        $t_array = $this->table_array_part();
        $project_arr = $t_array ['project_arr'];
        $teamtalk_arr = $t_array ['teamtalk_arr'];
        $chat_arr = $t_array ['chat_arr'];

        // $contant_a = $contant_b = $contant_c = 'Sorry <b>' . $keyword . '</b> was not found';
        $contant_a = $contant_b = $contant_c = '<li>No search results.</li>';



        if (isset($resultArray) && !empty($resultArray)) {
            foreach ($resultArray as $database => $tableArray) {
                $contant_a = $contant_b = $contant_c = null;

                foreach ($tableArray as $table => $fieldArray) {
                    $sum = 0;
                    foreach ($fieldArray as $num => $values) {
                        // if( !in_array($values['field'], $this->exclude_fields)) {
                        $sum += $values ['num'];
                        // }
                    }

                    /*
                     * if (in_array($table, array_keys($project_arr))) {
                     * $project_count = $project_count+$sum;
                     * $contant_a .= '<li><a href="#'.$table.'" class="search-'.$table.'">'.$project_a_arr[$table].' ('.$sum.')</a></li>';
                     * } else if (!in_array($table, array_keys($project_arr))) {
                     * $contant_a .= '<li><a href="#'.$table.'" class="search-'.$table.'">'.$project_a_arr[$table].' (0)</a></li>';
                     * }
                     */

                    if (in_array($table, array_keys($project_arr))) {
                        // pr($project_arr);
                        $project_count = $project_count + $sum;
                        $contant_b .= '<li id="search-' . $table . '"><a href="#' . $table . '" class="search-sub-' . $table . '">' . $project_arr [$table] . ' (' . $sum . ')</a></li>';
                    } else if (in_array($table, array_keys($teamtalk_arr))) {
                        $teamtalk_count = $teamtalk_count + $sum;
                        $contant_c .= '<li id="search-' . $table . '"><a href="#' . $table . '" class="search-sub-' . $table . '">' . $teamtalk_arr [$table] . ' (' . $sum . ')</a></li>';
                    } else if (!in_array($table, array_keys($teamtalk_arr))) {

                        $contant_c .= '<li><a href="#' . $table . '" class="search-' . $table . '">' . $teamtalk_arr [$table] . ' (0)</a></li>';
                    }
                }
            }
        }
        $response ['tab_one'] = '<div class="panel panel-green items">';
        $response ['tab_one'] .= '<div class="panel-heading">';
        $response ['tab_one'] .= '<h5><a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#project_accordion">';
        $response ['tab_one'] .= '<i class="fa fa-minus"></i> Projects (' . $project_count . ')</a></h5></div>';
        $response ['tab_one'] .= '<div class=" collapse in" id="project_accordion">';

        // $response['tab_one'] .= '<ul class="search-items first">';
        // $response['tab_one'] .= $contant_a;
        // $response['tab_one'] .= '</ul>';

        $response ['tab_one'] .= '<ul class="search-items search-items-all" >';
        $response ['tab_one'] .= '<li class="view-all" ><a data-id="project" href="#"><b>All</b></a></li>';
        $response ['tab_one'] .= '</ul>';

        $response ['tab_one'] .= '<ul class="search-items">';
        $response ['tab_one'] .= $contant_b;
        $response ['tab_one'] .= '</ul>';
        $response ['tab_one'] .= '</div>';
        $response ['tab_one'] .= '</div>';

        /* $response ['tab_two'] = '<div class="panel panel-green items" >';
        $response ['tab_two'] .= '<div class="panel-heading">';
        $response ['tab_two'] .= '<h5>';
        $response ['tab_two'] .= '<a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#teamtalk_accordion">';
        $response ['tab_two'] .= '<i class="fa fa-plus"></i> Info Center (' . $teamtalk_count . ')';
        $response ['tab_two'] .= '</a>';
        $response ['tab_two'] .= '</h5>';
        $response ['tab_two'] .= '</div>';
        $response ['tab_two'] .= '<div class="  collapse" id="teamtalk_accordion">';

        $response ['tab_two'] .= '<ul class="search-items search-items-all">';
        $response ['tab_two'] .= '<li class="view-all" ><a data-id="teamtalk" href="#"><b>All</b></a></li>';
        $response ['tab_two'] .= '</ul>';

        $response ['tab_two'] .= '<ul class="search-items">';
        $response ['tab_two'] .= $contant_c;
        $response ['tab_two'] .= '</ul>';
        $response ['tab_two'] .= '</div>';
        $response ['tab_two'] .= '</div>'; */

        // $response ['tab_thired'] = '<div class="panel panel-green items" >';
        // $response ['tab_thired'] .= '<div class="panel-heading">';
        // $response ['tab_thired'] .= '<h5>';
        // $response ['tab_thired'] .= '<a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#chat_accordion">';
        // $response ['tab_thired'] .= '<i class="fa fa-plus"></i> Chat (0)';
        // $response ['tab_thired'] .= '</a>';
        // $response ['tab_thired'] .= '</h5>';
        // $response ['tab_thired'] .= '</div>';
        // $response ['tab_thired'] .= '<div class="panel-body collapse" id="chat_accordion">';
        // $response ['tab_thired'] .= '<ul class="search-items search-items-all">';
        // $response ['tab_thired'] .= '<li class="view-all" ><a data-id="chat" href="#"><b>All</b></a></li>';
        // $response ['tab_thired'] .= '</ul>';
        // $response ['tab_thired'] .= '<ul class="search-items">';
        // $response ['tab_thired'] .= '<li><a href="#" class="search-link">Messages (0)</a></li>';
        // $response ['tab_thired'] .= '<li><a href="#" class="search-link">Broadcasts (0)</a></li>';
        // $response ['tab_thired'] .= '<li><a href="#" class="search-link">Conversations (0)</a></li>';
        // $response ['tab_thired'] .= '</ul>';
        // $response ['tab_thired'] .= '</div>';
        // $response ['tab_thired'] .= '</div>';

        $response ['tab_thired'] .= '</div>';

        return $response;
    }

    public function table_array() {
        $project_arr = [
            'projects' => 'Project',
            'workspaces' => 'Workspace',
            'elements' => 'Task',
            'element_decisions' => 'Task decision',
            'element_decision_details' => 'Task decision detail',
            'element_documents' => 'Task documents',
            'feedback' => 'Task Feedbacks',
            'feedback_attachments' => 'Task feedback attachment',
            // 'feedback_results' => 'Task feedback result',
            'element_links' => 'Task link',
            'element_notes' => 'Task note',
            'template_relations' => 'Template',
            'votes' => 'Task vote',
            'do_lists' => 'Todo',
            'do_list_comments' => 'Todo',
            'do_list_comment_uploads' => 'Todo comment file',
            'blogs' => 'Blog',
            'blog_comments' => 'Blog comment',
            'blog_documents' => 'Blog document',
            'wikies' => 'Wiki',
            'wiki_pages' => 'Wiki page',
            'wiki_page_comments' => 'Wiki page comment',
            'wiki_page_comment_documents' => 'Wiki page comment document'
        ];

        return $project_arr;
    }

    public function table_array_part() {
        $arr = [
            'project_arr',
            'teamtalk_arr',
            'chat_arr'
        ];
         $arr ['project_arr'] = [
            'projects' => 'Projects',
            'workspaces' => 'Workspaces',
            'elements' => 'Tasks',
            'element_decisions' => 'Task Decisions',
            'element_decision_details' => 'Task Decision Details',
            'element_documents' => 'Task Documents',
            'feedback' => 'Task Feedbacks',
            'feedback_attachments' => 'Task Feedback Attachments',
            //'feedback_results' => 'Task feedback\'s results',
            'element_links' => 'Task Links',
            'element_notes' => 'Task Notes',
            'votes' => 'Task Votes',
            'template_relations' => 'Template',
            'do_lists' => 'Todos',
            'do_list_comments' => 'Todo Comments',
            'do_list_comment_uploads' => 'Todo Comment Files'
        ];

        $arr ['teamtalk_arr'] = [
            'blogs' => 'Blogs',
            'blog_comments' => 'Blog Comments',
            'blog_documents' => 'Blog Documents',
            'wikies' => 'Wiki Main',
            'wiki_pages' => 'Wiki Pages',
            'wiki_page_comments' => 'Wiki Page Comments',
            'wiki_page_comment_documents' => 'Wiki Page Comment Documents'
        ];

        $arr ['chat_arr'] = [];
        return $arr;
    }

    public function showAjaxResultAsMiddle($resultArray, $keyword) {
        $response = [
            'tab_middle' => null,
            'html' => null
        ];
        $project_count = $teamtalk_count = $chat_count = 0;
        $project_details = 0;
        $project_main_class = $team_main_class = $chat_main_class = ' ';
        $project_main_class_is = $team_main_class_is = $chat_main_class_is = false;

        // projects

        $project_arr = $this->table_array();

        $t_array = $this->table_array_part();
        $t_project_arr = $t_array ['project_arr'];
        $t_teamtalk_arr = $t_array ['teamtalk_arr'];
        $t_chat_arr = $t_array ['chat_arr'];

        $contant_a = $contant_b = $contant_c = null;
        // $html = "Sorry <b>$keyword</b> was not found in any of the table";
        $html = "No search results.";
        $total_rows = 0;
        if (isset($resultArray) && !empty($resultArray)) {
            foreach ($resultArray as $database => $tableArray) {
                $html = $table_value = $project_details = null;
                foreach ($tableArray as $table => $fieldArray) {
                    $sum = 0;

                    if (in_array($table, array_keys($t_project_arr)) && $project_main_class_is == false) {
                        $project_main_class = 'search-items-all-project ';
                        $main_class_is = true;
                    } else if (!in_array($table, array_keys($t_project_arr))) {
                        $project_main_class_is = false;
                        $project_main_class = ' ';
                    }

                    if (in_array($table, array_keys($t_teamtalk_arr)) && $team_main_class_is == false) {
                        $team_main_class = 'search-items-all-teamtalk ';
                        $team_main_class_is = true;
                    } else if (!in_array($table, array_keys($t_teamtalk_arr))) {
                        $team_main_class_is = false;
                        $team_main_class = ' ';
                    }

                    if (in_array($table, array_keys($t_chat_arr)) && $chat_main_class_is == false) {
                        $chat_main_class = 'search-items-all-chat ';
                        $chat_main_class_is = true;
                    } else if (!in_array($table, array_keys($t_chat_arr))) {
                        $chat_main_class_is = false;
                        $chat_main_class = ' ';
                    }

                    $html .= '<div class="' . $project_main_class . $team_main_class . $chat_main_class . 'search-div-main" id="' . $table . '">';
                    foreach ($fieldArray as $num => $values) {
                        $sum += $values ['num'];
                    }
                    if (in_array($table, array_keys($project_arr))) {
                        $table_value = $project_arr [$table];
                    }

                    $field_html = '';
                    $new_table_name = null;
                    foreach ($fieldArray as $fields) {

                        $fieldName = $fields ['FieldName'];
                        $keyword = $fields ['keyword'];
                        $field = $fields ['field'];
                        $type = $fields ['type'];

                        $more = explode(" ", trim($keyword));

                        $like = [];
                        $likes = '';
                        $like_any = [];
                        $like_any_or = [];
                        if (isset($more) && !empty($more)) {
                            $i = 0;
                            foreach ($more as $mor) {

                                if ($i == 0) {
                                $like_any [] = "   '% " . $mor . "%' OR  `" . $field . "` like  '%" . $mor . " %'  OR  `" . $field . "` like  '% " . $mor . " %')";
                                $like_any_or [] = "'%" . $mor . " %'";
                            } else {
                                $like_any [] = " AND (`" . $field . "` like  '% " . $mor . "%' OR  `" . $field . "` like  '%" . $mor . " %')";
                               // $like_any [] = " AND `" . $field . "` like  '%" . $mor . " %'";



								$like_any_or [] = " OR `" . $field . "` like  '% " . $mor . "%'";
								$like_any_or [] = " OR `" . $field . "` like  '%" . $mor . " %'";
                            }
                                $i ++;
                            }

                            $like [] = "  '% " . $keyword . " %'";
                            $like [] =  " OR `" . $field . "` like  '% " . $keyword . "%'";
                            $like [] =  " OR `" . $field . "` like  '%" . $keyword . " %')";
                            //$like [] =  " OR `" . $field . "` like  '%" . $keyword . "%'";

                        }

                        $likes = implode($like, '');

                        $like_any = implode($like_any, '');
                        $like_any_or = implode($like_any_or, '');

                        if ($type == 1) {
                            $keyword = strtolower($keyword);
                            $query = "SELECT id,$field FROM `$table` WHERE  ( `$field` like $likes    AND is_search='1'";
						  // $query = "SELECT id,$field FROM `$table` WHERE `$field` like $like_any  AND is_search='1'";

                        } elseif ($type == 2) {
                            $query = "SELECT id,$field FROM `$table` WHERE `$field` like '%$keyword%' OR `$field` like '$keyword%' OR `$field` like '%$keyword'  AND is_search='1'";
                        } elseif ($type == 3) {

                            $query = "SELECT id,$field FROM `$table` WHERE  binary `$field` like $likes or binary `$field` = '$keyword' or binary `$field` = '$keyword'  AND is_search='1'";
                        } elseif ($type == 4) {
                            $keyword = strtolower($keyword);

                              $query = "SELECT * FROM `$table` WHERE  ( `$field` like $like_any AND is_search='1'";
                        }

						$conn = mysqli_connect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD,SEARCH_DB);
						if (mysqli_connect_errno())
						{
							echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}

						//  echo  $query."<br>";
                        $data = mysqli_query($conn,$query);
                        if (isset($data) && !empty($data)) {
                            $nums = mysqli_num_rows($data);
                        }

                        if (isset($data) && !empty($data)) {
                            while ($getData = mysqli_fetch_assoc($data)) {
                                if (isset($getData)) {


                                    // if( !in_array($fieldName, $this->exclude_fields)) {
                                    $total_rows ++;
                                    $html .= '<div class="' . $table . '-' . $fieldName . '-' . $getData ['id'] . ' result-item">';
                                    $html .= '<div class="effect-div">';
                                    $html .= "<div class='find-in'><b>" . $table_value . ' ' . preg_replace('/s$/', '', $fieldName) . ":</b></div>";
                                    $html .= '<div class="details">';
                                    $html .= "<div class='type'>  <a data-original-title='View Details' class='find-search-details tipText' style='cursor: pointer;' data-id=" . $getData ['id'] . "  data-field=" . $field . " data-table=" . $table . " > " . $this->clean_field_html($getData [$field]) . "</a></div>";
                                    $html .= '  </div>';
                                    $html .= "</div>";
                                    $html .= "</div>";
                                    // }
                                }
                            }
                        }
                    }
                    $html .= '</div>';
                }
            }
        }


        $pagination = array(
            "total_rows" => $total_rows,
            "rows_per_page" => 10,
            "num_links" => 5,
            "cur_page" => 1,
            "adjacents" => 2,
            "show_first" => 1,
            "show_last" => 1,
            "show_prev" => 1,
            "show_next" => 1,
            "btn_class" => 'btn btn-default btn-sm'
        );

        $js_paging_links_response = jsPaginations($pagination);
        if (!empty($js_paging_links_response)) {
            $response ['pagination'] = $js_paging_links_response ["output"];
        }
        $response ['total_rows'] = $total_rows;
        $response ['tab_middle'] = $html;
        return $response;
    }

    public function clean_field_html($fields = null) {
        $text = (strlen(strip_tags(utf8_encode($fields))) > 220) ? substr(html_entity_decode(strip_tags(utf8_encode($fields))), 0, 220) . '...' : html_entity_decode(strip_tags(utf8_encode($fields)));
        return $text;
    }

    public function ajax_clean_field_html($fields = null) {

        // $text = (strlen(strip_tags(utf8_encode($fields))) > 220) ? substr(html_entity_decode(strip_tags(utf8_encode($fields))), 0, 220) . '...' : html_entity_decode(strip_tags(utf8_encode($fields)));
        $text = html_entity_decode(strip_tags(utf8_encode($fields)));
        return $text;
    }

    public function getAjaxProjects($table = null, $table_id = null, $table_field = null, $table_field_text = null) {
        // $table = null, $fields = null, $field = null
        // $table,$table_id,$table_field,$table_field_text
        $project_id = null;
        $project_title = '';
        $field_html = '';
        $permission_check = false;

        $fields ['id'] = $table_id;
        $field = $table_field;
        $fields [$field] = $table_field_text;

        if (isset($table) && $table == 'wikies') {
            $wikiid = $projectid = $wikipageid = $wikiperm = false;
            $permission_check = false;
            $herf = 'javascript:';
            $target = '';
            $project_id = $this->Wiki->find("first", [
                "fields" => [
                    "project_id",
                    "id"
                ],
                "conditions" => [
                    "Wiki.id" => $fields ['id']
                ]
            ]);

            if (isset($project_id ['Wiki'] ['project_id']) && !empty($project_id ['Wiki'] ['project_id'])) {
                $wikiid = $project_id ['Wiki'] ['id'];
                $projectid = $project_id ['Wiki'] ['project_id'];
                $wikiperm = $this->getWikiPermission($projectid, $wikiid, $wikipageid);

                $data = $this->getProjectDetail($project_id ['Wiki'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $projectid = $data ['Project'] ['id'];
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                } else {
                    $projectid = false;
                    $project_title = 'Unspecified\'s Project';
                }
            } else {
                $projectid = false;
                $project_title = 'Unspecified\'s Project';
            }

            if (isset($wikiperm) && $wikiperm == true && isset($projectid) && !empty($projectid)) {
                $permission_check = true;
                $herf = SITEURL . 'wikies/index/project_id:' . $projectid . '/wiki:' . $wikiid;
                $target = '';
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'wiki_pages') {
            $wikiid = $projectid = $wikipageid = $wikiperm = false;
            $permission_check = false;
            $herf = 'javascript:';
            $target = '';
            $project_id = $this->WikiPage->find("first", [
                "fields" => [
                    "project_id",
                    "wiki_id",
                    "id"
                ],
                "conditions" => [
                    "WikiPage.id" => $fields ['id']
                ]
            ]);
            if (isset($project_id ['WikiPage'] ['project_id']) && !empty($project_id ['WikiPage'] ['project_id'])) {
                $wikiid = $project_id ['WikiPage'] ['wiki_id'];
                $wikipageid = $project_id ['WikiPage'] ['id'];
                $projectid = $project_id ['WikiPage'] ['project_id'];
                $wikiperm = $this->getWikiPermission($projectid, $wikiid, $wikipageid);
                $data = $this->getProjectDetail($project_id ['WikiPage'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $projectid = $data ['Project'] ['id'];
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                } else {
                    $projectid = false;
                    $project_title = 'Unspecified\'s Project';
                }
            } else {
                $projectid = false;
                $project_title = 'Unspecified\'s Project';
            }

            if (isset($wikiperm) && $wikiperm == true && isset($projectid) && !empty($projectid)) {
                $herf = SITEURL . 'wikies/index/project_id:' . $projectid . '/wiki:' . $wikiid . '/wiki_page:' . $fields ['id'];
                $target = '';
                $permission_check = true;
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'wiki_page_comments') {
            $permission_check = false;
            $wikiid = $projectid = $wikipageid = $wikiperm = false;
            $herf = 'javascript:';
            $target = '';
            $project_id = $this->WikiPageComment->find("first", [
                "fields" => [
                    "project_id",
                    "wiki_id",
                    "wiki_page_id"
                ],
                "conditions" => [
                    "WikiPageComment.id" => $fields ['id']
                ]
            ]);

            if (isset($project_id ['WikiPageComment'] ['project_id']) && !empty($project_id ['WikiPageComment'] ['project_id'])) {
                $wikiid = $project_id ['WikiPageComment'] ['wiki_id'];
                $wikipageid = $project_id ['WikiPageComment'] ['wiki_page_id'];
                $projectid = $project_id ['WikiPageComment'] ['project_id'];
                $wikiperm = $this->getWikiPermission($projectid, $wikiid, $wikipageid);

                $data = $this->getProjectDetail($projectid, $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $projectid = $data ['Project'] ['id'];
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                } else {
                    $projectid = false;
                    $project_title = 'Unspecified\'s Project';
                }
            } else {
                $projectid = false;
                $project_title = 'Unspecified\'s Project';
            }

            if (isset($wikiperm) && $wikiperm == true && isset($projectid) && !empty($projectid)) {
                $herf = SITEURL . 'wikies/index/project_id:' . $projectid . '/wiki:' . $wikiid . '/comment:' . $fields ['id'] . '#comments';
                $target = '';
                $permission_check = true;
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'wiki_page_comment_documents') {
            $wikiid = $projectid = $wikipageid = $wikiperm = false;
            $permission_check = false;
            $herf = 'javascript:';
            $target = '';
            $wiki_page_comment_doc_id = $this->WikiPageCommentDocument->find("first", [
                "fields" => [
                    "wiki_page_comment_id"
                ],
                "conditions" => [
                    "WikiPageCommentDocument.id" => $fields ['id']
                ]
            ]);
            if (isset($wiki_page_comment_doc_id ['WikiPageCommentDocument'] ['wiki_page_comment_id']) && !empty($wiki_page_comment_doc_id ['WikiPageCommentDocument'] ['wiki_page_comment_id'])) {
                // pr($wiki_page_comment_doc_id);

                $project_id = $this->WikiPageComment->find("first", [
                    "fields" => [
                        "project_id",
                        "wiki_id",
                        "wiki_page_id"
                    ],
                    "conditions" => [
                        "WikiPageComment.id" => $wiki_page_comment_doc_id ['WikiPageCommentDocument'] ['wiki_page_comment_id']
                    ]
                ]);
                if (isset($project_id ['WikiPageComment'] ['project_id']) && !empty($project_id ['WikiPageComment'] ['project_id'])) {
                    $wikiid = $project_id ['WikiPageComment'] ['wiki_id'];
                    $wikipageid = $project_id ['WikiPageComment'] ['wiki_page_id'];
                    $projectid = $project_id ['WikiPageComment'] ['project_id'];
                    $wikiperm = $this->getWikiPermission($projectid, $wikiid, $wikipageid);

                    $data = $this->getProjectDetail($project_id ['WikiPageComment'] ['project_id'], $recursive = - 1);
                    if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                        $projectid = $data ['Project'] ['id'];
                        $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                    } else {
                        $projectid = false;
                        $project_title = 'Unspecified\'s Project';
                    }
                } else {
                    $projectid = false;
                    $project_title = 'Unspecified\'s Project';
                }
            }

            if (isset($wikiperm) && $wikiperm == true && isset($projectid) && !empty($projectid)) {
                $herf = SITEURL . 'wikies/index/project_id:' . $projectid . '/wiki:' . $wikiid;
                $target = '';
                $permission_check = true;
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        if (isset($table) && $table == 'do_lists') {
            $permission_check = false;
            $project_id = $this->DoList->find("first", [
                "fields" => [
                    "project_id"
                ],
                "conditions" => [
                    "DoList.id" => $fields ['id']
                ]
            ]);
            if (isset($project_id ['DoList'] ['project_id']) && !empty($project_id ['DoList'] ['project_id'])) {
                $data = $this->getProjectDetail($project_id ['DoList'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_id = $data ['Project'] ['id'];
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                } else {
                    $project_id = false;
                    $project_title = 'Unspecified\'s Project for this Todo';
                }
            } else {
                $project_id = false;
                $project_title = 'Unspecified\'s Project for this Todo';
            }
            $todoperm = $this->getTodoPermission($fields ['id']);

            // if (isset($todoperm) && $todoperm == true && isset($data['Project']['id']) && !empty($data['Project']['id'])) {
            if (isset($todoperm) && $todoperm == true) {
                $herf = SITEURL . 'todos/index/project:' . $project_id . '/dolist_id:' . $fields ['id'];
                $target = '';
                $permission_check = true;
            } else {

                $herf = 'javascript:';
                $target = '';
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'do_list_comments') {
            $todoperm = $dolist_id = false;
            $permission_check = false;
            $do_list_id = $this->DoListComment->find("first", [
                "fields" => [
                    "do_list_id"
                ],
                "conditions" => [
                    "DoListComment.id" => $fields ['id']
                ]
            ]);
            if (isset($do_list_id ['DoListComment'] ['do_list_id']) && !empty($do_list_id ['DoListComment'] ['do_list_id'])) {
                $dolist_id = $do_list_id ['DoListComment'] ['do_list_id'];
                $project_id = $this->DoList->find("first", [
                    "fields" => [
                        "project_id"
                    ],
                    "conditions" => [
                        "DoList.id" => $do_list_id ['DoListComment'] ['do_list_id']
                    ]
                ]);
                if (isset($project_id ['DoList'] ['project_id']) && !empty($project_id ['DoList'] ['project_id'])) {
                    $data = $this->getProjectDetail($project_id ['DoList'] ['project_id'], $recursive = - 1);
                    if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                        $project_id = $data ['Project'] ['id'];
                        $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                    } else {
                        $project_id = false;
                        $project_title = 'Unspecified\'s Project for this Todo';
                    }
                } else {
                    $project_id = false;
                    $project_title = 'Unspecified\'s Project for this Todo';
                }
                $todoperm = $this->getTodoPermission($dolist_id);
            }

            if (isset($todoperm) && $todoperm == true && isset($data ['Project'] ['id']) && !empty($data ['Project'] ['id'])) {
                $herf = SITEURL . 'todos/index/project:' . $project_id . '/dolist_id:' . $fields ['id'];
                $target = '';
                $permission_check = true;
            } else {
                $herf = 'javascript:';
                $target = '';
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'do_list_comment_uploads') {
            $todoperm = $dolist_id = false;
            $permission_check = false;
            $do_list_comment_id = $this->DoListCommentUpload->find("first", [
                "fields" => [
                    "do_list_comment_id"
                ],
                "conditions" => [
                    "DoListCommentUpload.id" => $fields ['id']
                ]
            ]);

            if (isset($do_list_comment_id ['DoListCommentUpload'] ['do_list_comment_id']) && !empty($do_list_comment_id ['DoListCommentUpload'] ['do_list_comment_id'])) {

                $do_list_id = $this->DoListComment->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        "do_list_id"
                    ],
                    "conditions" => [
                        "DoListComment.id" => $do_list_comment_id ['DoListCommentUpload'] ['do_list_comment_id']
                    ]
                ]);

                if (isset($do_list_id ['DoListComment'] ['do_list_id']) && !empty($do_list_id ['DoListComment'] ['do_list_id'])) {
                    $project_id = $this->DoList->find("first", [
                        "fields" => [
                            "project_id"
                        ],
                        "conditions" => [
                            "DoList.id" => $do_list_id ['DoListComment'] ['do_list_id']
                        ]
                    ]);
                    $dolist_id = $do_list_id ['DoListComment'] ['do_list_id'];
                    if (isset($project_id ['DoList'] ['project_id']) && !empty($project_id ['DoList'] ['project_id'])) {

                        $data = $this->getProjectDetail($project_id ['DoList'] ['project_id'], $recursive = - 1);
                        if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                            $project_id = $data ['Project'] ['id'];
                            $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                        } else {
                            $project_id = false;
                            $project_title = 'Unspecified\'s Project for this Todo';
                        }
                    } else {
                        $project_id = false;
                        $project_title = 'Unspecified\'s Project for this Todo';
                    }
                }
                $todoperm = $this->getTodoPermission($dolist_id);
            }

            if (isset($todoperm) && $todoperm == true && isset($data ['Project'] ['id']) && !empty($data ['Project'] ['id'])) {
                $herf = SITEURL . 'todos/index/project:' . $project_id . '/dolist_id:' . $dolist_id;
                $target = '';
                $permission_check = true;
            } else {
                $herf = 'javascript:';
                $target = '';
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        if (isset($table) && $table == 'blogs') {
            $project_id = $blogperm = $blogid = false;
            $herf = 'javascript:';
            $target = '';
            $permission_check = false;
            $project_id = $this->Blog->find("first", [
                "fields" => [
                    "project_id"
                ],
                "conditions" => [
                    "Blog.id" => $fields ['id']
                ]
            ]);
            if (isset($project_id ['Blog'] ['project_id']) && !empty($project_id ['Blog'] ['project_id'])) {

                $data = $this->getProjectDetail($project_id ['Blog'] ['project_id'], $recursive = - 1);

                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $projectid = $data ['Project'] ['id'];
                    $blogid = $project_id ['Blog'] ['id'];
                    $blogperm = $this->getBlogPermission($projectid, $blogid);

                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                } else {
                    $projectid = false;
                    $project_title = 'Unspecified\'s Project';
                }
            } else {
                $projectid = false;
                $project_title = 'Unspecified\'s Project';
            }

            if (isset($blogperm) && $blogperm == true) {
                $herf = SITEURL . 'team_talks/index/project:' . $projectid . '/blog:' . $fields ['id'];
                $target = '';
                $permission_check = true;
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'blog_comments') {
            $project_id = $blogperm = $blogid = false;
            $permission_check = false;
            $herf = 'javascript:';
            $target = '';
            $blog_id = $this->BlogComment->find("first", [
                "fields" => [
                    "blog_id"
                ],
                $recursive = - 1,
                "conditions" => [
                    "BlogComment.id" => $fields ['id']
                ]
            ]);
            if (isset($blog_id ['BlogComment'] ['blog_id']) && !empty($blog_id ['BlogComment'] ['blog_id'])) {

                $project_id = $this->Blog->find("first", [
                    "fields" => [
                        "id",
                        "project_id"
                    ],
                    "conditions" => [
                        "Blog.id" => $blog_id ['BlogComment'] ['blog_id']
                    ]
                ]);

                if (isset($project_id ['Blog'] ['project_id']) && !empty($project_id ['Blog'] ['project_id'])) {
                    $data = $this->getProjectDetail($project_id ['Blog'] ['project_id'], $recursive = - 1);
                    if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                        $projectid = $data ['Project'] ['id'];
                        $blogid = $project_id ['Blog'] ['id'];
                        $blogperm = $this->getBlogPermission($projectid, $blogid);

                        $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                    } else {
                        $projectid = null;
                        $project_title = 'Unspecified\'s Project';
                    }
                } else {
                    $projectid = null;
                    $project_title = 'Unspecified\'s Project';
                }
            }

            // http://192.168.4.29/ideascomposer/team_talks/index/project:2/blog:42/comment:88/#comments

            if (isset($blogperm) && $blogperm == true) {
                $herf = SITEURL . 'team_talks/index/project:' . $projectid . '/blog:' . $project_id ['Blog'] ['id'] . '/comment:' . $fields ['id'] . '#comments';
                $target = '';
                $permission_check = true;
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'blog_documents') {
            $project_id = $blogperm = $blogid = false;
            $permission_check = false;
            $herf = 'javascript:';
            $target = '';
            $blog_id = $this->BlogDocument->find("first", [
                "fields" => [
                    "blog_id"
                ],
                "conditions" => [
                    "BlogDocument.id" => $fields ['id']
                ]
            ]);
            if (isset($blog_id ['BlogDocument'] ['blog_id']) && !empty($blog_id ['BlogDocument'] ['blog_id'])) {
                $project_id = $this->Blog->find("first", [
                    "fields" => [
                        "project_id"
                    ],
                    "conditions" => [
                        "Blog.id" => $blog_id ['BlogDocument'] ['blog_id']
                    ]
                ]);
                if (isset($project_id ['Blog'] ['project_id']) && !empty($project_id ['Blog'] ['project_id'])) {
                    $data = $this->getProjectDetail($project_id ['Blog'] ['project_id'], $recursive = - 1);
                    if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                        $projectid = $data ['Project'] ['id'];
                        $blogid = $project_id ['Blog'] ['id'];
                        $blogperm = $this->getBlogPermission($projectid, $blogid);

                        $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                    } else {
                        $projectid = null;
                        $project_title = 'Unspecified\'s Project';
                    }
                } else {
                    $projectid = null;
                    $project_title = 'Unspecified\'s Project';
                }
            }

            if (isset($blogperm) && $blogperm == true) {
                $herf = SITEURL . 'team_talks/index/project:' . $projectid . '/blog:' . $fields ['id'];
                $target = '';
                $permission_check = true;
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        if (isset($table) && $table == 'workspaces') {
            $herf = 'javascript:';
            $target = '';
            $permission_check = false;

            $project_id = workspace_pid($fields ['id']);
            $permission = $this->getProjectWorkspacePermission($project_id, $fields ['id']);

            if (isset($project_id) && !empty($project_id)) {
                $data = $this->getProjectDetail($project_id, $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            } else {
                $project_title = 'Unspecified\'s Project';
            }
            if (isset($permission) && $permission == true) {
                $herf = SITEURL . 'projects/manage_elements/' . $project_id . '/' . $fields ['id'];
                $target = '';
                $permission_check = true;
            }
            $field_html = '<a  ' . $target . '  href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'projects') {
            $herf = 'javascript:';
            $target = '';
            $permission_check = false;
            $data = $this->getProjectDetail($fields ['id'], $recursive = - 1);
            $pro_permission = $this->getProjectPermission($fields ['id']);
            if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
            }
            if (isset($pro_permission) && $pro_permission == true) {
                $herf = SITEURL . 'projects/index/' . $data ['Project'] ['id'];
                $target = '';
                $permission_check = true;
            }

            $field_html = '<a  ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'template_relations') {
            $herf = 'javascript:';
            $target = '';
            $permission_check = false;
			$dd =  getByDbId('TemplateRelation',$table_id) ;
			$herf = SITEURL . '/templates/create_workspace/0/' .$dd ['TemplateRelation'] ['template_category_id'];

           $field_html = '<a  ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        if (isset($table) && $table == 'elements') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $project_id = $this->element_permissions->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'project_id'
                ],
                "conditions" => [
                    "ElementPermission.element_id" => $fields ['id']
                ]
            ]);
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $fields ['id']);

                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }

            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $fields ['id'] . '#tasks';
                $target = '';
            }

            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'element_decisions') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->ElementDecision->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "ElementDecision.id" => $fields ['id']
                ]
            ]);
            if (isset($element_id ['ElementDecision'] ['element_id']) && !empty($element_id ['ElementDecision'] ['element_id'])) {
                $e_id = $element_id ['ElementDecision'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['ElementDecision'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['ElementDecision'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#decisions';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'element_decision_details') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_decision_id = $this->ElementDecisionDetail->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_decision_id'
                ],
                "conditions" => [
                    "ElementDecisionDetail.id" => $fields ['id']
                ]
            ]);
            $element_id = $this->ElementDecision->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "ElementDecision.id" => $element_decision_id ['ElementDecisionDetail'] ['element_decision_id']
                ]
            ]);

            if (isset($element_id ['ElementDecision'] ['element_id']) && !empty($element_id ['ElementDecision'] ['element_id'])) {
                $e_id = $element_id ['ElementDecision'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['ElementDecision'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['ElementDecision'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#decisions';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'element_documents') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->ElementDocument->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "ElementDocument.id" => $fields ['id']
                ]
            ]);
            if (isset($element_id ['ElementDocument'] ['element_id']) && !empty($element_id ['ElementDocument'] ['element_id'])) {
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['ElementDocument'] ['element_id']
                    ]
                ]);
                $e_id = $element_id ['ElementDocument'] ['element_id'];
                if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                    $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['ElementDocument'] ['element_id']);
                }
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#documents';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'feedback') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->Feedback->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "Feedback.id" => $fields ['id']
                ]
            ]);

            if (isset($element_id ['Feedback'] ['element_id']) && !empty($element_id ['Feedback'] ['element_id'])) {
                $e_id = $element_id ['Feedback'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['Feedback'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['Feedback'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#feedbacks';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        if (isset($table) && $table == 'feedback_attachments') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;


            $element_id = $this->FeedbackAttachment->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "FeedbackAttachment.id" => $fields ['id']
                ]
            ]);


            if (isset($element_id ['FeedbackAttachment'] ['element_id']) && !empty($element_id ['FeedbackAttachment'] ['element_id'])) {
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['FeedbackAttachment'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['FeedbackAttachment'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }


            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $element_id ['FeedbackAttachment'] ['element_id'] . '#feedbacks';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';

			//pr($field_html); die;
        }
        if (isset($table) && $table == 'feedback_results') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_decision_id = $this->FeedbackResult->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'feedback_id'
                ],
                "conditions" => [
                    "FeedbackResult.id" => $fields ['id']
                ]
            ]);
            $element_id = $this->Feedback->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "Feedback.id" => $element_decision_id ['FeedbackResult'] ['feedback_id']
                ]
            ]);

            if (isset($element_id ['Feedback'] ['element_id']) && !empty($element_id ['Feedback'] ['element_id'])) {
                $e_id = $element_id ['Feedback'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['Feedback'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['Feedback'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#feedbacks';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'element_links') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->ElementLink->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "ElementLink.id" => $fields ['id']
                ]
            ]);
            if (isset($element_id ['ElementLink'] ['element_id']) && !empty($element_id ['ElementLink'] ['element_id'])) {
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['ElementLink'] ['element_id']
                    ]
                ]);
                $e_id = $element_id ['ElementLink'] ['element_id'];
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['ElementLink'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#links';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'element_notes') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->ElementNote->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "ElementNote.id" => $fields ['id']
                ]
            ]);

            if (isset($element_id ['ElementNote'] ['element_id']) && !empty($element_id ['ElementNote'] ['element_id'])) {
                $e_id = $element_id ['ElementNote'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['ElementNote'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['ElementNote'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#notes';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }
        if (isset($table) && $table == 'votes') {
            $herf = 'javascript:';
            $target = '';
            $eperm = $e_id = false;
            $permission_check = false;
            $element_id = $this->Vote->find("first", [
                'recursive' => - 1,
                "fields" => [
                    'element_id'
                ],
                "conditions" => [
                    "Vote.id" => $fields ['id']
                ]
            ]);

            if (isset($element_id ['Vote'] ['element_id']) && !empty($element_id ['Vote'] ['element_id'])) {
                $e_id = $element_id ['Vote'] ['element_id'];
                $project_id = $this->element_permissions->find("first", [
                    'recursive' => - 1,
                    "fields" => [
                        'project_id'
                    ],
                    "conditions" => [
                        "ElementPermission.element_id" => $element_id ['Vote'] ['element_id']
                    ]
                ]);
            }
            if (isset($project_id ['ElementPermission'] ['project_id']) && !empty($project_id ['ElementPermission'] ['project_id'])) {
                $eperm = $this->getProjectElementPermission($project_id ['ElementPermission'] ['project_id'], $element_id ['Vote'] ['element_id']);
                $data = $this->getProjectDetail($project_id ['ElementPermission'] ['project_id'], $recursive = - 1);
                if (isset($data ['Project'] ['title']) && !empty($data ['Project'] ['title'])) {
                    $project_title = html_entity_decode(strip_tags(utf8_encode(ucfirst($data ['Project'] ['title']))));
                }
            }
            if (isset($eperm) && $eperm == true) {
                $permission_check = true;
                $herf = SITEURL . 'entities/update_element/' . $e_id . '#votes';
                $target = '';
            }
            $field_html = '<a ' . $target . ' href="' . $herf . '" id="' . $field . '-' . $fields ['id'] . '">' . $this->ajax_clean_field_html($fields [$field]) . '</a>';
        }

        $response = [
            'project_id' => isset($data ['Project'] ['id']) && !empty($data ['Project'] ['id']) ? $data ['Project'] ['id'] : null,
            'project_title' => $project_title,
            'field_html' => $field_html,
            'permission_check' => $permission_check
        ];

        return $response;
    }

    public function getProjectDetail($project_id = null, $recursive = 1) {
        $user_id = $this->Session->read('Auth.User.id');
        if (!$user_id) {
            return;
        }

        $data = $this->_project->find('first', [
            'recursive' => $recursive,
            'conditions' => [
                'Project.id' => $project_id
            ],
            'fields' => [
                'id',
                'title'
            ]
        ]);
        return $data;
    }

    public function getProjectCreator($project_id = null) {
        $current_user_id = $this->Session->read('Auth.User.id');
        if (!$current_user_id) {
            return;
        }

        $data = $this->UserProject->find('first', [
            'recursive' => - 1,
            'conditions' => [
                'UserProject.owner_user' => 1,
                'UserProject.project_id' => $project_id
            ],
            'fields' => [
                'id',
                'user_id'
            ]
        ]);
        return $user_id = $data ['UserProject'] ['user_id'];
    }

    public function getProjectCreatorHtml($project_id = null) {
        $current_user_id = $this->Session->read('Auth.User.id');
        if (!$current_user_id) {
            return;
        }

        $user_id = $this->getProjectCreator($project_id);

        if (isset($user_id) && !empty($user_id)) {

            $user_data = $this->ViewModel->get_user_data($user_id);

			if( isset($user_data) && !empty($user_data) ){

				$pic = $user_data ['UserDetail'] ['profile_pic'];
				$profiles = SITEURL . USER_PIC_PATH . $pic;
				$job_title = $user_data ['UserDetail'] ['job_title'];

				if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
					$profiles = SITEURL . USER_PIC_PATH . $pic;
				} else {
					$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
				}

				$html = '';
				if ($user_id != $current_user_id) {
					$html = CHATHTML($user_id, $project_id);
				}
				$data = '<a href="#"  data-remote="' . SITEURL . 'shares/show_profile/' . $user_id . '"  data-target="#popup_modal"  data-toggle="modal" class="view_profile search-user-image" ><img align="left" style="margin: 0px 10px 10px 0px;" width="40"  src="' . $profiles . '" class="pophover" align="left" data-content="<div class=\'user-pophover\'><p>' . $user_data ['UserDetail'] ['first_name'] . ' ' . $user_data ['UserDetail'] ['last_name'] . '</p><p>' . $job_title . '</p>' . $html . '</div>" />
						</a>';
			} else {
				$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
				$html = '';
				if ($user_id != $current_user_id) {
					$html = CHATHTML($user_id, $project_id);
				}
				$data = '<a href="#"  data-remote="' . SITEURL . 'shares/show_profile/' . $user_id . '"  data-target="#popup_modal"  data-toggle="modal" class="view_profile search-user-image" ><img align="left" style="margin: 0px 10px 10px 0px;" width="40"  src="' . $profiles . '" class="pophover" align="left" data-content="<div class=\'user-pophover\'><p>N/A</p><p>N/A</p>' . $html . '</div>" />
						</a>';
			}
        }
        // pr($user_data);
        return $data;
    }

    public function getProjectPermission($project_id = null) {
        $current_user_id = $this->Session->read('Auth.User.id');

        $user_id = $this->Session->read('Auth.User.id');
        $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

        $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

        /* group Work Permission and group permission and level check */

        $grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);

        if (isset($grp_id) && !empty($grp_id)) {

            $group_permission = $this->Group->group_permission_details($project_id, $grp_id);
            if (isset($group_permission ['ProjectPermission'] ['project_level']) && $group_permission ['ProjectPermission'] ['project_level'] == 1) {
                $project_level = $group_permission ['ProjectPermission'] ['project_level'];
            }
        }
        /* Full level all elements */

        if ((isset($user_project) && !empty($user_project)) || ((isset($p_permission ['ProjectPermission'])) && (isset($p_permission ['ProjectPermission']) || $p_permission ['ProjectPermission'] ['project_level'] == 1)) || (isset($project_level) && $project_level == 1)) {
            return true;
        } else {
            return false;
        }

        // return $cky;
    }

    public function getProjectWorkspacePermission($project_id = null, $workspaceId = false) {
        $current_user_id = $this->Session->read('Auth.User.id');

        $user_id = $this->Session->read('Auth.User.id');
        $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

        $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

        $wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($workspaceId), $project_id, $this->Session->read('Auth.User.id'));

        /* group Work Permission and group permission and level check */

        $grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);

        if (isset($grp_id) && !empty($grp_id)) {

            $group_permission = $this->Group->group_permission_details($project_id, $grp_id);
            if (isset($group_permission ['ProjectPermission'] ['project_level']) && $group_permission ['ProjectPermission'] ['project_level'] == 1) {
                $project_level = $group_permission ['ProjectPermission'] ['project_level'];
            }

            $wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($workspaceId), $project_id, $grp_id);
        }
        /* Full level all elements */

        if ((isset($user_project) && !empty($user_project)) || (isset($p_permission ['ProjectPermission'] ['project_level']) && $p_permission ['ProjectPermission'] ['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
            return true;
        } else if (isset($wsp_permission [0] ['WorkspacePermission']) && !empty($wsp_permission [0] ['WorkspacePermission'])) {

            if ($wsp_permission [0] ['WorkspacePermission'] ['permit_read'] == 1) {

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

        // return $cky;
    }

    public function getProjectElementPermission($project_id = null, $element_id = false) {
        $current_user_id = $this->Session->read('Auth.User.id');

        $user_id = $this->Session->read('Auth.User.id');
        $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

        $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

        $e_permission = $this->Common->element_share_permission($element_id, $project_id, $user_id);

        /* group Work Permission and group permission and level check */

        $grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);

        if (isset($grp_id) && !empty($grp_id)) {

            $group_permission = $this->Group->group_permission_details($project_id, $grp_id);
            if (isset($group_permission ['ProjectPermission'] ['project_level']) && $group_permission ['ProjectPermission'] ['project_level'] == 1) {
                $project_level = $group_permission ['ProjectPermission'] ['project_level'];
            }

            if (isset($e_permission) && !empty($e_permission)) {
                $e_permissions = $this->Group->group_element_share_permission($element_id, $project_id, $grp_id);
                $e_permission = array_merge($e_permission, $e_permissions);
            } else {
                $e_permission = $this->Group->group_element_share_permission($element_id, $project_id, $grp_id);
            }
        }

        /* Full level all elements */

        if ((isset($user_project) && !empty($user_project)) || (isset($p_permission ['ProjectPermission'] ['project_level']) && $p_permission ['ProjectPermission'] ['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
            return true;
        } else if (isset($e_permission ['ElementPermission']) && !empty($e_permission ['ElementPermission'])) {

            if ($e_permission ['ElementPermission'] ['permit_read'] == 1) {

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

        // return $cky;
    }

    public function getTodoPermission($todo_id = null) {
        $current_user_id = $this->Session->read('Auth.User.id');

        $create_todo = $this->DoList->find("count", [
            "conditions" => [
                "DoList.id" => $todo_id,
                "DoList.user_id" => $current_user_id
            ]
        ]);

        if (isset($create_todo) && $create_todo <= 0) {
            $received_todo = $this->DoListUser->find("count", [
                "conditions" => [
                    "DoListUser.do_list_id" => $todo_id,
                    "DoListUser.user_id" => $current_user_id
                ]
            ]);
            if (isset($received_todo) && $received_todo > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function getWikiPermission($project_id = null, $wiki_id = null, $wiki_page_id = null) {
        $user_id = $this->Session->read('Auth.User.id');

        $p_permission = $this->Common->project_permission_details($project_id, $user_id);
        $user_project = $this->Common->userproject($project_id, $user_id);
        $gp_exists = $this->Group->GroupIDbyUserID($project_id, $user_id);
        if (isset($gp_exists) && !empty($gp_exists)) {
            $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
        }
        $wiki = $this->Wiki->findById($wiki_id);
        $wiki_user = $wiki ['Wiki'] ['user_id'];
        $wiki_status = $wiki ['Wiki'] ['status'];

        $is_requested_user = ClassRegistry::init('WikiUser')->find('count', [
            "fields" => [
                "WikiUser.id"
            ],
            'conditions' => [
                'WikiUser.wiki_id' => $wiki_id,
                'WikiUser.user_id' => $user_id,
                "WikiUser.approved" => 1
            ]
        ]);

        if ($user_id == $wiki_user && ($wiki_status == 0 || $wiki_status == 1)) {
            return true;
        } else if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission ['ProjectPermission'] ['project_level']) && $p_permission ['ProjectPermission'] ['project_level'] == 1)) {
            return true;
        } else if (isset($is_requested_user) && !empty($is_requested_user) && $is_requested_user == 1) {
            return true;
        } else if ($user_id != $wiki_user && ($wiki_status == 1)) {
            return true;
        }
    }

    public function getBlogPermission($project_id = null, $blog_id = null) {
        $user_id = $this->Session->read('Auth.User.id');

        $p_permission = $this->Common->project_permission_details($project_id, $user_id);
        $user_project = $this->Common->userproject($project_id, $user_id);
        $gp_exists = $this->Group->GroupIDbyUserID($project_id, $user_id);
        if (isset($gp_exists) && !empty($gp_exists)) {
            $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
        }

        $blog = $this->Blog->findById($blog_id);

        $blog_user = isset($blog ['Blog'] ['user_id']) ? $blog ['Blog'] ['user_id'] : null;

        if ($user_id == $blog_user) {
            return true;
        } else if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission ['ProjectPermission'] ['project_level']) && $p_permission ['ProjectPermission'] ['project_level'] == 1)) {
            return true;
        } else {
            return false;
        }
    }

}
