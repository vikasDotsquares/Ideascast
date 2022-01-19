<?php 

 echo $this->Html->script(array(        
	'star-rating'
	));

echo $this->Html->css(
		array(
			 
			'star-rating'	
		)
);
$current_user_id = $this->Session->read('Auth.User.id'); 
if (isset($blog_list) && !empty($blog_list)) {

//pr($blog_list); die;
	
foreach ($blog_list as $comment_list) { ?>
<li id="page-comment-<?php echo $comment_list['BlogComment']['id'];?>">
    <div class="comment-people-pic">
    <?php $user_data= $this->ViewModel->get_user_data($comment_list['BlogComment']['user_id']); 
		$pic = $user_data['UserDetail']['profile_pic']; 
		$profiles = SITEURL . USER_PIC_PATH . $pic; 
		if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) { 
		
		$profiles = SITEURL . USER_PIC_PATH . $pic; 
		
		} else { 
		$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif'; } 
		
		$job_title = $user_data['UserDetail']['job_title'];
		$html = '';
		if( $comment_list['BlogComment']['user_id'] != $current_user_id ) {
			$html = CHATHTML($comment_list['BlogComment']['user_id'],$project_id);
		}
		$user_name = $this->Common->userFullname($comment_list['BlogComment']['user_id']);
		$current_org = $this->Permission->current_org();
		$current_org_other = $this->Permission->current_org($comment_list['BlogComment']['user_id']);
		
	?>
        <a data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $comment_list['BlogComment']['user_id'];?>" data-target="#popup_modal" data-toggle="modal" href="#"  >
		<img class="pophover" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd" />
		<?php  if($current_org !=$current_org_other){ ?>
				<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
		<?php } ?>
		</a>

    </div>
    <div class="comment-people-info people-info" style="margin-left: 62px;">
        <p>
            <?php echo nl2br($comment_list[ 'BlogComment'][ 'description']); ?>
        </p>
        <!--  -->
        <p class="doc-type" id="comment_doc_list">
            <?php $documents = $comment_list[ 'BlogDocument']; 
				if (isset($documents) && !empty($documents)) { 
					foreach ($documents as $doc_key=> $doc_val) { 
						$urlofdoc = SITEURL . DO_LIST_BLOG_DOCUMENTS . $doc_val['document_name']; 
						//if(file_exists($urlofdoc)){ 
						$ext = pathinfo($doc_val['document_name']); ?>
            <span class="dolist-document"><span class="download_asset icon_btn icon_btn_sm icon_btn_teal"><span class="icon_text"><?php echo $ext['extension']; ?></span>
            </span>
            <a class="tipText " href="<?php echo $urlofdoc; ?>" download="download" title="<?php echo $doc_val['document_name']; ?>">
                <?php echo $ext[ 'filename']; ?>
            </a>
            </span>
            <?php //} 
				} 
			} ?>
        </p>
<div class="set-div-align">
        <p class="created-date set-margn">
            <?php 
			//echo date( 'd M, Y H:i A', strtotime($comment_list[ 'BlogComment'][ 'created'])); 
			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($comment_list[ 'BlogComment'][ 'updated'])),$format = 'd M, Y g:i A'); 
			?>&nbsp;&nbsp;
            <?php $logedin_user= $this->Session->read("Auth.User.id"); 
					$likes = $this->TeamTalk->blog_comment_likes($comment_list['BlogComment']['id']); 
					$like_posted = $this->TeamTalk->comment_like_posted($logedin_user, $comment_list['BlogComment']['id']); 
					$comments['id'] = $comment_list['BlogComment']['id']; 
					if ($logedin_user == $comment_list['BlogComment']['user_id']) { ?>
            <a class="btn btn-xs btn-default tipText like_no_comment" disabled="disabled" data-remote="" data-original-title="Likes">
                <i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
            </a>

            <a data-toggle="modal" data-target="#modal_edit_blogComments_list" id="edit_blog_comment" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/addedit_comment_blog/blog_id:<?php echo $comment_list['BlogComment']['blog_id']; ?>/comment_id:<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText edit_blog_comment" data-toggle="modal" data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>

            <a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment" data-original-title="Delete comment"><i class="fa fa-trash"></i>
													</a>

            <?php } else { ?>
            <a id="blog_comment_likes" data-value="<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_user_comment<?php } ?>" data-original-title="Like comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple" id="commentcounters<?php echo $comments['id']; ?>"><?php echo ($likes) ? $likes : 0; ?></span></a>
            <?php } ?>
        </p>
		<div class="rate_blog pull-right"> <input disabled="disabled" data-id="<?php echo $comment_list['BlogComment']['id'];?>"  id="input-<?php echo $comment_list['BlogComment']['id'];?>1" value="<?php echo $comment_list['BlogComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>	
    </div>
    </div>
</li>
<?php } } else { ?>
<li style="border:none;">No comments found</li>
<?php } ?>

<script type="text/javascript" >
 $(function(){	
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
 
.set-margn{ margin-top: 10px !important; width: 40%;float: left ; }
.set-div-align{ width:100%; overflow:hidden ; display:block; }
</style>