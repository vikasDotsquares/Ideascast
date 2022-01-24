<script type="text/javascript">
	$(function(){

		/*$.program = $('#program').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'program_id[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search Program',
	        enableCaseInsensitiveFiltering: true,
	        enableUserIcon: false,
	        nonSelectedText: 'Select Program',
	    });*/
		$.programs = $('#programs').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'program_id[]',
	        enableFiltering: true,
			includeSelectAllOption:true,
	        filterPlaceholder: 'Search Program',
	        enableCaseInsensitiveFiltering: true,
	        enableUserIcon: false,
	        nonSelectedText: 'No Programs',
			onChange: function(option, checked, select) {
				// Get selected options.
				var selectedOptions = jQuery('#programs option:selected');

				if (selectedOptions.length > 111111111111149) {
					// Disable all other checkboxes.
					var nonSelectedOptions = jQuery('#programs option').filter(function() {
						return !jQuery(this).is(':selected');
					});

					nonSelectedOptions.each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', true);
						input.parent('li').addClass('disabled');
					});
				}
				else {
					// Enable all checkboxes.
					jQuery('#programs option').each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', false);
						input.parent('li').addClass('disabled');
					});
				}
			},

			onInitialized: function(option, checked, select) {
				// Get selected options.
				var selectedOptions = jQuery('#programs option:selected');

				if (selectedOptions.length > 111111111111149) {
					// Disable all other checkboxes.
					var nonSelectedOptions = jQuery('#programs option').filter(function() {
						return !jQuery(this).is(':selected');
					});

					nonSelectedOptions.each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', true);
						input.parent('li').addClass('disabled');
					});
				}
				else {
					// Enable all checkboxes.
					jQuery('#programs option').each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', false);
						input.parent('li').addClass('disabled');
					});
				}
			},
	    });
	})
</script>
<?php

	if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
		$this->request->data = $_SESSION['data'];
	}

	// echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
	// echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
	// echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true));

	// echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
	// echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));

	echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
	echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

	echo $this->Html->script('projects/manage_project', array('inline' => true));

	echo $this->Html->css('projects/manage_project');


	$user_id = $this->Session->read('Auth.User.id');
	$timeZone = getTimezoneDetail('Timezone',$user_id,'name');

	$timeZone  = 'Europe/London';

	if((!isset($timeZone) || empty($timeZone)) || ($timeZone ==  'Etc/Unknown')){
		$timeZone = 'Europe/London';
	}
	$projects_signoff = '';
	if( isset($this->request->data['Project']['sign_off']) && !empty($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 1 ){
		$projects_signoff = 'disabled';
	}
?>
<style>
	textarea {
		resize: vertical;
	}
	.no-selection {
		pointer-events: none;
		background-color: #ccc !important;
	}
	.closed {
		pointer-events: none;
	}
	.create-error-message{
		display:block;
		color:#dd4b39;
	}
	.input-group .error-msg {
	    color: red;
	}
	.offer-confirm {
		display: none;
	}
	.btn-confirm {
	    padding: 1px 4px;
	    border-radius: 50%;
	}
	.task-type-controls .btn {
	    float: left;
	    margin-left: 3px;
	}
	.task-type-controls  .btn-confirm.confirm-no {
	    font-size: 13px;
	    padding: 1px 6px;
	}
	.project_type .form-group .loader-icon {
		position: absolute;
		right: -18px;
		top: 59%;
		display: none;
	}
    .multiselect-container.dropdown-menu > li:not(.multiselect-group) {
       /* margin-bottom: -4px !important;*/
    }
    .multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
        padding: 5px 20px 5px 40px !important;
    }

	.project_type .form-group {
		position: relative;
	}

	.project_type .risktype {
		display: inline-block;
	}

	.project_type .addupdate {
		padding-left: 10px;
		color: green;
		font-weight: normal;
		display: inline-block;
		vertical-align: middle;
		margin-top: -5px;
	}

	.project_type .addupdate .nav-tabs {
		border-bottom: none;
	}

	.project_type .addupdate .nav-tabs li {
		border-right: 1px solid #67A028;
	}

	.project_type .addupdate .nav-tabs li a {
		margin-right: 2px;
		line-height: 1.42857143;
		border: none;
		border-radius: 0;
		padding: 0px 10px;
		cursor: pointer;
		color: #67A028;
	}

	.project_type .addupdate .nav-tabs li.active a, .project_type .addupdate .nav-tabs li.active a:focus, .project_type .addupdate .nav-tabs li.active a:hover, .project_type .addupdate .nav-tabs li a:hover {
		color: #aaa;
		background-color: #fff;
		border: none;
		border-bottom-color: transparent;
	}

	.project_type .addupdate .nav-tabs li:last-child {
		border-right: none;
	}

	.project_type .addupdate .nav-tabs li a {
		margin-right: 2px;
		line-height: 1.42857143;
		border: none;
		border-radius: 0;
		padding: 0px 10px;
		cursor: pointer;
		color: #67A028;
	}

	.project_type .form-group .tab-pane.active {
		display: block;
		visibility: visible;
	}

	.rm-type-dd {
		display: block;
		background-color: #fff;
		border: 1px solid #ddd;
		position: relative;
		cursor: pointer;
		-webkit-transition: all 0.5s linear;
		-ms-transition: all 0.5s linear;
		transition: all 0.5s linear;
	}

	.rm-type-dd.open {
		background-color: #e0e0e0;
	}

	.rm-type-dd::after {
		background-color: transparent;
		border-left: none;
		box-sizing: border-box;
		color: #b7b7b7;
		content: "";
		display: inline-block;
		font-family: "FontAwesome";
		height: 30px;
		margin-left: -17px;
		padding: 6px 4px 0 3px;
		pointer-events: none;
		position: absolute;
		right: 1px;
		text-align: center;
		top: 1px;
		vertical-align: middle;
		width: 23px;
		z-index: 2;
		-webkit-transition: all 0.5s;
		transition: all 0.5s;
	}

	.rm-type-dd.open::after {
		transform: rotate(180deg);
	}


	.rm-type-dd .selected-type {
		padding: 7px 15px;
		margin-bottom: -1px;
		display: inline-block;
	}

	.rm-type-dd .dd-data {
		display: none;
		position: absolute;
		width: 100%;
		top: 100%;
		left: 0;
		z-index: 9;
		max-height: 557px;
		overflow-x: hidden;
		overflow-y: auto;
	}

    .project_type .form-group .tab-pane {
        display: none;
        visibility: hidden;
    }

	.dd-data .list-group-item {
		font-size: 13px;
		padding: 1px;
		cursor: default;
		-webkit-transition: all 0.5s linear;
		-ms-transition: all 0.5s linear;
		transition: all 0.5s linear;
		display: inline-block;
		width: 100%;
	}

	.list-group-item:first-child {
		border-top-left-radius: 4px;
		border-top-right-radius: 4px;
	}
	.list-group-item {
		position: relative;
		display: block;
		padding: 10px 15px;
		margin-bottom: -1px;
		background-color: #fff;
		border: 1px solid #ddd;
	}

	.dd-data .list-group-item .rm-type-text {
		padding: 8px 15px;
		display: inline-block;
	}
	.dd-data .list-group-item .pull-right.controls {
		margin: 7px 9px 0 0;
	}

	.project-checkbox-status input[type="checkbox"]{
		opacity:9999 !important;
	}
	#UserProjectIsBoard{
		opacity:9999 !important;
	}
	.project-checkbox-status .checkbox, .radio {
		margin-bottom: 15px;
	}

	.create-edit-date-f .error-message {
		padding-left: 0 !important;
	}

    .select2-selection__choice__remove {
    		float: right !important;
    		padding: 0 0 0 5px;
    }
    .font-smaller{ font-size : 11px;}
    .select2-container{ width:100% !important;}

    #date_constraints_dates .input-daterange > .control-label.col-sm-2{ margin-top :5px;padding-left : 7%;}

 	@media (min-width:1280px) and (max-width:1400px) {
		.project_type .form-group .risk-types-input {
		    width: 88%;
		}

	}
    @media (min-width:992px) and (max-width:1199px) {
    	#date_constraints_dates .input-daterange > .control-label.col-sm-2{padding-left : 0%; text-align:right;}
    }
    @media (min-width:768px) and (max-width:991px) {
	    #date_constraints_dates .input-daterange > .control-label.col-sm-2 {
	      font-size: 13px;
	      padding: 0;
	      text-align: right;
	    }
    }
    @media (max-width:767px){
    	#date_constraints_dates .input-daterange > .control-label.col-sm-2 {
    		padding-left:15px;
    	}
    }
	@media (max-width:479px){
	    .project_type .form-group .form-control.risk-types-input {
			width: 76%;

		}
    }
    .not-editable {
        pointer-events: none;
    }
</style>
<script type="text/javascript" >
	$('window').load(function(){
		$( ".wysihtml5-sandbox" ).resizable();

	})
    $(function () {


        var $triggered = $('.rm-type-dd');
        var $list = $triggered.find('ul.dd-data');
        $('.rm-type-dd').data('list', $list);
        $list.data('triggered', $triggered);
        $(document).on('click', function(e) {
            $('.rm-type-dd').each(function() {
                var $list = $(this).find('ul.dd-data');
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $list.has(e.target).length === 0) {
                    var $list = $(this).data('list');
                }
            });
        });

		$('body').delegate('#txa_title', 'keyup focus', function(event){
            var characters = 50;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })


		$('body').delegate('#txa_objective', 'keyup focus', function(event){
            var characters = 500;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })


        $('body').delegate('#txa_description', 'keyup focus', function(event){
            var characters = 500;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

    })

    $(document).ready(function () {

		/*$('#UserProjectIsBoard').change(function () {
			if ($(this).prop("checked")) {
				$("#skillbox").slideDown(500)
			} else {
				$("#skillbox").slideUp(500)
			}
		});*/

		/* To allow editor open above lines */

        var date_picker = {
            showButtonPanel: false,
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            closeText: 'Close',
            currentText: 'Today',
            dateFormat: 'dd M yy',
            gotoCurrent: false,
            hideIfNoPrevNext: true,
            prevText: "Prev",
            nextText: "Next",
            showAnim: "fade",

        }


        $("#start_date").datepicker($.extend(date_picker, {
            onClose: function (selectedDate) {
                var startDate = $(this).datepicker('getDate');
                var endDate = $("#end_date").datepicker('getDate');

              //  $("#end_date").datepicker("setDate", startDate);
                $('#end_date').datepicker('option', 'minDate', startDate);

                if (($("#end_date").datepicker('getDate') == null || startDate > endDate) && startDate != null) {


                  if(new Date() > startDate){
					  $('#end_date').datepicker('option', 'minDate', 0);
				  }else{
                  //  $("#end_date").datepicker("setDate", startDate);
                    $('#end_date').datepicker('option', 'minDate', startDate);
				  }
                }

				if (( new Date() > startDate) && startDate != null) {


					  $('#end_date').datepicker('option', 'minDate', 0);

                }



            }
        })
      )

	$("#end_date").datepicker(
		$.extend(date_picker, {
			onSelect: function (selectedDate) {
				$('#end_date').datepicker('option', 'minDate', 0);
			}
		})
    );


    <?php

    $dates = false;

    if (isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) {
        $dates = true;

		$this->request->data['Project']['start_date'] = date("Y-m-d",strtotime($this->request->data['Project']['start_date']));

		$this->request->data['Project']['end_date'] = date("Y-m-d",strtotime($this->request->data['Project']['end_date']));

		$projectStartDate = date("d M Y",strtotime($this->request->data['Project']['start_date']));
		$projectEndDate = date("d M Y",strtotime($this->request->data['Project']['end_date']));
        ?>

            sdate = new Date("<?php echo $projectStartDate; ?>");

			 sdate = new Date(sdate);

             curdate = new Date("<?php echo date('Y/m/d'); ?>");
			 curdate = new Date(curdate);

            $("#start_date").datepicker("setDate", sdate);
            if (curdate < sdate) {
                $('#start_date').datepicker('option', 'minDate', curdate);
            } else {
                $('#start_date').datepicker('option', 'minDate', sdate);
                $("#start_date").datepicker("setDate", sdate);
            }


			/*According to client 13 feb 2018*/

			<?php
			if (isset($this->request->data['Project']['created']) && !empty($this->request->data['Project']['created'])) {
			?>

			 CCdate = new Date("<?php echo date('r', strtotime(date('Y-m-d',$this->request->data['Project']['created']))); ?>");
			 CCdate = new Date(CCdate);

			 var CSdate = new Date("<?php echo $projectStartDate; ?>");
			 CSdate = new Date(CSdate);

			 if (CCdate <= curdate) {
				 CCdate = new Date("<?php echo date('r', strtotime("-1 month" , $this->request->data['Project']['created'])); ?>");
				 CCdate = new Date(CCdate);
			 }

			 if ( CSdate < curdate ){
				CCdate =  CSdate;
			 }

			 $('#start_date').datepicker('option', 'minDate', CCdate);
             $("#start_date").datepicker("setDate", sdate);

			<?php }
			?>

			/*According to client 13 feb 2018*/

           // $('#end_date').datepicker('option', 'minDate', 0);
            $("#el_dc_yes").trigger('change')

    <?php
    } else {
        ?>
    	curdate = new Date("<?php echo date('r', strtotime("-1 month" , strtotime(date('Y-m-d')))); ?>");
		curdate = new Date(curdate);

		$("#start_date").datepicker("option", "minDate",curdate);
        <?php
    }

    if ( isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']) && $this->request->data['Project']['end_date'] != '1970-01-01') {
        $dates = true;
        ?>
            edate = new Date("<?php echo $projectEndDate; ?>");
            edate = new Date(edate);
            $("#end_date").datepicker("setDate", edate);

            $("#el_dc_yes").trigger('change')

    <?php } else { ?>
		$("#end_date").datepicker("option", "minDate", 0);
    <?php } ?>

        $('.open-start-date-picker').on('click', function (event) {
            event.preventDefault();
            $("#start_date").datepicker('show').focus();
        });

        $('.open-end-date-picker').on('click', function (event) {
            event.preventDefault();
            $("#end_date").datepicker('show').focus();
        });
    })
</script>

<?php
	$is_owner = false;
	if (isset($project_id)) {
	    $is_owner = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
	    $original_owner = $this->Common->userprojectOwner($project_id, $this->Session->read('Auth.User.id'));
	    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
	}



?>

<div class="row">
    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">

                <h1 class="pull-left"><?php echo (isset($project_id)) ? htmlspecialchars($this->request->data['Project']['title'],ENT_QUOTES, "UTF-8") : $page_heading; ?>
<?php if (isset($project_id)) { ?>
                        <p class="text-muted date-time">Project:
                            <span>Created: <?php
							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$created),$format = 'd M Y h:i:s'); ?></span>
                            <span>Updated: <?php
							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($modified)),$format = 'd M Y h:i:s'); ?></span>
                        </p>
<?php } ?>
                </h1>

                <a id="btn_go_back" class="btn btn-warning tipText pull-right btn-sm"  href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'lists', 'admin' => FALSE), TRUE); ?>" type="submit"><i class="fa fa-fw fa-chevron-left"></i> Back</a>

            </section>

        </div>


        <div class="box-content">
			<?php echo $this->Session->flash(); ?>
            <div class="row ">
                <div class="col-xs-12">
            <?php
            echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'manage_project_new'), 'role' => 'form', 'id' => 'frm_manage_project', 'class' => 'clearfix'));
            ?>
                    <div class="fliter margin-top" style="padding :15px; margin:  0;  border-top-left-radius: 3px;    background-color: #f5f5f5;  text-align:right;   border: 1px solid #ddd;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd">
                        <button class="btn btn-sm btn-success <?php echo $projects_signoff;?>" type="submit"><?php echo $text_val; ?></button>
                        <a class="btn btn-sm btn-danger" href="<?php echo (isset($project_id) && !empty($project_id)) ? Router::Url(array('controller' => 'projects', 'action' => 'index', $project_id, 'admin' => FALSE), TRUE) : Router::Url(array('controller' => 'projects', 'action' => 'lists', 'admin' => FALSE), TRUE); ?>" type="submit">Cancel</a>
                    </div>
                    <div class="box noborder ">
                        <div class="box-header nopadding">

                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- END MODAL BOX -->

                        </div>

                        <div class="box-body border-top clearfix popup-select-icon">

                            <?php echo $this->Form->input('UserProject.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => '')); ?>
                            <?php echo $this->Form->input('Project.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => '')); ?>

                            <div class="form-group col-md-6 col-lg-7 createproject-col-one">

                                <label for="ProjectProgramId" class=" text-right">My Programs (optional):</label>
                                <?php

								echo $this->Form->input('programs', array(
                                        'options' => $my_programs,
                                        'class' => 'form-control',
										'multiple'=>true,
                                        'id' => 'programs',
                                        'label' => false,
                                        'div' => false,
										'selected' => $project_programs,
										'style'=>'height:30px;display:none;'
                                    ));
								  ?>

                                <span class="error-message text-danger" ></span>


                            </div>


                            <div class="form-group col-md-3 col-lg-3 createproject-col-two">

                                <label for="ProjectCategoryId" class=" ">Project Type:</label>

								<?php
								echo $this->Form->input('Project.aligned_id', array(
									'options' => $aligneds,
									'empty' => 'Select Type',
									'type' => 'select',
									'required' => false,
									'label' => false,
									'div' => false,
									'class' => 'form-control'
								));
								?>
                                <span class="error-message text-danger" ></span>


                            </div>

							<div id="currencybox" class="form-group col-md-3 col-lg-2 createproject-col-two">
								<label for="ProjectCurrencyIds" class=" ">Currency:</label>

								<?php
								$currencylist =  array_map('utf8_decode', $this->Common->getcurrencybyid());
								echo $this->Form->input('Project.currency_id', array(
									'options' => $currencylist,
									'default' => 12,
									'empty' => 'Select Currency',
									'type' => 'select',
									'required' => false,
									'label' => false,
									'div' => false,
									'class' => 'form-control'
								));
								?>
								<span class="error-message text-danger" ></span>
							</div>

							<!-- Start Project Type -->

							<div class="col-sm-6 project_type">
								<div class="form-group">
									<label for="RiskType">Task Types in Project: </label>
									<select class="form-control project_types" style="height:30px;display:none;"  id="project_types" multiple="multiple" name="data[ProjectElementType][id][]">
									<?php
										if( isset($project_id) && !empty($project_id) ){

											if(isset($projectType) && !empty($projectType)) {
												foreach($projectType as $k => $v){
													$selected = $data_opt = '';
													if(in_array($k, array_values($projectElementTypeSelected)) ){
														$selected = 'selected="selected"';
														$usedType = custom_type_involved($k,$project_id);
														if(!empty($usedType)){
														$selected = 'selected="selected" disabled';
														}

													}
													if($v == 'General') {
														$data_opt = 'data-disabled="1" ';
													}
									?>
									<option value="<?php echo $k; ?>"  <?php echo $selected; ?> <?php echo $data_opt; ?> ><?php echo htmlentities($v); ?></option>
									<?php
											}
										}

									} else {
										if(isset($projectType) && !empty($projectType)) {
											$atype = [];
											if(isset($projectElementTypeSelected) && !empty($projectElementTypeSelected)){
												$atype = array_values($projectElementTypeSelected);
											}
											foreach($projectType as $k => $v){

												$selected = $data_opt = '';
												if(in_array($v['ProjectElementTypeTemp']['id'], $atype) ){
													$selected = 'selected="selected"';
												}

												if($v == 'General') {
													$data_opt = 'data-disabled="1" ';
												}

												?>
													<option value="<?php echo $v['ProjectElementTypeTemp']['id']; ?>" <?php echo $selected;?> <?php echo $data_opt; ?> ><?php echo htmlentities($v['ProjectElementTypeTemp']['title']); ?></option>
										<?php
												}
											}
									}
									?>
									</select>
									<span class="loader-icon fa fa-spinner fa-pulse"></span>
									<?php
									if(isset($this->validationErrors['ProjectElementType']) && !empty($this->validationErrors['ProjectElementType'])){ ?>
										<span class="error-message error text-danger"><?php echo $this->validationErrors['ProjectElementType']['id']; ?></span>
									<?php } else if (isset($this->request->data['error']['eletasktype']) && !empty($this->request->data['error']['eletasktype'])){ ?>
										<span class="error-message error text-danger"><?php echo $this->request->data['error']['eletasktype']; ?></span>
									<?php } ?>

								</div>
							</div>
							<div class="col-sm-6 project_type">
								<div class="form-group">
									<label for="RiskTypes"><span class="risktype">Manage Task Types: </span>
										 <span class="addupdate">
											<ul class="nav nav-tabs">
												<li class="active">
													<a class="add-link" href="#add_types" data-toggle="tab" aria-expanded="false">Create</a></li>
												<li><a  href="#update_types" class="open_update_type " data-toggle="tab" lister="lister" aria-expanded="false">Update</a></li>
											</ul>
										 <?php //} ?>
										</span>
									</label>

									<div id="add_types" class="tab-pane fade active in">
										<input type="text" class="form-control risk-types-input" id="RiskTypes" maxlength="50" placeholder="max chars allowed 50" >
										<span class="iocn-plus ">
											<i class="fa fa-plus ico-types ico-add-type tipText" title="Add"></i>
											<i class="fa fa-close ico-types ico-clear-type tipText" title="Clear"></i>
										</span>
										<?php //} ?>
										<span class="error-message error text-danger" style="font-size: 11px;"></span>
										<span class="create-error-message error text-danger" style="font-size: 11px;"></span>

									</div>
									<div id="update_types" class="tab-pane fade">
										<div class="rm-type-dd">
											<span class="selected-type">No Task Type found</span>
										</div>
									</div>
									<span class="loader-icon fa fa-spinner fa-pulse" style="disable:block;"></span>
								</div>
							</div>

							<!-- End Project Type -->

                            <div class="form-group col-sm-12">

                                <label for="title" class=" ">Project Title:</label>

                        <?php
						echo $this->Form->input('Project.title', ['type'=> 'text', 'class' => 'form-control', 'id' => 'txa_title', 'required' => false, 'placeholder' => 'Project title provides understanding about the main goal of the project work and deliverables (max chars 50)', 'label' => false, 'div' => false]); ?>
                                <span class="error-message error text-danger" ></span>
                        <?php //echo $this->Form->error('Project.title'); ?>


                            </div>

                            <div class="form-group col-sm-12">

                                <label for="title" class=" ">Project Outcome:</label>

                                    <?php echo $this->Form->textarea('Project.objective', [ 'class' => 'form-control', 'id' => 'txa_objective', 'required' => false, 'escape' => true, 'rows' => 3, 'placeholder' => 'Project outcome represents the value delivered by the team (max chars 500)']); ?>
                                 <span class="error-message error text-danger" ></span>
                                    <?php echo $this->Form->error('Project.objective'); ?>


                            </div>

                            <div class="form-group col-sm-12 Description">

                                <label for="title" class=" ">Description:</label>


                        <?php
						$projectdesc = '';
						if( isset( $this->request->data['Project']['description'] ) && !empty($this->request->data['Project']['description']) ){
							$projectdesc =   $this->request->data['Project']['description'];
						}


						echo $this->Form->textarea('Project.description', [ 'class' => 'form-control', 'id' => 'txa_description', 'required' => false, 'escape' => true, 'rows' => 10, 'placeholder' => 'Project description outlines the concept and context of the project (max chars 500)', 'value' => $projectdesc ] ); ?>
                                                        <span class="error-message error text-danger"></span>
                        <?php echo $this->Form->error('Project.description'); ?>


                            </div>
                                <?php $dsss = ''; ?>
                            <div class="form-group col-sm-12 clearfix">
                                <label class="nopadding " style="display: inline-block;float: left;margin: 7px 10px 0 0;position: none;">Color Theme:</label>
                                <div class="col-md-5 nopadding">
                                    <div class="nopadding-left color-theme-but">
                            <?php echo $this->Form->input('Project.color_code', [ 'type' => 'hidden', 'id' => 'color_code']); ?>

                                        <div class="form-control noborder" >
                                            <a href="#" data-color="panel-red" data-preview-color="bg-red" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-red"></i></a>
                                            <a href="#" data-color="panel-blue" data-preview-color="bg-blue" class="btn btn-default <?php echo $projects_signoff; ?>  btn-xs el_color_box"><i class="fa fa-square text-blue"></i></a>
                                            <a href="#" data-color="panel-maroon" data-preview-color="bg-maroon" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-maroon"></i></a>
                                            <a href="#" data-color="panel-aqua" data-preview-color="bg-aqua" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-aqua"></i></a>
                                            <a href="#" data-color="panel-yellow" data-preview-color="bg-yellow" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-yellow"></i></a>
                                            <a href="#" data-color="panel-green" data-preview-color="bg-green" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-green"></i></a>
                                            <a href="#" data-color="panel-teal" data-preview-color="bg-teal" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-teal"></i></a>
                                            <a href="#" data-color="panel-purple" data-preview-color="bg-purple" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-purple"></i></a>
                                            <a href="#" data-color="panel-navy" data-preview-color="bg-navy" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-navy"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 preview" style="text-align: center; display: none;">
                                    <span style="margin-top: 8px; display: none;">Color preview</span>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 project-checkbox-status">


                                <?php // PASSWORD DELETE
                                $pass_cl = 'not-editable';
                                    if($is_owner) {
                                        $pass_cl = '';
                                    }
                                    if(!isset($project_id) || empty($project_id)) {
                                        $pass_cl = '';
                                    }
                                     ?>
								<div class="project-check-box <?php echo $pass_cl; ?>">

                                    <label for="UserProjectPassProtected" class=" ">Password to Delete:</label>
										<?php
										echo $this->Form->input('UserProject.pass_protected', array(
											'type' => 'checkbox',
											'format' => array('before', 'input', 'between', 'after', 'error')
										));
										?>
										<span class="error-message text-danger" ></span>
										<?php echo $this->Form->error('UserProject.pass_protected'); ?>
                                </div>

                                <?php $cl = '';
                                    if(isset($project_id) && !empty($project_id)) {
                                        if(is_reward_activity($project_id)) {
                                            $cl = 'not-editable';
                                        }
                                    } ?>
								<div class="project-check-box <?php echo $cl; ?>">

                                    <label for="UserProjectIsRewards" class=" ">Rewards:</label>

                                    <?php
                                    echo $this->Form->input('UserProject.is_rewards', array(
                                        'type' => 'checkbox',
                                        'format' => array('before', 'input', 'between', 'after', 'error')
                                    ));
									if(!empty($cl)){
                                    ?>
                                    <span class="font-smaller " >Cannot change because there are reward activities.</span>
									<?php } ?>
                                    <span class="error-message text-danger" ></span>
                                    <?php echo $this->Form->error('UserProjects.is_rewards'); ?>


                                </div>
                            </div>

                            <div class="col-sm-12 clearfix">

                                <div class="form-group clearfix" id="date_constraints_dates" >

                                    <div class="form-group input-daterange">
										<div class="date-row row">
                                            <div class="col-sm-6  create-edit-date-f">
    											<label class="control-label" for="start_date">Start date:</label>
                                                <div class="input-group start_dat">
                                                    <?php

													echo $this->Form->input('Project.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false, 'readonly' => 'readonly', $projects_signoff, 'class' => 'form-control dates input-small']); ?>
                                                    <div class="input-group-addon   <?php if( empty($projects_signoff) ){?>  open-start-date-picker calendar-trigger<?php } ?>">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                                <?php //echo $this->Form->error('Project.start_date', null, array('class' => 'error-message')); ?>
                                            </div>
                                            <div class="col-sm-6  create-edit-date-f">
    											 <label class="control-label" for="end_date">End date:</label>
                                                <div class="input-group start_dat">
                                                    <?php
													echo $this->Form->input('Project.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false, 'readonly' => 'readonly', $projects_signoff, 'class' => 'form-control dates input-small']); ?>
                                                    <div class="input-group-addon  <?php if( empty($projects_signoff) ){?>  open-end-date-picker calendar-trigger<?php } ?>">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>

                                                </div>
                                                <?php //echo $this->Form->error('Project.end_date', null, array('class' => 'error-message')); ?>
                                            </div>
                                        </div>
										<div class="date-row row">
                                            <div class="col-sm-6  create-edit-date-f">
    											<label class="control-label" for="start_date">&nbsp;</label>
                                                <div class="input-group ">
                                                     <?php echo $this->Form->error('Project.start_date', null, array('class' => 'error-message')); ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6  create-edit-date-f">
    											 <label class="control-label" for="end_date">&nbsp;</label>
                                                <div class="input-group">
                                                    <?php echo $this->Form->error('Project.end_date', null, array('class' => 'error-message')); ?>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>

							<div class="form-group col-sm-12 clearfix rag_tabs allpopuptabs">
    							<?php
    							if(isset($project_id) && !empty($project_id)){
    							$ragCLS = $this->Common->getRAG($project_id);
    							?>
    							<input type="hidden" value="<?php echo $ragCLS['rag_color']; ?>" name="email_rag">
    							<input type="hidden" value="<?php echo $this->request->data['Project']['rag_status']; ?>" name="email_rag_type">
    							<?php } ?>
    								<ul class="nav nav-tabs" style="cursor: move; display: inline-block;">
										<li class="active"><a data-toggle="tab" class="active" href="#rag_st">RAG Manual</a></li>
    									<li class="">
    										<a data-toggle="tab" href="#rag_rules" class="disableLinks">RAG Rules</a>
    									</li>
    								</ul>
    								<div class="tab-content" id="myTabContent">
    									<div id="rag_st" class="tab-pane fade active in">
    										<label class="nopadding " style="display: inline-block;float: left;margin: 7px 10px 0 0; position: none;">Select:</label>
    										<div class="nopadding-left" id="rag_holder" style="margin: 7px 0 -6px;">
    											<?php
    											if(isset($this->request->data['Project']['rag_status']) && !empty($this->request->data['Project']['rag_status']) ){
    												echo $this->Form->input('Project.rag_status', [ 'type' => 'hidden', 'id' => 'rag_status' ]);

    											}else{
    												echo $this->Form->input('Project.rag_status', [ 'type' => 'hidden', 'id' => 'rag_status','value'=>3]);
    											}
    											// pr($this->request->data['Project']);
    											?>
    											<a href="javascript:;" class="link_chk red <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 1) ? 'checked' : ''; ?>" data-value="1"> </a>
    											<a href="javascript:;" class="link_chk amber <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 2) ? 'checked' : ''; ?>" data-value="2"> </a>
    											<a href="javascript:;" class="link_chk green <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 3) ? 'checked' : (empty($this->request->data['Project']['rag_status']) ?  'checked' : '' ); ?>" data-value="3"> </a>
    										</div>
    									</div>

    									<div id="rag_rules" class="tab-pane fade">
    										<div class=" input-rag">
    											<div class="col-first">
    												<div class="cols">Select</div>
    												<?php
    												//pr($this->request->data['ProjectRag']);
    												$disableInputAmber = 'disableInput';
    												$amberChecked = "";
    												$redChecked = "";
    												$AmberData = 0;
    												$RedData = 0;

    												$ambrr = (isset($this->request->data['ProjectRag']['amber_value']) && !empty($this->request->data['ProjectRag']['amber_value'])) ? trim($this->request->data['ProjectRag']['amber_value']) : '';

    												$redrr = (isset($this->request->data['ProjectRag']['red_value']) && !empty($this->request->data['ProjectRag']['red_value'])) ? trim($this->request->data['ProjectRag']['red_value']) : '';

    												if(isset($ambrr) && !empty($ambrr)) {
    													$disableInputAmber = '';
    													$amberChecked = "checked";
    													$AmberData = 1;
    													$Amberattr = '';
    												}else{
    													$disableInputAmber = 'disableInput';
    													$amberChecked = "";
    													$AmberData = 0;
    													$Amberattr = 'disabled';
    												}
    												?>
    												<div class="cols">
                                                        <input type="hidden" name="data[ProjectRag][amber_check]" value="<?php echo $AmberData; ?>" />
    													<a href="javascript:;" class="link_radio amber <?php echo $amberChecked; ?>" data-value="<?php echo $AmberData; ?>"> </a>
    												</div>
    												<div class="cols">
                                                        <?php echo $this->Form->input('ProjectRag.amber_value', [ 'type' => 'text', 'class' => 'rag_input amber_input '.$disableInputAmber, 'label' => false, 'autocomplete' => 'off', $Amberattr]); ?>
    												</div>
    												<div class="cols">% Overdue Tasks <span  style="" class="amber-clear tipText iocn-plus" title="Clear"><i class="fa fa-close ico-types ico-clear-type " ></i></span></div>
    											</div>

    											<div class="col-second">
    												<div class="cols cols-next">Select</div>
    												<?php
    												$disableInputRed = 'disableInput';
    												if(isset($redrr) && !empty($redrr)){
    													$disableInputRed = '';
    													$redChecked = "checked";
    													$RedData = 1;
    													$Redattr = '';
    												}else{
    													$disableInputRed = 'disableInput';
    													$redChecked = "";
    													$RedData = 0;
    													$Redattr = 'disabled';
    												}
    												?>
    												<div class="cols">
    													<input type="hidden" name="data[ProjectRag][red_check]" value="<?php echo $RedData; ?>" />
    													<a href="javascript:;" class="link_radio red <?php echo $redChecked; ?>" data-value="<?php echo $RedData; ?>"> </a>
    												</div>

    												<div class="cols">
                                                        <?php echo $this->Form->input('ProjectRag.red_value', [ 'type' => 'text', 'class' => 'rag_input red_input '.$disableInputRed, 'label' => false, 'autocomplete' => 'off', $Redattr]); ?>
    												</div>
    												<div class="cols">% Overdue Tasks <span style="" class="iocn-plus red-clear tipText" title="Clear"><i class="fa fa-close ico-types ico-clear-type " ></i></span></div>
                                                </div>
                                                <div class="col-third"></div>

    										</div>
    									</div>
								</div>

							</div>
                            <div class="form-group col-sm-12 clearfix rag_tabs">
                                <button class="btn btn-sm btn-success <?php echo $projects_signoff; ?>" type="submit"><?php echo $text_val; ?></button>
                                <a class="btn btn-sm btn-danger" href="<?php echo (isset($project_id) && !empty($project_id)) ? Router::Url(array('controller' => 'projects', 'action' => 'index', $project_id, 'admin' => FALSE), TRUE) : Router::Url(array('controller' => 'projects', 'action' => 'lists', 'admin' => FALSE), TRUE); ?>" type="submit">Cancel</a>
                            </div>


                        </div>
                    </div>
<?php echo $this->Form->end(); ?>


<div id="activities">
	<div class="loader"></div>
	<div style="display: block; text-align: center;">Loading Activities</div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$( function() {

	$.ajax({
		url: $js_config.base_url + 'projects/get_activities',
		data: $.param({ project_id: $js_config.project_id }),
		type: 'post',
		dataType: 'json',
		success: function(response){
			$('#activities').html(response)
		}
	})

	var characters = 50;
    $(".risk-types-input1").keyup(function() {
        var $ts = $(this)
        if ($(this).val().length > characters) {
            $(this).val($(this).val().substr(0, characters));
        }

		$ts.parents(".form-group").find('.create-error-message').hide();

        var remaining = characters - $(this).val().length;
		$(this).parents('.project_type').find('.error-message').show();

        $(this).parents('.project_type').find('.error-message').html("Chars 50 , <strong>" + $(this).val().length + "</strong> characters entered.");

		if ( remaining == 50  ) {
			$(this).parents('.project_type').find('.error-message').html();
			$(this).parents('.project_type').find('.error-message').hide();
        }

    });

	$('body').delegate('input[name="rm_types"]', 'keyup', function(event) {
        var $ts = $(this)
        if ($(this).val().length > characters) {
            $(this).val($(this).val().substr(0, characters));
        }

		$ts.parents(".form-group").find('.create-error-message').hide();

        var remaining = characters - $(this).val().length;
		$(this).parents('.project_type').find('.error-message').show();

        $(this).parents('.project_type').find('.error-message').html("Chars 50 , <strong>" + $(this).val().length + "</strong> characters entered.");

		if ( remaining == 50  ) {
			$(this).parents('.project_type').find('.error-message').html();
			$(this).parents('.project_type').find('.error-message').hide();
        }

    });

    $('body').delegate('.risk-types-input', 'keyup focus', function(event){
        var characters = 50;

        event.preventDefault();
        var $error_el = $(this).parents('.project_type').find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

    $('body').delegate('input[name="rm_type"]', 'keyup focus', function(event){
        var characters = 50;

        event.preventDefault();
        var $error_el = $(this).parents('.project_type').find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

})
</script>
<style>
    .wysihtml5-toolbar li {
        margin: 0 5px 1px 0 !important;
    }
    #cke_1_contents {
        border: 1px solid #cccccc !important;
    }
    #cke_2_contents, #cke_3_contents {
        border: 0px solid #cccccc !important;
    }
    .cke_top{
        border: none !important;
        padding: 2px 2px 0 2px !important;
        white-space:normal;
        -moz-box-shadow:0 1px 0 #fff inset;
        -webkit-box-shadow:0 1px 0 #fff inset;
        box-shadow:0 1px 0 #fff inset;
        background: none !important;
        background-image: -webkit-gradient(linear,left top,left bottom,from(#e9e9e9),to(#eeeeee)) !important;
        background-image:-moz-linear-gradient(top,#e9e9e9,#eeeeee) !important;
        background-image:-webkit-linear-gradient(top,#e9e9e9,#eeeeee) !important;
        background-image:-o-linear-gradient(top,#e9e9e9,#eeeeee) !important;
        background-image:-ms-linear-gradient(top,#e9e9e9,#eeeeee) !important;
        background-image:linear-gradient(top,#e9e9e9,#eeeeee) !important;
        filter:progid:DXImageTransform.Microsoft.gradient(gradientType=0,startColorstr='#e9e9e9',endColorstr='#eeeeee') !important;
    }
    .cke_toolgroup {
        border: none !important;
        margin: 0 1px 0 0 !important;
    }
    .cke_1.cke.cke_reset.cke_chrome.cke_editor_txa_title.cke_ltr.cke_browser_gecko  {
        border:  0px solid #cccccc !important;
    }
    .cke_toolgroup  {
        background: transparent !important;
    }
    .cke_bottom  {
        background: transparent !important;
        border-top:  1px solid #cccccc !important;
    }

    .cke_button_off {
        background: -moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc;
        box-shadow: 0 0 1px rgba(0, 0, 0, 0.3) inset;
        margin: 1px !important;
    }
    .cke_button_off:hover {
        background: none !important;
    }

    .cke_button_on {
        background: -moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.7) inset, 0 1px 0 rgba(0, 0, 0, 0.2);
        margin: 1px !important;
    }
    .cke_button_on:hover {
        background:-moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc !important;
    }

    .start_dat .error-message{ display:none;}
    /* cke_editable cke_editable_themed cke_contents_ltr */


</style>
<?php //echo $this->Html->script('jquery.tokeninput', array('inline' => true)); ?>
<script type="text/javascript">
	$(function(){
		$('body').delegate('.cancel_assignment', 'click', function(event) {
			event.preventDefault();
			$(this).parents('.list-group-item').removeClass('clicked');
		})
		$('body').delegate('.assing_element', 'click', function(event) {
			event.preventDefault();
			$that = $(this);
			var projectid = $that.parent().find('select').data('projectid');
			var eletypeid = $that.parent().find('select').data('eletypeid');
			var seltypeid = $that.parent().find('select').val();

			if( projectid !== "" &&  eletypeid !== "" &&  seltypeid !== ""  ){

				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					data: $.param({project_id : projectid, typeid : seltypeid, exitstype : eletypeid}),
					url: $js_config.base_url + 'projects/switch_element_type/',
					global: true,
					success: function(response) {
						if( response.success ) {
							 $.default_project_types(projectid).done(function() {
                                $.custom_project_type(projectid).done(function() {

                                });
                            });
						}
					}
				});
			}
		});

		$('body').delegate('.typereassign', 'click', function(event) {
			event.preventDefault();

			var $that = $(this);
			var typeid = $that.data('typeid');
			var project_id = $that.data('project_id');
			var $ul = $that.parents(".list-group");
			var $li = $that.parents(".list-group-item:first");
			var $listdd = $that.parents(".list-group-item").find('.elementtypelist');
			var $listddnot = $that.parents(".list-group").find('li.list-group-item').not($that.next()).find('.elementtypelist');

			var type_length = $js_config.project_task_types;

			$ul.find('.list-group-item').not($li).removeClass('clicked');
			if(type_length > 1)
				$li.toggleClass('clicked');
			if( $li.hasClass('clicked') ){

				if( typeid !== "" &&  project_id !== "" ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({project_id:project_id,typeid:typeid}),
						url: $js_config.base_url + 'projects/project_element_type/',
						global: false,
						success: function(response_data) {

                            if( response_data.success ) {

                                if( response_data.content != null ) {
								$listdd.find('.element_typeid').html('');
									var mySelect = $listdd.find('.element_typeid');
									mySelect.append($('<option></option>').val('').html('Select Task Type'));
                                    $.each( response_data.content, function( key, val ) {
										mySelect.append(
											$('<option></option>').val(key).html(val)
										);
                                    });
                                }
                            }
						}
					});
				}
			}
		})

		$('#project_types').multiselect({
			enableUserIcon: false,
			multiple: true,
			enableHTML: true,
			enableFiltering: true,
			includeSelectAllOption: true,
			nonSelectedText: 'Select Task Type',
			numberDisplayed: 2,
			filterPlaceholder: 'Search Task Type',
			enableCaseInsensitiveFiltering: true,
			buttonWidth: '100%',
			maxHeight: 543,
		})
		$('body').delegate('.link_chk', 'click', function(event) {
			event.preventDefault();
			$('.link_chk').removeClass('checked');
			$(this).addClass('checked');

			var $input = $(this).parent("#rag_holder").find('input'),
				status = $(this).data('value');

			$input.val(status);

            if($('.link_chk.green.checked').length <= 0) {
            }
            else {
                 $('a[href="#rag_rules"]').removeClass('disableLink');
            }
		})


		var lastX = 0;
		var currentX = 0;
		var page = 1;
		$('.scrolling-history').scroll(function () {
			currentX = $(this).scrollTop();

			if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 100)) {
				if ((currentX - lastX) > (30 * page)) {
					lastX = currentX;
					page++;
					$.post( $js_config.base_url+'projects/more_history/'+$js_config.project_id+'/page:' + page, {project_id: $js_config.project_id}, function(data) {
						$('.hmgo').append(data);
					});
				}
			}
		});
	})
    $(document).ready(function () {

		$('body').delegate('.activity_wrapper', 'click', function(event){
			event.preventDefault();
			var $that = $(this),
				project_id = $that.data('id');

			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: $.param({}),
				url: $js_config.base_url + 'projects/project_activities/' + project_id,
				global: true,
				success: function(response) {
					$that.html(response);
				}
			});
		})





        $.setSelectedColorClass = function () {
            var previewClass = ($.trim($('input#color_code').val()) != '') ? $('input#color_code').val() : 'bg-jeera'

            var splited = previewClass.split('-'),
                    previewText = 'Color preview';

            if (splited[1] != '') {
                previewClass = 'panel-' + splited[1];

                previewText = splited[1];
            }

            $(".preview span").removeAttr('class')
                    .attr('class', previewClass)
                    .text(previewText)

            $("a[data-color=" + previewClass + "]").find('i').removeClass('fa-square').addClass('fa-check')
        }
        $.setSelectedColorClass();


        $(".el_color_box").on('click', function (event) {
            event.preventDefault();

            $.each($('.el_color_box'), function (i, el) {
                $(el).find('i').addClass('fa-square').removeClass('fa-check')
            })

            var $cb = $(this)
            $cb.find('i').addClass('fa-check').removeClass('fa-square')

            var $frm = $cb.closest('form#frm_manage_project')
            var $hd = $frm.find('input#color_code')
            var cls = $hd.val()
            // console.log($frm)
            // console.log(cls)
            var foundClass = (cls.match(/(^|\s)bg-\S+/g) || []).join('')
            if (foundClass != '') {
                $hd.val('')
            }

            var applyClass = $cb.data('color')

            var splited = applyClass.split('-'),
                    previewClass = 'bg-jeera',
                    previewText = 'Color preview';


            if (splited[1] != '') {
                previewClass = 'bg-' + splited[1];
                previewText = $.ucwords(splited[1]);
            }

            $(".preview span").removeAttr('class')
                    .attr('class', previewClass)
                    .text(previewText)

            $hd.val(applyClass);
        })


    });
</script>

<div class="modal fade" id="confirm-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header">

            </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn-sm btn_progress_wrapper" id="sign_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="sign_off_no" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>

<div class="modal modal-danger fade" id="signoff_comment_box" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<style>
	.list-group-item .elementtypelist {
		display:none;
		margin-bottom: 5px;
		padding: 5px 5px 5px 14px;
		transition: all 1s ease-in-out;
		width: 100%;
	}
	.list-group-item.clicked .elementtypelist {
		display:flex;
		display:-webkit-flex;
	}
	.elementtypelist .assing_element, .elementtypelist .cancel_assignment {
		margin-left: 5px;
	}

	.elementtypelist .element_typeid {
		width: 83%;
		height: 32px;
		padding: 3px 12px;
	}
    .btn_progress .btn_progressbar
	{
        background-color: rgba(0, 0, 0, 0.5);
        display: block;
        height: 36px;
        top: 0px;
        left: 0px;
        max-width: 100%;
        position: absolute;
        transition: width 3s ease 0s;
        width: 0;
    }


	.link_chk, .link_radio {
		display: inline-block;
		width: 20px;
		height: 20px;
		color: #fff;
		border-radius: 3px;
		vertical-align: middle;
	}
	.link_chk:hover, .link_radio:hover {
		color: #ffffff;
	}
	.link_chk:focus, .link_radio:focus {
		color: #fff;
	}
	.link_chk.red, .link_radio.red {
		background-color: #DD4B39;
		border-width: 1px;
		border-style: solid;
		border-color: #cd3b29;
	}
	.link_chk.amber, .link_radio.amber {
		background-color: #F39C12;
		border-width: 1px;
		border-style: solid;
		border-color: #e38c10;
	}
	.link_chk.green, .link_radio.green {
		background-color: #67A028;
		border-width: 1px;
		border-style: solid;
		border-color: #579018;
	}
	.link_chk.checked, .link_radio.checked  {
		box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.5);
		vertical-align: top;
		border-color: #fff;
	}
	.link_chk.checked::before, .link_radio.checked::before {
		content: "";
		font-family: "FontAwesome";
		display: inline-block;
		margin: 0 0 0 2px;
	}


</style>
