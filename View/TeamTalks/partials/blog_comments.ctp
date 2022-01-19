<div class="comments">
    <div class="comments-segment-btn">
        <div class="pull-left">Comments</div> 
        <div class="pull-right"> 
				<!-- <i class="fa fa-commenting-o"></i> -->
                <a style="cursor: pointer;" class="tipText" title="Add Comment" data-toggle="modal" data-target="#modal_add_blogComments" id="create_blog_comments" data-blog_id="<?php echo $this->request->data['blog_id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/add_blog_comments/blog_id:<?php echo $this->request->data['blog_id']; ?>/project_id:<?php echo $this->request->data['project_id']; ?>"><i class="fa fa-commenting-o"></i></a>
                <a style="cursor: pointer;"  class="tipText" title="Show Documents" onclick="getBlogDocumentList('<?php echo $this->request->data['blog_id']; ?>','<?php echo $this->request->data['project_id']; ?>')" id="open_blog_documents" data-blog_id="<?php echo $this->request->data['blog_id']; ?>" data-project="<?php echo $this->request->data['project_id']; ?>"><i class="fa fa-folder-o"></i></a>
        </div>
    </div>
    <div class="comments-segment">
        <ul class="blog-comment-lists list-group" id="">
       
        </ul>
    </div>

	

</div>

<script type="text/javascript" >
$(function(){
	$('#modal_add_blogComments').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
	
	
})
</script>