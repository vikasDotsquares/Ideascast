<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

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

	.fa-info {
	    background: #3c8dbc none repeat scroll 0 0;
	    border-radius: 50%;
	    color: #fff;
	    font-size: 13px;
	    height: 22px;
	    line-height: 24px;
	    width: 22px;
	}
	input.error, select.error {
	    color: #555;
	    font-size: 14px;
	}
	.no-scroll {
		overflow: hidden;
	}
.form-group .col-lg-1 {
    padding: 0;
}
</style>
	<?php
	if(isset($_SESSION['data']) && !empty($_SESSION['data']) ){
		$this->request->data = $_SESSION['data'];
	}
	?>
	<?php echo $this->Session->flash(); ?>
	<div class="panel panel-primary editprofileinfo">
	  	<div class="panel-heading">
         	<h3 class="panel-title">
            Add Profile
				</h3>
			<div class="changepaawrod-btn">
				<button class="btn save-prof save_contact" type="button">Add</button>
				<?php
				$refer_url = Router::Url( array( 'controller' => 'organisations', 'action' => 'manage_users', 'admin' => FALSE ), TRUE );
				if(isset($refer_to) && !empty($refer_to) && $refer_to == 'people'){
					$refer_url = Router::Url( array( 'controller' => 'resources', 'action' => 'people', 'admin' => FALSE ), TRUE );
				} ?>
				<a href="<?php echo $refer_url; ?>" id="" class="btn btn-primary cancel-prof" data-dismiss="modal"> Cancel</a>
			</div>
      	</div>
		<div class="editprofileinner">
		<div class="editprofilenav">
			<ul class="nav nav-tabs" id="edit_profile_tabs">
				<li class="active">
					<a data-toggle="tab" data-type="details" class="active skilltab tab-detail" data-target="#editprofile-tab" href="#editprofile-tab" aria-expanded="true">Details</a>
				</li>
			</ul>
		</div>

	<div class="tab-content profile-tab-content">
	 <div class="tab-pane fade active in" id="editprofile-tab">
    <div class="prof-editscroll">
    	<?php echo $this->Form->create('Usersss', array('url' => array('controller' => 'organisations', 'action' => 'user_add', $refer_to), 'type' => 'file', 'class' => 'form-horizontal form-bordered user-form', 'enctype' => 'multipart/form-data', 'id' => 'User_add_org')); ?>
    <?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
    <?php
	if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
	echo $this->Form->input('UserRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>
    <?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>

    <div class=" form-horizontal">
        <div class="details-prof-cont profile-tab-cont">

            <?php $no_org = (!isset($organizations) || empty($organizations)) ? 'no-data-there' : ''; ?>
            <div class="row <?php //echo $no_org; ?>">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserDetailOrganizationId">Organization:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.organization_id', array('type' => 'select', 'options' => $organizations, 'empty' => 'Select Organization', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'organization_id')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select organization" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

 				<div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="">Image:</label>
                        <div class="col-lg-9 col-sm-12">
                           <span class="style-popple-icon " style="cursor: default;" >
								<img src="<?php echo SITEURL . 'img/image_placeholders/logo_placeholder.gif'; ?>" class="user-image user-own-pic" align="left" width="36" height="36">
							</span>
                        </div>

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6 <?php //echo $no_org; ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserDetailLocationId">Location:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.location_id', array('type' => 'select', 'options' => [], 'empty' => 'Select Location', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_id')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select location" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Department:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php
                            $department = $this->Common->getDepartmentList();
                            $department = array_unique(array_filter($department));
                            // Update array values
                            $department = array_map(function ($v) {
                                return html_entity_decode(html_entity_decode($v, ENT_QUOTES));
                            }, $department);
                            echo $this->Form->input('UserDetail.department_id', array('options' => $department, 'id' => 'department_dropdown', 'empty' => 'Select Department', 'label' => false, 'div' => false, 'class' => 'form-control'));
                            ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select department" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">First Name:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter first name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Last Name:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter last name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Email:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off', 'id'=>'email' )); ?>
                            <label id="email-error" class="error" for="email"></label>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Email address (Username)" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-3 control-label">Contact Number:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.contact', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength' => 50, 'autocomplete' => 'off')); ?>
                            <span class="error custom-error"></span>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number with no spaces"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="reports_to_id">Reports To:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.reports_to_id', array('type' => 'select', 'options' => $all_report_users, 'empty' => 'Select Person', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'reports_to_id' )); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select who this person reports to" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-3 control-label">Dotted Lines To:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('UserDottedLine.dotted_user_id', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'dotted_user_id' )); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select who this person has dotted line reports to"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Job Title:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.job_title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job title" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Job Role:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.job_role', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => 'off' )); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job role" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col-md-1 control-label" style="width: 12.5%;" for="UserClassification">Biography:</label>
                <div class="col-md-10 col-sm-11" style="width:83.35%">
                    <?php echo $this->Form->textarea('UserDetail.bio', [ 'class' => 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 7, 'placeholder' => '','style'=>'resize:none;' ]); ?>
                </div>
                <div class="col-lg-1 col-sm-1" style="width:0.333%;margin:0;    padding: 0;">
                    <a data-content="Please enter bio" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                </div>
            </div>

			<div class="row padding-top">
				<label class="col-md-1 control-label" style="width: 12.5%;" for="UserClassification">LinkedIn:</label>
                <div class="col-md-10 col-sm-11" style="width:83.35%">
                    <?php echo $this->Form->input('UserDetail.linkedin_url', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>"https://in.linkedin.com/in/xxxxx" )); ?>
                </div>
                <div class="col-lg-1 col-sm-1" style="width:0.333%;    padding: 0;">
                    <a data-content="Copy and paste in your LinkedIn Me URL. For example, https://www.linkedin.com/in/xxxxxxx" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="left" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                </div>
			</div>

        </div>


    </div>

<?php echo $this->Form->end(); ?>
</div>

	 </div>
	 </div>

	</div><!-- /.modal-dialog -->
<style type="text/css">

</style>
<?php $orga_id = (isset($this->request->data["UserDetail"]["organization_id"]) && !empty($this->request->data["UserDetail"]["organization_id"])) ? $this->request->data["UserDetail"]["organization_id"] : ''; ?>
<?php $loca_id = (isset($this->request->data["UserDetail"]["location_id"]) && !empty($this->request->data["UserDetail"]["location_id"])) ? $this->request->data["UserDetail"]["location_id"] : ''; ?>

<script type="text/javascript" >
	$('html').addClass('no-scroll');

    $( "body" ).delegate( ".save-prof", "click", function(event) {
        $(".user-form").submit();
    });

    $("#UserDetailLinkedinUrl").on('focus', function(event) {
        event.preventDefault();
        if($(this).val() == '') {
            $(this).val('https://')
        }
    }).on('blur', function(event) {
        event.preventDefault();
        if($(this).val() == 'https://') {
            $(this).val('')
        }
    });

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

    var orga_id = '<?php echo $orga_id; ?>';
    var loc_id = '<?php echo $loca_id; ?>';
    $('#organization_id').on('change', function(event) {
        event.preventDefault();
        var org_id = $(this).val();
        $('#email-error').text('')
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
    	var email = $('#email').val();
    	if(orgid != '' && orgid !== undefined) {
    		$('#UserEmail-error,#email-error').text('')
	    	$.ajax({
	            url: $js_config.base_url + 'users/validate_email',
	            type: 'POST',
	            dataType: 'json',
	            data: {orgid: orgid, 'data[User][email]': email},
	            success: function(response){
	                if(response == false){
	                	if($('#UserEmail-error').length <= 0){
	                    	$('#email-error').text('Your email address is invalid or already taken').show();
	                	}
	                	else{
	                		$('#UserEmail-error').text('Your email address is invalid or already taken').show()
	                	}
	                }
	            }
	        })
	    }
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

    })(orga_id);


	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 32 && (charCode < 48 || (charCode > 57 && charCode !=107 )))
		return false;
		return true;
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

	$(document).ready(function(){

		$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
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
</script>

<?php   echo $this->Html->script(array('jquery.validate')); ?>

<script>
var varEmail = '';


$("#User_add_org").validate({

rules: {

		'data[UserDetail][first_name]': "required",
		'data[UserDetail][last_name]': "required",
		'data[User][email]': {
			required: true,
			email: true,
			"remote":
			{
			  url: $js_config.subdomain_base_url+'users/validate_email',
			  type: "post",
			  crossDomain: true,

			  data:
			  {
				  email: function(response)
				  {  // console.log(response);
					  return $('#User :input[name="data[User][email]"]').val();
				  },
				  orgid: function(response){
						return $('#organization_id').val();

				  }
			  }
			}

		},
},
messages: {

		'data[UserDetail][first_name]': {
			required: "First Name is required",
		},
		'data[UserDetail][last_name]': {
			required: "Last Name is required",
		} ,

		'data[User][email]': {
			required: "Email is required",
			email: "Your email address must be in the format of name@domain.com",
			remote: jQuery.validator.format("Your email address is invalid or already taken")

		}

	}

})

</script>

<?php
//remote: jQuery.validator.format("{0} is invalid or already taken.")
//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) {
$reports_to_id = (isset($this->data) && !empty($this->data)) ? $this->data['UserDetail']['reports_to_id'] : 0;

?>
<script>
$(function(){

	//REPORTS TO AND DOTTED USERS
	var reports_to_id = '<?php echo $reports_to_id ?>';

    function sort_arr_obj (a, b){
        var aName = a.label.toLowerCase();
        var bName = b.label.toLowerCase();
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }

    ;($.dottect_users = (reports_to) => {
        if(reports_to == '' || reports_to == 0 || reports_to == undefined) return;
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/dottect_users',
            type: 'POST',
            dataType: 'json',
            data: {reports_to: reports_to, type: 'new'},
            success: function(response) {
                var content = response.content.sort(sort_arr_obj);
                $("#dotted_user_id").multiselect('dataprovider', content);
                setTimeout(()=>{
                    $("#dotted_user_id").val(response.dotted_users).multiselect('select', response.dotted_users).multiselect("refresh");
                }, 1);
                dfd.resolve(response);
            }
        })
        return dfd.promise();
    })(reports_to_id)

    $('#reports_to_id').on('change', function(event) {
        event.preventDefault();
        var val = $(this).val();
        if(val != '' && val != 0 && val != undefined){
            $.dottect_users(val);
        }
    });

    $('#discard_updates').on('click', function(event) {
        event.preventDefault();
        var url = $(this).data('url');
        location.href = url;
    });

    ;($.report_to_users = () => {
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/report_to_users',
            type: 'POST',
            dataType: 'json',
            data: {type: 'new'},
            success: function(response) {
                var content = response.content.sort(sort_arr_obj);
                var selected_id = response.reports_to_id;

                $('#reports_to_id').empty();
                    $('#reports_to_id').append('<option value="">Select Person</option>');

                    if (content != null) {
                        $('#reports_to_id').append(function() {

                            var output = '';

                            $.each(content, function(key, value) {
                                var sel = '';
                                if (value.value == selected_id) {
                                    sel = 'selected="selected"';
                                }
                                output += '<option ' + sel + ' value="' + value.value + '">' + value.label + '</option>';
                            });
                            return output;
                        });
                        // $('#reports_to_id').prop('disabled', false);
                    }

                dfd.resolve(response);
            }
        })
        return dfd.promise();
    })();



	$('body').delegate('.readpolicyshows', 'click', function(event){
		event.preventDefault();
		var href = $js_config.base_url + 'users/list_program_policy';;
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

	// RESIZE/SCROLL

	$('#edit_profile_tabs').removeAttr('style');


    // RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        var $scroll_wrapper = $('.prof-editscroll', $('.tab-pane:visible'));
        $scroll_wrapper.animate({
            minHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 37,
            maxHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 37
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })


    $("#edit_profile_tabs").on('show.bs.tab', function(e){
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1000);
    })
});
</script>
<?php //} ?>
<style>
input.error {
    font-size: 14px;
}
.error {
    color: #dd4b39;
    font-size: 11px;
    font-weight: normal;
	display: block;
}</style>