<!-- ADD NEW Industry User -->

	<div class="panel panel-primary">
	<?php echo $this->Session->flash();?>

	   <div class="box-header" style="cursor: move;">
			<ol class="breadcrumb">
				<li><a href="<?php echo SITEURL ?>sitepanel/organisations" data-original-title="" title=""><i class="fa fa-building"></i> Organization</a></li>
				<li><a href="<?php echo SITEURL ?>sitepanel/organisations" data-original-title="" title=""><?php echo (isset($organisationname) && !empty($organisationname) )? $organisationname : "";?></a></li>
				<li class="active">Add Linked Domain</li>
			</ol>
	   </div>

	  <div class="panel-heading">
         <h3 class="panel-title">
            Add Linked Domain
           <!-- <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/organisations">Back</a></h3>-->
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('OrgSetting', array( 'url' => array('controller'=>'organisations', 'action'=>'add_domain', $id), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'domainAdd')); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Domain:</label>
							  <div class="col-lg-6">
								<?php
								$validatecontent = '
* Please enter domain name.<br/>
* Domain length should not exceed than 32 characters.<br/>
* Domain length should be minimum of 2 characters.<br/>
* . is not allowed in domain name.<br/>
* At the start and end of domain name, special characters will not be allowed including hyphen (-)<br/>
* Domain should start and end with characters only.<br/>';
//,'style'=>'text-transform:lowercase'
								echo $this->Form->input('OrgSetting.subdomain', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Linked Domain Name','minlength'=>'2', 'maxlength'=>'32' )); ?>
								<div id="domainchkmsg" style="display:none;font-size: 11px;"></div>
							  </div>
							  <div class="col-lg-2">
								<button type="button" onclick="checkDomain()" id="checkorgdomain" class="btn btn-primary">Check</button>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="<?php echo $validatecontent;?>"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label" style="padding-top:0;">Version:</label>
							  <div class="col-lg-8">
									<?php echo $ideasCastVersion; ?>
							  </div>
							</div>
						</div>

				</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Start date:</label>
							  <div class="col-lg-8">
									<?php echo $this->Form->input('OrgSetting.start_date', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control dates input-small hasDatepicker', 'autocomplete'=>'off', 'id'=>'start_date','placeholder'=>'Start Date')); ?>

									<?php
									if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ){
										$this->request->data['OrgSetting']['user_id'] = $this->params['pass'][0];
										$this->request->data['OrgSetting']['org_id'] = $this->params['pass'][0];
									}

									echo $this->Form->input('OrgSetting.user_id', array('type' => 'hidden','label' => false, 'div' => false));
									echo $this->Form->input('OrgSetting.org_id', array('type' => 'hidden','label' => false, 'div' => false)); ?>

							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter start date"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">End Date:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('OrgSetting.end_date', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control dates input-small hasDatepicker', 'autocomplete'=>'off', 'id'=>'end_date','placeholder'=>'End Date','required'=>'required')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter end date"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Licensed Users:</label>
							  <div class="col-lg-8">
									<?php echo $this->Form->input('OrgSetting.license', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control dates input-small hasDatepicker', 'autocomplete'=>'off','id'=>'license','placeholder'=>'Number of Licenses', "onkeypress"=>"return isNumberKey(event)", "maxlength"=>"4", "min"=>"1")); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter number of user license"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>


					  <div class="col-md-6">
						<div class="form-group">
						  <label for="UserUser" class="col-lg-3 control-label">Data Storage:</label>
						  <div class="col-lg-8">
								<?php echo $this->Form->input('OrgSetting.allowed_space', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control dates input-small hasDatepicker', 'autocomplete'=>'off','id'=>'allowed_space','placeholder'=>'Allocated Storage in GB', "onkeypress"=>"return isNumberKey(event)", "maxlength"=>"6", "min"=>"1", "max"=>"1000", "step"=>"0.1" )); ?>
						  </div>
						  <div class="col-lg-1">
							<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter allocated space in GB"><i class="fa fa-info-circle fa-3 martop"></i></a>
						  </div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
						  <label for="UserUser" class="col-lg-3 control-label">API License:</label>
						  <div class="col-lg-8">
								<?php echo $this->Form->input('OrgSetting.apilicense', array('type' => 'number','label' => false, 'div' => false, 'class' => 'form-control dates input-small hasDatepicker', 'autocomplete'=>'off','id'=>'apilicense','placeholder'=>'Number of API Licenses', "onkeypress"=>"return isNumberKey(event)", "maxlength"=>"4", "min"=>"0", "max"=>"1000" )); ?>
						  </div>
						  <div class="col-lg-1">
							<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter number of API license"><i class="fa fa-info-circle fa-3 martop"></i></a>
						  </div>
						</div>
					</div>
				</div>


				<div class="modal-footer clearfix">
					<?php if( isset($this->params['pass'][0]) ){
						$userid = $this->params['pass'][0];
					} else {
						$userid = $this->request->data['OrgSetting']['user_id'];
					}

					?>
					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Create</button>
					<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/organisations/list_domain/<?php echo $userid;?>">Close</a>
					<!--<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> --->
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


		$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});

		var startDate = new Date();
		var FromEndDate = new Date();
		var ToEndDate = new Date();

		ToEndDate.setDate(ToEndDate.getDate()+365);

			$('#start_date').datepicker({
			weekStart: 1,
			format: 'dd/mm/yyyy',
			startDate: FromEndDate,
			autoclose: true
			}).on('changeDate', function(selected){
			startDate = new Date(selected.date.valueOf());
			startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
			$('#end_date').datepicker('setStartDate', startDate);
			});


			$('#end_date').datepicker({
				weekStart: 1,
				format: 'dd/mm/yyyy',
				startDate: FromEndDate,
				EndDate: FromEndDate,
				autoclose: true,
				//maxDate:FromEndDate,


			}).on('changeDate', function(selected){
				FromEndDate = new Date(selected.date.valueOf());
				FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
				$('#start_date').datepicker('setEndDate', FromEndDate);
			})


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

		var dataString = 'domainName='+$("#OrgSettingSubdomain").val();
		//console.log(dataString);
		var strName = $("#OrgSettingSubdomain").val();
		var regex = /[^\w\s]/gi;
		//if(/^[a-zA-Z0-9._-]*$/.test(strName) == false) {
		if( strName.length < 1 && strName.length != 0 ){

			$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Domain 'https://"+strName+"<?php echo WEBDOMAIN; ?>' should be greater than 1 character.");
			return false;

		} else if( strName.length > 32 ){

			$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Domain 'https://"+strName+"<?php echo WEBDOMAIN; ?>' should not be greater than 32 characters.");
			return false;

		} else {
			//if(/^[a-zA-Z0-9\._-]*$/.test(strName) == false) {
			if(/^[a-zA-Z0-9\-]*$/.test(strName) == false) {
			//if(/^[a-zA-Z](\-?[a-zA-Z0-9]+)+[a-zA-Z]$/.test(strName) == false) {

				$("#domainchkmsg").removeClass('text-green').addClass('text-red').html("Domain 'https://"+strName+"<?php echo WEBDOMAIN; ?>' contains illegal characters.").show();

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