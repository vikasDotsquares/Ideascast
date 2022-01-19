<?php 
//pr($viewUsers);

//BlogView

 
if( isset($viewUsers) && !empty($viewUsers) ) {
		
?>
	<div class="modal-header"> 
		<h3 id="modalTitle" class="modal-title" >Viewed this Blog</h3> 	
	</div>
	<div class="modal-body clearfix people">
		
			<?php foreach( $viewUsers as $key => $listUsers ) { 
				$val = $listUsers['BlogView'];
					
					$userDtal = $this->ViewModel->get_user( $val['user_id'] ); 
					$userDetail = $this->ViewModel->get_user_data( $val['user_id'] ); 
					// pr($userDetail, 1);
					$current_org = $this->Permission->current_org();
					$current_org_other = $this->Permission->current_org($val['user_id']);					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
				?>
				<div class="row">
					<div class="col-sm-2  ">
					<div class="noavailwith">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
						<?php  if($current_org !=$current_org_other){ ?>
						<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
						<?php } ?>	
					</div>
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name"><?php echo $user_name; ?></p>
						<p><?php echo $userDtal['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
				<?php }  
				} ?>
		
	</div>
	
	<div class="modal-footer"> 
		<button class="btn btn-danger" data-dismiss="modal">Close</button> 	
	</div>	 

<?php
}
 ?>
        
     

<script type="text/javascript" > 
$(function(){ 	 
    $('#modal_medium').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
});

	
</script>