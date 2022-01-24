<!-- ADD NEW Industry User -->
<link href="<?php echo SITEURL;?>plugins/select2/dist/css/select2.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo SITEURL;?>plugins/select2/dist/js/select2.full.js"></script>
<style>
.select2-selection__choice__remove {
		float: right !important;
		padding: 0 0 0 5px;
}
.pdf-icon {
    background: url(../images/lightbulb.png) no-repeat left center;
    width: 11px;
    height: 11px;
    display: inline-block;
    vertical-align: top;
    margin-left: 4px;
}
.saveinterest  {
    border-color: #67a028;
    cursor: pointer;
}
.interest-error {
    border-color: #d73925;
}
.not_editable {
    cursor: not-allowed;
}
.not_editable #userinterest, .not_editable .saveinterest  {
    pointer-events: none;
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
        <?php echo $this->Html->script('projects/skillpdf', array('inline' => true)); ?>
        <?php echo $this->Html->css('skillpdf'); ?>

        <script type="text/javascript" >
            $(function () {

                $(".js-example-basic-multiple").select2({
                    maximumSelectionLength: 20
                });

				/* $('body').delegate('.selection', "click", function(event) {
					alert(0);
				}) */

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

    <?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'myaccountedit'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'User')); ?>
    <?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
    <?php
	if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
	echo $this->Form->input('UserRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>
    <?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>

    <div class="panel-body form-horizontal">
        <div class="panel-heading"></div>

        <div class="modal-body">

            <?php //echo $this->Session->flash('auth'); ?>
			<div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Organization Name:</label>
                        <div class="col-lg-7 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.org_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter organization name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>
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
                            <?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off' )); ?>
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
                            <a tabindex="0" data-placement="top" class="btn  toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number with no spaces."><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>
			<?php /*
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
            </div>	*/ ?>

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
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Department:</label>
                        <div class="col-lg-7 col-sm-11 ">
                            <?php echo $this->Form->input('UserDetail.department', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter department" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">


                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Job Title:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php echo $this->Form->input('UserDetail.job_title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job title" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Job Role:</label>
                        <div class="col-lg-7 col-sm-11 ">
                            <?php echo $this->Form->textarea('UserDetail.job_role', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control','escape' => true, 'rows' => 2, 'placeholder' => '')); ?>

                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job role" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">


                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Skills:</label>
                        <div class="col-lg-7 col-sm-11">
                            <?php
                            /* echo $this->Form->input('Skill.Skill', array(
                                'options' => $skills,
                                'multiple' => true,
                                'required' => false,
                                'label' => false,
                                'div' => false,
                                'class' => 'js-example-basic-multiple select2-me form-control'
                                    )
                            ); */

							 $skillhtml = '';
							 $skillidss = '';

							 //pr($this->request->data);

							 if( isset($this->request->data['Skill']) && !empty($this->request->data['Skill']) ){
									foreach($this->request->data['Skill'] as $listexists){

										$pdfcount = $this->Common->getUserSkillPdfCount($listexists['id'],$this->Session->read("Auth.User.id"));

										$skilpdfcnt = (isset($pdfcount) && !empty($pdfcount) )? $pdfcount : 0;
										$skillhtml .='<div class="tag-row"><span data-tagid="'.$listexists['id'].'" class="tag-text">'.$listexists['title'].'</span> <span class="tag-count">('.$skilpdfcnt.')</span><span class="tag-delete">✖</span></div>';
										$skillidss .= $listexists['id'].',';
								}
							 }

                            ?>
							<div class="tag-container">
								<?php echo $skillhtml;?>
								<input type="text" name="term" class="term" autocomplete="off"  />
								<input type="hidden" name="skillpdf" id="skillpdfids" value="<?php echo substr(trim($skillidss), 0, -1); ?>" />
								<span class="loader-icon fa fa-spinner fa-pulse"></span>
							</div>
							<label id="SkillSkill-error" class="error" for="SkillSkill"></label>
                        </div>

                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Maximum of 20 Skills" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>

                </div>



                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="UserClassification">Interests:</label>
                        <div class="col-lg-7 col-sm-11">
							<div class="tag-container">
								<div class="input-group mb-3 interest-wrapper">
								  <input type="text" id="userinterest" class="form-control" placeholder="Enter Interest" aria-label="Enter Interest" aria-describedby="basic-addon2" onkeydown="return (event.keyCode!=13);" autocomplete="off"  >
									<div class="input-group-addon bg-green saveinterest" style="">
										<i class="fa fa-check" aria-hidden="true"></i>
									</div>
									<div class="input-group-addon bg-red closeinterest hide" >
										<i class="fa fa-times" aria-hidden="true"></i>
									</div>
								</div>
								<div class="interest-container"></div>
							</div>
							<label id="userinterests-error" class="error" for="SkillSkill"></label>
                        </div>

                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Maximum of 20 Interests" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>

                </div>



            </div>
            <div class="row">


                <label class="col-md-1 control-label" style="width: 16.5%;" for="UserClassification">Bio:</label>
                <div class="col-md-10 col-sm-11" style="width:79.35%">
<?php echo $this->Form->textarea('UserDetail.bio', [ 'class' => 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 7, 'placeholder' => '']); ?>
                </div>
                <div class="col-lg-1 col-sm-1" style="width:0.333%;margin:0; padding:0">
                    <a data-content="Please enter bio" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                </div>


            </div>

        </div>
    </div>

    <div class="modal-footer clearfix">

                                        <button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
<?php $url = SITEURL . "projects/lists"; ?>
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

	.interest-confirm {
        display: none;
    }
    .btn-confirm {
        padding: 1px 4px;
        border-radius: 50%;
    }

</style>
<script type="text/javascript">
//UserDetailContact-error
//id = UserDetailContact
/*
$('#UserDetailContact').keypress(function(event){

	if( !isNaN( $(this).val() ) ) {
		  event.preventDefault(); //stop character from entering input
		  $("#UserDetailContact-error").show('slow');
	} else {
		$("#UserDetailContact-error").hide('slow');
	}

});	 */


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

$(function() {

    $.checkInterestCount().done(function(response){
        if(response == false){
            $(".interest-wrapper").addClass('not_editable');
        }
    })
	$('.saveinterest').on('click', function (event) {
		event.stopImmediatePropagation();
		event.preventDefault();
		
		var $userinerest = $.trim($("#userinterest").val());
		$("#userinterest").val(''); 
		
		if( $userinerest.length > 0  ){
			
			var $interestid = '';
			if( $(".interest-container").find(".active").data("interestid") ){
				var $interestid = $(".interest-container").find(".active").data("interestid");
			}
			var save = false;
            $.checkInterestCount().done(function(status){
                if($interestid != ''){
                    save = true;
                }
                else if( status == true ) {
                    save = true;
                }
                if(save){

                    $("#userinterest").removeClass('interest-error');
        			$.ajax({
        				url: $js_config.base_url + 'users/save_interest',
        				type: "POST",
        				data: $.param({'interest-title':$userinerest, 'interest-id':$interestid}),
        				dataType: "JSON",
        				global: false,
        				success: function(response) {
        					$("#userinterest").val('');							 
							$(".closeinterest").addClass('hide');
                            $.showUserInterests();
                            $.checkInterestCount().done(function(response){
                                if(response == false){
                                    $(".interest-wrapper").addClass('not_editable');
                                }
                            })
        				}
        			})

                } else {
                    $(".interest-wrapper").addClass('not_editable');
                }
            })

		} else {
			$("#userinterest").addClass('interest-error');
		}
	});

	$( "body" ).delegate( ".interest-edit", "click", function() {
		event.preventDefault();
	
		$(this).parents('.interest-container').find('.interest-row').removeClass('active');
		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);
		
			$("#userinterest").removeClass("interest-error");
			var $btn_txt = $(this).parents('.interest-row:first');
			var $interestUpdateVal = $btn_txt.find(".interest-text").text();
			var $interestUpdateValId = $btn_txt.data(".interestid");
			
			$("#userinterest").val($interestUpdateVal);
			$btn_txt.addClass('active');
			$("#userinterest").parent().removeClass('not_editable');
			$(".closeinterest").removeClass('hide');

	});
	
	$( "body" ).delegate( ".closeinterest", "click", function() {
		event.preventDefault();
		
		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);
		
		var $btn_txt = $(".interest-container").find('.active');		
		$("#userinterest").val('');
		$btn_txt.removeClass('active');
		$(".closeinterest").addClass('hide');
		$("#userinterest").removeClass("interest-error");
		$.checkInterestCount().done(function(response){
			if(response == false){
				$(".interest-wrapper").addClass('not_editable');
			}
			else{
				$(".interest-wrapper").removeClass('not_editable');
			}			 
		}) 

	});
	
	$( "body" ).delegate( ".interest-delete", "click", function() {
		event.preventDefault();
		
		$(this).parents('.interest-container').find('.interest-row').removeClass('active');
		$(".closeinterest").addClass('hide');
		$("#userinterest").val('');
		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);
		 
		
		var $btn_grp = $(this).parents('.interest-row:first');
		$('.interest-confirm', $btn_grp).show('slide', {direction: 'right'}, 600);
		
		//$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm');
		
	});


	$( "body" ).delegate( ".confirm-no", "click", function() {
		event.preventDefault();
		var $btn_grp = $(this).parents('.interest-row:first');
		$('.interest-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600);
	});

	$( "body" ).delegate( ".confirm-yes", "click", function() {
		event.preventDefault();
		var $btn_grp = $(this).parents('.interest-row:first'),
		$list = $(this).parents('.interest-row:first'),
		data = $list.data(),
		interestid = data.interestid;

		$.ajax({
			url: $js_config.base_url + 'users/delete_user_interest',
			type: 'POST',
			dataType: 'json',
			data: {id: interestid},
			success: function(response) {
				$('.interest-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600, function(){
					$list.slideUp(300, function(){
						$(this).remove();
                        $.checkInterestCount().done(function(response){
                            if(response == false){
                                $(".interest-wrapper").addClass('not_editable');
                            }
                            else{
                                $(".interest-wrapper").removeClass('not_editable');
                            }
							if( $(".interest-row").length == 0 ){
								$html = '<div class="interest-row no-interest-record">No interests found</div>';
								$(".interest-container").html($html);
							}
                        })
					})
				});
			}
		})

	})

});

    ($.showUserInterests = function(){
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'users/user_interest_list',
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function(response) {
                    $(".interest-container").html(response);
                    dfd.resolve('done');
                }
            })
            return dfd.promise();
        })();

    $.checkInterestCount = function(){
    	var dfd = new $.Deferred();
        $.ajax({
    		url: $js_config.base_url + 'users/check_interest_count',
    		type: 'POST',
    		dataType: 'json',
    		data: {},
    		success: function(response) {
    			dfd.resolve(response.success);
    		}
    	})
        return dfd.promise();
    }

</script>
