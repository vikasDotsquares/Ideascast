
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>		
			<h4><?php echo h($post['Post']['title']);?></h4>			
		</div>
		<div class="modal-body" style="max-height:600px; overflow:auto;">
				<div class="form-group clearfix center" >
					<div class="col-lg-12" align="center">
				<?php 
					if(isset($post['Post']['blog_img']) && !empty($post['Post']['blog_img'])){						
						$blogImg = $post['Post']['blog_img'];						
						if(file_exists(POST_PIC_PATH.$blogImg)){ 
						$img_url = ( isset($post['Post']['blog_img']) && !empty($post['Post']['blog_img']) )? $post['Post']['blog_img']: 'no_image.png';
						echo $this->Image->resizeBlog( $img_url, 570, 150, array(), 100);
						/*
						?>
							<img src="<?php echo SITEURL.POST_PIC_PATH.$blogImg; ?>" class="img-circle" alt="Blog Image" />
					<?php 
					*/
						}
					}else{ 
				?>
					<img src="<?php echo POST_RESIZE_SHOW_PATH.'no_image.jpg'; ?>" class="img-circle" alt="Blog Image" />
				<?php
					}
				?>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-2 col-xs-4">Blog Title:</label>
					<p class="control-label col-md-10 col-xs-8"><?php echo h($post['Post']['title']);?></p>
				</div>
				<div class="form-group">
				  <label class="control-label col-md-2 col-xs-4">Blog Description:</label>
				<div class="control-label col-md-10 col-xs-8"><?php echo $post['Post']['description'];?></div>
				</div>
				<div class="form-group">
				  <label class="control-label col-md-2 col-xs-4">Blog Status:</label>
					<p class="control-label col-md-10 col-xs-8">
					<?php echo ($post['Post']['status'])?'Active':'In active';?>
					</p>
				</div>
				<div class="form-group">
				  <label class="control-label col-md-2 col-xs-4">&nbsp;</label>
					<p class="control-label col-md-10 col-xs-8">&nbsp;</p>
				</div>
				<div class="form-group">
				  <label class="control-label col-md-2 col-xs-4">&nbsp;</label>
					<p class="control-label col-md-10 col-xs-8">&nbsp;</p>
				</div>
		</div>	
		
<style>
#blogRecordView img{ width: 100% !important; height : 100% !important; }
</style>		