<div class="smaill-banner-inner about-us">
	<img class="img-responsive" src="<?php echo SITEURL?>images/2017/blog-banner.jpg" alt="" />
			<div class="smaill-banner-contant">
				<h2>Blog</h2>
			</div>
		</div>
		<div class="container">
			<div class="row">
			<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {     
    FB.init({
        appId      : '1914788908740116',
        status     : true,
        xfbml      : true
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "http://connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>
				<?php if(isset($bloglist) && !empty($bloglist)){ 					
					foreach($bloglist as $blist){	
				?>
						<div class="col-sm-6">
							<div class="blog-sec">
								<!-- <img src="http://placehold.it/570x150"> -->
								<div class="bloglist_img">
									 
									<?php if( file_exists(WWW_ROOT.POST_RESIZE_PIC_PATH.$blist['Post']['blog_img']) ) {
										
										$img_url = ( isset($blist['Post']['blog_img']) && !empty($blist['Post']['blog_img']) )? $blist['Post']['blog_img']: 'no_image.png';
										echo $this->Image->resizeBlog( $img_url, 570, 150, array(), 100);
									} else {
										$img_url = 'no_image.png';
										echo $this->Image->resizeBlog( $img_url, 570, 150, array(), 100);
									}	
									
									?>
								</div>
								<div class="blog-sec-cont">
									<div class="date-user"> By IdeasCast, Posted <?php echo date('M d, Y',strtotime($blist['Post']['created']));?></div>
									<?php /* <h4><a href="<?php echo SITEURL?>blogdetail/<?php echo $blist['Post']['id'];?>"><?php echo $blist['Post']['title'] ;?></a></h4>*/?>
									<h4><a href="<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>"><?php echo $blist['Post']['title'] ;?></a></h4>
									
									<div class="blogContent">
									<?php 
										$bcontent = preg_replace("/<img[^>]+\>/i", "(image) ", $blist['Post']['description']);
									
											echo $this->Text->truncate(
												strip_tags($bcontent),
												200,
												array(
													'ellipsis' => '...',
													'exact' => false
												)
											).'</p>';
									?>
									</div>
									<a class="read-more" href="<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>">Read More</a>
									<div class="medialink">
										<div class="tweeter-share">
											<?php /* <a target="_blank" class="twitter-share-button" href="https://twitter.com/intent/tweet?text=<?php echo SITEURL?>blogdetail/<?php echo $blist['Post']['id'];?>">Tweet</a>											
											*/ ?>
											
											<a target="_blank" class="twitter-share-button" href="https://twitter.com/intent/tweet?text=<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>">Tweet</a>
											
										</div>
										<div class="linkedin-share">
											<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script><script type="IN/Share" data-url="<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>" data-counter="right"></script>
										</div>
										<div class="facebook-share">
											  
											<div class="fb-like" data-href="<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
											<div class="fb-share-button">
											<a class="omag" data-link="<?php echo SITEURL?>blog/<?php echo $blist['Post']['slug'];?>"  data-desc="<?php echo strip_tags($blist['Post']['description']); ?>" data-title="<?php echo strip_tags($blist['Post']['title']); ?>" data-src="<?php echo ( isset($blist['Post']['blog_img']) && !empty($blist['Post']['blog_img']) )? SITEURL.POST_PIC_PATH.$blist['Post']['blog_img']: SITEURL.POST_PIC_PATH.'no_image.png'; ?>" href="javascript:"><img src="<?php echo SITEURL?>images/fb_share.png"></a>
											</div>
										</div>
										
									</div>  
								</div>    
							</div>        
						</div>
				<?php } ?>
						<div class="col-sm-12 pull-right text-right">
							<?php if($this->params['paging']['Post']['pageCount'] > 1) { ?> 
								<tr>
                                    <td align="right">
									<ul class="pagination">
										<?php 										
										
											echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); 
											
											echo $this->Paginator->numbers(array('first' => 1, 'last' => 1,'currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'','modulus' => 2));
											
											echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); 
										
										?>
										</ul>
									</td>
								</tr>
							<?php } ?>
						</div>	
				<?php }?>
			</div>
		</div>
		
		
<script>
$(function(){	   
	$('a.omag').click(function(e){
		FB.ui({
			method: 'share',
			name: $(this).data('title'),
			link: $(this).data('link'),
			href: $(this).data('link'),
			picture: $(this).data('src'),
			caption: $(this).data('title'),
			description: $(this).data('desc')
		});
	})   
})
</script>		