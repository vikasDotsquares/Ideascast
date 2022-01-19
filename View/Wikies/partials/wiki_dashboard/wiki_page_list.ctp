<div class="admin-left-section dash-blog-tab-content" >
    <div class="tast-list-left-main">
	        <div class="task-list-left-tabs">
            <div class="second-header ">
            
			
				<ul class="name-lysting">
					<li class="hidden-md hidden-sm"><strong>Totals:</strong></li>
					<li>
						<?php 
						$wikipage = $this->Wiki->getWikiPageLists($project_id,$this->Session->read("Auth.User.id"),$wiki_id);
						?>
						Wiki Pages (<?php echo ( isset($wikipage) && !empty($wikipage) ) ? count($wikipage) : 0;?>)
					</li>
					<?php 
							 
					if(isset($wiki_page_id) && !empty($wiki_page_id)){
						$views = $this->Wiki->get_wiki_page_views($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$wiki_page_id);
					}else{
						$views = $this->Wiki->get_wiki_page_views($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$wiki_page_id = null);
					}
					 
					?>
					
					<li> Views (<?php echo isset($views[0]['views']) && !empty($views[0]['views']) ? $views[0]['views'] : 0;?>)</li>
					<?php
					if(isset($wiki_page_id) && !empty($wiki_page_id)){
						$comment = $this->Wiki->userTotalWikiComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$wiki_page_id);
					}else{
						$comment = $this->Wiki->userTotalWikiComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$wiki_page_id = null);
					}
					?>
					
					<li>Comments (<?php echo $comment;?>)</li>
					 
				</ul>
				
				
				
				 
				<div id="blg_filter" class="pull-right custom-dropdown">
				
				<select id="PageCreatedFillter" style="width:100%;" class="form-control aqua" data-remote="<?php echo SITEURL;?>wikies/get_wiki_page_by_user_dashboard/<?php echo $project_id."/".$this->Session->read("Auth.User.id")."/".$wiki_id;?>" name="data[Page][created]">
                <option <?php echo isset($filter) && !empty($filter) && $filter == 'all' ? "selected='selected'" : '';?> value="all">All</option>
                <option <?php echo isset($filter) && !empty($filter) && $filter == 'today' ? "selected='selected'" : '';?> value="today">Today's</option>
                <option <?php echo isset($filter) && !empty($filter) && $filter == 'last_7_day' ? "selected='selected'" : '';?>  value="last_7_day">Last 7 days</option>
                <option <?php echo isset($filter) && !empty($filter) && $filter == 'this_month' ? "selected='selected'" : '';?>  value="this_month">This Months</option>
            </select>
					
				</div>
					
				</div>	
			 
	
	 
<div class="tab-content dash-blog-tab-content" id="myTabContent">
                <div id="alldashboardblist" class="tab-pane fade active in dashboard-b-list">


<div class="table-responsive" style="clear: both;">
 <div   class="tab-pane fade active in dashboard-b-list">
 <ul id="dashboard_blog_list" class="list-unstyled idea-team-talks-dashboard-blog-list">		
						

<div id="all" class="tab-pane ">
    <table width="100%" border="0" class="table">
        <thead>
            <tr >
                
                <th  width="30%">
                    <?php if(!isset($author) || $author != 1){?>
                    Wiki Page
                    <?php 
                    }else{ 
                       $user_data = $this->ViewModel->get_user_data($user_id);
                        echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name'];
                    }
                    ?>
                </th>
                <?php if(!isset($author) || $author != 1){?>
                <th  >Author</th>
                <?php } ?>
                <th>Views</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(isset($allWikiPages) && !empty($allWikiPages)){
                foreach($allWikiPages as $key => $page){//pr($allWikiPage['WikiPage']);
            ?>
                <tr  >
                    <td data-label="Wiki Page" >
                        <a style="cursor: pointer;color: #000;" class="page_select_default" data-id="<?php echo $page['WikiPage']['id'];?>" data-remote="<?php echo SITEURL;?>wikies/index/project_id:<?php echo $project_id;?>/page_id:<?php echo $page['WikiPage']['id']; ?>" title="Views" >
                        <?php echo $page['WikiPage']['title']; //echo '<br>'.date("Y-m-d h:i:s",$page['WikiPage']['created']);?>
                        </a>
                    
                    </td>
                    <?php if(!isset($author) || $author != 1){?>
                    <td  data-label="Creator">
                        
                        <?php 
                        $user_data = $this->ViewModel->get_user_data($page['WikiPage']['user_id']);
                        ?>
                        <?php echo $user_data['UserDetail']['first_name'] . '<br> ' .$user_data['UserDetail']['last_name']; ?>
                    </td>
                    <?php } ?>
                    <td  data-label="Views">
                        
                         
                        <?php 
                        $views = $this->Wiki->get_wiki_page_views($project_id,$page['WikiPage']['user_id'],$page['WikiPage']['wiki_id'],$page['WikiPage']['id']);
                        ?>
                        <p>
                            <a style="color:#ff0000; font-weight: bold;" class="tipText"  title="Views" >
                           <?php echo isset($views[0]['views']) && !empty($views[0]['views']) ? $views[0]['views'] : 0;?>
                            </a>
                        </p>
                        
                        
                        
                        <?php
                        $allwikipageviewusers = $this->Wiki->getWikiPageViewUsers($project_id, $user_id,$wiki_id,$page['WikiPage']['id']);
                        
                        ?>
                        
                        <?php 
                        if(isset($allwikipageviewusers) && !empty($allwikipageviewusers) && $allwikipageviewusers >= 1){
                        ?>
                        <a href="" data-target="#modal_people" title="People" data-toggle="modal"  data-remote="<?php echo SITEURL;?>wikies/get_user_page_view/<?php echo $project_id.'/'.$user_id.'/'.$wiki_id.'/'.$page['WikiPage']['id'];?>" class="get_user_page_view tipText text-black btn btn-xs btn-default" ><i class="fa fa-user-victor"></i>&nbsp;<?php echo ( isset($allwikipageviewusers) && !empty($allwikipageviewusers) ) ? count($allwikipageviewusers) : 0; ?> </a>
                        <?php }else{?>
                        
                        
                            <a class=" btn btn-xs btn-default  tipText text-black" >
                                <i class="fa fa-user-victor"></i>&nbsp;<?php echo ( isset($allwikipageviewusers) && !empty($allwikipageviewusers) ) ? count($allwikipageviewusers) : 0;  ?> 
                            </a>
                        <?php }?>
                         
                        
                        <?php
                        $likes = $this->Wiki->wiki_page_likes($page['WikiPage']['id']);
                        ?>
                        <a class="btn btn-xs btn-default tipText" data-remote="" data-original-title="Page Likes">
                            <i class="fa fa-thumbs-o-up"></i>&nbsp;
                            <span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
                        </a>
                    </td>
                    <td data-label="Comments" >
                        <?php 
                        $comment =  $this->Wiki->userTotalWikiComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$page['WikiPage']['id']);
                        ?>
                     <?php   if(is_array($comment) && $comment && count($comment) >= 1 ) { ?>
                        <p>
                            <a style="color:#ff0000; font-weight: bold; <?php echo isset($comment) && !empty($comment) && count($comment) >= 1 ? 'cursor: pointer;' : ''; ?> " class="tipText <?php echo isset($comment) && !empty($comment) && count($comment) >= 1 ? 'comment_select_default' : ''; ?> " data-id="<?php echo $page['WikiPage']['id'];?>" title="Comments" style="cursor: pointer;">
                                <?php echo $comment;?>
                            </a>
                        </p> 
                        
                        <?php 
                    }
                        $usercount = $this->Wiki->getAllUserOfComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$page['WikiPage']['id']);
                        if(isset($usercount) && !empty($usercount) && $usercount >= 1){
                        ?>
                        <a href="" data-target="#modal_people" title="People" data-toggle="modal"  data-remote="<?php echo SITEURL;?>wikies/get_user_page_comment/<?php echo $project_id.'/'.$user_id.'/'.$wiki_id.'/'.$page['WikiPage']['id'];?>" class=" btn btn-xs btn-default get_user_page_view tipText text-black" >
                        <i class="fa fa-user-victor"></i>&nbsp;<?php echo $this->Wiki->getAllUserOfComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$page['WikiPage']['id']);?>
                        </a>
                        <?php }else{?>
                        
                        
                            <a class=" btn btn-xs btn-default  tipText text-black" >
                                <i class="fa fa-user-victor"></i>&nbsp;<?php echo $this->Wiki->getAllUserOfComment($project_id,$this->Session->read("Auth.User.id"),$wiki_id,$page['WikiPage']['id']);?>
                            </a>
                        <?php }?>
                     
                        <?php
                        $likes = $this->Wiki->all_wiki_page_comment_likes($project_id,$wiki_id,$page['WikiPage']['id']);
                        ?>
                        <a class="btn btn-xs btn-default tipText" data-remote="" data-original-title="Comment Likes">
                            <i class="fa fa-thumbs-o-up"></i>&nbsp;
                            <span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
                        </a>
                       
                    </td>
                </tr>
            <?php
                }
            }
            ?>

        </tbody>
    </table>
</div>					</ul>
                </div>
            </div>     
        </div>
    </div>
</div>
</div>
</div>


		
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
 
.total-view a{
  color: #ff0000;
  display: block;
  font-weight: bold;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}

.admin-left-section tr td:first-child{ 
	max-width:320px;
	word-break: break-all;
    overflow-wrap: break-word;
}

</style>