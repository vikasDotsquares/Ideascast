
<!-- ADD NEW Industry User -->
<link href="<?php echo SITEURL;?>plugins/select2/dist/css/select2.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo SITEURL;?>plugins/select2/dist/js/select2.full.js"></script>
<style>
.select2-selection__choice__remove {
		float: right !important;
		padding: 0 0 0 5px;
}
</style>
<?php
if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
$this->request->data = $_SESSION['data'];}
//pr($this->request->data); die;
?>
<?php echo $this->Session->flash( ); ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <?php echo $this->Html->script('jquery.validate', array('inline' => true)); ?>
        <?php echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true)); ?>
        <?php echo $this->Html->script('custom_validate', array('inline' => true)); ?>

        <script type="text/javascript" >
            $(function () {

                $(".js-example-basic-multiple").select2({
                    maximumSelectionLength: 20
                });

                var elm = [$('#txa_title'), $('#txa_objective'), $('#txa_description')];
                wysihtml5_editor.set_elements(elm)
                $.wysihtml5_config = $.get_wysihtml5_config()

                setTimeout(function () {

                    var title_config = $.wysihtml5_config;
                    $.extend(title_config, {'parserRules': {'tags': {'br': {'remove': 1}}}})

                    //	$("#txa_title").wysihtml5( title_config );

                    //	$("#txa_objective").wysihtml5( $.extend( $.wysihtml5_config, {'lists': true, 'limit': 250, 'parserRules': { 'tags': { 'br': { 'remove': 0 } } }}, $.wysihtml5_config) );
                    //	$("#txa_description").wysihtml5( $.extend( $.wysihtml5_config, {'lists': true, 'limit': 500, 'parserRules': { 'tags': { 'br': { 'remove': 0 } } }}, $.wysihtml5_config)  );
                }, 1000);

            })
        </script>

        <h3 class="panel-title">Edit Profile</h3>
    </div>

    <?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'orgaccountedit'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'User')); ?>
    <?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
    <?php
	if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
	echo $this->Form->input('UserRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>
    <?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>
    <?php //echo $this->Form->input('UserEmails', array('type' => 'hidden')); ?>
	<input type="hidden" name="UserEmails" value="<?php echo $this->request->data['User']['email'];?>" >


    <div class="panel-body form-horizontal">
        <div class="panel-heading"></div>

        <div class="modal-body">

            <?php //echo $this->Session->flash('auth'); ?>

            <div class="row">
				<div class="col-md-6">

                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">First Name:</label>
                        <div class="col-lg-7 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter first name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Last Name:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
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
                        <label class="col-lg-4 control-label" for="UserClassification">Email:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter email(username)" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-4 control-label">Contact Number:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.contact', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">New Password:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('User.password', array('type' => 'password', 'autocomplete' => 'off', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							<?php if(isset($this->request->data['error']['password']) && !empty($this->request->data['error']['password'])){ ?>
								<span class="text-red pass_error"><?php echo $this->request->data['error']['password']; ?></span>
							<?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
							<a data-content="Please enter secure password" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Confirm Password:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please re-enter secure password" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CompanyCompany" class="col-lg-4 control-label">Country:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.country_id', array('options' => $this->Common->getCountryList(), 'empty' => 'Select Country', 'label' => false, 'div' => false, 'onchange' => 'selectCity(this.options[this.selectedIndex].value)', 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select country"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php
                    $states = array();
                    if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
                        $states = $this->Common->getStateList($this->data['UserDetail']['country_id']);

                    }
                    ?>
                    <div class="form-group">
                        <label for="CompanyCompany" class="col-lg-4 control-label">State/County:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.state_id', array('options' => $states, 'id' => 'state_dropdown', 'empty' => 'Select State', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select state/county"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-4 control-label">City/Town:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.city', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter city/town"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-4 control-label">Zip/Postcode:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.zip', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter zip/postcode"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-4 control-label">Address:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.address', array('type' => 'textarea', 'rows' => 2, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter address"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="modal-footer clearfix">

                                        <button type="button" class="btn btn-success" id="formsubmit"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
<?php
//$url = SITEURL . "projects/lists";
$url = SITEURL . "organisations/manage_users";
?>
                                        <a href="<?php echo $url; ?>" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</a>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.modal-content -->




<script type="text/javascript" >

// Submit Add Form
    $("#Userd").submit(function (e) {
        var postData = new FormData($(this)[0]);
        var formURL = $(this).attr("action");

        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#popup_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            },
            cache: false,
            contentType: false,
            processData: false,
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action
        //e.unbind(); //unbind. to stop multiple form submit.
    });


</script>
<style>
    .modal-open .modal ,.fade.in{
        /* overflow-x: hidden !important;
        overflow-y: auto !important; */
    }
    .panel-primary > .panel-heading {
        background-color: #5f9323 !important;
    }
    .error{ color : #f00 ; font-weight:normal}
    .form-group .col-lg-1 {
        padding: 0;
    }

    .fa-info {
        background: #00aff0 none repeat scroll 0 0;
        border-radius: 50%;
        color: #fff;
        font-size: 13px;
        height: 22px;
        line-height: 24px;
        width: 22px;
    }

    #User label { font-size : 13px ;}
</style>
<script type="text/javascript">

	$("#formsubmit").on('click',function(e){
	e.preventDefault();

		var puraniEail = $("input[name=UserEmails]").val();
		var newEmalis =  $("input[name='data[User][email]']").val();
		if( puraniEail !== newEmalis ){

			BootstrapDialog.show({
	            title: 'Confirmation',
	            message: 'Please Inform IdeasCast you have updated the Admin email address.',
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: true,
	            buttons: [{
	                    icon: 'fa fa-check',
	                    label: ' Yes',
	                    cssClass: 'btn-success',
	                    autospin: true,
	                    action: function(dialogRef) {

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							setTimeout(function() {
								dialogRef.getModalBody().html('<div class="loader"></div>');
								dialogRef.close();
								$("#User").submit();
							}, 500);

	                    }
	                },
	                {
	                    label: ' No',
	                    icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }

	                }
	            ]
	        });
		} else {
			$("#User").submit();
		}

	})

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
        $("#" + loadType + "_loader").fadeIn(400).html('Please wait... <?php echo $this->Html->image('loading1.gif'); ?>');
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller' => 'users', 'action' => 'get_state_city', 'admin' => true)); ?>",
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
</script>
