<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>		
	<h4><?php echo h('Third Party User Detail');?></h4>			
</div>
<div class="modal-body" style="max-height:600px; overflow:auto;">
		<div class="form-group clearfix center" >
			<div class="col-lg-12" align="center">
				<?php 
					 if( isset($user['ThirdParty']['profile_img']) && !empty($user['ThirdParty']['profile_img']) && file_exists(WWW_ROOT.THIRD_PARTY_USER_PATH.$user['ThirdParty']['profile_img'] ) ){ 
				?>
					<img src="<?php echo SITEURL.THIRD_PARTY_USER_PATH.$user['ThirdParty']['profile_img']; ?>" style="height: auto;
width: 20%;" >
				<?php
					} 
				?>
			</div>
		</div>				
		<div class="form-group col-lg-12 col-md-12 col-xs-12">
			<label class="control-label col-md-2 col-xs-4">Username:</label>
			<div class="control-label col-md-10 col-xs-8"><?php echo h($user['ThirdParty']['username']);?></div>
		</div>
		<div class="form-group col-lg-12 col-md-12 col-xs-12">
			<label class="control-label col-md-2 col-xs-4">Email:</label>
			<div class="control-label col-md-10 col-xs-8"><?php echo $user['ThirdParty']['email'];?></div>
		</div>
		<div class="form-group col-lg-12 col-md-12 col-xs-12">
			<label class="control-label col-md-2 col-xs-4">Address:</label>
			<div class="control-label col-md-10 col-xs-8"><?php echo $user['ThirdParty']['address'];?></div>
		</div>
		<div class="form-group col-lg-12 col-md-12 col-xs-12">
			<label class="control-label col-md-2 col-xs-4">Phone:</label>
			<div class="control-label col-md-10 col-xs-8"><?php echo $user['ThirdParty']['phone'];?></div>
		</div>
		<div class="form-group col-lg-12 col-md-12 col-xs-12">
			<label class="control-label col-md-2 col-xs-4">Status:</label>
			<div class="control-label col-md-10 col-xs-8">
			<?php echo ($user['ThirdParty']['status'])?'Active':'In active';?>
			</div>
		</div>				
</div>	
		
<style>
#blogRecordView img{ width: 100% !important; height : 100% !important; }
</style>		