<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<div class="comments">
    <div class="">
        <div class="pull-left">Documents</div> 
        <div class="pull-right">
				<a class="tipText" title="Add Document" data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'blog_document_uploads', 'blog_id'=>$this->request->data['blog_id'], 'project_id'=>$this->request->data['project_id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $this->request->data['blog_id'];?>" data-area="<?php echo $this->request->data['blog_id'];?>" id="create_wiki" data-target="#modal_edit_blogpost" data-toggle="modal" style="cursor: pointer;" ><i class="fa fa-folder-o"></i></a>				
				<a  class="tipText" title="Show Comments"  style="cursor: pointer;" data-rel="<?php echo $this->request->data['blog_id']; ?>" data-project="<?php echo $this->request->data['project_id']; ?>" id="list_blog_comments" data-blog_id="<?php echo $this->request->data['blog_id']; ?>" ><i class="fa fa-commenting-o"></i></a>                
        </div>
    </div>
    <div class="">
        <ul class="blog-comment-lists list-group" id="bdoc-list">
		<?php			
			//pr($blog_documents);
			if (isset($blog_documents) && !empty($blog_documents)) {

				//pr($blog_documents); exit;
				foreach($blog_documents as $document){
					$documents = $document['BlogDocument'];
					$up = $document['BlogDocument'];
		
		?>
        <li id="bcommentlists<?php echo $documents['id']; ?>">
            <div class="comment-people-pic">
                <?php
                $user_data = $this->ViewModel->get_user_data($documents['user_id']);
                $pic = $user_data['UserDetail']['profile_pic'];
                $profiles = SITEURL . USER_PIC_PATH . $pic;
				

                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }
                				
				$job_title = $user_data['UserDetail']['job_title'];
				$html = '';
				if( $documents['user_id'] != $current_user_id ) {
					if( !empty($documents['project_id']) ){
						$html = CHATHTML($documents['user_id'],$documents['project_id']);
					} else {
						$html = CHATHTML($documents['user_id']);
					}	
					
				}
				$user_name = $this->Common->userFullname($documents['user_id']);
				$current_org = $this->Permission->current_org();
				$current_org_other = $this->Permission->current_org($documents['user_id']);
				
                ?>
                <a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $documents['user_id']; ?>"  >
				<img class="pophover"  data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd" alt="Personal Image" />
				<?php  if($current_org !=$current_org_other){ ?>
				<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
				<?php } ?>
				</a>
            </div>

            <div class="comment-people-info people-info">
				<!-- <h2><?php //echo nl2br($documents['title']); ?></h2> -->
                <p class="created-date">
				<?php echo _displayDate($documents['created']); ?>
				</p><br>
                <p class="created-date" >
                    <?php
                    $logedin_user = $this->Session->read("Auth.User.id");
                    if ($logedin_user == $documents['user_id']) { ?>
                        <!-- <a data-value="<?php //echo $documents['id']; ?>" data-remote="<?php //echo Router::Url(array("controller" => "team_talks", "action" => "edit_blog_document", 'id'=>$documents['id'], 'project_id'=>$this->request->data['project_id'] ), true); ?>" class="btn btn-xs btn-default tipText" data-toggle="modal"  data-original-title="Edit document" data-target="#modal_edit_blogpost"><i class="fa fa-pencil"></i></a>-->
                        <a data-value="<?php echo $documents['id']; ?>" data-projectid="<?php echo $this->request->data['project_id'];?>" id="confirm_document_blog_delete" class="btn btn-xs btn-danger tipText text-white delete_document" data-original-title="Delete document"><i class="fa fa-trash"></i>
                        </a>
                <?php } ?>
                </p>
				 <p class="doc-type" style="width:100%;" >
                    <?php	$ext = pathinfo($up['document_name']);
                            ?>
                            <span class="dolist-document">
                                <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
                                    <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                </span>
                                <a href="<?php echo SITEURL . DO_LIST_BLOG_DOCUMENTS . $up['document_name']; ?>" class="tipText" download="<?php echo $up['document_name']; ?>"><?php echo $ext['filename'] ?></a>
                            </span>
                </p>
            </div>
        </li>
		<?php } ?>
			
        <?php   
} else {
    echo '<li>No Documents</li>';
}
?>
        </ul>
    </div>
</div>
<style>
.blog-comment-lists li .comment-people-info > p > span {
  display: inline-block;
  width: 100%;
  margin: 3px 0;
}

.blog-comment-lists li .comment-people-info{
	  margin: 3px 0;
}
.idea-blog-list li p {
    color: #333;
    font-size: 13px;
}

.comment-people-info .created-date{ margin: 2px 0 6px 0; }

.idea-blog-list li {
  border-bottom: 1px solid #ccc;
  display: inline-block;
  width: 100%;
  padding: 9px 0;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}
</style>

<script type="text/javascript" >
$(function(){
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });	
});
</script>



