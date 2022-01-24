<style>
.user-profile .form-group {
	margin-bottom: 8px;
}
.user-profile .control-label {
	display: block;
	margin-bottom: 3px;
}
.user-profile .skill-bio-box {
	background-color: #f0f0f0;
	display: block;
	max-height: 130px;
	min-height: 130px;
	overflow-x: hidden;
	overflow-y: auto;
	padding: 5px 8px;
}
</style>
<div class="modal-dialog  modal-lg">
    <div class="modal-content">
	
        <div class="modal-header"> 
             <h3 id="myModalLabel" class="modal-title">
            <?php // echo $this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ? 'Your': 'User'; ?> 
			<?php 
			
			//pr($user_details['ThirdParty']);
			
			if (isset($user_details['ThirdParty']['username']) && !empty($user_details['ThirdParty']['username'])) { 
				echo $user_details['ThirdParty']['username'];
			}else{
				echo "Not Given";
			}
			?>
			</h3> 
        </div>
        <div class="modal-body user-profile-body">                
           <?php /* if($this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ) {?>
		   <div class="row">
                <div class="col-lg-12">
				 
                    <a class="rhts pull-right" href="<?php echo SITEURL . 'users/myaccountedit?refer='.$referer; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>				
                </div>
            </div>
			<?php }*/ ?>
			<div class="row user-profile">
                <div class="col-lg-3 profile-cols">
                    <div class="form-group">
                    <?php 
                        $menu_pic = isset($user_details['ThirdParty']['profile_img']) && !empty($user_details['ThirdParty']['profile_img']) ? $user_details['ThirdParty']['profile_img'] : '' ;
						if(!empty($menu_pic) && file_exists(THIRD_PARTY_USER_PATH.$menu_pic)){
							$profilesPic = SITEURL.THIRD_PARTY_USER_PATH.$menu_pic;
						}else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
						}
					?>
                    <img class="" alt="Logo Image" style="width: 140px;"  src="<?php echo $profilesPic ?>" alt="logo" /> 
						
                    </div>
					
                    <div class="form-group">
                        
                        <label class="control-label" style="display: block;">Address:</label>
                        <?php if (isset($user_details['ThirdParty']['address']) && !empty($user_details['ThirdParty']['address'])) {
							echo strip_tags(trim($user_details['ThirdParty']['address'])).'<br />';
						}  
						?>						
                    </div>

                </div>
                <div class="col-lg-4 profile-cols" style="border-left: 1px solid #cccccc; padding-left:25px;">
					<div class="row">
						<div class="form-group">                        
							<label class="control-label" style="display: block;">Username:</label>

							<?php if (isset($user_details['ThirdParty']['username']) && !empty($user_details['ThirdParty']['username'])) {
								echo trim($user_details['ThirdParty']['username']).'<br />';
							}  
							?>						
						</div>
                    </div>
					<div class="row">
						<div class="form-group">
							<label class="control-label">Email:</label>
							
							<?php 
								if (isset($user_details['ThirdParty']['email']) && !empty($user_details['ThirdParty']['email'])) { 
									echo $user_details['ThirdParty']['email'];
									}else{
									echo "Not Given";
								}
							?>
							
						</div>
					</div>
					<div class="row">
						<div class="form-group">                        
							<label class="control-label" style="display: block;">Contact Number:</label>

							<?php if (isset($user_details['ThirdParty']['phone']) && !empty($user_details['ThirdParty']['phone'])) {
								echo trim($user_details['ThirdParty']['phone']).'<br />';
							}  
							?>						
						</div>
                    </div>
					<?php if (isset($user_details['ThirdParty']['contact2']) && !empty($user_details['ThirdParty']['contact2'])) { ?>
					<div class="row">
						<div class="form-group">                        
							<label class="control-label" style="display: block;">Contact2:</label>

							<?php 
								echo trim($user_details['ThirdParty']['contact2']);							  
							?>						
						</div>
                    </div>
					<?php } ?>
					
					<?php if (isset($user_details['ThirdParty']['website']) && !empty($user_details['ThirdParty']['website'])) { ?>
					<div class="row">
						<div class="form-group">                        
							<label class="control-label" style="display: block;">Website:</label>
							<?php 
							$parsed = parse_url(trim($user_details['ThirdParty']['website']));
							$urlStr = $user_details['ThirdParty']['website'];
							if (empty($parsed['scheme'])) {
								$urlStr = 'http://' . ltrim($urlStr, '/');
								echo $this->Html->link($user_details['ThirdParty']['website'], $urlStr, array('target' => '_blank'));
							} else {
								echo $this->Html->link(trim($user_details['ThirdParty']['website']), $urlStr, array('target' => '_blank'));
							}
							?>						
						</div>
                    </div>
					<?php } ?>
					
					<?php if (isset($user_details['ThirdParty']['summary']) && !empty($user_details['ThirdParty']['summary'])) { ?>
					<div class="row">
						<div class="form-group">                        
							<label class="control-label" style="display: block;">Summary:</label>
							<?php echo strip_tags(trim($user_details['ThirdParty']['summary'])); ?>						
						</div>
                    </div>
					<?php } ?>					
                </div>		
								
            </div>
        </div>

        <div class="modal-footer">
			<?php 
			if($this->Session->read("Auth.User.id") != $user_details['ThirdParty']['id']) { ?>
				<div style="display: inline-block; float: left;">
					<a data-id="<?php echo $user_details['ThirdParty']['id']; ?>" class='btn btn-default chat_start_email btn-sm disabled'>Send Email</a> 
					<a data-id="<?php echo $user_details['ThirdParty']['id']; ?>" class='btn btn-default btn-sm chat_start_section  disabled'>start chat</a>
				</div>
			<?php } ?>
            <button data-dismiss="modal" class="btn btn-danger" type="button">Close</button>

        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script type="text/javascript" >
$(function(){
	$('#popup_modal').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL ?>images/ajax-loader-1.gif" style="margin: auto;"></div>');
    });
		// $(".modal-content").hide()
		setTimeout(function(){
			// $(".modal-content").show()
		}, 1500)
})


</script>