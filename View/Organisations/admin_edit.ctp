<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>
<!-- EDIT Industry User -->
	<div class="panel panel-primary">
	<?php echo $this->Session->flash(); ?>
	  <div class="panel-heading">
         <h3 class="panel-title">
            Edit Organization : <?php echo $this->request->data['UserDetail']['org_name']; ?>
        <!--    <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/organisations">Back</a></h3>-->
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('User', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
				<?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); 
				
				//pr($this->data);
				?>				
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Organization:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.org_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Organization Name')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter organization name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Setup First Name:</label>
							  <div class="col-lg-8">						
								<?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user first name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>						
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Setup Last Name:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user last name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Setup Email:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.email', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user email(username)"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
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
								
									$docimg1 = $this->Common->userDetail($this->data['User']['id']);
									$docimg = $docimg1['UserDetail']['profile_pic'];
									
									//if( isset($docimg['name']) && !empty($docimg['name']) ){ 
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
								<?php //} ?> 
							</div>
							
							
						</div>						
						
						
						
					</div>
					
					<?php /* <div class="row">
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'form-control','autocomplete'=>'off')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Confirm Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please re-enter above secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
					</div> */ ?>
					
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
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact Number:</label>
							  <div class="col-lg-8"> 								
								<?php echo $this->Form->input('UserDetail.contact', array('type' => 'text','label' => false, 'div' => false, "maxlength"=>"40", 'class' => 'form-control','placeholder'=>'Contact number','autocomplete'=>'off')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						

					</div>							
				
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/organisations">Close</a>
					
					<!-- <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
$(function(){
	
		var startDate = new Date();
		var FromEndDate = new Date();
		var ToEndDate = new Date();

		ToEndDate.setDate(ToEndDate.getDate()+365);

		$('#start_date').datepicker({
			weekStart: 1,
			startDate: FromEndDate, 
			autoclose: true
		}).on('changeDate', function(selected){
			startDate = new Date(selected.date.valueOf());
			startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
			$('#end_date').datepicker('setStartDate', startDate);
		}); 
		
		$('#end_date').datepicker({
			weekStart: 1,
			endDate: ToEndDate,
			autoclose: true
		}) .on('changeDate', function(selected){
			FromEndDate = new Date(selected.date.valueOf());
			FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
			//$('#start_date').datepicker('setEndDate', FromEndDate);
		})

});
// Submit Edit Form 
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


$(document).ready(function(){

	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
	
});


$("#OrganisationUserDomainName").on('keyup', function(){
		checkDomain();
})
function checkDomain() {

	var dataString = 'userid='+$("#UserId").val()+'&domainName='+ $("#OrganisationUserDomainName").val();
	
	$.ajax({
		type: "POST",
		dataType: "json",
		url: "<?php echo SITEURL ?>sitepanel/users/checkOrgdomain",
		data: dataString,
		cache: false,
		success: function (result) {
			$("#domainchkmsg").show();					
			if(result.success == true){					
				$("#domainchkmsg").removeClass('text-red').addClass('text-green');
			} else {
				$("#domainchkmsg").removeClass('text-green').addClass('text-red');
			}
			$("#domainchkmsg").html(result.content);		
		}
	});
}

</script>