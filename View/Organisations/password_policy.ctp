<style>
/* input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
   opacity: 1;
} */

input[type=number]::-webkit-inner-spin-button { 
    -webkit-appearance: block;
    cursor:pointer;
    display:block;
    width:8px;
    color: #333;
    text-align:center;
    position:relative;
	opacity: 1;
	margin-right:0;
}

input[type=number]::-webkit-inner-spin-button:before,
input[type=number]::-webkit-inner-spin-button:after {
    content: "^";
    position:absolute;
    right: 0;
	opacity: 1;
    font-family:monospace;
    line-height:
}

input[type=number]::-webkit-inner-spin-button:before {
    top:0px;
}

input[type=number]::-webkit-inner-spin-button:after {
    bottom:0px;
    -webkit-transform: rotate(180deg);
}

.password-policy .modal-header {
    background: #eee;
}
.password-policy .rows p{
    padding-bottom:10px;
}

.password-policy .pp h3{
    margin:10px 0;
}
</style>
<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
           

				
            </section>
		</div>
		<!-- END HEADING AND MENUS -->
<!-- Password Policy` -->
	<div class="password-policy">
		     <div class="left-policy">
				<h1 class="pull-left" style="font-size:24px;"><?php echo $viewData['page_heading']; 
				$userdetails =  $this->Common->getOrganisationId($this->Session->read('Auth.User.id'),$this->Session->read('Auth.User.role_id'));
				?>             
                </h1>
				<div class="policy-view-edit">
                        <span class="text-muted policydate-time"  style="text-transform: none;"><?php echo $viewData['page_subheading']; ?></span> <span class="policyupdate" style="text-transform: none;"> (Last updated by: <?php echo ( isset($this->request->data['OrgPassPolicy']['id']) && !empty($this->request->data['OrgPassPolicy']['updated']) ) ? $this->Session->read('Auth.User.UserDetail.full_name') : ' N/A ';?>: <?php
							if( isset($this->request->data['OrgPassPolicy']['updated']) && !empty($this->request->data['OrgPassPolicy']['updated']) ){								
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($this->request->data['OrgPassPolicy']['updated'])),$format = 'd M, Y h:i A');
							} else {
								echo ' N/A';
							}	
						?>)</span>
                    </div>	
				</div>
		
		
		
	<?php echo $this->Session->flash(); ?>
		
			<?php 		 
			
				echo $this->Form->create('OrgPassPolicy', array('type' => 'file','url'=>SITEURL.'organisations/password_policy/', 'class' => 'form-horizontal form-bordered', 'id' => 'passwordpolicyedit')); ?>
				
				<div class="rows">
					<div class="" >	
						<h3>Two-Factor Authentication</h3>
						<p>Enable two-factor authentication (2FA) for all users through an authenticator application.</p>
						
					</div>
					 
				</div>	
				
				<div class="modal-header clearfix">
					
					<div class="row">
					<div class="col-md-5" >	
						<div class="password-input" >						
							<label for="UserUser" class="control-label" style="    margin-top: -1px; display: inline-block;  float: left;">Status: 
							
							<?php 
							$ud = $this->Common->userDetail($this->Session->read('Auth.User.id'));	
							 
							if(isset($ud['UserDetail']['membership_code']) && !empty($ud['UserDetail']['membership_code'])){ ?>
								Enabled
							<?php }else{ ?>
								Disabled
							<?php } ?>
							
							</label>
						</div>
						
					</div>
					<div class="col-md-7">
					  
						<?php 
						
						
						$restURL = SITEURL."organisations/organisation_user_reset";
						if(isset($ud['UserDetail']['membership_code']) && !empty($ud['UserDetail']['membership_code'])){ 
						 
						?>
							
							<div class="pull-right">
							<a data-target="#popup_modal_start" data-remote="<?php echo SITEURL.'organisations/backup_code/1' ?>" data-toggle="modal" id="back_code_new"  class="btn btn-success "><!--<i class="fa fa-fw fa-check"></i>--> New Backup Code</a> 
							
							<input type="hidden" data-target="#popup_modal_start" data-remote="<?php echo SITEURL.'organisations/backup_code/2' ?>" data-toggle="modal" id="back_code"   /> 
							
							<a data-target="#popup_modal_start" data-remote="<?php echo SITEURL.'organisations/authentication/1' ?>" data-toggle="modal"  class="btn btn-success "><!--<i class="fa fa-fw fa-check"></i>--> Reset 2FA</a> 
							
							<a class="reset-fa btn  btn-danger " data-user="<?php echo $this->Session->read('Auth.User.id'); ?>" data-whatever="<?php echo $restURL; ?>"    style="cursor:pointer;">Disable Two-Factor Authentication </a>
							
							</div>
							
						
						<?php	
						}else { ?>
						<a data-target="#popup_modal_start" data-remote="<?php echo SITEURL.'organisations/authentication/1' ?>" data-toggle="modal"  class="btn btn-success pull-right"><!--<i class="fa fa-fw fa-check"></i>--> Enable Two-Factor Authentication</a> 
						<?php } ?> 
						
					</div>	
					</div>	
						
				</div>
				
				<div class="modal-body">
				<div class="row pp">
					<div class="" >	
						<h3>Password Policy</h3>
						
					</div>
					 
				</div>	
				
				
				<?php echo $this->Form->input('OrgPassPolicy.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				
				<?php echo $this->Form->input('OrgPassPolicy.org_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>$userdetails )); ?>
				<?php echo $this->Form->input('OrgPassPolicy.updated_by', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>$this->Session->read('Auth.User.id'))); ?>
					
					
					<div class="policy-form-sec">
					<div class="policy-formin">
					<label for="UserUser" class="control-label">Minimum Length:</label>
					<div class="password-input">						
								<?php								
									$options = array(); 
									for($i=4; $i<=20;$i++){
										$options[$i] = $i;	
									}								 	
						$default_min_lenght = isset($this->request->data['OrgPassPolicy']['min_lenght']) ? $this->request->data['OrgPassPolicy']['min_lenght'] : 4;		
						echo $this->Form->input('OrgPassPolicy.min_lenght', array('type' => 'number','label' => false, 'div' => false, 'required' => false, 'class' => 'form-control','step'=>'1','min'=>'4','value'=>$default_min_lenght)); ?> 
							  </div>
					</div>	
					</div>
					
					<div class="policy-form-sec">
					<div class="policy-formin">
					<label for="UserUser" class="control-label">Numeric Characters:</label>
					<div class="password-input">						
						<?php 
									$checked = '';									
									if(isset($this->data['OrgPassPolicy']['numeric_char']) && !empty($this->data['OrgPassPolicy']['numeric_char'])){
										$checked = 'checked'; 
									}
								?>
								<input id="numeric_char" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[OrgPassPolicy][numeric_char]" <?php echo $checked; ?> type="checkbox">
								<i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="ON TICK: User must have at least 1 number character in their password." ></i>
							  </div>
					</div>	
						
						<div class="policy-formin">
							  <label for="UserUser" class="control-label">Alphabetic Characters:</label>
							  <div class="password-input">								
								<?php 
									$checked = '';
									if(isset($this->data['OrgPassPolicy']['alph_char']) && !empty($this->data['OrgPassPolicy']['alph_char'])){
										$checked = 'checked'; 
									}
								?>
								<input id="alph_char" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[OrgPassPolicy][alph_char]" <?php echo $checked; ?> type="checkbox">
								<i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="ON TICK: User must have at least 1 alphabetical character in their password." ></i>
							  </div>
							  					  
							</div>
						
						
						
						
					</div>
					
									<div class="policy-form-sec">
					<div class="policy-formin">
					<label for="UserUser" class="control-label">Special Characters:</label>
					<div class="password-input">						
						<?php 
									$checked = '';									
									if(isset($this->data['OrgPassPolicy']['special_char']) && !empty($this->data['OrgPassPolicy']['special_char'])){
										$checked = 'checked'; 
									}
								?>
								<input id="special_char" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[OrgPassPolicy][special_char]" <?php echo $checked; ?> type="checkbox">
								<i class="fa fa-exclamation-circle" aria-hidden="true"data-toggle="popover" title="" data-content="ON TICK: User must have at least 1 special character  e.g. %. ?, /, ! in their password." ></i>
							  </div>
					</div>	
						
						<div class="policy-formin">
							  <label for="UserUser" class="control-label">Capital Characters:</label>
							  <div class="password-input">								
								<?php 
									$checked = '';
									if(isset($this->data['OrgPassPolicy']['caps_char']) && !empty($this->data['OrgPassPolicy']['caps_char'])){
										$checked = 'checked'; 
									}
								?>
								<input id="caps_char" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[OrgPassPolicy][caps_char]" <?php echo $checked; ?> type="checkbox">
								<i class="fa fa-exclamation-circle" aria-hidden="true"data-toggle="popover" title="" data-content="ON TICK: User must have at least 1 capital character in their password." ></i>
							  </div>
							  					  
							</div>
						
						
						
						
					</div>	
					
					<style>
						.policy-formin.full-width{width:100%; margin-bottom: 5px;}
					</style>
					
					<div class="policy-form-bottom">					
							<div class="policy-form-sec">
					<div class="policy-formin full-width">
					<label for="UserUser" class="control-label">Change Password:</label>
					<div class="password-input">						
							<?php		/* $options = array(); 
										for($i=1; $i<=20;$i++){
											$options[$i] = $i;	
										} */ 
								
								$default_chng_pass = isset($this->request->data['OrgPassPolicy']['change_pass_time']) ? $this->request->data['OrgPassPolicy']['change_pass_time'] : 1;
								
								echo $this->Form->input('OrgPassPolicy.change_pass_time', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control','required' => false,'step'=>'1','min'=>'1','value'=>$default_chng_pass));
								
							?>
						<span>days <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="Select number of days after which the password needs to be changed by the user."></i></span>
						</div>
					</div>
								<div class="policy-formin full-width">
					<label for="UserUser" class="control-label">No Repeat:</label>
					<div class="password-input">						
								<?php								
									$options = array(); 
									for($i=1; $i<=20;$i++){
										$options[$i] = $i;	
									}							
					  
					    $default_pass_repeat = isset($this->request->data['OrgPassPolicy']['pass_repeat']) ? $this->request->data['OrgPassPolicy']['pass_repeat'] : 1;
						echo $this->Form->input('OrgPassPolicy.pass_repeat', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control','required' => false,'step'=>'1','min'=>'1','value'=>$default_pass_repeat));
						
						?> <span>previous <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="Select number of previously selected password that cannot be selected again."></i></span>
							  </div>
					</div>	
								
					</div>
					
							<div class="policy-form-sec">
					<div class="policy-formin full-width">
					<label for="UserUser" class="control-label">Temporary Lockout:</label>
					<div class="password-input">						
								<?php								
									$options = array(); 
									for($i=1; $i<=20;$i++){
										$options[$i] = $i;	
									}
									
						$default_tmp_lockout = isset($this->request->data['OrgPassPolicy']['temp_lockout']) ? $this->request->data['OrgPassPolicy']['temp_lockout'] : 1;						
						echo $this->Form->input('OrgPassPolicy.temp_lockout', array('type' => 'number', 'label' => false, 'div' => false, 'class' => 'form-control','required' => false,'step'=>'1','min'=>'1','value'=>$default_tmp_lockout));
						
						?> <span>invalid attempts <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="Select the number of unsuccessful attempts to login before lockout."></i></span>
							  </div>
					</div>
					<div class="policy-formin full-width">
						<label for="UserUser" class="control-label">Lockout Period:</label>
							<div class="password-input">						
									<?php								
										$options = array(); 
										for($i=1; $i<=20;$i++){
											$options[$i] = $i;	
										}
								$default_lockout = isset($this->request->data['OrgPassPolicy']['lockout_period']) ? $this->request->data['OrgPassPolicy']['lockout_period'] : 1;
								echo $this->Form->input('OrgPassPolicy.lockout_period', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control','required' => false,'step'=>'1','min'=>'1','value'=>$default_lockout ));
							
							?> <span>minutes <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="popover" title="" data-content="Select how many minutes the login is locked out." ></i></span>
							</div>
					</div>	 
				</div>
					
			</div>
					

				</div>
				<div class="modal-footer clearfix">
					<div class="row">
					<div class="col-md-6" >	
						<div class="password-input" >						
							<label for="UserUser" class="control-label" style="    margin-top: -1px; display: inline-block;  float: left;">Force users to change password:</label>&nbsp;
							<?php 
									$force_change_pass = '';
									if(isset($this->data['OrgPassPolicy']['force_change_pass']) && !empty($this->data['OrgPassPolicy']['force_change_pass'])){
										$force_change_pass = 'checked'; 
									}
								?>
							<input id="force_change_pass" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[OrgPassPolicy][force_change_pass]" <?php echo $force_change_pass; ?> type="checkbox">	
							
						</div>
						
					</div>
					<div class="col-md-6">
						<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Confirm New Policy</button>
						<a class="btn btn-danger" href="<?php echo SITEURL ?>organisations/manage_users">Cancel</a>
					</div>	
					</div>	
						
				</div>	
			</form>
</div>
<script>
if ($("#successFlashMsg").length > 0) {

	setTimeout(function () {
		$("#successFlashMsg").animate({
			opacity: 0,
			height: 0
		}, 1000, function () {
			$(this).remove()
		})

	}, 4000)
}
</script>

<script>
$(document).ready(function(){	

	var param = $.cookie('backup_code');
	if(param ==1){
		setTimeout(function(){
			$('#back_code').trigger('click');
			$.cookie('backup_code', 2, {path: '/'});
		},100)
	}
	
	    
	$('[data-toggle="popover"]').popover({ trigger: "manual" , html: true, animation:false})
		.on("mouseenter", function () {
			var _this = this;
			$(this).popover("show");
			$(".popover").on("mouseleave", function () {
				$(_this).popover('hide');
			});
		}).on("mouseleave", function () {
			var _this = this;
			setTimeout(function () {
				if (!$(".popover:hover").length) {
					$(_this).popover("hide");
				}
			}, 300);
	});
	
	$(".reset-fa").click(function(event){
			event.preventDefault();

			$that = $(this);
			var row = $that.parents('tr:first');

			var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
			var deleteid = $(this).attr('data-user');

			BootstrapDialog.show({
				title: 'Two-Factor Authentication',
				message: "<p>Are you sure you want to disable two-factor authentication? </p>",
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Disable',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : deleteURL,
								type: "POST",
								data: $.param({user_id: deleteid}),
								global: false,
								// async:false,
								success:function(response){
									 location.reload();
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							} else {

/* 								$that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								}); */

								window.location.href=$js_config.base_url+'organisations/manage_users';

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							//dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								// location.reload();
							}, 500);
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});
		})
	
	
});
</script>
