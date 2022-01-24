<style type="text/css">
	.no-data-there {
	    pointer-events: none;
	}
	.no-data-there .toltipover {
	    pointer-events: none !important;
	}
	.email-error {
	    color: #dd4b39;
	    font-size: 11px;
	    font-weight: normal;
	    display: block;
	    max-width: 100%;
    	margin-bottom: 5px;
	}
</style>
<?php echo $this->Session->flash(); ?>
	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Edit User
          <!--  <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>organisations/manage_users">Back</a></h3>-->
      </div>
		<div class="panel-body add-user-form">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php
			echo $this->Form->create('User', array('type' => 'file','url'=>SITEURL.'organisations/user_edit/'.$this->request->data['User']['id'], 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit', 'autocomplete'=>'off'));


				$_SERVER['HTTP_REFERER'] = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : '';
				?>
				<div class="modal-body">
				<?php echo $this->Form->input('pagerefer', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>$_SERVER['HTTP_REFERER'])); ?>
				<?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>

				<?php echo $this->Form->input('UserDetail.org_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>


				<?php echo $this->Form->input('OrganisationUser.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php echo $this->Form->input('OrganisationUser.user_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php echo $this->Form->input('OrganisationUser.creator_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				<?php $no_org = (!isset($organizations) || empty($organizations)) ? 'no-data-there' : ''; ?>
					<div class="row <?php echo $no_org; ?>">
		                <div class="col-md-6">

		                    <div class="form-group">
		                        <label class="col-lg-3 control-label add-label-col" for="organization_id">Organization:</label>
		                        <div class="col-lg-8 add-filds-col">
		                            <?php echo $this->Form->input('UserDetail.organization_id', array('type' => 'select', 'options' => $organizations, 'empty' => 'Select Organization', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'organization_id')); ?>
		                        </div>
		                        <div class="col-lg-1 padding0">
									<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select organization"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  	</div>
		                    </div>
		                </div>
		                <div class="col-md-6">

		                    <div class="form-group">
		                        <label class="col-lg-3 control-label add-label-col" for="location_id">Location:</label>
		                        <div class="col-lg-8 add-filds-col">
		                            <?php echo $this->Form->input('UserDetail.location_id', array('type' => 'select', 'options' => [], 'empty' => 'Select Location', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_id')); ?>
		                        </div>
		                        <div class="col-lg-1 padding0">
		                            <a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select location"><i class="fa fa-info-circle fa-3 martop"></i></a>
		                        </div>
		                    </div>
		                </div>
		            </div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">First Name:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user first name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Last Name:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user last name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Email:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('User.email', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
								<label class="email-error"></label>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user email(username)"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="IndustryClassificationClassification" class="col-lg-3 control-label add-label-col">Profile Image:</label>
								<div class="col-lg-8 add-filds-col">
									<?php echo $this->Form->input('UserDetail.profile_pic', array('type' => 'file', 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'height:auto')); ?>
								</div>
								<div class="col-lg-1 padding0">
									<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select user profile image"><i class="fa fa-info-circle fa-3 martop"></i></a>
								</div>
									<?php
									if( isset($this->data['UserDetail']['profile_pic']) && !empty($this->data['UserDetail']['profile_pic']) ){

										if( !empty($this->data['UserDetail']['profile_pic']) ){
											$docimg = $this->data['UserDetail']['profile_pic'];

											if( !empty($docimg) && file_exists(USER_PIC_PATH.$docimg)){
												$docimgs = SITEURL.USER_PIC_PATH.$docimg;
											} else {
												$docimgs = SITEURL.'img/image_placeholders/logo_placeholder.gif';
											}

										} else if( !empty($this->data['UserDetail']['profile_pic']['name']) ) {

											$docimg = $this->data['UserDetail']['profile_pic']['name'];
											$docimgs = SITEURL.USER_PIC_PATH.$docimg;
											if( !empty($docimg) && file_exists(USER_PIC_PATH.$docimg)){
												$docimgs = SITEURL.USER_PIC_PATH.$docimg;
											} else {
												$docimgs = SITEURL.'img/image_placeholders/logo_placeholder.gif';
											}

										} else {
											$docimgs = SITEURL.'img/image_placeholders/logo_placeholder.gif';
										}


									} else{
										$docimgs = SITEURL.'img/image_placeholders/logo_placeholder.gif';
									}


									?>
								<div class="col-lg-3 add-label-col">&nbsp;</div>

								  <div class="col-lg-8 add-filds-col">

									  <img src="<?php echo $docimgs ?>" class="img-circles11" alt="Profile Image" />
								  </div>
							</div>


						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Password:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('User.password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'form-control readpolicyshows','autocomplete'=>'false'  )); ?>
								<span style="font-size:13px;">Note: Type in - Not copy/paste in</span>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Confirm Password:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								<span style="font-size:13px;">Note: Type in - Not copy/paste in</span>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please re-enter above secure password"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>

		            <div class="row">
		                <div class="col-md-6">
		                    <div class="form-group">
		                        <label class="col-lg-3 control-label add-label-col" for="UserDetailReportsToId">Reports To:</label>
		                        <div class="col-lg-8 add-filds-col">
		                            <?php echo $this->Form->input('UserDetail.reports_to_id', array('type' => 'select', 'options' => $all_users, 'empty' => 'Select User', 'label' => false, 'div' => false, 'class' => 'form-control' )); ?>
		                        </div>
		                        <div class="col-lg-1 padding0">
		                            <a data-content="Please select reports to person" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn btn-default toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info-circle fa-3 martop"></i></a>
		                        </div>
		                    </div>
		                </div>

		                <div class="col-md-6">
		                    <div class="form-group">
		                        <label for="dotted_user_id" class="col-lg-3 control-label add-label-col">Dotted Lines:</label>
		                        <div class="col-lg-8 add-filds-col popup-select-icon">
		                            <?php echo $this->Form->input('UserDottedLine.dotted_user_id', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'dotted_user_id' )); ?>
		                        </div>
		                        <div class="col-lg-1 padding0">
		                            <a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select dotted lines"><i class="fa fa-info-circle fa-3 martop"></i></a>
		                        </div>
		                    </div>
		                </div>

		            </div>

		            <?php /* ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="CompanyCompany" class="col-lg-3 control-label add-label-col">Country:</label>
							  <div class="col-lg-8 add-filds-col">
								 <?php echo $this->Form->input('UserDetail.country_id', array('options' => $this->Common->getCountryList(),  'empty' => 'Select Country', 'label' => false, 'div' => false,  'onchange' => 'selectCity(this.options[this.selectedIndex].value)','class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
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
							  <label for="CompanyCompany" class="col-lg-3 control-label add-label-col">State/County:</label>
							  <div class="col-lg-8 add-filds-col">
							   <?php
								 echo $this->Form->input('UserDetail.state_id', array('options' => $states,'id' => 'state_dropdown', 'empty' => 'Select State', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select state"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">City/Town:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('UserDetail.city', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user city"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Zip/Postcode:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('UserDetail.zip', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user zip code"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Address:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php echo $this->Form->input('UserDetail.address', array('type' => 'textarea','rows' => 2,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user address"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>


					</div>

					<?php */ ?>


					<div class="row">
						<div class="col-md-6">
							<?php
								//pr($this->data['User']['status']);
								$checked = '';
								if(isset($this->data['User']['status']) && !empty($this->data['User']['status'])){
									$checked = 'checked';
								}

							?>
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label label-status add-label-col">Status:</label>
							  <div class="col-lg-8  add-filds-col">
							  	<?php if(empty($this->data['User']['is_activated']) && !empty($this->data['User']['activation_time'])){ ?>
							  		<span style="cursor: not-allowed;">
										<input id="UserStatusADD" disabled="" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[User][status]" type="checkbox" style="pointer-events: none;">
									</span>
							  	<?php }else{ ?>
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[User][status]" <?php echo $checked; ?> type="checkbox">
								<?php } ?>

							  </div>
							  <div class="col-lg-1 padding0">
								<!-- <a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="An email will be sent to the new user to activate their account"><i class="fa fa-info-circle fa-3 martop"></i></a> -->
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label add-label-col">Contact Number:</label>
							  <div class="col-lg-8 add-filds-col">
								<?php
								echo $this->Form->input('UserDetail.contact', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control', "maxlength"=>"50",'placeholder'=>'','autocomplete'=>'off')); ?>
							  </div>
							  <div class="col-lg-1 padding0">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number (+44)(0)20-12341234 | 02012341234 | +44 (0) 1234-1234"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>

					</div>
				</div>
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>

					<a class="btn btn-danger" href="<?php echo SITEURL ?>organisations/manage_users">Close</a>

					<!-- <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->


<?php //pr($this->data, 1);
$org_id = (isset($this->data['UserDetail']['organization_id']) && !empty($this->data['UserDetail']['organization_id'])) ? $this->data['UserDetail']['organization_id'] : 0; ?>
<?php $loc_id = (isset($this->data['UserDetail']['location_id']) && !empty($this->data['UserDetail']['location_id'])) ? $this->data['UserDetail']['location_id'] : 0; ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<script type="text/javascript" >

	$dotted_user_id = $('#dotted_user_id').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dotted_user_id[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search People',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select People',
            onSelectAll:function(){
            },
            onDeselectAll:function(){
            },
            onChange: function(element, checked) {
            }
    });

    var orga_id = '<?php echo $org_id; ?>';
    var loc_id = '<?php echo $loc_id; ?>';
    $('#organization_id').on('change', function(event) {
        event.preventDefault();
        var org_id = $(this).val();
        if(org_id != '' && org_id != undefined) {
            $.org_location(org_id);
            $.validate_org_email();
        }
        else{
            $('#location_id').empty();
            $('#location_id').append('<option value="">Select Location</option>');
        }
    });

    function sort_arr_obj (a, b){
        var aName = a.name.toLowerCase();
        var bName = b.name.toLowerCase();
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }

    $.validate_org_email = function(){
    	var orgid = $('#organization_id').val();
    	var email = $('#UserEmail').val();

    		$('#UserEmail-error,.email-error').text('')
	    	$.ajax({
	            url: $js_config.base_url + 'users/validate_email',
	            type: 'POST',
	            dataType: 'json',
	            data: {orgid: orgid, email: email},
	            success: function(response){
	            	console.log('response', response)
	                if(response == false){
	                	if($('#UserEmail-error').length <= 0){
	                    	$('.email-error').text('Your email address is invalid or already taken.')
	                	}
	                	else{
	                		$('#UserEmail-error').text('Your email address is invalid or already taken.').show()
	                	}
	                }
	            }
	        })
    }

    ;($.org_location = function(org_id){
    	if(org_id == '' || org_id === undefined) return;

        $.ajax({
            url: $js_config.base_url + 'users/org_location',
            type: 'POST',
            dataType: 'json',
            data: {org_id: org_id},
            success: function(response){
                if(response.success){
                    $('#location_id').empty();
                    if(response.content){
                        var content = response.content.sort(sort_arr_obj);
                        $('#location_id').append('<option value="">Select Location</option>');
                        $('#location_id').append(function() {
                            var output = '';
                            $.each(content, function(key, value) {
                                var selected = '';
                                if(loc_id == value.id){
                                    selected = 'selected="selected"';
                                }
                                output += '<option value="' + value.id + '" '+selected+'>' + value.name + '</option>';
                            });
                            return output;
                        });
                    }
                }
            }
        })

    })(orga_id)

    var typingTimer;                //timer identifier
	var doneTypingInterval = 300;  //time in ms
    $('[name="data[User][email]"]').on('keyup', function(event) {
    	typingTimer = setTimeout($.proxy(function(){
    		$.validate_org_email();
    	},this), doneTypingInterval);
    });
    $('[name="data[User][email]"]').on('keydown', function(event) {
	  	clearTimeout(typingTimer);

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

	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31
		&& (charCode < 48 || charCode > 57))
		return false;
		return true;
	}

	function selectCity(country_id) {
        if (country_id != "-1") {
            loadData('state', country_id);
            $("#city_dropdown").html("<option value=''>Select city</option>");
        } else {
            $("#state_dropdown").html("<option value=''>Select state</option>");
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

    function selectState(state_id) {
        if (state_id != "-1") {
            loadData('city', state_id);
        } else {
            $("#city_dropdown").html("<option value=''>Select city</option>");
        }
    }

	function loadData(loadType, loadId) {
        var dataString = 'loadType=' + loadType + '&loadId=' + loadId;
        $("#" + loadType + "_loader").show();
        $("#" + loadType + "_loader").fadeIn(400).html('Please wait... <img src="<?php echo SITEURL ?>img/loading1.gif" alt="" />');
        $.ajax({
            type: "POST",
            url: "<?php echo SITEURL ?>sitepanel/users/get_state_city",
            data: dataString,
            cache: false,
            success: function (result) {
                //$("#" + loadType + "_loader").hide();
                if ($("#" + loadType + "_dropdownEdit").length > 0 && $("#Recordedit").css('display') != 'none') {
                    $("#" + loadType + "_dropdownEdit").html("<option value=''>Select " + loadType + "</option>");
                    $("#" + loadType + "_dropdownEdit").append(result);
                } else {
                    $("#" + loadType + "_dropdown").html("<option value=''>Select " + loadType + "</option>");
                    $("#" + loadType + "_dropdown").append(result);
                }
            }
        });
    }

	$("#OrganisationUserDomainName").on('keyup', function(){
		checkDomain();
	})
	function checkDomain() {

		var dataString = 'userid='+$("#UserId").val()+'&domainName='+ $("#OrganisationUserDomainName").val();

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo SITEURL ?>organisations/checkdomain",
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
<script>
$(function(){

	$('body').delegate('.readpolicyshows', 'click', function(event){
		event.preventDefault();
		var href = $js_config.base_url + 'users/list_program_policy';
		$t = $(this);
		$(this).popover('destroy');
	  	$.ajax({
			url: href,
			type: "POST",
			data: $.param({listpolicy:'policy'}),
			global: false,

			success: function (response) {
                $t.popover({
				placement : 'right',
				trigger : 'hover',
				html : true,
				template: '<div class="popover " role="tooltip"><div class="arrow"></div><h3 class="popover-title" style="display:none;"></h3><div class="popover-content chngpass-popover pop-content"></div></div>',
				container: 'body',
				});
				$t.attr('data-content',response);
				$t.popover('show');
            }

		})
	})

	$('body').delegate('.readpolicyshows', 'mouseout', function(event){
		event.preventDefault();
		$(this).popover('destroy');
	});


});
</script>

<?php   echo $this->Html->script(array('jquery.validate')); ?>

<script>
$(function(){
$("#RecordFormedit").validate({
rules: {
'data[User][email]': {
required: true,
email: true
}
},

});
})
</script>
<style>.error {
    color: #dd4b39;
    font-size: 11px;
    font-weight: normal;
}</style>