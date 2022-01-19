<div class="modal-dialog  modal-lg">
    <div class="modal-content">
	
        <div class="modal-header"> 
             <h3 id="myModalLabel" class="modal-title">
            <?php echo $this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ? 'Your': 'User'; ?> Profile</h3> 
        </div>
        <div class="modal-body">
                <!--<a class="rht" data-toggle="modal" data-target="#popup_modal" href="<?php echo SITEURL . 'users/myaccountedit'; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>	-->
           <?php if($this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ) {?>
		   <div class="row">
                <div class="col-lg-12">
                    <a class="rhts pull-right" href="<?php echo SITEURL . 'users/myaccountedit'; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>				
                </div>
            </div>
			<?php } ?>

            <?php //echo $this->Session->flash('auth'); ?>
            
			<div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <?php 
                        $menu_pic = isset($user_details['UserDetail']['profile_pic']) && !empty($user_details['UserDetail']['profile_pic']) ? $user_details['UserDetail']['profile_pic'] : '' ;
						if(!empty($menu_pic) && file_exists(USER_PIC_PATH.$menu_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$menu_pic;
						}else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
						} ?>

                        <img class="img-circles" alt="Logo Image" style="position: absolute; right: 10px; width: 140px;"  src="<?php echo $profilesPic ?>" alt="logo" />
                        
                        
                        <label  class="control-label">First Name:</label>
                       <?php if (isset($user_details['UserDetail']['first_name']) && !empty($user_details['UserDetail']['first_name'])) { 
                        echo $user_details['UserDetail']['first_name'];
						}else{
						echo "Not Given";
						}
                        ?>
                    </div>
                    <div class="form-group">
                        <label class=" control-label">Last Name:</label>

						<?php if (isset($user_details['UserDetail']['last_name']) && !empty($user_details['UserDetail']['last_name'])) { 
                        echo $user_details['UserDetail']['last_name'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>


                    <div class="form-group">
                        <label class="control-label">Email:</label>

						<?php if (isset($user_details['User']['email']) && !empty($user_details['User']['email'])) { 
                        echo $user_details['User']['email'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>


                    

                    <div class="form-group" style="width:60%">
                        <label  class="control-label">Address:</label>

                        <?php echo strip_tags(trim($user_details['UserDetail']['address']));?><?php if (isset($user_details['UserDetail']['city']) && !empty($user_details['UserDetail']['city'])) { ?><?php echo trim(", ".strip_tags(trim($user_details['UserDetail']['city'])));}?><?php if (isset($user_details['UserDetail']['state_id']) && !empty($user_details['UserDetail']['state_id'])) { ?><?php echo trim(", ".strip_tags(trim($this->Common->getState($user_details['UserDetail']['state_id']))));}?><?php if (isset($user_details['UserDetail']['country_id']) && !empty($user_details['UserDetail']['country_id'])) { ?><?php echo trim(", ".strip_tags(trim($this->Common->getCountry($user_details['UserDetail']['country_id']))));
							  
 
}

if( (!isset($user_details['UserDetail']['country_id']) || empty($user_details['UserDetail']['country_id'])) && (!isset($user_details['UserDetail']['state_id']) || empty($user_details['UserDetail']['state_id'])) &&  (!isset($user_details['UserDetail']['address']) || empty($user_details['UserDetail']['address']))){
echo "Not Given";
} 
?> 



                    </div>



                    <div class="form-group">
                        <label  class="control-label">Zip/Postcode:</label>

						<?php if (isset($user_details['UserDetail']['zip']) && !empty($user_details['UserDetail']['zip'])) { 
                        echo $user_details['UserDetail']['zip'];
						}else{
						echo "Not Given";
						}
                        ?>
                    </div>

                    <div class="form-group">
                        <label  class="control-label">Contact Number:</label>

						<?php if (isset($user_details['UserDetail']['contact']) && !empty($user_details['UserDetail']['contact'])) { 
                        echo $user_details['UserDetail']['contact'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>
                </div>
                <div class="col-lg-6">
                 <?php if($this->Session->read("Auth.User.id") == $user_details['UserDetail']['user_id'] ) {?>    
			<div class="form-group">
			  <label  class="control-label">Feedback Rating:</label>
			  <?php if(isset($user_details['User']['id']) && !empty($user_details['User']['id'])){ ?>
				<?php echo number_format($this->Common->feedbackRateAverage( $user_details['User']['id']),1); ?>
			 <?php }else{
			echo "Not Given";
			}?>
			</div>
			<?php } ?>
			
                    <div class="form-group">
                        <label  class="control-label">Bio:</label>

						<?php if (isset($user_details['UserDetail']['bio']) && !empty($user_details['UserDetail']['bio'])) { 
                        echo $user_details['UserDetail']['bio'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>
                         
						<div class="form-group">
                            <label  class="control-label">Organization Name:</label>
							<?php if (isset($user_details['UserDetail']['self_org']) && !empty($user_details['UserDetail']['self_org'])) {  echo $user_details['UserDetail']['self_org'];
							}else{
							echo "Not Given";
							}
							?>

                        </div>
                        <div class="form-group">
                            <label  class="control-label">Department:</label>
						<?php if (isset($user_details['UserDetail']['department']) && !empty($user_details['UserDetail']['department'])) { 
                        echo $user_details['UserDetail']['department'];
						}else{
						echo "Not Given";
						}
                        ?>
                        
                        </div>
 

     
                        <div class="form-group">
                            <label  class="control-label">Job Title:</label>
						<?php if (isset($user_details['UserDetail']['job_title']) && !empty($user_details['UserDetail']['job_title'])) { 
                        echo $user_details['UserDetail']['job_title'];
						}else{
						echo "Not Given";
						}
                        ?>
                         
                        </div>
 

                         
                        <div class="form-group">
                            <label  class="control-label">Job Role:</label>
						<?php if (isset($user_details['UserDetail']['job_role']) && !empty($user_details['UserDetail']['job_role'])) { 
                        echo $user_details['UserDetail']['job_role'];
						}else{
						echo "Not Given";
						}
                        ?>	
                        
                        </div>
                        

                        
                        <div class="form-group">
                            <label  class="control-label">Skills:</label>
							<?php if (isset($user_details['Skill']) && !empty($user_details['Skill'])) { ?>
                            <?php
                            $skilWithComma = '';
                            foreach ($user_details['Skill'] as $skill) {
                                $skilWithComma .= ', <span class="skill">' . $skill["title"] . "</span>";
                            }
                            echo  trim(substr($skilWithComma,1));
                            ?>
							<?php }else{
							echo "Not Given";
							}
							?>
                        </div>

                </div>
            </div>







            <!--<div class="form-group">
              <label  class="control-label">Additional Products Features:</label>
            </div>  -->
            <?php /* if(isset($userplan) && !empty($userplan)){

              foreach($userplan as $plan){

              ?>

              <div class="form-group text-maroon">
              <?php
              if(!empty($plan['UserPlan']['start_date'])){

              echo $plan['Plan']['description'] ." =  <span class='text-black'>Start : ". date('d M Y',$plan['UserPlan']['start_date']) ." End : ". date('d M Y',$plan['UserPlan']['end_date'])."</span>" ;

              }else{
              echo $plan['Plan']['description'] ." = <span class='text-black'> Full Time </span>" ;
              }
              ?>

              </div>
              <?php }
              } */ ?> 
        </div>

        <div class="modal-footer">

            <button data-dismiss="modal" class="btn btn-danger" type="button">Close</button>

        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script type="text/javascript" >
$(function(){
	$('#popup_model_box').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL ?>images/ajax-loader-1.gif" style="margin: auto;"></div>');
    });
		// $(".modal-content").hide()
		setTimeout(function(){
			// $(".modal-content").show()
		}, 1500)
})


</script>


			
			
			
			
		
