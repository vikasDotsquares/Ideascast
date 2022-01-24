 

	<div class="modal-header"> 
		<h3 id="modalTitle" class="modal-title" >Commented on this page</h3> 	
	</div>
	<div class="modal-body clearfix people">
                <?php
                $allwikipageviewusers = $this->Wiki->get_user_page_comment($project_id, $user_id,$wiki_id,$wiki_page_id);
                ?>
		<?php if(isset($allwikipageviewusers) && !empty($allwikipageviewusers)) { ?>
			<?php foreach( $allwikipageviewusers as $key => $val ) { ?>
				
				<?php 
                  
                                $userDetail =  $this->ViewModel->get_user_data($val['WikiPageComment']['user_id']);
                                $user =  $this->ViewModel->get_user($val['WikiPageComment']['user_id']);
                                
                                if(isset($userDetail) && !empty($userDetail)) {
                                        $user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

                                        $profile_pic = $userDetail['UserDetail']['profile_pic'];

                                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
                                            $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
                                        }else{
                                            $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                                        }
					
				?>
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Owner: <?php echo $user_name; ?></p>
						<p><?php echo $user['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
				<?php }  ?>
			
			<?php } ?>
		<?php }else{ ?>	
                    <div class="text-center bold padding">No User found!</div>
                <?php }?>
		

	</div>
	
	<div class="modal-footer"> 
		<button class="btn btn-danger" data-dismiss="modal">Close</button> 	
	</div>	 


<script type="text/javascript" > 
$(function(){ 	 
    $('#modal_medium').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
});

	
</script>