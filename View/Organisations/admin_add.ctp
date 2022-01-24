<!-- ADD NEW Industry User -->

	<div class="panel panel-primary">
	<?php echo $this->Session->flash(); ?>
	  <div class="panel-heading">
         <h3 class="panel-title">
            Add Organization
           <!-- <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/organisations">Back</a></h3>-->
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('User', array( 'url' => array('controller'=>'organisations', 'action'=>'add'),  'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'UserADDs')); ?>
				<div class="modal-body">
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
								<?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'First name')); ?>
								<?php echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => '3', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								<?php echo $this->Form->input('OrganisationUser.creator_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.Admin.User.id'), 'label' => false, 'div' => false, 'class' => 'form-control',)); ?>
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
								<?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Last name')); ?>
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
								<?php echo $this->Form->input('User.email', array('type' => 'email','label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Email address')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user email(username)"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Password')); ?>
								<span style="font-size:13px;">Note: Type in - Not copy/paste in</span>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
					
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Confirm Password:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Confirm password')); ?>
								<span style="font-size:13px;">Note: Type in - Not copy/paste in</span>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please re-enter above secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
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
								<?php echo $this->Form->input('UserDetail.city', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'City/Town')); ?>
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
								<?php echo $this->Form->input('UserDetail.zip', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Postcode')); ?>
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
								<?php echo $this->Form->input('UserDetail.address', array('type' => 'textarea','rows' => 2,'label' => false, 'div' => false, 'class' => 'form-control','placeholder'=>'Address')); ?>
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
								<?php echo $this->Form->input('UserDetail.contact', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control', "maxlength"=>"40",'placeholder'=>'Contact number','autocomplete'=>'off')); ?> 
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
				</div>
				
				<div class="row">
					<div class="col-md-6">	
						<div class="form-group">
						  <label for="UserUser" class="col-lg-3 control-label">Status:</label>
						  <div class="col-lg-9">
								<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[User][status]" checked  type="checkbox">
							
						  </div>
						</div>
					</div>
				</div>
				
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Create</button>
					<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/organisations">Close</a>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
	
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57))
		return false;
		return true;
	} 
	
	$(document).ready(function(){
		
		$("#UserDetailOrgName").keypress(function(event) {
		    var character = String.fromCharCode(event.keyCode);
		    return isValid(character);     
		});

		$("#UserDetailFirstName").keypress(function(event) {
		    var character = String.fromCharCode(event.keyCode);
		    return isValid(character);     
		});

		$("#UserDetailLastName").keypress(function(event) {
			var character = String.fromCharCode(event.keyCode);
			return isValid(character);     
		});


		function isValid(str) {
		    return !/[~`!@#$%\^&*()+=\\[\]\\';,/{}|\\":<>\?]/g.test(str);
		}

		$("#UserDetailOrgName").bind('paste', function(e) {
		    var character = e.originalEvent.clipboardData.getData('Text');
		       return isValid(character); 
		});$("#UserDetailFirstName").bind('paste', function(e) {
		    var character = e.originalEvent.clipboardData.getData('Text');
		       return isValid(character); 
		});$("#UserDetailLastName").bind('paste', function(e) {
		    var character = e.originalEvent.clipboardData.getData('Text');
		       return isValid(character); 
		});
		
		$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
		
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
			});
		
	});
	// Submit Add Form 
	$("#Users").submit(function(e){
		var postData = new FormData($(this)[0]);
		var formURL = $(this).attr("action");
		
		$.ajax({
			url : formURL,
			type: "POST",
			data : postData,
			success:function(response){	
				if($.trim(response) != 'success'){
					$('#RecordAdd').html(response);
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


	$("#OrganisationUserDomainName1111").on( "keyup" , function(event) {		
		 
		
		var englishAlphabetAndWhiteSpace = new RegExp('^[a-zA-Z0-9]$');
		
		var key = String.fromCharCode(event.which);    
		if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetAndWhiteSpace.test(key)) {
			$('#domainchkmsg').html("");
			checkDomain();
			return true;
		}else {
			$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
			return false;  
		}
		
	});
	
	function checkDomain() {
		 
		var dataString = 'domainName='+$("#OrganisationUserDomainName").val();
		//console.log(dataString);
		var strName = $("#OrganisationUserDomainName").val();
		var regex = /[^\w\s]/gi;
		//if(/^[a-zA-Z0-9._-]*$/.test(strName) == false) {
		if( strName.length < 2 && strName.length != 0 ){
			
			$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Your domain '"+strName+"' should be greater than 2 characters.");
			return false;
			
		} else if( strName.length > 16 ){
			
			$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Your domain '"+strName+"' should not be greater than 16 characters.");
			return false;
			
		} else {			
			//if(/^[a-zA-Z0-9\._-]*$/.test(strName) == false) {
			if(/^[a-zA-Z0-9\-]*$/.test(strName) == false) {
			//if(/^[a-zA-Z](\-?[a-zA-Z0-9]+)+[a-zA-Z]$/.test(strName) == false) {
				
				$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Your domain '"+strName+"' contains illegal characters.").show();
				
				//$("#OrganisationUserDomainName").val('').focus();			
				return false;
			}
		}
		
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