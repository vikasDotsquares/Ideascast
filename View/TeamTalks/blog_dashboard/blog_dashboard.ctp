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

$documentCount = $this->TeamTalk->documentCounter($project_id);
$commentCount = $this->TeamTalk->commentCounter($project_id);
$totalblog = $this->TeamTalk->totalBlogCnt($project_id);
$totalBlogViews = $this->TeamTalk->totalBlogViews($project_id);
$cntProjectBlogViews = 0;
foreach($totalBlogViews as $viewcount){
	$cntProjectBlogViews +=$viewcount['BlogView']['bview']; 
} 
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
            <div class="second-header">
			
				<ul class="name-lysting">
					<li class="hidden-md hidden-sm"><strong>Totals:</strong></li>
					<li>Blog Posts (<?php echo isset($totalblog)? $totalblog : 0;?>)</li>
					<li>Views (<?php echo isset($cntProjectBlogViews)? $cntProjectBlogViews : 0;?>)</li>
					<li>Comments (<?php echo isset($commentCount)? $commentCount : 0; ?>)</li>
					<li>Documents (<?php echo isset($documentCount)? $documentCount : 0; ?>)</li>
				</ul>
				
				<div class="pull-right custom-dropdown" id="blg_filter">
					<?php
						$options = array('all' => 'All','today' => 'Today', 'last_7_day' => 'Last 7 Days', 'last_30_day' => 'This Month');

						echo $this->Form->input('Blog.created', array(							
							'type' => 'select',
							'options' => $options,
							'label' => false,
							'div' => false,
							'class' => 'form-control aqua blog_filterby_days',
							'style' => 'width:100%;',
							'data-project'=>$project_id,
							'data-value'=>$this->Session->read("Auth.User.id")							
						));
					?>
					
				</div>	
			</div>
            <div id="myTabContent" class="tab-content dash-blog-tab-content">
                <div class="tab-pane fade active in dashboard-b-list" id="alldashboardblist">
					<ul class="list-unstyled idea-team-talks-dashboard-blog-list" id="dashboard_blog_list"  >		
						<?php 
							echo $this->element('../TeamTalks/blog_dashboard/blog_lists', array('blog_list'=>$bloglist) );
							//echo "under process...";
						?>
					</ul>
                </div>
            </div>     
        </div>
    </div>
</div>
