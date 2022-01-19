<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">
			<?php 
			if(isset($this->data['User']['role_id']) && $this->data['User']['role_id'] == 3){   
				
				if( !empty($this->data['UserDetail']['org_name']) && strlen($this->data['UserDetail']['org_name']) > 40 ){		
					echo "Organization Details: ".mb_strimwidth($this->data['UserDetail']['org_name'], 0, 40, "...");
				} else {
					echo "Organization Details: ".$this->data['UserDetail']['org_name']; 
				}
				
			}else{ 
				echo "Organization User"; 
			}?>
			</h4>
		</div>
		<div class="modal-body">
				<div class="form-group clearfix" style="display:none">
					<div class="col-lg-12 center">
				<?php 
					if(isset($this->data['UserDetail']['profile_pic']) && !empty($this->data['UserDetail']['profile_pic'])){
						$profile = $this->data['UserDetail']['profile_pic'];
						if(file_exists(USER_PIC_PATH.$profile)){ ?>
							<img src="<?php echo SITEURL.USER_PIC_PATH.$profile; ?>" class="img-circle" alt="User Image" />
				<?php
						}
					}else{ 
				?>
					<img src="<?php echo SITEURL.'img/avatar3.png'; ?>" class="img-circle" alt="User Image" />
				<?php
					}
				?>
					</div>
				</div>
				
				
			<?php 
			//echo $this->Session->flash('auth'); ?>
			
			<?php if($this->data['User']['role_id']==4){ ?>
			<div class="form-group">
			<label  class="control-label col-md-4 col-xs-6">Institution Name:</label>
			<p class="control-label col-md-8 col-xs-6"><?php  echo $this->data['UserDetail']['first_name']; 
			//echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?></p>
			</div>
			<?php }else { ?>
			<div class="form-group">
			<label  class="control-label col-md-4 col-xs-6">Setup First Name:</label>
			<p class="control-label col-md-8 col-xs-6"><?php  echo (isset($this->data['UserDetail']['first_name']) && !empty($this->data['UserDetail']['first_name'])) ? $this->data['UserDetail']['first_name'] : 'N/A';  
			//echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?></p>
			</div>


			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">Setup Last Name:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo (isset($this->data['UserDetail']['last_name']) && !empty($this->data['UserDetail']['last_name'])) ? $this->data['UserDetail']['last_name']  : 'N/A'; //echo $this->Form->input('UserDetail.last_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?></p>

			</div>			
			<?php } ?>
			
			
			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">Setup Email:</label>
			   
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['User']['email']; //echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control')); ?></p>
			 
			</div>
			 	
			
			<?php if($this->data['User']['role_id']==2){ ?>
			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">Secret Question:</label>
			 
				<p class="control-label col-md-8 col-xs-6">
				<?php if(isset($this->data['UserDetail']['question']) && !empty($this->data['UserDetail']['question'])){
				echo $this->data['UserDetail']['question'];  
				}else{
				echo 'N/A';
				}
				?>
				</p>
			 
			</div>
			
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Secret Answer:</label>
			  
				<p class="control-label col-md-8 col-xs-6">
				<?php if(isset($this->data['UserDetail']['answer']) && !empty($this->data['UserDetail']['answer'])){
				echo $this->data['UserDetail']['answer'];  
				}else{
				echo 'N/A';
				}
				?>
				</p>
			 
			</div>
			<?php } ?>
			
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Address:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserDetail']['address']) && !empty($this->data['UserDetail']['address'])){
							echo $this->data['UserDetail']['address'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>
			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">City/Town:</label>
			 
				<p class="control-label col-md-8 col-xs-6">
				<?php if(isset($this->data['UserDetail']['city']) && !empty($this->data['UserDetail']['city'])){
				echo $this->data['UserDetail']['city'];  
				}else{
				echo 'N/A';
				}
				?>
				</p>
			 
			</div>
				

			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">State/County:</label>
			 
				<p class="control-label col-md-8 col-xs-6">
				<?php if(isset($this->data['UserDetail']['state_id']) && !empty($this->data['UserDetail']['state_id'])){
				echo $this->Common->getState($this->data['UserDetail']['state_id']);  
				}else{
				echo 'N/A';
				}
				?>
				</p>
			 
			</div>	
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Zip/Postcode:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserDetail']['zip']) && !empty($this->data['UserDetail']['zip'])){
							echo $this->data['UserDetail']['zip'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>			
			<div class="form-group">
			  <label class="control-label col-md-4 col-xs-6">Country:</label>
			 
				<p class="control-label col-md-8 col-xs-6">
				<?php if(isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])){
				echo $this->Common->getCountry($this->data['UserDetail']['country_id']);  
				}else{
				echo 'N/A';
				}
				?>
				</p>
			 
			</div>
			
			
			<?php if(isset($this->data['UserInstitution']['person']) && !empty($this->data['UserInstitution']['person'])) { ?>
			
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Contact Person:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserInstitution']['person']) && !empty($this->data['UserInstitution']['person'])){
							echo $this->data['UserInstitution']['person'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>

			<?php } ?>
			
			
			<?php if(isset($this->data['UserInstitution']['contact']) && !empty($this->data['UserInstitution']['contact'])) { ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Contact Number:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserInstitution']['contact']) && !empty($this->data['UserInstitution']['contact'])){
							echo $this->data['UserInstitution']['contact'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>	
			<?php }else{ ?>

			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Contact Number:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserDetail']['contact']) && !empty($this->data['UserDetail']['contact'])){
							echo $this->data['UserDetail']['contact'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>	
			<?php } ?>		

			<?php if(isset($this->data['UserInstitution']['membership_code']) && !empty($this->data['UserInstitution']['membership_code'])) { ?>			

			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Membership Code:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['UserInstitution']['membership_code']) && !empty($this->data['UserInstitution']['membership_code'])){
							echo $this->data['UserInstitution']['membership_code'];
						}else{
							echo 'N/A';
						} ?></p>
			 
			</div>				
			<?php } ?>
			
			<?php if(isset($this->data['UserDetail']['bio']) && !empty($this->data['UserDetail']['bio'])){ ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Bio:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['UserDetail']['bio'];  ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($this->data['UserDetail']['self_org']) && !empty($this->data['UserDetail']['self_org'])){ ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Organisation Name:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['UserDetail']['self_org'];   ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($this->data['UserDetail']['department']) && !empty($this->data['UserDetail']['department'])){ ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Department:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['UserDetail']['department']; ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($this->data['UserDetail']['job_title']) && !empty($this->data['UserDetail']['job_title'])){ ?>
			<div class="form-group">
			 <label  class="control-label col-md-4 col-xs-6">Job Title:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['UserDetail']['job_title'];  ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($this->data['UserDetail']['job_role']) && !empty($this->data['UserDetail']['job_role'])){ ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Job Role:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['UserDetail']['job_role'];   ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($this->data['OrganisationUser']['domain_name']) && !empty($this->data['OrganisationUser']['domain_name'])){ ?>
			<div class="form-group">
			  <label  class="control-label col-md-4 col-xs-6">Domain Name:</label>
			  
				<p class="control-label col-md-8 col-xs-6"><?php echo $this->data['OrganisationUser']['domain_name'];   ?></p>
			 
			</div>
			<?php } ?>
			
			<?php if(isset($userplan) && !empty($userplan)){ ?>		
				<!--<div class="form-group clearfix" style="border-bottom: 1px solid #ccc">-->
				<div class="form-group clearfix" >
			<?php }else { ?>
				<div class="form-group clearfix" >
			<?php } ?>
				  <label  class="control-label col-md-4 col-xs-6">Status:</label>
					
					<p  class="control-label col-md-8 col-xs-6"><?php 
						if(isset($this->data['User']['status']) && !empty($this->data['User']['status'])){
							echo 'Active';
						}else{
							echo 'Deactive';
						} ?><p>
					
				</div>
			
			<?php /* if(isset($userplan) && !empty($userplan)){ ?>
			<div class="form-group clearfix" >
			  <label  class="control-label col-lg-12">Additional Products Features:</label>
			  
				<?php if(isset($userplan) && !empty($userplan)){
				
				foreach($userplan as $plan){				
				?>				
				<?php
				if(!empty($plan['UserPlan']['start_date'])){
				
				echo "<div class='form-group text-maroon'><p class='form-group text-maroon col-md-12'>".$plan['Plan']['description'] ." </p>  <p class='text-black col-md-12 '>( Start : ". date('d M Y',$plan['UserPlan']['start_date']) ." &nbsp;&nbsp;&nbsp;&nbsp;  End : ". date('d M Y',$plan['UserPlan']['end_date'])." ) </p>"."</div>" ;
				
				}else{
					echo "<div class='form-group text-maroon'><p class='form-group text-maroon col-md-12'>".$plan['Plan']['description'] ."</p><p class='text-black col-md-12 '> ( Full Time ) </span>"."</p>"."</div>" ;
				}
				?>
				<?php }
				}	?> </div>
				<?php } */  ?>
				
			</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->