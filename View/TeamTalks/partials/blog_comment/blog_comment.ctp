<?php $is_full_permission_to_current_login = false;
 echo $this->Html->script(array(        
	'star-rating'
	));

echo $this->Html->css(
		array(
			 
			'star-rating'	
		)
);
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));

if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}

if (( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
            $is_full_permission_to_current_login = true;
}

$current_user_id = $this->Session->read('Auth.User.id'); 

?>
<style>
#comment_doc_list{
	margin-top:14px;
}
.text-label {
  background: #367fa9 none repeat scroll 0 0;
  color: #ffffff;
  cursor: pointer;
  display: block;
  margin: 0 0 4px;
  padding: 5px;
  width: 100%;
}
</style>

<div class="wiki-right-section">
    <div id="comments_list" class="tast-list-left-main">
        
        <div class=" left-main-header clear-box">
            <!-- <h5 class="pull-left">Comments</h5> -->
            <a class="btn btn-sm btn-default pull-right disabled_comment_list add_blogcomments" data-id="1" id="blogcomments_add" title="Add blog Comment" class="tipText" style="cursor: pointer;" data-original-title="Add Comment"  >Add Comment or Update a file</a>
			
        </div>

        <div class="task-list-left-tabs">
            <ul class="nav nav-tabs comments padding-bottom">
                <li class="active">
                    <a aria-expanded="true" href="#all" class="active" data-toggle="tab">All</a>
                </li>
                <li class="">
                    <a aria-expanded="false" href="#people" data-toggle="tab">Team Members</a>
                </li>
				<li class="">
                    <a aria-expanded="false" href="#relevance" data-toggle="tab">Popular</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
			
				<div class="tab-pane fade" id="relevance">
                    <ul class="people-list" id="blog-comment-list">
                        <?php						
                        if (isset($relevanceData) && !empty($relevanceData)) {
                            foreach ($relevanceData as $comment_list) {
                                $current_org = $this->Permission->current_org();
								$current_org_other = $this->Permission->current_org($comment_list['BlogComment']['user_id']);
                                ?>
                                <li id="page-comment-<?php echo $comment_list['BlogComment']['id'];?>">
                                    <div class="comment-people-pic">
                                        <?php
                                        $user_data = $this->ViewModel->get_user_data($comment_list['BlogComment']['user_id']);
                                        $pic = $user_data['UserDetail']['profile_pic'];
                                        $profiles = SITEURL . USER_PIC_PATH . $pic;

                                        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                        } else {
                                            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                                        }
										
										$job_title = $user_data['UserDetail']['job_title'];
										$html = '';
										if( $comment_list['BlogComment']['user_id'] != $current_user_id ) {
											$html = CHATHTML($comment_list['BlogComment']['user_id'],$project_id);
										}
										$user_name = $this->Common->userFullname($comment_list['BlogComment']['user_id']);
										
                                        ?>
                                        <a data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $comment_list['BlogComment']['user_id'];?>" data-target="#popup_modal" data-toggle="modal" href="#" >
										<img class="pophover" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>"  src="<?php echo $profiles ?>" class="img-circledd "  />
										<?php if($current_org !=$current_org_other){ ?>
										<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
										<?php } ?>
										</a>

                                    </div>
                                    <div class="comment-people-info people-info" style="margin-left: 62px;">
                                        <p><?php echo nl2br($comment_list['BlogComment']['description']); ?></p>
										<!--  -->
                                            <?php
                                            if (isset($comment_list['BlogDocument']) && !empty($comment_list['BlogDocument'])) {
												$documents = $comment_list['BlogDocument'];
											?>
                                        <p class="doc-type" id="comment_doc_list">
                                              <?php  foreach ($documents as $doc_key => $doc_val) {
                                                    $urlofdoc = SITEURL . DO_LIST_BLOG_DOCUMENTS . $doc_val['document_name'];
                                                    //if(file_exists($urlofdoc)){
                                                    $ext = pathinfo($doc_val['document_name']);
                                                    ?>
                                                    <span class="dolist-document">
                                                        <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                            <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                        </span>
                                                        <a class="tipText " href="<?php echo $urlofdoc; ?>" download="<?php echo $doc_val['document_name']; ?>" title="<?php echo $doc_val['document_name']; ?>">
                <?php echo $ext['filename']; ?>
                                                        </a>
                                                    </span>
                <?php
                //}
            } ?>
			
                                        </p>
			<?php
        }
        ?>
										<div class="set-div-align">
										<p class="created-date set-margn"><?php 
										//echo date('d M, Y g:i A', strtotime($comment_list['BlogComment']['created'])); 
										echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($comment_list['BlogComment']['updated'])),$format = 'd M, Y g:i A');
										?>&nbsp;&nbsp;
										<?php
												$logedin_user = $this->Session->read("Auth.User.id");

												$likes = $this->TeamTalk->blog_comment_likes($comment_list['BlogComment']['id']);
												
												$like_posted = $this->TeamTalk->comment_like_posted($logedin_user, $comment_list['BlogComment']['id']);
												
												$comments['id'] = $comment_list['BlogComment']['id'];		
												
												if ($logedin_user == $comment_list['BlogComment']['user_id']) {
													
												 
												?>
													<a class="btn btn-xs btn-default tipText like_no_comment" disabled="disabled" data-remote="" data-original-title="Likes">
														<i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
													</a>
												    
													<a data-toggle="modal" data-target="#modal_edit_blogComments_list" id="edit_blog_comment" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/addedit_comment_blog/blog_id:<?php echo $comment_list['BlogComment']['blog_id']; ?>/comment_id:<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText edit_blog_comment" data-toggle="modal"  data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>
													
													<a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment" data-original-title="Delete comment"><i class="fa fa-trash"></i>
													</a>
													
											<?php
												} else {
													?>						
													<a id="blog_comment_likes" data-value="<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_comment<?php } ?>" data-original-title="Like comment" ><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple" id="commentcounters<?php echo $comments['id']; ?>"><?php echo ($likes) ? $likes : 0; ?></span></a>
											<?php
											}
											 
										?>
										</p> 
										<?php
										/* 	if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login==true){
												$clds = '';
											}else{
												$clds = 'disabled';
											} */
										?>
										<div class="rate_blog pull-right"> <input disabled="disabled" data-id="<?php echo $comment_list['BlogComment']['id'];?>"  id="input-<?php echo $comment_list['BlogComment']['id'];?>1" value="<?php echo $comment_list['BlogComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>		
                                    </div>
									
									
                                    </div>
                                </li>
        <?php
    }
} else {
    ?>
                            <li style="border:none;">No comments found</li>
<?php
    }
?>
                    </ul>					
                </div>
			
				
                <div class="tab-pane fade active in" id="all">
                    <ul class="people-list" id="blog-comment-list">
                        <?php
						 
                        if (isset($blog_list) && !empty($blog_list)) {
                            foreach ($blog_list as $comment_list) {
                                $current_org = $this->Permission->current_org();
								$current_org_other = $this->Permission->current_org($comment_list['BlogComment']['user_id']);
                                ?>
                                <li id="page-comment-<?php echo $comment_list['BlogComment']['id'];?>">
                                    <div class="comment-people-pic">
                                        <?php
                                        $user_data = $this->ViewModel->get_user_data($comment_list['BlogComment']['user_id']);
                                        $pic = $user_data['UserDetail']['profile_pic'];
                                        $profiles = SITEURL . USER_PIC_PATH . $pic;

                                        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                        } else {
                                            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                                        }
										
										$job_title = $user_data['UserDetail']['job_title'];
										$html = '';
										if( $comment_list['BlogComment']['user_id'] != $current_user_id ) {
											$html = CHATHTML($comment_list['BlogComment']['user_id'],$project_id);
										}
										$user_name = $this->Common->userFullname($comment_list['BlogComment']['user_id']);
										
                                        ?>
                                        <a data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $comment_list['BlogComment']['user_id'];?>" data-target="#popup_modal" data-toggle="modal" href="#"  >
										<img class="pophover"  data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd "  />
										<?php if($current_org !=$current_org_other){ ?>
										<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
										<?php } ?>
										</a>

                                    </div>
                                    <div class="comment-people-info people-info" style="margin-left: 62px;">
                                        <p><?php echo nl2br($comment_list['BlogComment']['description']); ?></p>
										<!--  -->
                                        
                                            <?php
											$documents = $comment_list['BlogDocument'];
                                            if (isset($documents) && !empty($documents)) {
											?>
											<p class="doc-type" id="comment_doc_list">
											<?php
                                                foreach ($documents as $doc_key => $doc_val) {
                                                    $urlofdoc = SITEURL . DO_LIST_BLOG_DOCUMENTS . $doc_val['document_name'];
                                                    //if(file_exists($urlofdoc)){
                                                    $ext = pathinfo($doc_val['document_name']);
                                                    ?>
                                                    <span class="dolist-document">
                                                        <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                            <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                        </span>
                                                        <a class="tipText " href="<?php echo $urlofdoc; ?>" download="<?php echo $doc_val['document_name']; ?>" title="<?php echo $doc_val['document_name']; ?>">
                <?php echo $ext['filename']; ?>
                                                        </a>
                                                    </span>
                <?php
                //}
            }
			?>
			   </p>
			<?php
        }
        ?>
                                     
										<div class="set-div-align">
										<p class="created-date set-margn"><?php 
										//echo date('d M, Y g:i A', strtotime($comment_list['BlogComment']['created'])); 
										echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($comment_list['BlogComment']['updated'])),$format = 'd M, Y g:i A'); 
										?>&nbsp;&nbsp;
										<?php
												$logedin_user = $this->Session->read("Auth.User.id");

												$likes = $this->TeamTalk->blog_comment_likes($comment_list['BlogComment']['id']);
												
												$like_posted = $this->TeamTalk->comment_like_posted($logedin_user, $comment_list['BlogComment']['id']);
												
												$comments['id'] = $comment_list['BlogComment']['id'];		
												
												if ($logedin_user == $comment_list['BlogComment']['user_id']) {
													
												 
												?>
													<a class="btn btn-xs btn-default tipText like_no_comment" disabled="disabled" data-remote="" data-original-title="Likes">
														<i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
													</a>
												    
													<a data-toggle="modal" data-target="#modal_edit_blogComments_list" id="edit_blog_comment" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/addedit_comment_blog/blog_id:<?php echo $comment_list['BlogComment']['blog_id']; ?>/comment_id:<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText edit_blog_comment" data-toggle="modal"  data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>
													
													<a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment" data-original-title="Delete comment"><i class="fa fa-trash"></i>
													</a>
													
											<?php
												} else {
													?>						
													<a id="blog_comment_likes" data-value="<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_comment<?php } ?>" data-original-title="Like comment" ><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple" id="commentcounters<?php echo $comments['id']; ?>"><?php echo ($likes) ? $likes : 0; ?></span></a>
											<?php
											}
											 
										?>
										</p> 
										
										<div class="rate_blog pull-right"> <input disabled="disabled" data-id="<?php echo $comment_list['BlogComment']['id'];?>"  id="input-<?php echo $comment_list['BlogComment']['id'];?>" value="<?php echo $comment_list['BlogComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>		
                                    </div>
									
									
                                    </div>
                                </li>
        <?php
    }
} else {
    ?>
                            <li style="border:none;">No comments found</li>
<?php
    }
?>
                    </ul>
					
                </div>
				<script type="text/javascript" >
				        /* $('body').delegate('#people_change', 'change', function(event){
							event.preventDefault();
							var $that = $(this),
									data = $that.data(),
									href = data.remote,
									blog_id = $that.attr("data-blogid"),
									project_id = $that.attr("data-projectid")
									
								if( href != '' ) {
									$.ajax({
											url: href,
											type: "POST",
											data: $.param({user_id:$that.val(),project_id:project_id,blog_id:blog_id}),
											global: true,
											success: function (response) { 												
												 $("#people #blog-update-comment-list").html(response)   
											}
									})
								} 

						}) */
				</script>
				<div class="tab-pane fade" id="people">
				<div class="row">
				
                
                    <?php                    
					$users =  $this->TeamTalk->getBlogUsersWithName($project_id);							
					$bAlUser = array();
					foreach($users as $userlists ){ 
						$userfname = $this->Common->userFullname($userlists['BlogComment']['user_id']);						
						$bAlUser[$userlists['BlogComment']['user_id']] = $userfname;
					}
					
                    $remote = SITEURL."team_talks/get_blog_comment_by_user";
                    ?><div class="col-lg-4"> <?php
					echo $this->Form->input("user_id",array("id"=>"people_change","multiple"=>true,"type"=>"select","options"=>$bAlUser,"div"=>false,"label"=>false,"class"=>"form-control aqua","data-projectid"=>$project_id, "data-blogid"=>"","data-remote"=>$remote)); 
                    ?>
					</div>
                    
                    <ul class="people-list" id="blog-update-comment-list"></ul>

                
				</div>
				</div>
            </div>
			<a class="btn btn-sm btn-default pull-left disabled_comment_list add_blogcomments" data-id="1" id="blogcomments_add" title="Add blog Comment" class="tipText" style="cursor: pointer;" data-original-title="Add Comment" >Add Comment or Update a file</a>            
        </div>
    </div>
</div>
<script type="text/javascript" >
    $(function(){
		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
            $('.tooltip').hide()
        });

		$('#people_change option').removeAttr('selected');	
		
        $('#people_change').multiselect({
			
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
            onInitialized: function() {

            },
            onChange: function(element, checked) {

        }
        });
		
		
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });		 

		
    })

</script>
<style>
#tabContent3 #comments_list .clear-rating{ display:none !important; }
.set-margn{ margin-top: 10px !important; width: 40%;float: left ; }
.set-div-align{ width:100%; overflow:hidden ; display:block; }
</style>