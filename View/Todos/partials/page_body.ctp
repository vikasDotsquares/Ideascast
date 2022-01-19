<?php
$counter_data = $this->Group->dolist_counters($prj_id, $day, $sdate, $edate);
$do_lists = $this->Group->do_lists($prj_id, $day, $sdate, $edate);
$rec_do_lists = $this->Group->rec_do_lists($prj_id, $day, $sdate, $edate);
$archive_do_lists = $this->Group->archive_do_lists($prj_id, $day, $sdate, $edate);

?>

<script type="text/javascript" >

    $(document).ready(function () {

        $(".fillter_calender").daterange({
            numberOfMonths: 2,
            dateFormat: "yy-mm-dd",
            //minDate: 0,
            autoUpdateInput: false,
            onSelect: function (selected, inst) {
            },
            onClose: function (selected, inst) {
                filterdate(selected);
            },
            beforeShow: function (input, inst) {
            },
        })

    });
    function filterdate(date) {
        var curUrl = window.location.href, dates = date.split(" - "),
                start_date = dates[0],
                end_date = (dates[1] !== '' && typeof dates[1] !== 'undefined') ? dates[1] : dates[0];
        if (date !== null && date !== '') {
            var view_vars = $js_config.view_vars;
            var project = (view_vars['project'] != undefined) ? '/project:' + $.trim(view_vars['project']) : '',
                    day = (view_vars['day'] != undefined) ? '/day:' + $.trim(view_vars['day']) : '',
                    startdate = (start_date != undefined) ? '/sdate:' + $.trim(start_date) : '',
                    enddate = (end_date != undefined) ? '/edate:' + $.trim(end_date) : '';
            window.location.href = $js_config.base_url + 'todos/index' + project + day + startdate + enddate;
        }
    }
</script>
<style>
    .dolist_description p{ margin: 1px 0;}
    .top-panel {
    	border-bottom: 1px solid #ccc;
    	display: block;
    	float: left;
    	width: 100%;
    }
	.gery-bg-todos {
		border-top: 1px solid #ccc;
		background: rgb(239, 239, 239);
		border-left: 1px solid #ccc;
		border-right: 1px solid #ccc;
	}
	.gery-bg-todos .tast-list-left-head .tast-list-status{
		border-right: 1px solid #ccc;
	}

	@media (max-width:1227px) {
	.tast-list-right-head {
		float: right;
		width: auto;
	}
	}
	@media (max-width:900px) {
	.gery-bg-todos .tast-list-left-head .tast-list-status{
		border-right: none;
	}
	}

</style>
<?php //pr($this->params['named']);  ?>
<div class="clear-box">
	<div class="top-panel  gery-bg-todos">
		<div class="tast-list-left-head">

			<?php $prj_link = (isset($prj_id) && !empty($prj_id)) ? '/project:' . $prj_id : ''; ?>
			<ul class="tast-list-status list-inline text-left  ">
                <?php
                $all_todo = 0;
                $all_todo = (isset($counter_data['today_count']) && !empty($counter_data['today_count'])) ? $all_todo + $counter_data['today_count'] : $all_todo;
                $all_todo = (isset($counter_data['tom_count']) && !empty($counter_data['tom_count'])) ? $all_todo + $counter_data['tom_count'] : $all_todo;
                $all_todo = (isset($counter_data['up_count']) && !empty($counter_data['up_count'])) ? $all_todo + $counter_data['up_count'] : $all_todo;
                $all_todo = (isset($counter_data['over_count']) && !empty($counter_data['over_count'])) ? $all_todo + $counter_data['over_count'] : $all_todo;
                $all_todo = (isset($counter_data['ns_count']) && !empty($counter_data['ns_count'])) ? $all_todo + $counter_data['ns_count'] : $all_todo;
                $all_todo = (isset($counter_data['com_count']) && !empty($counter_data['com_count'])) ? $all_todo + $counter_data['com_count'] : $all_todo;
                ?>
				<li class="list-counters" <?php if(isset($all_todo) && !empty($all_todo) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:all" <?php } ?>>
					<span class="tast-list-status-heading">All</span>
					<span class="tast-list-status-count">
						<?php echo (isset($all_todo) ) ? $all_todo : 0; ?>
					</span>
				</li>
				<li class="list-counters" <?php if(isset($counter_data['today_count']) && !empty($counter_data['today_count']) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:today" <?php } ?>>
					<span class="tast-list-status-heading">In Progress</span>
					<span class="tast-list-status-count">
                        <?php echo (isset($counter_data['today_count']) ) ? $counter_data['today_count'] : 0; ?>
					</span>
				</li>
                <?php
                $total_notstarted = 0;
                $total_notstarted = (isset($counter_data['tom_count']) && !empty($counter_data['tom_count'])) ? $total_notstarted + $counter_data['tom_count'] : $total_notstarted;
                $total_notstarted = (isset($counter_data['up_count']) && !empty($counter_data['up_count'])) ? $total_notstarted + $counter_data['up_count'] : $total_notstarted;
                ?>
                <?php /* ?>
				<li class="list-counters" <?php if(isset($counter_data['tom_count']) && !empty($counter_data['tom_count']) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:tomorrow" <?php } ?>>
					<span class="tast-list-status-heading">Tomorrow</span>
					<span class="tast-list-status-count">
                        <?php echo (isset($counter_data['tom_count']) ) ? $counter_data['tom_count'] : 0; ?> </span>

				</li>
                <?php */ ?>
				<li class="list-counters" <?php if(isset($total_notstarted) && !empty($total_notstarted) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:notstarted" <?php } ?>>
					<span class="tast-list-status-heading">Not Started</span>
					<span class="tast-list-status-count"><?php echo $total_notstarted; ?></span>
				</li>
                <li class="list-counters" <?php if(isset($counter_data['over_count']) && !empty($counter_data['over_count']) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:overdue" <?php } ?>>
                    <span class="tast-list-status-heading">Overdue</span>
                    <span class="tast-list-status-count bg-red"><?php echo (isset($counter_data['over_count']) ) ? $counter_data['over_count'] : 0; ?></span>
                </li>
				<li class="list-counters" <?php if(isset($counter_data['ns_count']) && !empty($counter_data['ns_count']) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:notset" <?php } ?>>
					<span class="tast-list-status-heading">Not Set</span>
					<span class="tast-list-status-count"><?php echo (isset($counter_data['ns_count']) ) ? $counter_data['ns_count'] : 0; ?></span>
				</li>
				<li class="list-counters" <?php if(isset($counter_data['com_count']) && !empty($counter_data['com_count']) ){ ?>data-remote="<?php echo SITEURL; ?>todos/index<?php echo $prj_link; ?>/day:completed" <?php } ?>>
					<span class="tast-list-status-heading">Completed</span>
					<span class="tast-list-status-count"><?php echo (isset($counter_data['com_count']) ) ? $counter_data['com_count'] : 0; ?></span>
				</li>
			</ul>
		</div>
	</div>
	<div class="clearfix"></div>

	   <div class="col-md-4 no-padding tast-list-right-wrap">

		<div   id="buttons_panel_sidebar" >
		<div class="todocombutton_bottom">
						<!-- <a href="javascript:void(0);" class="btn btn-xs btn-default tipText" title="Calendar"><i class="fa fa-calendar-check-o "></i></a>-->
						<input type="hidden" class="fillter_calender" name="fillter_calender" />
						<a href="javascript:void(0);" class="btn btn-xs tipText btn-primary disabled" title="Expand All" id="open">
							<i class="fa fa-expand"></i>
						</a>
						<a href="javascript:void(0);" class="btn btn-xs btn-primary tipText disabled" title="Collapse All" id="close">
							<i class="fa fa-compress"></i>
						</a>
						<a  class="btn btn-xs btn-success todo-esd tipText disabled" title="Edit"><i class="editwhite"></i></a>

						<button class="btn btn-xs btn-success todo-esd tipText disabled" title="Sign Off">
							<i class="signoffwhite"></i>
						</button>

						<button class="btn btn-xs btn-danger todo-esd tipText disabled" title="Delete"><i class="deletewhite"></i>
						</button>

						<?php
							if (isset($prj_id) && !empty($prj_id) && is_numeric($prj_id)) { ?>
							<a href="<?php echo SITEURL; ?>projects/index/<?php echo $prj_id; ?>" title="Open Project"  class="btn btn-xs btn-default tipText">
								<i class="fa fa-folder-open"></i>
							</a>
						<?php } ?>


					</div>
		</div>

        <div class="tast-list-right-wrap">

			<div class="tast-list-left-main todo_listing ">
                <?php
                $mycount = $this->Group->do_lists($prj_id, $day, $sdate, $edate, true);
                // echo $this->requestAction(array("action" => "getMyTodoCount", 'project' => $prj_id, 'day' => $day, "sdate" => $sdate, "edate" => $edate));
                // $mycount = $this->requestAction(array("action" => "getMyTodoCount", 'project' => $prj_id, 'day' => $day, "sdate" => $sdate, "edate" => $edate));
                ?>
                <div class="text-label" id="my_todos" data-chk="0" >Created (<?php echo $mycount; ?>)</div>
                <?php
                // Get all todos
                if (isset($do_lists) && !empty($do_lists)) {
                    ?>
                    <div id="do_list">
                        <div class="panel list-group list-todo">
                            <?php
                            foreach ($do_lists as $key => $val) {//pr($val);
                                $list = $val;
                                // pr($list['children']);
                                $list_rec_do_users = $this->Group->do_list_users($list['id']);
								$htmlupdate ='';
								if( $list['sign_off'] == 0 ){
									//$htmlupdate = 'onclick="updatetodo('.$list['id'].')"';
								}
								//onclick="updatetodo('echo $list['id'];')"
                                ?>
                                <div href="#" class="list-group-item" data-parent="#do_lists" data-id="<?php echo $list['id']; ?>" >
                                     <i class="todoslist" <?php echo $htmlupdate; ?> ></i>
                                    <a class="expand-collapse-link_later parent-todo-title" data-toggle="collapse" <?php //echo $htmlupdate;?> >
                                        <?php echo htmlentities($list['title'], ENT_QUOTES, "UTF-8"); ?>
                                    </a>


                                    <span class="tdates">
                                        <span>
                                            <?php if (isset($list['start_date']) && !empty($list['start_date'])) { ?>
                                                [ Start: <?php echo date("d M, Y", strtotime($list['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($list['end_date'])); ?> ]
                                            <?php } else { ?>
                                                [ Start: N/A - End: N/A ]
                                            <?php } ?>
                                        </span>
                                    </span>

                                    <span class="tdates">
                                        <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#do_list_detail_<?php echo $key; ?>">
                                        </i>
                                        <?php
                                        $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $list['id'], $my = true));
                                        //pr($status);
                                        ?>
                                        <?php
                                        $status_title = 'In Progress';
                                        $status_class = 'ps-flag progressing';
                                        if ($status == 'Completed') {
                                            $status_title = 'Completed';
                                            $status_class = 'ps-flag  bg-completed';
                                        } else if ($status == 'Overdue') {
                                            $status_title = 'Overdue';
                                            $status_class = 'ps-flag bg-overdue';
                                        } else if ($status == 'Not Started') {
                                            $status_title = 'Not Started';
                                            $status_class = 'ps-flag bg-not_started';
                                        } else if ($status == 'N/A') {
                                            $status_title = 'Not Set';
                                            $status_class = 'ps-flag bg-undefined';
                                        } else if ($status == 'Not Set') {
                                            $status_title = 'Not Set';
                                            $status_class = 'ps-flag bg-undefined';
                                        }

										$todoUploadCount = $this->ViewModel->todoUpload($list['id']);


                                        ?>
                                        <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" style="cursor:default;" title="<?php echo $status_title; ?>"></i>
										<?php if( isset($todoUploadCount) && $todoUploadCount > 0 ) {

                                            $uploadlists = $this->ViewModel->todoUploadlist($list['id']) ;
                                            $datacontentli = '';
                                            if (isset($uploadlists) && !empty($uploadlists)) {
                                                $datacontentli = '<div class="todouploadlistpop"><ul>';
                                                foreach ($uploadlists as $key => $todolistvalue) {

													$todolistvalue['DoListUpload']['file_name'] = str_replace("'", "", $todolistvalue['DoListUpload']['file_name']);
													$todolistvalue['DoListUpload']['file_name'] = str_replace('"', "",$todolistvalue['DoListUpload']['file_name']);


                                                    $datacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
                                                }
                                                $datacontentli .= '</ul></div>';
                                            }



                                        ?>
											<i data-todoid="<?php echo $list['id'];?>" id="todolist_<?php echo $list['id'];?>" class="tipTexts btn-xs btn btn-default text-green fa fa-folder-o poptodolist" data-toggle="modal" data-content='<?php echo $datacontentli; ?>'  title="<?php echo "Documents: ".$todoUploadCount; ?>"></i>
										<?php } else { ?>
											<i class="tipText btn-xs btn btn-default default-cursor fa fa-folder-o" title="<?php echo "No Documents"; ?>"></i>
                                        <?php }
                                        if (sizeof($list_rec_do_users) > 0 && is_array($list_rec_do_users)) {
                                            ?>
                                            <i class="all-do-user fa fa-user-victor tipText btn-xs btn btn-default" title="Show Team" data-toggle="collapse" href="#do_list_user_<?php echo $list['id']; ?>"></i>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                            $todo_comments = get_todo_comments($list['id'], true);
                                            if($todo_comments){
                                                $comment_class = 'text-green';
                                                $comment_title = 'Comments';
                                            }
                                            else{
                                                $comment_class = 'text-dark-gray';
                                                $comment_title = 'No Comments';
                                            }
                                        ?>
                                        <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
                                        <?php
                                        if (isset($list['children']) && sizeof($list['children']) > 0 && is_array($list['children'])) {
                                            ?>
                                            <i class="sub-level fa fa-level-down tipText  btn-xs btn btn-default"       href="#do_lists_<?php echo $key; ?>" title="Show Sub To-dos"></i><?php } ?>
                                        <?php
                                        if (isset($list['sign_off']) && isset($list['is_archive']) && $list['sign_off'] == 1 && $list['is_archive'] == 0) {

                                            if (isset($list['parent_id']) && !empty($list['parent_id'])) {

                                            } else {
                                                ?>
                                                <i title="Move To Archive" data-id="<?php echo $list['id']; ?>" class="move-to-archive fa fa-long-arrow-right tipText  btn-xs btn btn-default"></i>
                                                <?php
                                            }
                                        }
                                        ?>


                                    </span>
                                    <div class="collapse dolist_user" id="do_list_user_<?php echo $list['id']; ?>">
                                        <?php
                                        if (isset($list_rec_do_users) && !empty($list_rec_do_users)) {
                                            foreach ($list_rec_do_users as $user) {
                                                ?>
                                                <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                    <i class="fa fa-user"></i>
                                                </a>
                                                <?php
                                                echo $this->Common->userFullname($user) . '<br>';
                                            }
                                        } else {
                                            echo "No User";
                                        }
                                        ?>

                                    </div>
                                    <div class="collapse dolist_description" id="do_list_detail_<?php echo $list['id']; ?>">

                                        <?php
                                        $user_data = $this->ViewModel->get_user_data($list['user_id']);
                                        ?>
                                        <p>
                                            <span class="created-by">Created By:</span>
                                            <span class="creator" ><?php echo (isset($user_data) && !empty($user_data )) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span class="date" id="datecreated">Created On:</span>
                                            <span class="date" id="datecreated"><?php echo _displayDate($list['created']); ?></span>
                                        </p>
                                        <p>
                                            <span class="">Last Updated:</span>
                                            <span class=""><?php echo _displayDate($list['modified']); ?></span>
                                        </p>
                                        <p>
                                            <span class="updated-by">Updated By:</span>
                                           <span class="creator" ><?php echo (isset($user_data) && !empty($user_data)) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span>Sign Off:</span>
                                            <span><?php echo ($list['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                        </p>

                                    </div>

                                </div>
                                <?php
                                if (isset($list['children']) && !empty($list['children'])) {
                                    ?>
                                    <div id="do_lists_<?php echo $key; ?>" class="sublinks collapse">
                                        <?php
                                        foreach ($list['children'] as $k => $v) {
                                            $slist = $v;
                                            $list_rec_child_do_users = $this->Group->do_list_users($slist['id']);
											$htmlupdatestodo ='';
											if( $slist['sign_off'] == 0 ){
												//$htmlupdatestodo = 'onclick="updatetodo('.$slist['id'].')"';
											}
                                            ?>
                                            <div class="list-group-item small" data-id="<?php echo $slist['id']; ?>">
                                                <i class="sub-to-dos" <?php echo $htmlupdatestodo; ?>></i>

												<span class="child-todo-title" <?php //echo $htmlupdatestodo; ?> ><?php echo htmlentities($slist['title'], ENT_QUOTES, "UTF-8"); ?></span>

                                                <span class="tdates">
                                                    <span>
                                                        <?php if (isset($slist['start_date']) && !empty($slist['start_date'])) { ?>
                                                            [ Start: <?php echo date("d M, Y", strtotime($slist['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($slist['end_date'])); ?> ]
                                                        <?php } else { ?>
                                                            [ Start: N/A - End: N/A ]
                <?php } ?>
                                                    </span>
                                                </span>
                                                <span class="tdates">
                                                    <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#rec_sub_do_list_detail_<?php echo $slist['id']; ?>">
                                                    </i>
                                                    <?php
                                                    $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $slist['id'], $my = true));
                                                    //pr($status);
                                                    ?>
                                                    <?php
                                                    $status_title = 'In Progress';                                                     
													$status_class = 'ps-flag bg-progressing';
													if ($status == 'Completed') {
														$status_title = 'Completed';
														$status_class = 'ps-flag  bg-completed';
													} else if ($status == 'Overdue') {
														$status_title = 'Overdue';
														$status_class = 'ps-flag bg-overdue';
													} else if ($status == 'Not Started') {
														$status_title = 'Not Started';
														$status_class = 'ps-flag bg-not_started';
													} else if ($status == 'N/A') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													} else if ($status == 'Not Set') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													}													
                                                    ?>
                                                    <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" title="<?php echo $status_title; ?>" style="cursor:default;"></i>

													<?php
													$subTodoUploadCount = $this->ViewModel->todoUpload($slist['id']);
													$subTodoUploadlist = $this->ViewModel->todoUploadlist($slist['id']);
													$subtodohtml = '';
													if( isset($subTodoUploadlist) && !empty($subTodoUploadlist) ){
														foreach($subTodoUploadlist as $suploadlist){

															$subtodohtml .= '<li>'.$suploadlist['DoListUpload']['file_name'].'</li>';
														}
													}
													if( isset($subTodoUploadCount) && $subTodoUploadCount > 0 ) {


													$subuploadlists = $this->ViewModel->todoUploadlist($slist['id']) ;
													$sdatacontentli = '';
													if (isset($subuploadlists) && !empty($subuploadlists)) {
														$sdatacontentli = '<div class="todouploadlistpop"><ul>';
														foreach ($subuploadlists as $key => $todolistvalue) {
															$sdatacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
														}
														$sdatacontentli .= '</ul></div>';
													}

                                                     ?>
														<i id="subtodolist_<?php echo $slist['id'];?>" data-subtodoid="<?php echo $slist['id'];?>" align="right" class="tipTexts btn-xs btn btn-default text-green fa fa-folder-o subtodolist" data-toggle="modal" data-content='<?php echo $sdatacontentli; ?>' title="<?php echo "Documents: ".$subTodoUploadCount; ?>" ></i>

													<?php } else { ?>
														<i class="tipText btn-xs btn btn-default default-cursor fa fa-folder-o" title="<?php echo "No Documents"; ?>"></i>
													<?php }

                                                    if (sizeof($list_rec_child_do_users) > 0 && is_array($list_rec_child_do_users)) {
                                                        ?>
                                                        <i class="all-do-user fa-user-victor tipText btn-xs btn btn-default" title="Sub To-do Users" data-toggle="collapse" href="#do_child_list_user_<?php echo $slist['id'] . '_' . $key; ?>"></i>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                        $todo_comments = get_todo_comments($slist['id'], true);
                                                        if($todo_comments){
                                                            $comment_class = 'text-green';
                                                            $comment_title = 'Comments';
                                                        }
                                                        else{
                                                            $comment_class = 'text-dark-gray';
                                                            $comment_title = 'No Comments';
                                                        }
                                                    ?>
                                                    <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
                                                </span>
                                                <div class="collapse dolist_user" id="do_child_list_user_<?php echo $slist['id'] . '_' . $key; ?>">

                                                    <?php
                                                    if (isset($list_rec_child_do_users) && !empty($list_rec_child_do_users)) {
                                                        foreach ($list_rec_child_do_users as $user) {
                                                            ?>
                                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                                <i class="fa fa-user"></i>
                                                            </a>
                                                            <?php
                                                            echo $this->Common->userFullname($user) . '<br>';
                                                        }
                                                    } else {
                                                        echo "No User";
                                                    }
                                                    ?>

                                                </div>
                                                <div class="collapse dolist_description" id="rec_sub_do_list_detail_<?php echo $slist['id']; ?>">

													<?php
													$user_data_sub = $this->ViewModel->get_user_data($slist['user_id']);

													?>
                                                    <p>
                                                        <span class="created-by">Created By:</span>

														 <span class="creator" ><?php echo (isset($user_data_sub) && !empty($user_data_sub)) ? $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] : 'N/A'; ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="date" id="datecreated">Created On:</span>
                                                        <span class="date" id="datecreated"><?php echo _displayDate($slist['created']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="">Last Updated:</span>
                                                        <span class=""><?php echo _displayDate($slist['modified']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="updated-by">Updated By:</span>
                                                        <span class="creator" ><?php echo (isset($user_data_sub) && !empty($user_data_sub)) ? $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] : 'N/A'; ?></span>
                                                    </p>
                                                    <p>
                                                        <span>Sign Off:</span>
                                                        <span><?php echo ($slist['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                                    </p>

                                                </div>
                                            </div>
								<?php } ?>
                                    </div>
                                    <?php
                                } else if (!isset($list['parent_id']) || empty($list['parent_id'])) {
                                    ?>
                                    <div id="rec_do_lists_<?php echo $key; ?>" class="sublinks1 collapse">
                                        <a class="list-group-item small no-link">No Sub To-do</a>
                                    </div>
							<?php }
							?>

                    <?php }
                    ?>
                        </div>
                    </div>
                <?php } else {
                    ?>
                    <div class="no-todo" id="do_list">There are no created To-dos.</div>
                <?php
                }

                $reccount = $this->Group->rec_do_lists($prj_id, $day, $sdate, $edate, true);
                // $reccount = $this->requestAction(array("action" => "getRecTodoCount", 'project' => $prj_id, 'day' => $day, "sdate" => $sdate, "edate" => $edate));
                ?>

                <div class="text-label" id="rec_todos"  data-chk="0" >Received (<?php echo $reccount; ?>)</div>


                        <?php
                        if (isset($rec_do_lists) && !empty($rec_do_lists)) {
                            ?>
                    <div id="rec_do_lists">
                        <div class="panel list-group">
    <?php
    foreach ($rec_do_lists as $key => $val) {
        $list = $val;
        //pr($val);
        $list_rec_do_users = $this->Group->do_list_users($list['id']);
        ?>
                                <div href="#" class="list-group-item" data-parent="#rec_do_lists" data-id="<?php echo $list['id']; ?>" >
                                    <i class="fa fa-list-ul border-black" ></i>
                                    <a class="expand-collapse-link_later" data-toggle="collapse"  style="display: inline;">

                                            <?php echo htmlentities($list['title'], ENT_QUOTES, "UTF-8") ?>
                                    </a>

                                    <span class="tdates rectodo">
                                        <span>
        <?php if (isset($list['start_date']) && !empty($list['start_date'])) { ?>
                                                [ Start: <?php echo date("d M, Y", strtotime($list['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($list['end_date'])); ?> ]
        <?php } else { ?>
                                                [ Start: N/A - End: N/A ]
                                        <?php } ?>
                                        </span>
                                    </span>

                                    <span class="tdates">
                                        <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#rec_do_list_detail_<?php echo $list['id']; ?>">
                                        </i>
                                        <?php
                                        $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $list['id']));
                                        //pr($status);
                                        ?>
                                        <?php
													$status_title = 'In Progress';
													$status_class = 'ps-flag bg-progressing';
													if ($status == 'Completed') {
														$status_title = 'Completed';
														$status_class = 'ps-flag  bg-completed';
													} else if ($status == 'Overdue') {
														$status_title = 'Overdue';
														$status_class = 'ps-flag bg-overdue';
													} else if ($status == 'Not Started') {
														$status_title = 'Not Started';
														$status_class = 'ps-flag bg-not_started';
													} else if ($status == 'N/A') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													} else if ($status == 'Not Set') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													}

                                        $receivedTodoUploadCount = $this->ViewModel->todoUpload($list['id']);

                                        ?>
                                        <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" title="<?php echo $status_title; ?>" style="cursor:default;"></i>
										<?php if( isset($receivedTodoUploadCount) && $receivedTodoUploadCount > 0 ) {
										$recuploadlists = $this->ViewModel->todoUploadlist($list['id']) ;
										$recdatacontentli = '';
										if (isset($recuploadlists) && !empty($recuploadlists)) {
											$recdatacontentli = '<div class="todouploadlistpop "><ul>';
											foreach ($recuploadlists as $key => $todolistvalue) {
												$recdatacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
											}
											$recdatacontentli .= '</ul></div>';
										}

                                        ?>
											<i data-todoid="<?php echo $list['id'];?>" id="todolist_<?php echo $list['id'];?>" class="tipTexts btn-xs btn btn-default text-green fa fa-folder-o poptodolist"  data-toggle="modal"  data-content='<?php echo $recdatacontentli; ?>' title="<?php echo "Documents: ".$receivedTodoUploadCount; ?>"></i>
										<?php } else { ?>
											<i class="tipText default-cursor btn-xs btn btn-default fa fa-folder-o" title="<?php echo "No Documents"; ?>"></i>
                                        <?php } ?>

                                        <?php
                                        if (sizeof($list_rec_do_users) > 0 && is_array($list_rec_do_users)) {
                                            ?>
                                            <i class="all-do-user fa fa-user-victor tipText btn-xs btn btn-default" title="Show Team" data-toggle="collapse" href="#do_rec_list_user_<?php echo $key; ?>"></i>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                            $todo_comments = get_todo_comments($list['id'], true);
                                            if($todo_comments){
                                                $comment_class = 'text-green';
                                                $comment_title = 'Comments';
                                            }
                                            else{
                                                $comment_class = 'text-dark-gray';
                                                $comment_title = 'No Comments';
                                            }
                                        ?>
                                        <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
								<?php
								if (isset($list['children']) && sizeof($list['children']) > 0 && is_array($list['children'])) {
									?>
                                            <i class="sub-level fa fa-level-down tipText  btn-xs btn btn-default"   href="#rec_do_lists_<?php echo $key; ?>" title="Show Sub To-dos"></i>
                                            <?php
                                        }
                                        ?>
                                    </span>
                                    <div class="collapse dolist_user" id="do_rec_list_user_<?php echo $key; ?>">
                                        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $list['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                            <i class="fa fa-user"></i>
                                        </a>
                                        <?php
                                        echo $this->Common->userFullname($list['user_id']) . ' (Requester)<br>';
                                        if (isset($list_rec_do_users) && !empty($list_rec_do_users)) {
                                            foreach ($list_rec_do_users as $user) {
                                                ?>
                                                <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                    <i class="fa fa-user"></i>
                                                </a>
						<?php
						echo $this->Common->userFullname($user) . '<br>';
						}
					} else {
						echo "No User";
					}
					?>

                                    </div>
                                    <div class="collapse dolist_description" id="rec_do_list_detail_<?php echo $list['id']; ?>">

        <?php
        $user_data = $this->ViewModel->get_user_data($list['user_id']);
        ?>
                                        <p>
                                            <span class="created-by">Created By:</span>
                                           <span class="creator" ><?php echo (isset($user_data) && !empty($user_data)) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span class="date" id="datecreated">Created On:</span>
                                            <span class="date" id="datecreated"><?php echo _displayDate($list['created']); ?></span>
                                        </p>
                                        <p>
                                            <span class="">Last Updated:</span>
                                            <span class=""><?php echo _displayDate($list['modified']); ?></span>
                                        </p>
                                        <p>
                                            <span class="updated-by">Updated By:</span>
                                           <span class="creator" ><?php echo (isset($user_data) && !empty($user_data)) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span>Sign Off:</span>
                                            <span><?php echo ($list['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                        </p>

                                    </div>

                                </div>
        <?php
        if (isset($list['children']) && !empty($list['children'])) {
            ?>
                                    <div id="rec_do_lists_<?php echo $key; ?>" class="sublinks collapse">
            <?php
            foreach ($list['children'] as $k => $v) {
                $slist = $v;
                $list_rec_child_do_users = $this->Group->do_list_users($slist['id']);
                ?>
                                            <div class="list-group-item small" data-id="<?php echo $slist['id']; ?>">
                                                <i class="fa fa-list-ul border-black" style="pointer-events: none;" ></i>

                <?php echo htmlentities($slist['title'], ENT_QUOTES, "UTF-8"); ?>

                                                <span class="tdates">
                                                    <span>
                                                    <?php if (isset($slist['start_date']) && !empty($slist['start_date'])) { ?>
                                                            [ Start: <?php echo date("d M, Y", strtotime($slist['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($slist['end_date'])); ?> ]
                                                    <?php } else { ?>
                                                            [ Start: N/A - End: N/A ]
                                                    <?php } ?>
                                                    </span>
                                                </span>
                                                <span class="tdates">
                                                    <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#rec_sub_do_list_detail_<?php echo $slist['id']; ?>">
                                                    </i>
                                                    <?php
                                                    $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $slist['id']));
                                                    //pr($status);
                                                    ?>
                                                    <?php
                                                    $status_title = 'In Progress';
                                                    $status_class = 'ps-flag bg-progressing';
													if ($status == 'Completed') {
														$status_title = 'Completed';
														$status_class = 'ps-flag  bg-completed';
													} else if ($status == 'Overdue') {
														$status_title = 'Overdue';
														$status_class = 'ps-flag bg-overdue';
													} else if ($status == 'Not Started') {
														$status_title = 'Not Started';
														$status_class = 'ps-flag bg-not_started';
													} else if ($status == 'N/A') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													} else if ($status == 'Not Set') {
														$status_title = 'Not Set';
														$status_class = 'ps-flag bg-undefined';
													}

                                                    $receivedSubTodoUploadCount = $this->ViewModel->todoUpload($slist['id']);
                                                    ?>
                                                    <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" title="<?php echo $status_title; ?>"></i>

                                                    <?php if( isset($receivedSubTodoUploadCount) && $receivedSubTodoUploadCount > 0 ) {

													$recsubuploadlists = $this->ViewModel->todoUploadlist($list['id']) ;
													$recsubdatacontentli = '';
													if (isset($recsubuploadlists) && !empty($recsubuploadlists)) {
														$recsubdatacontentli = '<div class="todouploadlistpop "><ul>';
														foreach ($recsubuploadlists as $key => $todolistvalue) {
																	$todolistvalue['DoListUpload']['file_name'] = str_replace("'", "", $todolistvalue['DoListUpload']['file_name']);
																	$todolistvalue['DoListUpload']['file_name'] = str_replace('"', "",$todolistvalue['DoListUpload']['file_name']);

																	pr($todolistvalue['DoListUpload']['file_name']);

															$recsubdatacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
														}
														$recsubdatacontentli .= '</ul></div>';
													}




                                                     ?>
                                                        <i data-todoid="<?php echo $slist['id'];?>" id="todolist_<?php echo $slist['id'];?>" class="tipTexts btn-xs btn btn-default text-green fa fa-folder-o poptodolist" data-toggle="modal" data-content='<?php echo $recsubdatacontentli; ?>' title="<?php echo "Documents: ".$receivedSubTodoUploadCount; ?>"></i>
                                                    <?php } else { ?>
                                                        <i class="tipText btn-xs btn btn-default default-cursor fa fa-folder-o" title="<?php echo "No Documents"; ?>"></i>
                                                    <?php } ?>

                                                    <?php if (sizeof($list_rec_child_do_users) > 0 && is_array($list_rec_child_do_users)) { ?>
                                                        <i class="all-do-user fa-user-victor tipText btn-xs btn btn-default" title="Sub To-do Users" data-toggle="collapse" href="#do_rec_child_list_user_<?php echo $slist['id']; ?>"></i>
                                                    <?php } ?>
                                                    <?php
                                                        $todo_comments = get_todo_comments($slist['id'], true);
                                                        if($todo_comments){
                                                            $comment_class = 'text-green';
                                                            $comment_title = 'Comments';
                                                        }
                                                        else{
                                                            $comment_class = 'text-dark-gray';
                                                            $comment_title = 'No Comments';
                                                        }
                                                    ?>
                                                    <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
                                                </span>
                                                <div class="collapse dolist_user" id="do_rec_child_list_user_<?php echo $slist['id']; ?>">
                                                    <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $slist['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                        <i class="fa fa-user"></i>
                                                    </a>
                                                    <?php
                                                    echo $this->Common->userFullname($slist['user_id']) . ' (Requester)<br>';
                                                    if (isset($list_rec_child_do_users) && !empty($list_rec_child_do_users)) {
                                                        foreach ($list_rec_child_do_users as $user) {
                                                            ?>
                                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                                <i class="fa fa-user"></i>
                                                            </a>
                                                            <?php
                                                            echo $this->Common->userFullname($user) . '<br>';
                                                        }
                                                    } else {
                                                        echo "No User";
                                                    }
                                                    ?>

                                                </div>
                                                <div class="collapse dolist_description" id="rec_sub_do_list_detail_<?php echo $slist['id']; ?>">

													<?php
													$user_data_sub = $this->ViewModel->get_user_data($slist['user_id']);
													?>
                                                    <p>
                                                        <span class="created-by">Created By:</span>
                                                         <span class="creator" ><?php echo (isset($user_data_sub) && !empty($user_data_sub)) ? $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] : 'N/A'; ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="date" id="datecreated">Created On:</span>
                                                        <span class="date" id="datecreated"><?php echo _displayDate($slist['created']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="">Last Updated:</span>
                                                        <span class=""><?php echo _displayDate($slist['modified']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="updated-by">Updated By:</span>
                                                         <span class="creator" ><?php echo (isset($user_data_sub) && !empty($user_data_sub)) ? $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] : 'N/A'; ?></span>
                                                    </p>
                                                    <p>
                                                        <span>Sign Off:</span>
                                                        <span><?php echo ($slist['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                                    </p>

                                                </div>
                                            </div>
                            <?php } ?>
                                    </div>
                            <?php
                        } else if (!isset($list['parent_id']) || empty($list['parent_id'])) {
                            ?>
                                    <div id="rec_do_lists_<?php echo $key; ?>" class="sublinks1 collapse">
                                        <a class="list-group-item small no-link">No Sub To-do</a>
                                    </div>
                        <?php }
                        ?>

    <?php } ?>
                        </div>
                    </div>
                <?php } else {
                    ?>
                    <div class="no-todo" id="rec_do_lists">There are no received To-dos.</div>
<?php
}


$archivecount = $this->Group->archive_do_lists($prj_id, $day, $sdate, $edate, true);
// $archivecount = $this->requestAction(array("action" => "getIsArchiveCount", 'project' => $prj_id, 'day' => $day, "sdate" => $sdate, "edate" => $edate));
?>


                <div class="text-label" id="archive_todos"  data-chk="0" >Archived (<?php echo $archivecount; ?>)</div>


                                <?php
                                if (isset($archive_do_lists) && !empty($archive_do_lists)) {
                                    ?>
                    <div id="archive__do_lists">
                        <div class="panel list-group">
                                        <?php
                                        foreach ($archive_do_lists as $key => $val) {
                                            $list = $val;
                                            $list_rec_do_users = $this->Group->do_list_users($list['id']);
                                            ?>
                                <div href="#" class="list-group-item" data-parent="#archive_do_lists" data-id="<?php echo $list['id']; ?>" >
                                    <i class="fa fa-list-ul border-black"></i>
                                    <a class="expand-collapse-link" data-toggle="collapse" href="#archive_do_lists_<?php echo $key; ?>"  style="display: inline;">
                                       <!-- <i class="fa fa-list-ul border-black"></i> -->
        <?php echo htmlentities($list['title'], ENT_QUOTES, "UTF-8"); ?>
                                    </a>

                                    <span class="tdates">
                                        <span>
                                        <?php if (isset($list['start_date']) && !empty($list['start_date'])) { ?>
                                                [ Start: <?php echo date("d M, Y", strtotime($list['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($list['end_date'])); ?> ]
                                        <?php } else { ?>
                                                [ Start: N/A - End: N/A ]
                                        <?php } ?>
                                        </span>
                                    </span>

                                    <span class="tdates">
                                        <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#archive_list_detail_<?php echo $list['id']; ?>">
                                        </i>
                                        <?php
                                        $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $list['id'], $my = true));
                                        //pr($status);
                                        ?>
                                        <?php
                                        $status_title = 'In Archived';
                                        $status_class = 'fa text-archive text-archive-flag';
                                        ?>
                                        <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" title="<?php echo $status_title; ?>" style="cursor:default;"></i>



<?php

$archiveTodoUploadCount = $this->ViewModel->todoUpload($list['id']);

if( isset($archiveTodoUploadCount) && $archiveTodoUploadCount > 0 ) {

$arcsubuploadlists = $this->ViewModel->todoUploadlist($list['id']) ;
    $arcsubdatacontentli = '';
    if (isset($arcsubuploadlists) && !empty($arcsubuploadlists)) {
        $arcsubdatacontentli = '<div class="todouploadlistpop "><ul>';
        foreach ($arcsubuploadlists as $key => $todolistvalue) {
			pr($todolistvalue['DoListUpload']['file_name']);
				$todolistvalue['DoListUpload']['file_name'] = str_replace("'", "", $todolistvalue['DoListUpload']['file_name']);
				$todolistvalue['DoListUpload']['file_name'] = str_replace('"', "",$todolistvalue['DoListUpload']['file_name']);

            $arcsubdatacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
        }
        $arcsubdatacontentli .= '</ul></div>';
    }



 ?>
    <i data-todoid="<?php echo $list['id'];?>" id="todolist_<?php echo $list['id'];?>" class="tipTexts btn-xs btn btn-default text-green fa fa-folder-o poptodolist" data-toggle="modal" data-content='<?php echo $arcsubdatacontentli; ?>' title="<?php echo "Documents: ".$archiveTodoUploadCount; ?>"></i>
<?php } else { ?>
    <i class="tipText btn-xs btn btn-default fa fa-folder-o default-cursor" title="<?php echo "No Documents"; ?>"></i>
<?php } ?>


                                        <?php
                                        if (sizeof($list_rec_do_users) > 0 && is_array($list_rec_do_users)) {
                                            ?>
                                            <i class="all-do-user fa fa-user-victor tipText btn-xs btn btn-default" title="Show Team" data-toggle="collapse" href="#archive_do_list_user_<?php echo $list['id']; ?>"></i>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                            $todo_comments = get_todo_comments($list['id'], true);
                                            if($todo_comments){
                                                $comment_class = 'text-green';
                                                $comment_title = 'Comments';
                                            }
                                            else{
                                                $comment_class = 'text-dark-gray';
                                                $comment_title = 'No Comments';
                                            }
                                        ?>
                                        <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
        <?php
        if (isset($list['children']) && sizeof($list['children']) > 0 && is_array($list['children'])) {
            ?>
                                            <i class="sub-level fa fa-level-down tipText  btn-xs btn btn-default"       href="#archive_do_lists_<?php echo $key; ?>" title="Show Sub To-dos"></i>
                                            <?php
                                        }
                                        ?>





                                    </span>
                                    <div class="collapse dolist_user" id="archive_do_list_user_<?php echo $list['id']; ?>">
                                        <?php
                                        if (isset($list['user_id']) && $list['user_id'] != $this->Session->read("Auth.User.id")) {
                                            ?>
                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $list['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                <i class="fa fa-user"></i>
                                            </a>
                                            <?php
                                            echo $this->Common->userFullname($list['user_id']) . ' (Requester)<br>';
                                        }
                                        ?>
                                        <?php
                                        if (isset($list_rec_do_users) && !empty($list_rec_do_users)) {
                                            foreach ($list_rec_do_users as $user) {
                                                ?>
                                                <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                    <i class="fa fa-user"></i>
                                                </a>
                                                <?php
                                                echo $this->Common->userFullname($user) . '<br>';
                                            }
                                        } else {
                                            echo "No User";
                                        }
                                        ?>

                                    </div>
                                    <div class="collapse dolist_description" id="archive_list_detail_<?php echo $list['id']; ?>">

        <?php
        $user_data = $this->ViewModel->get_user_data($list['user_id']);
        ?>
                                        <p>
                                            <span class="created-by">Created By:</span>
                                            <span class="creator" ><?php echo (isset($user_data) && !empty($user_data)) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span class="date" id="datecreated">Created On:</span>
                                            <span class="date" id="datecreated"><?php echo _displayDate($list['created']); ?></span>
                                        </p>
                                        <p>
                                            <span class="">Last Updated:</span>
                                            <span class=""><?php echo _displayDate($list['modified']); ?></span>
                                        </p>
                                        <p>
                                            <span class="updated-by">Updated By:</span>
                                           <span class="creator" ><?php echo (isset($user_data) && !empty($user_data)) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></span>
                                        </p>
                                        <p>
                                            <span>Sign Off:</span>
                                            <span><?php echo ($list['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                        </p>

                                    </div>

                                </div>
        <?php
        if (isset($list['children']) && !empty($list['children'])) {
            ?>
                                    <div id="archive_do_lists_<?php echo $key; ?>" class="sublinks collapse">
            <?php
            foreach ($list['children'] as $k => $v) {
                $slist = $v;
                $list_rec_child_do_users = $this->Group->do_list_users($slist['id']);
                ?>
                                            <div class="list-group-item small" data-id="<?php echo $slist['id']; ?>">
                                                <i class="fa fa-list-ul border-black" style="pointer-events: none;" ></i>

                <?php echo htmlentities($slist['title'], ENT_QUOTES, "UTF-8"); ?>

                                                <span class="tdates">
                                                    <span>
                                                    <?php if (isset($slist['start_date']) && !empty($slist['start_date'])) { ?>
                                                            [ Start: <?php echo date("d M, Y", strtotime($slist['start_date'])); ?> - End: <?php echo date("d M, Y", strtotime($slist['end_date'])); ?> ]
                                                    <?php } else { ?>
                                                            [ Start: N/A - End: N/A ]
                                                    <?php } ?>
                                                    </span>
                                                </span>
                                                <span class="tdates">
                                                    <i title="More Detail" style="cursor: pointer;" class="fa tipText fa-chevron-down show-detail btn-xs btn btn-default" data-toggle="collapse" href="#archive_sub_do_list_detail_<?php echo $slist['id'] . '_' . $key; ?>">
                                                    </i>
                                                    <?php
                                                    $status = $this->requestAction(array("controller" => "todos", "action" => "get_status", $slist['id'], $my = true));
                                                    //pr($status);
                                                    ?>
                                                    <?php
                                                    $status_title = 'In Archived';
                                                    $status_class = 'fa text-archive text-archive-flag';
                                                    ?>
                                                    <i class="tipText <?php echo $status_class; ?>  btn-xs btn btn-default" title="<?php echo $status_title; ?>" style="cursor:default;"></i>

<?php

$archiveSubTodoUploadCount = $this->ViewModel->todoUpload($slist['id']);

if( isset($archiveSubTodoUploadCount) && $archiveSubTodoUploadCount > 0 ) {

$arcssubuploadlists = $this->ViewModel->todoUploadlist($slist['id']) ;
    $arcssubdatacontentli = '';
    if (isset($arcssubuploadlists) && !empty($arcssubuploadlists)) {
        $arcssubdatacontentli = '<div class="todouploadlistpop "><ul>';
        foreach ($arcssubuploadlists as $key => $todolistvalue) {

				$todolistvalue['DoListUpload']['file_name'] = str_replace("'", "", $todolistvalue['DoListUpload']['file_name']);
				$todolistvalue['DoListUpload']['file_name'] = str_replace('"', "",$todolistvalue['DoListUpload']['file_name']);
            $arcssubdatacontentli .='<li><a href="'.SITEURL.TODO.$todolistvalue['DoListUpload']['file_name'].'" class="todoimglink" download >'.$todolistvalue['DoListUpload']['file_name'].'</a></li>';
        }
        $arcssubdatacontentli .= '</ul></div>';
    }



 ?>
    <i data-todoid="<?php echo $slist['id'];?>" id="todolist_<?php echo $slist['id'];?>" class="tipTexts btn-xs btn btn-success fa fa-folder-o poptodolist" data-toggle="modal" data-content='<?php echo $arcssubdatacontentli; ?>' title="<?php echo "Documents: ".$archiveSubTodoUploadCount; ?>"></i>
<?php } else { ?>
    <i class="tipText btn-xs btn btn-default fa fa-folder-o default-cursor" title="<?php echo "No Documents"; ?>"></i>
<?php } ?>

                                                    <?php
                                                    if (sizeof($list_rec_child_do_users) > 0 && is_array($list_rec_child_do_users)) {
                                                        ?>
                                                        <i class="all-do-user fa-user-victor tipText btn-xs btn btn-default" title="Sub To-do Users" data-toggle="collapse" href="#archive_child_list_user_<?php echo $slist['id'] . '_' . $key; ?>"></i>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                        $todo_comments = get_todo_comments($list['id'], true);
                                                        if($todo_comments){
                                                            $comment_class = 'text-green';
                                                            $comment_title = 'Comments';
                                                        }
                                                        else{
                                                            $comment_class = 'text-dark-gray';
                                                            $comment_title = 'No Comments';
                                                        }
                                                    ?>
                                                    <i class="todo_comments fa fa-comments tipText btn-xs btn btn-default <?php echo $comment_class; ?>" title="<?php echo $comment_title; ?>" ></i>
                                                </span>
                                                <div class="collapse dolist_user" id="archive_child_list_user_<?php echo $slist['id'] . '_' . $key; ?>">
                                                    <?php
                                                    if (isset($list['user_id']) && $slist['user_id'] != $this->Session->read("Auth.User.id")) {
                                                        ?>
                                                        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $slist['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                            <i class="fa fa-user"></i>
                                                        </a>
                    <?php
                    echo $this->Common->userFullname($slist['user_id']) . ' (Requester)<br>';
                }
                ?>
                                                    <?php
                                                    if (isset($list_rec_child_do_users) && !empty($list_rec_child_do_users)) {
                                                        foreach ($list_rec_child_do_users as $user) {
                                                            ?>
                                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $user; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                                <i class="fa fa-user"></i>
                                                            </a>
                        <?php
                        echo $this->Common->userFullname($user) . '<br>';
                    }
                } else {
                    echo "No User";
                }
                ?>

                                                </div>
                                                <div class="collapse dolist_description" id="archive_sub_do_list_detail_<?php echo $slist['id'] . '_' . $key; ?>">

                <?php
                $user_data_sub = $this->ViewModel->get_user_data($slist['user_id']);
                ?>
                                                    <p>
                                                        <span class="created-by">Created By:</span>
                                                        <span class="creator" ><?php echo $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="date" id="datecreated">Created On:</span>
                                                        <span class="date" id="datecreated"><?php echo _displayDate($slist['created']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="">Last Updated:</span>
                                                        <span class=""><?php echo _displayDate($slist['modified']); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="updated-by">Updated By:</span>
                                                        <span class="creator"><?php echo $user_data_sub['UserDetail']['first_name'] . ' ' . $user_data_sub['UserDetail']['last_name'] ?></span>
                                                    </p>
                                                    <p>
                                                        <span>Sign Off:</span>
                                                        <span><?php echo ($slist['sign_off'] == 0) ? 'NO' : 'Yes'; ?></span>
                                                    </p>

                                                </div>
                                            </div>
                                    <?php } ?>
                                    </div>
                                    <?php
                                } else if (!isset($list['parent_id']) || empty($list['parent_id'])) {
                                    ?>
                                    <div id="rec_do_lists_<?php echo $key; ?>" class="sublinks1 collapse">
                                        <a class="list-group-item small no-link">No Sub To-do</a>
                                    </div>
                        <?php }
                        ?>

                    <?php }
                    ?>
                        </div>
                    </div>
<?php } else {
    ?>
                    <div class="no-todo" id="archive__do_lists">There are no archived To-dos.</div>
<?php }
?>
            </div>
        </div>
    </div>



    <div class="col-md-8 no-padding task-list-left-wrap">

        <div class="task-list-left-wrap">

            <div class="tast-list-left-main" id="comments_list">



                <div class="task-list-left-tabs">
                    <ul class="nav nav-tabs comments">
                        <li class="active">
                            <a href="#all" class="active" data-toggle="tab">All</a>
                        </li>
                        <li>
                            <a href="#people" data-toggle="tab">People</a>
                        </li>
						<div class=" left-main-header clear-boxs">
							<!--<h5 class="pull-left">Comments</h5>-->
							<a data-todo-type="ToDoComment" data-remote="" class="btn btn-sm btn-access pull-right select-dolist ">Add Comment</a>
						</div>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="all">
                            <h4 class="no-comments">Select a To-do to view comments.</h4>
                        </div>
                        <div class="tab-pane fade" id="people">
                            <h4 class="no-comments">Select a To-do to view comments.</h4>
                        </div>
                    </div>
                    <a class="btn btn-success btn-sm select-dolist" data-remote="" data-todo-type="ToDoComment">Add Comment</a>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="popup_model_box_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<!-- END MODAL BOX -->
<script type="text/javascript" >
    $('.move-to-archive').on('click', function (event) {
        event.preventDefault();
        var $that = $(this),
                data = $that.data();
        if (data.id != '') {
            var params = {do_list_id: data.id};
            BootstrapDialog.show({
                title: 'Archive To-do',
                message: 'Are you sure you want to archive this To-do?',
                type: BootstrapDialog.TYPE_SUCCESS,
                draggable: true,
                buttons: [
                    {
                        label: ' Archive',
                        cssClass: 'btn-success',
                        autospin: true,
                        action: function (dialogRef) {
                            $.when(
                                    $.ajax({
                                        url: $js_config.base_url + 'todos/archive_dolist',
                                        type: "POST",
                                        data: $.param(params),
                                        dataType: "JSON",
                                        global: false,
                                        success: function (response) {
                                            if (response.success) {
                                                $that.addClass('disabled')
                                            }
                                        }
                                    })
                                    ).then(function (data, textStatus, jqXHR) {
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                dialogRef.getModalBody().html('<div class="loader">Loading...</div>');
                                setTimeout(function () {
                                    dialogRef.close();
                                    window.location.reload()
                                }, 500);
                            })
                        }
                    },
                    {
                        label: ' Cancel',
                        //icon: 'fa fa-times',
                        cssClass: 'btn-danger',
                        action: function (dialogRef) {
                            dialogRef.close();
                        }
                    }
                ]
            });
        }
    })





    jQuery.fn.toggleAttr = function (attr, attr1, attr2) {
        return this.each(function () {
            var self = $(this);
            if (self.attr(attr) == attr1)
                self.attr(attr, attr2);
            else
                self.attr(attr, attr1);
        });
    };
    $(function () {


        $("#popup_model_box_profile").on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        })
        $('.show-detail').on('click', function () {

            $(this).parents('.list-group-item.selected').find(".dolist_user").removeClass("in");

            $(this).toggleClass('fa-chevron-up fa-chevron-down')

            if ($(this).hasClass('fa-chevron-up')) {
                $(this).attr('data-original-title', 'Less Detail');
            }
            else {
                $(this).attr('data-original-title', 'More Detail');
            }
        });

        $('.all-do-user').on('click', function () {
            $(this).parents('.list-group-item:first').find(".dolist_description").removeClass("in");



			if ($(this).parents('.list-group-item:first').find(".show-detail").hasClass('fa-chevron-up')) {
                $(this).parents('.list-group-item:first').find(".show-detail").attr('data-original-title', 'More Detail');
				$(this).parents('.list-group-item:first').find(".show-detail").toggleClass('fa-chevron-down fa-chevron-up');
            }
            else {
              //  $(this).parents('.list-group-item:first').find(".show-detail").attr('data-original-title', 'More Detail');
            }


        });

        $('.expand-collapse-link, .sub-level').on('click', function (event) {
            if ($(event.target).is('.sub-level')) {
                $(this).toggleClass('fa-level-up fa-level-down', 1000)
                $(this).toggleAttr('data-original-title', 'Hide Sub To-dos', 'Show Sub To-dos');
                // $($(this).attr('href')).removeAttr('style');
                if ($($(this).attr('href')).hasClass('collapse')) {
                    $($(this).attr('href')).toggleClass('in');
                } else {
                    $($(this).attr('href')).addClass('collapse');
                    $($(this).attr('href')).toggleClass('in');
                }

            }
            else {
                $(this).parents(".list-group-item:first").find(".sub-level").toggleClass('fa-level-up fa-level-down');
                $(this).parents(".list-group-item:first").find(".sub-level").toggleAttr('data-original-title', 'Hide Sub To-dos', 'Show Sub To-dos');
            }
        });


    });

	function updatetodo(todo_id) {
      //  $(this).preventDefault();
		location.href = $js_config.base_url+'todos/manage/'+todo_id;
	}

    $("#my_todos, #rec_todos, #archive_todos").popover('destroy');


    $('.poptodolist,.subtodolist').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        container: 'body',
        //template: '<div class="popover todouploadlistpop" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
        delay: {show: 50, hide: 400}
    });

</script>

<style>
    .project-users {
        max-height: 92px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 10px 5px;
        border-left:1px solid #cccccc;
        min-height:92px;
        display:inline-block;
		float: left;
    }
	
	.default-cursor{ cursor : default ; }
	
    .project-users .user-image {
        border: 1px solid rgb(204, 204, 204);
        margin: 1px;
        width: 45px;
    }

    @media (min-width:768px) and (max-width:1024px) {
        .project-users {
            max-height: 100px;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 10px 5px;
            border-left:1px solid #cccccc;

             display:inline-block;

        }

    }
</style>