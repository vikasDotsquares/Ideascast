<!-- ADD NEW Industry User -->
	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Edit Institution
            <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/users/institution_users">Back</a></h3>
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('User', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
				<?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				
				<?php echo $this->Form->input('UserInstitution.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Institution Name:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								<?php echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => '4', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user first name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<!--<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Last Name:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user last name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div> -->
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Email:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.email', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user email(username)"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
					</div>
					
					<!--<div class="row">

					<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
					
						</div>
						
						
						
					</div> -->
					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Start:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserInstitution.start', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control from_date')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">End:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserInstitution.end', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control to_date')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
							
						</div>
					</div>
					

					<div class="row">
					<!--	<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Confirm Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please re-enter above secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>-->
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact Person:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserInstitution.person', array('type' => 'text', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact Number:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserInstitution.contact', array('type' => 'text', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
						

					</div>
					
					<div class="row">
					
						<div class="col-md-6">
							<div class="form-group">
								<label for="IndustryClassificationClassification" class="col-lg-3 control-label">Profile Image:</label>
								<div class="col-lg-8">
									<?php echo $this->Form->input('UserDetail.profile_pic', array('type' => 'file', 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'height:auto')); ?>
								</div>
								<div class="col-lg-1">
									<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select user profile image"><i class="fa fa-info-circle fa-3 martop"></i></a>
								</div>
									<?php	
								
									$docimg = $this->data['UserDetail']['profile_pic'];
									 $docimgs = SITEURL.USER_PIC_PATH.$docimg;
								
									if(!empty($docimg) && file_exists(USER_PIC_PATH.$docimg)){
										$docimgs = SITEURL.USER_PIC_PATH.$docimg;
									}else{
										$docimgs = SITEURL.'img/image_placeholders/logo_placeholder.gif';
									} 
									
									
									?>
								<div class="col-lg-3">&nbsp;</div>
					
								  <div class="col-lg-8">
									
									  <img src="<?php echo $docimgs ?>" class="img-circles" alt="Profile Image" />
								  </div>								
							</div>
							
							
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Membership Code:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserInstitution.membership_code', array('type' => 'text', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
 
					</div>
					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="CompanyCompany" class="col-lg-3 control-label">Country:</label>
							  <div class="col-lg-8">
								 <?php echo $this->Form->input('UserDetail.country_id', array('options' => $this->Common->getCountryList(),  'empty' => 'Select Country', 'label' => false, 'div' => false,  'onchange' => 'selectCity(this.options[this.selectedIndex].value)','class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select country"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">
							<?php
								$states = array();
								if(isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])){
									$states = $this->Common->getStateList($this->data['UserDetail']['country_id']);
								}
							?>
							<div class="form-group">
							  <label for="CompanyCompany" class="col-lg-3 control-label">State/County:</label>
							  <div class="col-lg-8">
							   <?php 
								 echo $this->Form->input('UserDetail.state_id', array('options' => $states,'id' => 'state_dropdown', 'empty' => 'Select State', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select state"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					
			
					<div class="row">
						<div class="col-md-6">					
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">City/Town:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.city', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user city"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Zip/Postcode:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.zip', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user zip code"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Address:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.address', array('type' => 'textarea','rows' => 2,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user address"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						

						
						<div class="col-md-6">
							<?php 
								$checked = '';
								if(isset($this->data['User']['status']) && !empty($this->data['User']['status'])){
									$checked = 'checked'; 
								}
							?>
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Status:</label>
							  <div class="col-lg-9">
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[User][status]" <?php echo $checked; ?> type="checkbox">
								
							  </div>
							</div>
						</div>
						
						
					</div>
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/users/institution_users">Cancel</a>
					
					<!--<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
$(document).ready(function(){
	var startDate = new Date();
var FromEndDate = new Date();
var ToEndDate = new Date();

ToEndDate.setDate(ToEndDate.getDate()+365);

	$('.from_date').datepicker({
	weekStart: 1,
	endDate: FromEndDate, 
	autoclose: true
	})
	.on('changeDate', function(selected){
	startDate = new Date(selected.date.valueOf());
	startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
	$('.to_date').datepicker('setStartDate', startDate);
	}); 
	
	$('.to_date').datepicker({
        
        weekStart: 1,
        endDate: ToEndDate,
        autoclose: true
    })
    .on('changeDate', function(selected){
        FromEndDate = new Date(selected.date.valueOf());
        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
        $('.from_date').datepicker('setEndDate', FromEndDate);
    });
	
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});

$("#RecordFormedits").submit(function(e){
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#Recordedit').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		},
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
	e.preventDefault(); //STOP default action
	//e.unbind(); //unbind. to stop multiple form submit.
});



</script>