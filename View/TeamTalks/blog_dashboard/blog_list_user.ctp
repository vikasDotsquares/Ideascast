<?php $current_user_id = $this->Session->read('Auth.User.id');
	if( isset($blogusers) ){		
?>
		<ul class="list-inline">
			<?php foreach($blogusers as $userList){
			
						$userDetail = $this->ViewModel->get_user( $userList['Blog']['user_id'], null, 1 );
						$job_title = htmlentities($userDetail['UserDetail']['job_title']);
						$html = '';
						if( $userList['Blog']['user_id'] != $current_user_id ) {
							$html = CHATHTML($userList['Blog']['user_id'],$userList['Blog']['project_id']);
						}
						$user_name = $this->Common->userFullname($userList['Blog']['user_id']);	
				
			?>
			<li>
				<a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $userList['Blog']['user_id']; ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" ><img src="<?php echo $this->Common->get_profile_pic($userList['Blog']['user_id']);?>" /></a>
				<h5 class="bh_head"><?php echo $this->Common->userFullname($userList['Blog']['user_id']);?></h5>
				<p class="btn btn-xs btn-default">
					
					<a href="javascript:void(0);" class="userDashboardBlogcount tipText" title="Show Blog Posts" data-value="<?php echo $userList['Blog']['user_id']; ?>" data-listby="blog_user_list" data-project="<?php echo $userList['Blog']['project_id']; ?>" ><span><img src="<?php echo SITEURL ;?>img/blog-icon-black-300.png" /></span><?php echo $this->TeamTalk->userTotalBlog($userList['Blog']['project_id'],$userList['Blog']['user_id']);?></a>
				</p>
			</li>	
		<?php } ?>
		</ul>
<?php } ?>
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