<?php

$current_user_id = $this->Session->read('Auth.User.id');
	 
$current_org = $this->Permission->current_org();
if( isset($do_list_id) && !empty($do_list_id) ) {

	$dolist_detail = $this->Group->dolist_detail($do_list_id);


?>


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
				<a data-id="<?php echo $do_list_id ?>" data-toggle="modal" data-target="#todo_model" data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "manage_comment", $do_list_id), true); ?>" class="btn btn-sm btn-success pull-right addcomments <?php if(isset($dolist_detail['DoList']['sign_off']) && !empty($dolist_detail['DoList']['sign_off'])){ ?>disabled<?php } ?>">Add Comment</a>
			</div>

		</ul>
		<div id="myTabContent" class="tab-content">
			<div class="tab-pane fade active in" id="all">
				<?php
				if( isset($data) && !empty($data) ) {
				?>
				<ul class="people-list" id="update-comment-list">
					<?php
					foreach( $data as $key => $val ) {
						$comments = $val['DoListComment'];
						$job_title = 'Not Available';
					?>
					<li>
						 
							<?php
								$user_data = $this->ViewModel->get_user_data($comments['user_id']);
								
								$current_org_other = $this->Permission->current_org($comments['user_id']);
								$job_title = isset($user_data['UserDetail']['job_title']) ? htmlentities($user_data['UserDetail']['job_title'],ENT_QUOTES) : 'N/A' ;
								$pic =  isset($user_data['UserDetail']['profile_pic']) ? $user_data['UserDetail']['profile_pic'] : 'N/A' ;
								$profiles = SITEURL . USER_PIC_PATH . $pic;

								$html = '';
								if( $comments['user_id'] != $current_user_id ) {
									$project_id = null;
									if(isset($dolist_detail['DoList']['project_id']) && !empty($dolist_detail['DoList']['project_id']))
										$project_id = $dolist_detail['DoList']['project_id'];
									$html = CHATHTML($comments['user_id'], $project_id);
								}

								if(!empty($pic) && file_exists(USER_PIC_PATH.$pic)){
									$profiles = SITEURL . USER_PIC_PATH . $pic;
								}else{
									$profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
								}
								// echo $profiles;
							?>
                                                    <a href="#" style="float: none;"  data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $comments['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
													
							<span class="style-popple-icon-out">
							<span class="style-popple-icon" style="cursor: default;">						
							<img src="<?php echo $profiles ?>" class="img-circledds pophover" title="" align="left" data-content="<div><p><?php echo isset( $user_data['UserDetail']) ? htmlentities($user_data['UserDetail']['first_name'],ENT_QUOTES) . ' '. htmlentities($user_data['UserDetail']['last_name'],ENT_QUOTES) : 'N/A'; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" />
                               <?php   if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
								<i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
							<?php }  ?>

						</span></span>                     
													
													</a>
							

						<div class="comment-people-info people-info">
							<!--<h2><?php echo nl2br($comments['comments']); ?></h2>-->
							<h2><?php echo nl2br(htmlentities($comments['comments'],ENT_QUOTES)); ?></h2>
							<p class="doc-type">
								<?php
								$uploads = $this->Group->do_comment_uploads($comments['id']);
								if( isset($uploads) && !empty($uploads) ) {
									foreach($uploads as $upkey => $up) {
										$upval = $up['DoListCommentUpload'];
										$ext = pathinfo($upval['file_name']);
								?>
									<span class="dolist-document">
											<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
												<span class="icon_text"><?php echo $ext['extension']; ?></span>
											</span>
										<a download  href="<?php echo SITEURL . DO_LIST_COMMENT . $upval['file_name'];?>"  class="tipText "><?php echo $ext['filename'] ?></a>
									</span>
								<?php
									}
								}
								else { ?>
									<!--<span class="dolist-document">
										No Attachment
									</span>-->
								<?php }
								?>
							</p>
							<p class="created-date">
							<?php
							// echo $comments['created']."<br />";
							 //echo date('d M, Y h:iA',strtotime($comments['created']));
							 echo _displayDate($comments['modified']);

							$logedin_user = $this->Session->read("Auth.User.id");

							$likes = $this->Group->do_comment_likes($comments['id']);

							$like_posted = like_posted($logedin_user, $comments['id']);

							$disabledCls = '';
							$disablests = 0;
							if(isset($dolist_detail['DoList']['sign_off']) && !empty($dolist_detail['DoList']['sign_off'])){
									$disabledCls = "disabled";
									$disablests = 1;
							}

							if($logedin_user == $comments['user_id']) {

							?>

								<a class="btn btn-xs btn-default tipText like_no_comment <?php echo $disabledCls; ?>" data-remote="" data-original-title="Likes">
									<i class="fa fa-thumbs-o-up"></i>
									<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
								</a>

								<a  href="#" class="btn btn-xs btn-default tipText <?php echo $disabledCls; ?>" <?php if( $disablests == 0){?> data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "manage_comment", $do_list_id, $comments['id']), true); ?>"  data-toggle="modal" data-target="#todo_model" <?php } ?> data-original-title="Edit comment" ><i class="fa fa-pencil"></i></a>

								<a class="btn btn-xs btn-danger tipText delete_comment <?php echo $disabledCls; ?>" <?php if( $disablests == 0){?>  data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "delete_comment", $comments['id']), true); ?>" <?php } ?> data-original-title="Delete comment"><i class="fa fa-trash"></i>
								</a>
							<?php
							}
							else
							{


							?>
								<a class="btn btn-xs btn-default tipText <?php if( $like_posted ){ ?>disabled<?php }else{ ?>like_comment<?php } ?>  <?php echo $disabledCls; ?>" <?php if( $disablests == 0){?>   data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "like_comment", $comments['id']), true); ?>" <?php } ?>data-original-title="Like comment">
									<i class="fa fa-thumbs-o-up"></i>
									<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
								</a>
								<!-- <a href="#" data-original-title="Edit comment"  class="tipText disable btn btn-xs btn-default comment_resticted" ><i class="fa fa-pencil"></i></a>
								<a href="#" data-original-title="Delete comment" class="tipText disable btn btn-xs btn-danger comment_resticted" ><i class="fa fa-trash"></i></a> -->
							<?php
							}
							?>
							</p>
						</div>
					</li>
					<?php } ?>
				</ul>
				<?php }
				else {
				?>
				<h4 class="no-comments">No Comments have been posted.</h4>
				<?php }


				?>
			</div>
			<div class="tab-pane fade" id="people">
				<?php
				 $people_list = null;
				$people = $this->Group->dolist_users($do_list_id);
				 if(isset($dolist_detail) && !empty($dolist_detail['DoList']['user_id'])){
				 $owner = $dolist_detail['DoList']['user_id'];

					$user_dataOwner = $this->ViewModel->get_user_data($dolist_detail['DoList']['user_id']);
					$people_list[$dolist_detail['DoList']['user_id']] = isset( $user_dataOwner['UserDetail']) ? htmlentities($user_dataOwner['UserDetail']['first_name'],ENT_QUOTES) . ' '. htmlentities($user_dataOwner['UserDetail']['last_name'],ENT_QUOTES) : 'N/A';
				}
				if( isset($people) && !empty($people) ) {
					foreach($people as $key => $user ) {

						$user_data = $this->ViewModel->get_user_data($user['DoListUser']['user_id']);

						$people_list[$user_data['UserDetail']['user_id']] = isset( $user_data['UserDetail']) ? htmlentities($user_data['UserDetail']['first_name'],ENT_QUOTES) . ' '. htmlentities($user_data['UserDetail']['last_name'],ENT_QUOTES) : 'N/A';
					}


				}
					echo $this->Form->input("do_list_id", array( "type"=>"hidden",  "id"=>"people_do_list_id", "value" => $do_list_id));
					echo $this->Form->input("people_select",array("title"=>"Select User", "multiple"=>"multiple", "id"=>"people_select", "class"=>"form-control aqua", "options"=>$people_list, "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));
				?>
				<ul class="people-list" id="people-comment-list" style="margin-top: 10px; border-top: 1px solid rgb(204, 204, 204) ! important; padding-top: 10px;">

				</ul>

			</div>
		</div>

		<a data-id="<?php echo $do_list_id ?>" data-toggle="modal" data-target="#todo_model" data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "manage_comment", $do_list_id), true); ?>" class="btn btn-sm btn-success <?php if(isset($dolist_detail['DoList']['sign_off']) && !empty($dolist_detail['DoList']['sign_off'])){ ?>disabled<?php } ?>">Add Comment</a>
	</div>


<script type="text/javascript" >
	$(function(){
		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
		$('.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		})
		$('body').on('click', function (e) {
			$('.pophover').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					var $that = $(this);
					$that.popover('hide');
				}
			});
		});

	})
</script>

<script type="text/javascript" >
$(function(){
    $(".comment_resticted").click( function (event) {
        event.preventDefault();
        var $current = $(this);
        BootstrapDialog.alert({
            title: 'WARNING',
            message: 'You are not authorized to access that location!',
            type: BootstrapDialog.TYPE_WARNING,
            callback: function (result) {
            }
        });
    })

	// setTimeout(function(){
		$('#people_select').multiselect({
			maxHeight: '400',
			buttonWidth: '100%',
			buttonClass: 'btn btn-info',
			// checkboxName: 'data[DoListUser][user_id][]',
			enableFiltering: true,
			filterBehavior: 'text',
			includeFilterClearBtn: true,
			enableCaseInsensitiveFiltering: true,
			// numberDisplayed: 3,
			includeSelectAllOption: true,
			includeSelectAllIfMoreThan: 5,
			selectAllText: ' Select all',
			// disableIfEmpty: true
			onDropdownHide: function( ) {
				var brands = $('#people_select option:selected');
				var selected = {};
				$(brands).each(function(index, brand) {
					selected[index] = $(this).val();
				});

				if( !$.isEmptyObject(selected) ) {

					$.ajax({
						type:'POST',
						data: $.param({ 'do_list_id': $('#people_do_list_id').val(), 'users': selected }),
						dataType: 'json',
						url: $js_config.base_url + 'todos/people_comments',
						global: true,
						success: function( response ) {
							$("#people-comment-list").html(response)
						}
					});
				}
			}
		});
	// }, 2000)
})
</script>
<?php } ?>
