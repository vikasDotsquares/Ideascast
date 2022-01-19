<div class="smaill-banner-inner about-us">
	<img class="img-responsive" src="<?php echo SITEURL;?>images/2017/blog-banner.jpg" alt="" />
			<div class="smaill-banner-contant">
				<h2>BLOG</h2>
			</div>
		</div>					
		<div class="container">
			<div class="row">    
				<div class="col-sm-8">
					<div class="blog-details">
						<h4><?php echo $blog['Post']['title'];?></h4>
						<div class="date-user"> By IdeasCast, Posted <?php echo date('M d, Y',strtotime($blog['Post']['created']));?> </div>
						<div class="medialink">
										<div class="tweeter-share">											
											<a target="_blank" class="twitter-share-button" href="https://twitter.com/intent/tweet?text=<?php echo $blog['Post']['title'];?>">Tweet</a>
											
										</div>
										<div class="linkedin-share">											
											<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script><script type="IN/Share" data-url="<?php echo SITEURL?>blog/<?php echo $blog['Post']['slug'];?>" data-counter="right"></script>
											
										</div>
										<div class="facebook-share">
											  <div id="fb-root"></div>
												<script>(function(d, s, id) {
												  var js, fjs = d.getElementsByTagName(s)[0];
												  if (d.getElementById(id)) return;
												  js = d.createElement(s); js.id = id;
												  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=219753171426241";
												  fjs.parentNode.insertBefore(js, fjs);
												}(document, 'script', 'facebook-jssdk'));</script>
											<div class="fb-like" data-href="<?php echo SITEURL?>blog/<?php echo $blog['Post']['slug'];?>" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
											
											<div class="fb-share-button" data-href="<?php echo SITEURL?>blogdetail/<?php echo $blog['Post']['id'];?>" data-layout="button" data-size="small" data-mobile-iframe="false"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo SITEURL?>blog/<?php echo $blog['Post']['slug'];?>&src=sdkpreparse">Share</a></div>
											
										</div>
						</div>    
						<div class="blog-details-cont">
							<div class="blog_image">
							<?php if( file_exists(WWW_ROOT.POST_RESIZE_PIC_PATH.$blog['Post']['blog_img']) ) {
								if( isset($blog['Post']['blog_img']) && !empty($blog['Post']['blog_img']) ){
							?>							
							<img src="<?php echo SITEURL.POST_PIC_PATH.$blog['Post']['blog_img']; ?>">
							<?php }
							} ?>
							</div>
							<div class="blog_description"><?php echo $blog['Post']['description'];?></div>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="recent-post">
						<div class="recent-post-heading">
							<h4>Recent Posts</h4>
							<span>
								<a href="<?php echo SITEURL;?>blog">Home</a>
							</span>
						</div>
						<div class="whyjeera-content">
							<ul id="recentpost">
							<?php 							
							if(isset($recentBlogList) && !empty($recentBlogList)){
								foreach($recentBlogList as $bloglist){?>
								<?php /* <li><a href="<?php echo SITEURL;?>blogdetail/<?php echo $bloglist['Post']['id'];?>"><?php echo $bloglist['Post']['title'];?></a></li>*/?>
								<li><a href="<?php echo SITEURL;?>blog/<?php echo $bloglist['Post']['slug'];?>"><?php echo $bloglist['Post']['title'];?></a></li>
							<?php }
							} else {?>	
								<li>No Recent Blog found</li>
							<?php } ?>	
							</ul>
						</div>
						<a class="more-but" id="loadMore" href="javascript:">More</a>
						<a class="more-but" id="showLess" href="javascript:">Less</a>
					</div>
				</div>
			</div>
		</div>
<script>
$(document).ready(function () {
    size_li = $("#recentpost li").size();
	
	size_li_count = $("#recentpost li:visible").size();
	 
	if(size_li <=10){
		$('#loadMore').hide();
	}
	
	$('#showLess').hide();
  
    x=10;
    $('#recentpost li:lt('+x+')').show();
	
	$( "body" ).delegate( "#loadMore", "click", function() {
		if( x == 0){
			 x=10;
			 $('#loadMore').show();
		}	 
        x = (x+5 <= size_li) ? x+5 : size_li;
		if(x > 5){
			//console.log(x);
			$('#recentpost li:lt('+x+')').show();
			$('#showLess').show();
		}
		
		if( x == size_li ){
			$('#loadMore').hide();
		}
		
    });
	$( "body" ).delegate( "#showLess", "click", function() { 
		
		x=(x-5<10) ? 10 : x-5;
		if(x > 10  ){
			 console.log(x);
			$('#recentpost li').not(':lt('+x+')').hide();
		}else if( x<=10 ){
				console.log(x);
				$('#showLess').hide();
				$('#recentpost li').not(':lt('+x+')').hide();
		}
		 
		if(x < size_li){
			$('#loadMore').show();
		}
		
    });
});
</script>		
		