<?php
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));

if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
$type = (isset($type) && !empty($type)) ? $type : "comment";
?>
<div class="panel-group" id="page-accordion-blog">
<?php
    if (isset($bloglist) && !empty($bloglist)) {
		
		foreach ($bloglist as $blog) {
			
            $blog_id  = $blog['Blog']['id'];			
			$blogconter = $this->TeamTalk->getBlogCounter($project_id,$blog_id);
			$userBlog = $this->TeamTalk->userBlogLike($blog['Blog']['project_id'],$blog_id,$this->Session->read('Auth.User.id'));			
?>
            <div class="panel panel-default page-collapse-<?php echo $blog['Blog']['id'] ?> ">
                <div class="panel-heading bg-curious-Blue">
                    <h4 class="panel-title">
                        <a class="users_blog_document accordion-toggle page-accordion collapsed" data-toggle="collapse" data-parent="#page-accordion-blog" href="#<?php echo $type;?>-page-collapse-<?php echo $blog['Blog']['id'] ?>" data-blogid="<?php echo $blog['Blog']['id'] ?>">                            
                            <?php 
							echo $this->Text->truncate(
								$blog['Blog']['title'],
								45,
								array(
									'ellipsis' => '...',
									'exact' => false
								)
							);
							//echo $blog['Blog']['title']; ?> (<?php echo ( isset($blog['BlogDocument']) && count($blog['BlogDocument']) > 0 )? count($blog['BlogDocument']) : 0 ; ?>)
                        </a>
                    </h4>
                </div>
                <div id="<?php echo $type;?>-page-collapse-<?php echo $blog['Blog']['id'] ?>" class="panel-collapse collapse">
                    <div class="panel-body nopadding">
						<div class="idea-right-up-blog padding">
							<?php 
							if( isset($blog['BlogDocument']) && !empty($blog['BlogDocument']) ){
									
									krsort($blog['BlogDocument']);		
									foreach($blog['BlogDocument'] as $blogdoc){
										$ext = pathinfo($blogdoc['document_name']);										
														
							?>
										<p class="doc-type">
									
											<span class="dolist-document" >
												<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
													<span class="icon_text"><?php echo $ext['extension']; ?></span>
												</span>
												<a href="<?php echo SITEURL . DO_LIST_BLOG_DOCUMENTS . $blogdoc['document_name']; ?>" class="tipText" download="<?php echo $blogdoc['document_name']; ?>"><?php
												
												echo $ext['filename']; ?></a>
											</span>
													
										</p>
							<?php 	} 
							} else { ?><p class="doc-type">No Documents</p><?php } ?>
						</div>
                    </div>	
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="text-center "> No blog found! <!--<a href="" class="backtoread" >  Back</a>--></div>
        <?php
    }
    ?>
</div>
<script type="text/javascript" >
 $(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;'); 		
})	
</script>