<?php
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));

$current_user_id = $this->Session->read('Auth.User.id'); 

if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
$type = (isset($type) && !empty($type)) ? $type : "admincomment";
?>
<div class="panel-group" id="page-accordion-blog">
<?php
    if (isset($bloglist) && !empty($bloglist)) {
		//pr($bloglist);
		foreach ($bloglist as $blog) {
			 
            $blog_id  = $blog['Blog']['id'];			
			$blogconter = $this->TeamTalk->getBlogCounter($project_id,$blog_id);
			$userBlog = $this->TeamTalk->userBlogLike($blog['Blog']['project_id'],$blog_id,$this->Session->read('Auth.User.id'));
				
?>
            <div class="panel panel-default page-collapse-<?php echo $blog['Blog']['id'] ?>">
                <div class="panel-heading bg-curious-Blue">
                    <h4 class="panel-title">
                        <a class="accordion-toggle page-accordion collapsed" data-toggle="collapse" href="#<?php echo $blog['Blog']['id'];?>-page-accordion" data-parent="#page-accordion-blog"><?php 
						echo $this->Text->truncate(
								$blog['Blog']['title'],
								45,
								array(
									'ellipsis' => '...',
									'exact' => false
								)
							);
						//echo $blog['Blog']['title']; ?></a>
                    </h4>
                </div>
                <div id="<?php echo $blog['Blog']['id'];?>-page-accordion" class="panel-collapse collapse adm-blg-usr-dtl">
				
                    <?php 
					$all = $this->TeamTalk->blogUsers($blog['Blog']['id']);
					$blogUserCount = (isset($all) && !empty($all) && count($all) > 0 )? count($all) : 0;
					if( isset($all) && !empty($all) && count($all) > 0 ){
					$i=0;
					$blgCmntCnt=0;
					$blgDocCnt = 0;
					foreach($all as $cmtUsers){ 							
							
							$commentCnt = $this->TeamTalk->userBlogCommentCnt($blog['Blog']['id'], $cmtUsers);  
							$documentCnt = $this->TeamTalk->userBlogDocumentCnt($blog['Blog']['id'], $blog['Blog']['project_id'], $cmtUsers);
							
							$blgCmntCnt = $this->TeamTalk->blogCommentCount($blog['Blog']['id'], $cmtUsers);
							$blgDocCnt = $this->TeamTalk->blogDocumentCount($blog['Blog']['id'], $cmtUsers);
							//echo $blogUserCount."=="; 
							$current_org = $this->Permission->current_org();
							$current_org_other = $this->Permission->current_org($cmtUsers);
							
							if( $i == 0 && $blogUserCount != 1 ) { $divClass = "idea-admin-doc-com";} else { $divClass = "";  }
							
					?>
						<div class="panel-body padding admin-right-side-section <?php echo $divClass; ?>">
							<div class="comment-people-pic">
								<?php
								$user_data = $this->ViewModel->get_user_data($cmtUsers);
								$pic = $user_data['UserDetail']['profile_pic'];
								$profiles = SITEURL . USER_PIC_PATH . $pic;

								if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
									$profiles = SITEURL . USER_PIC_PATH . $pic;
								} else {
									$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
								}
								
								$job_title = $user_data['UserDetail']['job_title'];
								$html = '';
								if( $cmtUsers != $current_user_id ) {
									$html = CHATHTML($cmtUsers, $blog['Blog']['project_id']);
								}
								$user_name = $this->Common->userFullname($cmtUsers);
								
								
								?>
								<a data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $cmtUsers;?>" data-target="#popup_modal" data-toggle="modal" href="#"  >
								<img class="pophover" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd" />
								<?php  if($current_org !=$current_org_other){ ?>
								<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
								<?php } ?>
								</a>

							</div>
							
							<ul class="list-inline">								
								
								<?php /* <li class="btn btn-xs btn-default"><a class="blog_admin_comment tipText" data-ctype="show_admin_blog" data-project="<?php echo $blog['Blog']['project_id']; ?>" data-blogid="<?php echo $blog['Blog']['id']; ?>" data-bloguser="<?php echo $cmtUsers;?>" title="Show Blog" >
								<img src="<?php echo SITEURL.'img/blog-icon-black-300.png' ?>" width="20" />&nbsp;<?php echo $this->TeamTalk->userTotalBlog($blog['Blog']['project_id'],$cmtUsers);?></a></li> */ ?>
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($commentCnt) && $commentCnt > 0 ){?>blog_admin_comment<?php } ?> tipText btn btn-xs" data-ctype="show_comment" data-project="<?php echo $blog['Blog']['project_id']; ?>" data-blogid="<?php echo $blog['Blog']['id']; ?>" data-bloguser="<?php echo $cmtUsers;?>" title="Show Comment" ><i class="fa fa-comments"></i>&nbsp;<?php echo $commentCnt; ?></a></li>
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($documentCnt) && $documentCnt > 0 ){?>blog_admin_comment<?php } ?> tipText btn btn-xs" data-ctype="show_document" data-project="<?php echo $blog['Blog']['project_id']; ?>" data-blogid="<?php echo $blog['Blog']['id']; ?>"  data-bloguser="<?php echo $cmtUsers;?>" title="Show Document"><i class="fa fa-folder-o"></i>&nbsp;<?php echo $documentCnt; ?></a></li>
								
							</ul>
							<h5 class="bh_head_admin"><?php echo $user_data['UserDetail']['first_name'].' '.$user_data['UserDetail']['last_name']; ?></h5>
							<?php if($i == 0 && $blogUserCount > 1){?>
							<ul class="list-inline admin-corner-icons">
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($blgCmntCnt) && $blgCmntCnt > 0 ){?>blog_admin_comment<?php } ?> text-center tipText btn btn-xs" data-ctype="show_all_comment" data-project="<?php echo $blog['Blog']['project_id']; ?>" title="All Blog Comments" data-blogid="<?php echo $blog['Blog']['id']; ?>" data-bloguser="<?php echo $cmtUsers;?>" ><i class="fa fa-comments nomargin"></i>&nbsp;<?php //echo $blgCmntCnt; ?></a></li>
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($blgDocCnt) && $blgDocCnt > 0 ){?>blog_admin_comment<?php } ?> text-center tipText btn btn-xs" data-ctype="show_all_document" data-project="<?php echo $blog['Blog']['project_id']; ?>" title="All Blog Documents" data-blogid="<?php echo $blog['Blog']['id']; ?>"  data-bloguser="<?php echo $cmtUsers;?>" ><i class="fa fa-folder-o nomargin"></i>&nbsp;<?php //echo $blgDocCnt; ?></a></li>								
							</ul>
							<?php } ?>
							
						</div>
					<?php $i++; }
					
					}  else {	
						
						$commentCnt = $this->TeamTalk->userBlogCommentCnt($blog['Blog']['id'], $blog['Blog']['user_id']);  
						$documentCnt = $this->TeamTalk->userBlogDocumentCnt($blog['Blog']['id'], $blog['Blog']['project_id'], $blog['Blog']['user_id']);
						
						$blgCmntCnt = $this->TeamTalk->blogCommentCount($blog['Blog']['id']);
						$blgDocCnt = $this->TeamTalk->blogDocumentCount($blog['Blog']['id']);
						
					?>
						<div class="panel-body padding admin-right-side-section">
							<div class="comment-people-pic">
								<?php
								$user_data = $this->ViewModel->get_user_data($blog['Blog']['user_id']);
								$pic = $user_data['UserDetail']['profile_pic'];
								$profiles = SITEURL . USER_PIC_PATH . $pic;

								if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
									$profiles = SITEURL . USER_PIC_PATH . $pic;
								} else {
									$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
								}
								
								$job_title = $user_data['UserDetail']['job_title'];
								
								$cmtUsers = $blog['Blog']['user_id'];
								$html = '';
								if( ( isset($cmtUsers) && !empty($cmtUsers) ) && $cmtUsers != $current_user_id ) {
									$html = CHATHTML($cmtUsers,$blog['Blog']['project_id']);
								}
								$user_name = $this->Common->userFullname($cmtUsers);
								$current_org = $this->Permission->current_org();
								$current_org_other = $this->Permission->current_org($cmtUsers);								
								?>
								<a data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $blog['Blog']['user_id'];?>" data-target="#popup_modal" data-toggle="modal" href="#" >
								<img class="pophover" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>"  src="<?php echo $profiles ?>" class="img-circledd" />
								<?php  if($current_org !=$current_org_other){ ?>
								<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
								<?php } ?>
								</a>

							</div>
							<ul class="list-inline">
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($commentCnt) && $commentCnt > 0 ){?>blog_admin_comment<?php } ?> tipText btn btn-xs" data-ctype="show_comment" data-project="<?php echo $blog['Blog']['project_id']; ?>" data-blogid="<?php echo $blog['Blog']['id']; ?>" data-bloguser="<?php echo $blog['Blog']['user_id'];?>" title="Show Comment" ><i class="fa fa-comments"></i>&nbsp;<?php echo $commentCnt; ?></a></li>
								
								<li class="btn btn-xs btn-default"><a class="<?php if(isset($documentCnt) && $documentCnt > 0 ){?>blog_admin_comment<?php } ?> tipText btn btn-xs" data-ctype="show_document" data-project="<?php echo $blog['Blog']['project_id']; ?>" data-blogid="<?php echo $blog['Blog']['id']; ?>"  data-bloguser="<?php echo $blog['Blog']['user_id'];?>" title="Show Document"><i class="fa fa-folder-o"></i>&nbsp;<?php echo $documentCnt; ?></a></li>
								
							</ul>
							<h5 class="bh_head_admin"><?php echo $user_data['UserDetail']['first_name'].' '.$user_data['UserDetail']['last_name']; ?></h5>
							
							<?php /*<ul class="list-inline admin-corner-icons">
								
								<li class="btn btn-xs btn-default"><a class="blog_admin_comment text-center tipText btn btn-xs" data-ctype="show_all_comment" data-project="<?php echo $blog['Blog']['project_id']; ?>" title="All Blog Comments" data-blogid="<?php echo $blog['Blog']['id']; ?>" data-bloguser="<?php echo $blog['Blog']['user_id'];?>" ><i class="fa fa-comments nomargin"></i>&nbsp;<?php //echo $blgCmntCnt; ?></a></li>
								
								<li class="btn btn-xs btn-default"><a class="blog_admin_comment text-center tipText btn btn-xs" data-ctype="show_all_document" data-project="<?php echo $blog['Blog']['project_id']; ?>" title="All Blog Documents" data-blogid="<?php echo $blog['Blog']['id']; ?>"  data-bloguser="<?php echo $blog['Blog']['user_id'];?>" ><i class="fa fa-folder-o nomargin"></i>&nbsp;<?php //echo $blgDocCnt; ?></a></li>
								
							</ul> */ ?>
													
							
						</div>
					<?php } ?>	
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="text-center noblog"> No blog found</div>
        <?php
    }
    ?>
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
    });		 

		
})	
</script>