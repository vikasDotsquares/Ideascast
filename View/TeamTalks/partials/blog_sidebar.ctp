<?php 
$current_user_id = $this->Session->read('Auth.User.id');
	if( isset($listusers) ){
?>
		<ul class="list-inline">
			<?php foreach($listusers as $userList){
				
				$userDetail = $this->ViewModel->get_user( $userList['Blog']['user_id'], null, 1 );
				$job_title = htmlentities($userDetail['UserDetail']['job_title']);
				$html = '';
				if( $userList['Blog']['user_id'] != $current_user_id ) {
					$html = CHATHTML($userList['Blog']['user_id'],$userList['Blog']['project_id']);
				}
				$user_name = $this->Common->userFullname($userList['Blog']['user_id']);
				$current_org = $this->Permission->current_org();
				$current_org_other = $this->Permission->current_org($userList['Blog']['user_id']);
 
			?>
			<li>
				<a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $userList['Blog']['user_id']; ?>"  >
				<img class="pophover"  data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" src="<?php echo $this->Common->get_profile_pic($userList['Blog']['user_id']);?>" alt="" />
				<?php if($current_org !=$current_org_other){ ?>
				<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
				<?php } ?>
				</a>
				<h5 class="bh_head"><?php echo $user_name;?></h5>
				<p class="btn btn-xs btn-default">					
					<a href="javascript:void(0);" class="userBlogcount tipText" title="Show Blog Posts" data-value="<?php echo $userList['Blog']['user_id']; ?>" data-id="<?php echo $userList['Blog']['project_id']; ?>" ><span><img src="<?php echo SITEURL ;?>img/blog-icon-black-300.png" alt="" width="18" /></span><?php echo $this->TeamTalk->userTotalBlog($userList['Blog']['project_id'],$userList['Blog']['user_id']);?></a>
				</p>
			</li>	
		<?php } ?>
		</ul>
<script type="text/javascript" >
$(function ($) { 

	$(".userBlogcount").click(function(e){
		e.preventDefault(); 
		
		$("#tabContent3").hide(function(){
			$("#blog_comments").parent('li').removeClass('active');
		});
		$("#tabContent4").hide();
		$("#tabContent6").hide();
		$("#tabContent7").hide();
		$("#tabContent1").show(function(){
			$("#blog_read").parent('li').addClass('active');
		})
		
		/* function(){
			$( "#blog_read" ).trigger( "click" );
		} */
		
		var project_id = $(this).data('id');		
		var user_id = $(this).data('value');
		var actionURL =  "<?php echo SITEURL.'team_talks/list_userblogs/'?>";
		 
		$.ajax({
			url : actionURL,
			type: "POST",
			global: true,
			data : {'project_id':project_id, 'user_id':user_id},			
			success:function(response){				 			
				if( response ){
					$("#accordion2").html(response);
				}
			} 
		});
		return;
	});
	
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
});	
</script>
<?php } ?>