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
            
			<?php 
			if (isset($user_details['UserDetail']['first_name']) && !empty($user_details['UserDetail']['first_name'])) { 
				echo $user_details['UserDetail']['first_name'] . ' ' . $user_details['UserDetail']['last_name'];
			}else{
				echo "Not Given";
			}
			?>
			</h3> 
        </div>
        <div class="modal-body user-profile-body">
                <!--<a class="rht" data-toggle="modal" data-target="#popup_modal" href="<?php echo SITEURL . 'users/myaccountedit'; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>	-->
           <?php
		   
		   if( $this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ) {
			   if( $this->Session->read("Auth.User.role_id") == 3 ){
				   $userEditUrl = SITEURL.'users/orgaccountedit?refer='.$referer;
			   } else {
				   $userEditUrl = SITEURL.'users/myaccountedit?refer='.$referer;
			   }
			   ?>
		   <div class="row">
                <div class="col-lg-12">
                    <a class="rhts pull-right" href="<?php echo $userEditUrl; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>				
                </div>
            </div>
			<?php } ?> 
            
			<div class="row user-profile">
                <div class="col-lg-3 profile-cols">
                    <div class="form-group">
                        <?php 
                        $menu_pic = isset($user_details['UserDetail']['profile_pic']) && !empty($user_details['UserDetail']['profile_pic']) ? $user_details['UserDetail']['profile_pic'] : '' ;
						if(!empty($menu_pic) && file_exists(USER_PIC_PATH.$menu_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$menu_pic;
						}else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
						}

if($user_details['UserDetail']['user_id'] == 199) {
	//$profilesPic = SITEURL.USER_PIC_PATH.'1428931549123.png';
}						
						?>

                        <img class="" alt="Logo Image" style="width: 140px;"  src="<?php echo $profilesPic ?>" alt="logo" /> 
						
                    </div>
					
                    <div class="form-group">
                        
                        <label class="control-label" style="display: block;">Address:</label>

                        <?php if (isset($user_details['UserDetail']['address']) && !empty($user_details['UserDetail']['address'])) {
						echo strip_tags(trim($user_details['UserDetail']['address'])).'<br />';
						} ?>
						
						<?php if (isset($user_details['UserDetail']['city']) && !empty($user_details['UserDetail']['city'])) {
								echo trim( strip_tags(trim($user_details['UserDetail']['city']))).'<br />';
							}
							?>
						<?php if (isset($user_details['UserDetail']['state_id']) && !empty($user_details['UserDetail']['state_id'])) {
								echo trim( strip_tags(trim($this->Common->getState($user_details['UserDetail']['state_id'])))).'<br />';
							} 
							?>
						<?php if (isset($user_details['UserDetail']['zip']) && !empty($user_details['UserDetail']['zip'])) { 
							echo $user_details['UserDetail']['zip'].'4<br />';
						} 
                        ?>
						<?php if (isset($user_details['UserDetail']['country_id']) && !empty($user_details['UserDetail']['country_id'])) {
							echo trim( strip_tags(trim($this->Common->getCountry($user_details['UserDetail']['country_id'])))).'<br />';
						} 
						?>
						<?php
						if( (!isset($user_details['UserDetail']['country_id']) || empty($user_details['UserDetail']['country_id'])) && (!isset($user_details['UserDetail']['state_id']) || empty($user_details['UserDetail']['state_id'])) &&  (!isset($user_details['UserDetail']['address']) || empty($user_details['UserDetail']['address']))){
							//echo "Not Given";
						} 
						?>
                    </div>

                </div>
                <div class="col-lg-4 profile-cols" style="border-left: 1px solid #cccccc;">
					<div class="form-group">
						<label  class="control-label">Organization Name:</label>
						<?php 
						 if (isset($user_details['UserDetail']['self_org']) && !empty($user_details['UserDetail']['self_org'])) {  
							echo $user_details['UserDetail']['self_org'];
						}
						else{
							echo "Not Given";
						} 
						/* if (isset($user_details['UserDetail']['org_name']) && !empty($user_details['UserDetail']['org_name'])) {  
							echo $user_details['UserDetail']['org_name'];
						}
						else{
							echo "Not Given";
						} */
						
						?>
					</div>
					<div class="form-group">
						<label class="control-label">Email:</label>
						
						<?php 
							if (isset($user_details['User']['email']) && !empty($user_details['User']['email'])) { 
								echo $user_details['User']['email'];
								}else{
								echo "Not Given";
							}
                        ?>
						
					</div>
                    
					<div class="form-group">
						<label  class="control-label">Country:</label>
						
						<?php 
						if (isset($user_details['UserDetail']['country_id']) && !empty($user_details['UserDetail']['country_id'])) { 							 
							echo $this->Common->getCountry($user_details['UserDetail']['country_id']); 
						}
						else{
							echo "Not Given";
						}
						?>

					</div>
					<div class="form-group">
						<label  class="control-label">State/County:</label>
						
						<?php 
						if (isset($user_details['UserDetail']['state_id']) && !empty($user_details['UserDetail']['state_id'])) { 
							echo $this->Common->getState($user_details['UserDetail']['state_id']);
						}
						else{
							echo "Not Given";
						}
						?>

					</div> 
					<div class="form-group">
						<label  class="control-label">City/Town:</label>
						
						<?php 
						if (isset($user_details['UserDetail']['city']) && !empty($user_details['UserDetail']['city'])) { 
							echo $user_details['UserDetail']['city'];
						}
						else{
							echo "Not Given";
						}
						?>	

					</div>					
					<div class="form-group">
                        <label  class="control-label">Contact Number:</label>

						<?php 
						if (isset($user_details['UserDetail']['contact']) && !empty($user_details['UserDetail']['contact'])) {
							echo $user_details['UserDetail']['contact'];
						}
						else {
							echo "Not Given";
						}
                        ?>

                    </div>										

                </div>
            </div>          
        </div>

        <div class="modal-footer">
			<?php if($this->Session->read("Auth.User.id") != $user_details['UserDetail']['user_id']) { 
				
			?>
				<div style="display: inline-block; float: left;">
					<!--<a data-id="<?php echo $user_details['UserDetail']['user_id']; ?>" class='btn btn-default chat_start_email btn-sm disabled'>Send Email</a> 
					<a data-id="<?php echo $user_details['UserDetail']['user_id']; ?>" class='btn btn-default btn-sm chat_start_section   <?php echo (isset($project_id) && !empty($project_id)) ? '' : 'disabled'; ?> '>Start Chat</a>-->
					<?php echo $html = CHATHTML($user_details['UserDetail']['user_id']); ?>
				</div>
			<?php } ?>
            <button data-dismiss="modal" class="btn btn-danger" type="button">Close</button>

        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script type="text/javascript" >
$(function(){
	$('#popup_model_box').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('');
    });
		// $(".modal-content").hide()
		setTimeout(function(){
			// $(".modal-content").show()
		}, 1500)
})


</script>


			
			
			
			
		
