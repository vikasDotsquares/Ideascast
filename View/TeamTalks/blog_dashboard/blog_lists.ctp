

<div class="tab-pane " id="all">
	
	<table width="100%" border="0" class="table">
		<thead>
			<tr>
			<?php if(isset($bloglistby) && isset($bloglistuser) && !empty($bloglistuser)){?>
				<th><?php echo $this->Common->userFullname($bloglistuser);?></th>				
				<th>Views</th>
				<th>Comments</th>
				<th>Documents</th>
			<?php } else {?>	
				<th width="30%">Blog Post</th>	
				<th width="15%">Author</th>
				<th>Views</th>
				<th>Comments</th>
				<th>Documents</th>
			<?php } ?>		 
			</tr>
		</thead>
		<tbody>
		<?php 
		
	    //pr($bloglistuser);
		
		if (isset($blog_list) && !empty($blog_list)) {
			
			foreach ($blog_list as $blist) {
				
			// Blog total views, people and likes
			$blogViews = $this->TeamTalk->totalBlogViews($project_id,$blist['Blog']['id']);
			$totalBlogViewPeople = $this->TeamTalk->totalBlogViewsPeople($project_id,$blist['Blog']['id']);
			$totalBlogLike = $this->TeamTalk->totalBlogLikePeople($project_id,$blist['Blog']['id']);
			
			// Blog Comments total people and likes			
			$blogComCnt = $this->TeamTalk->commentCounter($project_id, $blist['Blog']['id']);
			$blogComCntPeople = $this->TeamTalk->commentCounterPeople($project_id, $blist['Blog']['id']);
			$ComLikeCnt = $this->TeamTalk->totalCommentLike($project_id, $blist['Blog']['id']);
			
			// Blog Document total and how many people added documents			 
			$blogDocCnt = $this->TeamTalk->documentCounter($project_id, $blist['Blog']['id']);	
			$blogDocCntPeople = $this->TeamTalk->userBlogDocumentCntPeople($blist['Blog']['id']);
			$userDetails = $this->Common->userDetail($blist['Blog']['user_id']);		
		?>
			<tr>
				<td data-label="Blog Post">
				<a href="javascript:void(0);" id="gotoblog" data-blogid="<?php echo $blist['Blog']['id'];?>" data-projectid="<?php echo $blist['Blog']['project_id']; ?>" style="color:#000;"><?php echo $blist['Blog']['title'];?></a>
				</td>
				<?php if(!isset($bloglistby) && !isset($bloglistuser) && empty($bloglistuser)){?>
				<td data-label="Creator"><?php echo $userDetails['UserDetail']['first_name'].'<br /> '.$userDetails['UserDetail']['last_name'];?></td>
				<?php } ?>
				<td data-label="Views">
					<?php /*if($totalBlogViewPeople == 0 && $totalBlogViewPeople ==0 && $totalBlogLike ==0 ){ ?>
						<span class="total-view">0</span>
						None						
					<?php } else {	*/?>
					<span class="total-view">					
					
					<a style="color:#f00;"><?php echo isset($blogViews)? $blogViews : 0;?></span></a> <?php 					
					if(isset($totalBlogViewPeople) && $totalBlogViewPeople > 0  ){ ?><a class="btn btn-xs btn-default" style="color:#626262;" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>team_talks/blogviewspeople/project_id:<?php echo $blist['Blog']['project_id']; ?>/blog_id:<?php echo $blist['Blog']['id'];?>"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $totalBlogViewPeople;?></a><?php } else { echo '<a class="btn btn-xs btn-default"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;0</a>'; }?>
					
					<a data-original-title="Likes" data-remote="" class="btn btn-xs btn-default tipText like_no_comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php if(isset($totalBlogLike)){echo $totalBlogLike;}?></span></a>
					<?php //} ?>
					
				</td>
				<td data-label="Comments">
				<?php 
				if($blogComCnt == 0 ){ ?>
					<span class="total-view">0</span>						
				<?php } else {  ?>
					 <a href="javascript:void(0);" id="gotoblogcomment" data-blogid="<?php echo $blist['Blog']['id'];?>" data-projectid="<?php echo $blist['Blog']['project_id']; ?>" style="color:#f00;">
					<span class="total-view"><?php echo isset($blogComCnt)? $blogComCnt : 0; ?></span></a>
				<?php } ?>
					<?php if( isset($blogComCntPeople) && $blogComCntPeople > 0 ){ ?> 
					<a class="btn btn-xs btn-default" style="color:#626262;" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>team_talks/commentpeople/project_id:<?php echo $blist['Blog']['project_id']; ?>/blog_id:<?php echo $blist['Blog']['id'];?>"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $blogComCntPeople;?></a>
					<?php } else { echo '<a class="btn btn-xs btn-default"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;0</a>'; } ?>
					
					<a data-original-title="Likes" data-remote="" class="btn btn-xs btn-default tipText like_no_comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo isset($ComLikeCnt)? $ComLikeCnt : 0;?></span></a>
				<?php //} ?>
				</td>
				<td data-label="Documents">
				<?php if( $blogDocCnt == 0 ){?>	
					<span class="total-view">0</span>
				<?php } else { ?>
					<a href="javascript:void(0);" id="gotoblogdocument" data-blogid="<?php echo $blist['Blog']['id'];?>" data-projectid="<?php echo $blist['Blog']['project_id']; ?>" style="color:#f00;"><span class="total-view"><span class="total-view"><?php echo isset($blogDocCnt)? $blogDocCnt : 0; ?></span></a>
				<?php } ?>			
					<?php if( isset($blogDocCntPeople) && $blogDocCntPeople > 0  ) {?>
					<a class="btn btn-xs btn-default" style="color:#626262;" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>team_talks/blogdocumentpeople/blog_id:<?php echo $blist['Blog']['id'];?>"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $blogDocCntPeople;?></a>
					<?php } else {echo '<a class="btn btn-xs btn-default"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;&nbsp;0</a>';} ?>					
				<?php //} ?>	
				</td>
			</tr>
		<?php
			}
		} else { ?>
			<tr>
				<td data-label="Blog Post">No Blog</td>
			<tr>			
		<?php } ?>	
		</tbody>
	</table>
	
		<?php /* <ul class=" t-head-list">
			<li>Blog Post</li>
			<li>Creator</li>
			<li>Views</li>
			<li>Comments</li>
			<li>Documents</li>
		</ul>
	<?php 
	
	    //pr($blog_list);
		
		if (isset($blog_list) && !empty($blog_list)) {
			foreach ($blog_list as $blist) {
			 
			$blogDocCnt = $this->TeamTalk->documentCounter($project_id, $blist['Blog']['id']);	
			$blogComCnt = $this->TeamTalk->commentCounter($project_id, $blist['Blog']['id']);	
					
		?>
			<ul class="people-list  t-body-list" id="dashboard-blog-list">	
				<li id="blog-list-<?php echo $blist['Blog']['id'];?>">                                    
					<?php echo $blist['Blog']['title'];?>
				</li>
				<li><?php echo $this->Common->userFullname($blist['Blog']['user_id']);?></li>
				<li><span class="total-view">7</span> 4 People <a data-original-title="Likes" data-remote="" class="btn btn-xs btn-default tipText like_no_comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple">0</span></a></li>
				<li><span class="total-view"><?php echo isset($blogComCnt)? $blogComCnt : 0; ?></span> 3 People <a data-original-title="Likes" data-remote="" class="btn btn-xs btn-default tipText like_no_comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple">0</span></a></li>
				<li><span class="total-view"><?php echo isset($blogDocCnt)? $blogDocCnt : 0; ?></span> 1 People <a data-original-title="Likes" data-remote="" class="btn btn-xs btn-default tipText like_no_comment"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple">0</span></a></li>		
			</ul>
		<?php
			}
		} else { ?>
			<ul class="people-list  t-body-list" id="dashboard-blog-list">
				<li  class="borderBT_none">No Blog</li>
			</ul>	
		<?php } */ ?>
</div>