<?php $is_full_permission_to_current_login = false;

$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));

if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}

if ( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
    $is_full_permission_to_current_login = true;
}

?>
<style>
#comment_doc_list{
	margin-top:14px;
	display:inline-block;
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

.comment-people-info .created-date{ clear:both; margin: 2px 0 6px 0; }

#comment_doc_list .dolist-document{clear:both; float:left; margin:0 0 10px;}

.idea-blog-list li {
  border-bottom: 1px solid #ccc;
  display: inline-block;
  width: 100%;
  padding: 9px 0;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}
</style>


<div class="admin-left-section">
    <div class="tast-list-left-main">

        <div class="task-list-left-tabs">
            <ul class="nav nav-tabs blog_admin_list padding-bottom">
                <li class="active">
                    <a  class="all_blgT" aria-expanded="true" href="#allAdminBlogs" class="active" data-toggle="tab">All Blogs
					</a>
                </li>
				<li class="">
                    <a  class="all_comT" aria-expanded="true" href="#allComments"  data-toggle="tab">All Comments</a>
                </li>
                <li class="">
                    <a  class="all_docT" aria-expanded="false" href="#allDocuments" data-toggle="tab">All Documents</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="allAdminBlogs">
					<ul class="list-unstyled idea-team-talks-admin_commentlist" id="admin_blg_list"  >		
						<?php 
							echo $this->element('../TeamTalks/blog_admin/allblog_list', array('allbloglist'=>$allbloglist, 'deletepermission'=>$is_full_permission_to_current_login) );
						?>
					</ul>
                </div>
				<div class="tab-pane fade" id="allComments">
					<ul class="list-unstyled idea-team-talks-admin_commentlist" id="admin_cmt_list"  >		
						<?php 
							echo $this->element('../TeamTalks/blog_admin/comment_list', array('blog_list'=>$com_list, 'deletepermission'=>$is_full_permission_to_current_login) );
						?>
					</ul>
                </div>				
                <div class="tab-pane fade" id="allDocuments">
					<div class="idea-doc-list">
						<div class="row">
							<ul class="list-unstyled idea-team-talks-admin-document-list" id="admin_doc_list"><?php echo $this->element('../TeamTalks/blog_admin/document_list', array('doc_list'=>$doc_list, 'deletepermission'=>$is_full_permission_to_current_login) ); ?></ul>
						</div>
					</div>
                </div>
            </div>     
        </div>
    </div>
</div>
<script type="text/javascript" >
 $(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
})	
</script>
