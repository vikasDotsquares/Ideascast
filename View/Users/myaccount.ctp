<div class="modal-dialog  modal-lg">
    <div class="modal-content">

        <div class="modal-header"> 
            <h3 id="myModalLabel" class="modal-title">
            <?php echo $this->Session->read("Auth.User.id") == $this->data['UserDetail']['user_id'] ? 'Your': 'User'; ?> Profile</h3> 	
        </div>
        <div class="modal-body">
                <!--<a class="rht" data-toggle="modal" data-target="#popup_modal" href="<?php echo SITEURL . 'users/myaccountedit'; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>	-->
            <?php if($this->Session->read("Auth.User.id") ==  $this->data['UserDetail']['user_id'] ) {?>
		   <div class="row">
                <div class="col-lg-12">
                    <a class="rhts pull-right" href="<?php echo SITEURL . 'users/myaccountedit'; ?>"><i class="fa fa-fw fa-edit"></i> Edit</a>				
                </div>
            </div>
			<?php } ?>

            <?php //echo $this->Session->flash('auth'); ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="img-circles">
                        
                        <?php 
                        //pr($this->data['UserDetail']);
                        $profilepic = $this->data['UserDetail']['profile_pic'];
                        if(!empty($profilepic) && file_exists(USER_PIC_PATH.$profilepic)){
                                $profilesPic = SITEURL.USER_PIC_PATH.$profilepic;
                        }else{
                                $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        } ?>

                        <img class="myaccountPic" alt="Logo Image" style=""  src="<?php echo $profilesPic ?>" alt="logo" />
                    </div>
                    <div class="form-group">
                       
                        
                        <label  class="control-label">First Name:</label>
                        <?php if (isset($this->data['UserDetail']['first_name']) && !empty($this->data['UserDetail']['first_name'])) { 
                        echo $this->data['UserDetail']['first_name'];
						}else{
						echo "Not Given";
						}
                        ?>
                    </div>
                    <div class="form-group">
                        <label class=" control-label">Last Name:</label>

						<?php if (isset($this->data['UserDetail']['last_name']) && !empty($this->data['UserDetail']['last_name'])) { 
                        echo $this->data['UserDetail']['last_name'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>


                    <div class="form-group">
                        <label class="control-label">Email:</label>

						<?php if (isset($this->data['User']['email']) && !empty($this->data['User']['email'])) { 
                        echo $this->data['User']['email'];
						}else{
						echo "Not Given";
						}
                        ?>

                    </div>


                    <div class="form-group">
                        <label class="control-label">Secret Question:</label>

 
						<?php if (isset($this->data['UserDetail']['question']) && !empty($this->data['UserDetail']['question'])) { 
                        echo $this->data['UserDetail']['question'];
						}else{
						echo "Not Given";
						}
                        ?>   

                    </div>

                    <div class="form-group">
                        <label  class="control-label">Secret Answer:</label>

						<?php if (isset($this->data['UserDetail']['answer']) && !empty($this->data['UserDetail']['answer'])) { 
                        echo $this->data['UserDetail']['answer'];
						}else{
						echo "Not Given";
						}
                        ?> 

                    </div>

                    <div class="form-group" style="width:60%">
                        <label  class="control-label">Address:</label>
                        <?php echo strip_tags(trim($this->data['UserDetail']['address']));?><?php if (isset($this->data['UserDetail']['city']) && !empty($this->data['UserDetail']['city'])) { ?><?php echo trim(", ".strip_tags(trim($this->data['UserDetail']['city'])));}?><?php if (isset($this->data['UserDetail']['state_id']) && !empty($this->data['UserDetail']['state_id'])) { ?><?php echo trim(", ".strip_tags(trim($this->Common->getState($this->data['UserDetail']['state_id']))));}?><?php if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) { ?><?php echo trim(", ".strip_tags(trim($this->Common->getCountry($this->data['UserDetail']['country_id']))));
}

if( (!isset($this->data['UserDetail']['country_id']) || empty($this->data['UserDetail']['country_id'])) && (!isset($this->data['UserDetail']['state_id']) || empty($this->data['UserDetail']['state_id'])) &&  (!isset($this->data['UserDetail']['address']) || empty($this->data['UserDetail']['address']))){
echo "Not Given";
} 
?><!-- &rdquo; --> 

                    

                    </div>



                    <div class="form-group">
                        <label  class="control-label">Zip/Postcode:</label>

						<?php if (isset($this->data['UserDetail']['zip']) && !empty($this->data['UserDetail']['zip'])) { 
                        echo $this->data['UserDetail']['zip'];
						}else{
						echo "Not Given";
						}
                        ?> 

                    </div>

                    <div class="form-group">
                        <label  class="control-label">Contact Number:</label>

						<?php if (isset($this->data['UserDetail']['contact']) && !empty($this->data['UserDetail']['contact'])) { 
                        echo $this->data['UserDetail']['contact'];
						}else{
						echo "Not Given";
						}
                        ?> 

                    </div>
                </div>
                <div class="col-lg-6">
				
				              <?php if($this->Session->read("Auth.User.id") == $this->data['UserDetail']['user_id'] ) {?>       
			<div class="form-group">
			  <label  class="control-label">Feedback Rating:</label>
			  
				<?php if(isset($this->data['User']['id']) && !empty($this->data['User']['id'])){ ?>
				<?php echo number_format($this->Common->feedbackRateAverage( $this->data['User']['id']),1); ?>
			 <?php }else{
			echo "Not Given";
			}?>
			 
			</div>
			<?php } ?>
                    <div class="form-group">
                        <label  class="control-label">Bio:</label>

						<?php if (isset($this->data['UserDetail']['bio']) && !empty($this->data['UserDetail']['bio'])) { 
                        echo $this->data['UserDetail']['bio'];
						}else{
						echo "Not Given";
						}
                        ?> 

                    </div>
                        
                        <div class="form-group">
                            <label  class="control-label">Organization Name:</label>
							<?php if (isset($this->data['UserDetail']['self_org']) && !empty($this->data['UserDetail']['self_org'])) {  echo $this->data['UserDetail']['self_org'];
							}else{
							echo "Not Given";
							}
							?>

                        </div>


                        
                        <div class="form-group">
                            <label  class="control-label">Department:</label>

                        <?php if (isset($this->data['UserDetail']['department']) && !empty($this->data['UserDetail']['department'])) {
						echo $this->data['UserDetail']['department'];
						}else{
						echo "Not Given";
						}
						?>

                        </div>
 

                         
                        <div class="form-group">
                            <label  class="control-label">Job Title:</label>

                        <?php if (isset($this->data['UserDetail']['job_title']) && !empty($this->data['UserDetail']['job_title'])) {
						echo $this->data['UserDetail']['job_title']; }
						else{
						echo "Not Given";
						}
						?>

                        </div>
 
                        
                        <div class="form-group">
                            <label  class="control-label">Job Role:</label>

                        <?php if (isset($this->data['UserDetail']['job_role']) && !empty($this->data['UserDetail']['job_role'])) { 
						echo $this->data['UserDetail']['job_role'];
						}else{
						echo "Not Given";
						}
						?>

                        </div> 
						
                        <div class="form-group">
                            <label  class="control-label">Skills:</label>
							 <?php if (isset($this->data['Skill']) && !empty($this->data['Skill'])) { ?>
                            <?php
                            $skilWithComma = '';
                            foreach ($this->data['Skill'] as $skill) {
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



