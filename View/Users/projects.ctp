<?php echo $this->Html->css('jquery.treeview.css'); ?>

<?php echo $this->Html->script('jquery.cookie', array('inline' => true)); ?>
<?php echo $this->Html->script('jquery.treeview', array('inline' => true)); ?>

<?php
echo $this->Html->script('demoTree', array('inline' => true));
$uuid = $this->Session->read('Auth.User.id');
$pid = $project_id;
//$pid = $this->params['pass']['0'];


$proejct_icon = 'prj_briefcase ps-flag';
$wsp_icon = 'wsp_squares ps-flag';
$area_icon = 'area_squares ps-flag';
$task_ele_icon = 'assets_task_squares ps-flag';

?>

<!-- Include the plugin's CSS and JS: -->
<script type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/selectbox/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>css/projects/bs-selectbox/bootstrap-multiselect.css" type="text/css"/>
<style>



	#task_status_type #s1{
		color: #FFC000 !important;
	}


	ul.list .folders{
		cursor:default;
	}

    .second_row{
    	clear: both;
    	display: inline-block;
    	width: 100%;
    }
    .radio label{
    	width: auto;
    }
    #message_box {
    	/* color: #f00; */
    	/* display: block; */
    	/* font-size: 12px;
    	width: 100%; */
    }

	.partial_data {
		padding: 0;
		width: 100%;
		border-image: none;
		/* border-style: solid solid solid;
		border-width: 1px 1px 1px;
		border-color: #ccc; */
		background:#fff;
	}

    .sect .btn-group .aqua {
        border-color: #00c0ef;
    	background:#fff;
    	border-radius:0px;
    }

    .sect .btn-group.open .dropdown-toggle{
    	box-shadow: none !important;
    }

    .sect .multiselect.dropdown-toggle.btn {
        background: #fff none repeat scroll 0 0 !important;
    }

    #elementContextMenu, .dropdown-menu {
        box-shadow: none !important;
        padding: 0 !important;
    	border-radius:0 !important;
    }

    .sect .dropdown-menu.selectpicker{
    	border:none !important;
    }

    .sect .open ul.dropdown-menu > li{
    border: medium none !important;
    }

    .sect .open ul.dropdown-menu > li a:hover {
        background: #3399FF;
    	color:#fff;
    }

    .sect .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
        background: #3399FF !important;
      color: #fff !important;
      outline: 0 none;
      text-decoration: none;
    }

    .sect .dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus {
       background: #3399FF !important;
      color: #fff !important;
      background-image: linear-gradient(to bottom, #428bca 0%, #357ebd 100%);
      background-repeat: repeat-x;
    }

    .sect .open ul.dropdown-menu > li a {

        color: #000;
        display: inline-table;
        margin: 0;
        padding: 0 0 0 2px;
        width: 100%;
    }



    .sect .multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
        display: inline-block !important;
        float: left !important;
        min-width: 75%;
        padding: 5px 20px 2px 40px;
    }

    .second_row .aqua {
        border-color: #00c0ef;
    	background:#fff !important;
    	border-radius:0px;

    }
    .box-tools .btn-group, .btn-group-vertical{
    background:#fff !important;
    border-radius:0px;
    }

    .sect .caret{
    border-top-color:#b7b7b7;
    }

	.project_data {
    margin: 10px;
    min-height: 177px;
	border-style: solid solid solid;
    border-width: 1px 1px 1px;
    border-color: #ccc;
	width: 1065px;

	}
    .select-project-sec {
        /* border: 0px solid #ccc; */
        min-height: 177px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
	.select-project-sec .no-data {
    color: #bbbbbb;
    font-size: 30px;

    text-align: center;
    text-transform: uppercase;
   /* top: 35%;
    width: 98%;
           left: 4px;
    position: absolute;*/
	}

	.wsp_flagbg:hover{
		/* color:#A6A6A6 !important;  */
		cursor: default;
	}


	.filetree li a:hover {
		color: #3c8dbc !important;
	}

	.filetree li a .text-red{
		color: #3c8dbc !important;
	}

	.filetree li a .text-red:hover{
		color: #3c8dbc !important;
	}

	.treeview .hover {
		color: #3c8dbc !important;
	}

	.filetree span.element  {
		cursor:default;
	}

	.area_folder span.folders {
		cursor:default !important;
	}

	.bootstrap-select:not([class*="span"]):not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn){ width: 255px;}

.content-header #create_form_element label {
    font-size: 13px;
    line-height: 20px;
}
.custom-dropdown {
    position: relative;
    display: inline-block;
    margin: 0 auto;
}

.custom-dropdown select {
    border: 1px solid #efefef;
    outline: none;
    background: transparent;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0;
    border: 1px solid #cccccc;
    display: block;
    width: 100%;
    cursor: pointer;
    margin: 0 0 5px;
    padding: 5px 4px 6px 10px;
    font-size: 14px;
    font-weight: normal;
}



.no-scroll {
overflow: hidden;
}

#mind_maps{ overflow : auto ; }

	/* .bootstrap-select:not([class*="span"]):not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
    width: 255px;
} */

    @media (min-width:1340px) and (max-width:1400px) {
        .bootstrap-select:not([class*="span"]):not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn){ width: 255px;}
    }


</style>
<?php
$varS = false;
if(isset($_REQUEST['types']) && !empty($_REQUEST['types'])){
	$varS = true;
}
if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])){
	$varS = true;
}

$wsp = (isset($_REQUEST['wsp']) && !empty($_REQUEST['wsp'])) ? $_REQUEST['wsp'] : 0;

?>
<script type="text/javascript" >

    $(function () {

		// RESIZE MAIN FRAME
        ($.adjust_resize = function(){
            if($('#mind_maps').length > 0){
                $('#mind_maps').animate({
                    minHeight: (($(window).height() - $('#mind_maps').offset().top) ) - 17,
                    maxHeight: (($(window).height() - $('#mind_maps').offset().top) ) - 17
                }, 1)
            }
            else{
                $('.partial_data').animate({
                    minHeight: (($(window).height() - $('.partial_data').offset().top) ) - 17,
                    maxHeight: (($(window).height() - $('.partial_data').offset().top) ) - 17
                }, 1)
            }
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
            $.adjust_resize();
            const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
            setTimeout( () => clearInterval(fix), 1500);
        })

        // RESIZE FRAME ON WINDOW RESIZE EVENT
        $(window).resize(function() {
            $.adjust_resize();
        })

		$('#sidetreecontrol .btn').click(function(){
			 $.adjust_resize();
		})




		$('html').addClass('no-scroll');


		var FlagC = '<?php echo $varS; ?>';
		if(FlagC == true){
			$('.searchbtn[data-original-title="Expand"]').trigger('click');
			$('.searchbtn[title="Expand"]').trigger('click');
			 $.adjust_resize();
		}else{
		$('#browser > li:first-child > div').trigger('click');
		 $.adjust_resize();

		var wsp = '<?php echo $wsp; ?>';
		if(wsp > 0){
			if($('#browser > li:first-child').hasClass('closed')){
				$('.wsp[data-id='+wsp+']').find('.hitarea').trigger('click');
				 $.adjust_resize();
			}
		}


		}

        var href = window.location.href,
                splited = href.split('/'),
                last = splited[splited.length - 1],
                last_split = last.split(':');


        $(".getWrkSpaces").change(function () {
            var current_val = $(this).val();
            var select_box_type = $(this).attr("id");

            if (select_box_type == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/" + current_val);
            }

			/* if (select_box_type == 'my_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/r_project:" + current_val);
            }
            if (select_box_type == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/m_project:" + current_val);
            }
            if (select_box_type == 'group_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/g_project:" + current_val);
            } */
            if (current_val != '') {
                $("#create_form_element").submit();
            }
        });
        $(".project_type").click(function () {

            $('#message_box').html("");

            var vlu = $(this).val();
            var id = '';
            if (vlu == 'my_r') {
                id = 'my_received_project';
                $(".sect").hide();
                $(".my_r").show();
            } else if (vlu == 'my_p') {
                id = 'my_project';
                $(".sect").hide();
                $(".my_p").show();
            } else if (vlu == 'my_g') {
                id = 'group_received_project';
                $(".sect").hide();
                $(".my_g").show();
            }


            //alert(id);
            var current_val = $('#' + id).val();

            if (id == 'my_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/r_project:" + current_val);
            }
            if (id == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/m_project:" + current_val);
            }
            if (id == 'group_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/g_project:" + current_val);
            }

            if (current_val != '' && current_val != null) {
                $("#create_form_element").submit();
            }
            else {

                // $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/" + last);
                // $("#create_form_element").submit();
                //$('#message_box').html('No Project Found.')
                $(".box-body").html('<div class="text-center"> No Recored Found!</div>');
            }
        })

        $('.selectpicker').selectpicker();
        $('#multiple_element_type').hide()
        $('#task_status_type').hide()
        setTimeout(function () {
            $('#multiple_element_type').multiselect({
                //enableFiltering: true,
				buttonClass	: 'btn  aqua new-extra',
                includeSelectAllOption: true,
                buttonWidth: '160px',
				numberDisplayed: 1,
				nonSelectedText: 'Asset Type',
            });

			$('#task_status_type').multiselect({
                //enableFiltering: true,
				buttonClass	: 'btn  aqua new-extra',
                includeSelectAllOption: true,
                buttonWidth: '160px',
				numberDisplayed: 1,
				nonSelectedText: 'Task Status',
            });

            $('.span_profile').hide()
        }, 1)
        $("#multiple_element_type_submit").click(function () {
            var typesArr = [];
            $.each($("#multiple_element_type option:selected"), function () {
                typesArr.push($(this).val());
            });
            typesArr.join(", ");

			var statusArr = [];
            $.each($("#task_status_type option:selected"), function () {
                statusArr.push($(this).val());
            });
            statusArr.join(", ");


            //alert(typesArr);
            if (typesArr != '' && statusArr != '' ) {
                var url_str = $js_config.base_url + "users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>?types=" + typesArr+"&status=" + statusArr;

			} else if (typesArr != '' && statusArr == '' ) {
                var url_str = $js_config.base_url + "users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>?types=" + typesArr;

			} else if (typesArr == '' && statusArr != '' ) {
                var url_str = $js_config.base_url + "users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>?status=" + statusArr;
			} else {
                var url_str = $js_config.base_url + "users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>";
            }
            //alert(url_str);
            $("#create_form_element").attr("action", url_str);
            $("#create_form_element").submit();

        });

        $('#ajax_overlay').hide();
        $("#browser").show();
        $(".elm_docPp").sortable({
            connectWith: ".elm_docP",
            handle: ".portlet-headers",
            cancel: ".portlet-toggle",
            placeholder: "portlet-placeholder ui-corner-all"
        });

        $(".portlet")
                .addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
                .find(".portlet-header")
                .addClass("ui-widget-header ui-corner-all")
                .prepend("<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

        $(".portlet-toggle").click(function () {
            var icon = $(this);
            icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
            icon.closest(".portlet").find(".portlet-content").toggle();
        });



<?php if (isset($typesArr) && !empty($typesArr)) { ?>
            $(".expd").trigger('click')
			 $.adjust_resize();
<?php } ?>
    });
</script>
</head>
<body>


    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <section class="content-header clearfix">
                    <h1 class="box-title pull-left"><?php echo $page_heading; ?>
                        <p class="text-muted date-time" style="padding: 6px 0; ">
                            <span style="text-transform:none; ">Hierarchical view of Project assets</span>
                        </p>
                    </h1>
                </section>
            </div>
			<span id="project_header_image">
				<?php
					if( isset( $project_id ) && !empty( $project_id ) ) {
						echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id));
					}
				?>
			</span>
            <div class="row">
                <section class="content-header clearfix" style="margin :0 15px 0 ;  border-top-left-radius: 3px;    background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >

                    <div class="btn-group action more-dropdown more-btn-ast">
                        <!-- Project Options -->
                        <?php
                        if (isset($project_id) && !empty($project_id)) {
                            // $wsp_permission = $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($this->params['pass']['1']),$project_id,$this->Session->read('Auth.User.id'));
                            //pr($wsp_permission);
                            $p_permission = $this->Common->project_permission_details($pid, $this->Session->read('Auth.User.id'));
                            $user_project = $this->Common->userproject($pid, $this->Session->read('Auth.User.id'));
                            if (isset($gpid) && !empty($gpid)) {
                                $p_permission = $this->Group->group_permission_details($pid, $gpid);
                                //pr($wwsid); die;
                            }
                            //pr($data['workspace']['ProjectWorkspace']['0']['WorkspacePermission']['0']);
							//********************* More Button ************************
							//echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'resources' ));
						}else{
							?>

							<!-- Project Options -->

							<!--<a data-toggle="dropdown" style="margin: 0 0 0 2px;opacity:0.4;" class="btn btn-sm btn-default bg-black dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
							<span class="caret"></span>
							</a>-->



							<?php
						}

                                /* $allProjects = [];
                                $mprojects = $myprojectlist;
                                $rprojects = $mygroupprojectlist;
                                $gprojects = $myreceivedprojectlist;

                                if (isset($mprojects) && !empty($mprojects)) {
                                	$allProjects = $allProjects + $mprojects;
                                }
                                if (isset($rprojects) && !empty($rprojects)) {
                                	$allProjects = $allProjects + $rprojects;
                                }
                                if (isset($gprojects) && !empty($gprojects)) {
                                	$allProjects = $allProjects + $gprojects;
                                }

                                if (isset($allProjects) && !empty($allProjects)) {
                                	$allProjects = array_map("strip_tags", $allProjects);
                                	$allProjects = array_map("trim", $allProjects);
                                	$allProjects = array_map(function ($v) {
                                		return html_entity_decode($v, ENT_QUOTES, "UTF-8");
                                	}, $allProjects);
                                	natcasesort($allProjects);
                                } */

								//pr($allProjects);


                        ?>
                    </div>


					<?php //echo $this->Form->create('Project', array("id" => "create_form_element", "type" => "post")); ?>
                   <?php /*?> <div class="radio-family col-sm-12 col-md-12 col-lg-6 nopadding-left nomargin-left radio-family-left-unq"><?php */?>
                        <?php /* <div class="radio radio-warning">
                            <input class="project_type" id="my_projects" type="radio" value="my_p" <?php echo isset($project_type) && $project_type == "m_project" ? 'checked="checked"' : ''; ?> name="project">
                            <label for="my_projects">My Projects </label>
                        </div>
                        <div class="radio radio-warning">
                            <input class="project_type" id="rec_projects" type="radio" value="my_r" <?php echo isset($project_type) && $project_type == "r_project" ? 'checked="checked"' : ''; ?> name="project">
                            <label for="rec_projects">Received Projects </label>
                        </div>
                        <div class="radio radio-warning">
                            <input class="project_type" id="group_projects" type="radio" value="my_g" <?php echo isset($project_type) && $project_type == "g_project" ? 'checked="checked"' : ''; ?> name="project">
                            <label for="group_projects">Group Received Projects </label>
                        </div>
						<?php */?>

                        <?php /* <div class="box-tools pull-left box-tools-left-unq" style="padding: 0px; margin-right: 10px;">

                           <div class="my_g sect" style="display:<?php echo isset($project_type) && $project_type == "g_project" ? "block;" : "none;" ?>">
                                <label for="group_received_project">
                                    <!--Group Received Projects:-->&nbsp;
                                </label>

                                <?php
                                if (isset($mygroupprojectlist) && !empty($mygroupprojectlist)) {
                                    echo $this->Form->input("project_id", array("id" => "group_received_project", "div" => false, "selected" => $project_id, "label" => false, "class" => "getWrkSpaces selectpicker", "data-style" => "aqua", "style" => "display: none;", "type" => "select", "options" => $mygroupprojectlist));
                                } else {
                                    ?>
                                <select style="display: none;" data-style="aqua" class="getWrkSpaces selectpicker" id="group_received_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="my_r sect" style="display:<?php echo isset($project_type) && $project_type == "r_project" ? "block;" : "none;" ?>">
                                <label for="my_received_project">
                                    <!--Received Projects:-->&nbsp;
                                </label>
                                <?php
                                if(isset($myreceivedprojectlist) && !empty($myreceivedprojectlist)){
                                echo $this->Form->input("project_id", array("id" => "my_received_project", "selected" => $project_id, "div" => false, "label" => false, "class" => "getWrkSpaces selectpicker", "type" => "select", "data-style" => "aqua", "style" => "display: none;", "options" => $myreceivedprojectlist)); } else {
                                    ?>
                                <select style="display: none;" data-style="aqua" class="getWrkSpaces selectpicker" id="my_received_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>


                    </div>*/ ?>

                    <div class="second_row show-resources-top-row">
						<div class="row">
						<?php echo $this->Form->create('Project', array("id" => "create_form_element", "type" => "post")); ?>
						<div class="box-tools col-sm-12 col-md-6 col-lg-6 box-tools-right-unq"><div class="my_p sect" >
								<!-- <label class="show-label">Project </label> -->
								<label class="custom-dropdown">

								<?php
								if(!isset($project_id) || empty($project_id)){

									$project_id = "";
									$projects = [];
								}

								//pr($allProjects);

								if(isset($allProjects) && !empty($allProjects)){

								echo $this->Form->input("project_id", array("id" => "my_project", "div" => false, "selected" => $project_id, "label" => false, "class" => "getWrkSpaces selectpickers aqua", "data-style" => "aqua", "style" => "width: 312px;", "type" => "select", "options" => $allProjects,'empty'=>"Select Project"));
								} else {
                                    ?>

                                <select style="display: none;" data-style="aqua" class="getWrkSpaces selectpicker" id="my_project" name="data[Project][project_id]"><option value="">No Projects </option></select>
                                    <?php
                                }
                                ?>
								</label>
                            </div>
						</div>
                        <div class="box-tools col-sm-12 col-md-6 col-lg-6 box-tools-right-unq pull-right project-assets-sec-h">
						<?php if (isset($project_id) && !empty($project_id)) { ?>
                            <select name="type" id="task_status_type" style="display:none; width:  147px;" multiple="multiple">
                                <option <?php echo (isset($statusArr) && in_array('undefined',$statusArr) ) ? 'selected="selected"' : ''; ?> value="undefined" id="s1" >Not Set</option>
                                <option <?php echo (isset($statusArr) && in_array('not_started',$statusArr) ) ? 'selected="selected"' : ''; ?> value="not_started" id="s2">Not Started</option>
                                <option <?php echo (isset($statusArr) && in_array('in_progress',$statusArr) ) ? 'selected="selected"' : ''; ?> value="in_progress" id="s3">In Progress</option>
                                <option <?php echo (isset($statusArr) && in_array('overdue',$statusArr) ) ? 'selected="selected"' : ''; ?> value="overdue" id="s4">Overdue</option>
								<option <?php echo (isset($statusArr) && in_array('completed',$statusArr) ) ? 'selected="selected"' : ''; ?> value="completed" id="s5">Completed</option>
                            </select>&nbsp;

                            <select name="type" id="multiple_element_type" style="display:none; width:  147px;" multiple="multiple">
                                <option <?php echo isset($links) && !empty($links) && $links == 'links' ? 'selected="selected"' : '' ?> value="links" id="a2">Links</option>
                                <option <?php echo isset($notes) && !empty($notes) && $notes == 'notes' ? 'selected="selected"' : '' ?> value="notes" id="a3">Notes</option>
                                <option <?php echo isset($documents) && !empty($documents) && $documents == 'documents' ? 'selected="selected"' : '' ?> value="documents" id="a4">Documents</option>
                                <option <?php echo isset($mms) && !empty($mms) && $mms == 'mms' ? 'selected="selected"' : '' ?> value="mms" id="a5">Mind Maps</option>
                                <option <?php echo isset($decisions) && !empty($decisions) && $decisions == 'decisions' ? 'selected="selected"' : '' ?> value="decisions" id="a6">Decisions</option>
                                <option <?php echo isset($feedbacks) && !empty($feedbacks) && $feedbacks == 'feedbacks' ? 'selected="selected"' : '' ?> value="feedbacks" id="a7">Feedback</option>
                                <option <?php echo isset($votes) && !empty($votes) && $votes == 'votes' ? 'selected="selected"' : '' ?> value="votes" id="a8">Votes</option>
                            </select>
							<?php } else {?>
								<select name="type" id="task_status_type" style="display:none; width:  147px;" multiple="multiple" disabled="disabled" >
								</select>&nbsp;

                            <select name="type" id="multiple_element_type" style="display:none; width:  147px;" multiple="multiple" disabled="disabled" >
                            </select>
							<?php } ?>
                            <div class="two-btn-ass">
							<?php if (isset($project_id) && !empty($project_id)) { ?>
                                <button type="button" id="multiple_element_type_submit" style="margin-right:4px" class="btn btn-sm btn-success">Apply</button>

								<button type="button" onclick="window.location.href = $js_config.base_url + 'users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>'" id="multiple_element_type_cancel" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>

							<?php } else {?>
								 <button type="button" style="margin-right:4px" class="btn btn-sm btn-success disabled">Apply</button>

								  <button type="button" id="multiple_element_type_cancel" class="btn btn-danger btn-sm disabled"><i class="fa fa-times"></i></button>

							<?php } ?>

                            </div>

                            <?php


							$projectnahi = "";
							$readonly = '';
							$expand = 'Expand';
							$collspe = 'Collapse';
							$cursor = '';
							if(!isset($project_id) || empty($project_id)){
								$projectnahi = "disable";
								$readonly = 'readonly="readonly"';
								$expand = '';
								$collspe = '';
								$cursor = 'cursor:default;';
							}
							?>



						<div class="search_div pull-right">
								<input <?php echo $readonly;?> placeholder="Filter by Name…" class="search pull-left <?php echo $projectnahi;?>" id="search" style="margin: 0 12px ; width: 130px; padding: 4px; border: 1px solid #367fa9;<?php echo $cursor;?>">
                                <div id="sidetreecontrol" class="pull-left <?php echo $projectnahi;?>">
									 <a <?php echo $readonly;?> class="btn btn-primary btn-sm  searchbtn pull-right tipText" title="<?php echo $collspe;?>" href="?#" style="margin: 0 0 0 4px;<?php echo $cursor;?>"><i class="fa fa-compress"></i></a>
                                    <a <?php echo $readonly;?> class="btn btn-primary btn-sm  searchbtn expd tipText" title="<?php echo $expand;?>"  href="?#" style="<?php echo $cursor;?>"><i class="fa fa-expand"></i></a>
                                </div>
                                <!-- Project Options -->
                            </div>

                            </div>
							<?php echo $this->Form->end();  ?>


                     </div>
                    </div>

					   <!--<div class="box-tools pull-right col-sm-12 col-md-12  col-lg-6 radio-family-left-unq" style="padding: 3px 0px 10px 10px ">


                        </div>-->


                </section>
            </div>


            <div class="box-content">
                <div id="message_box"></div>
                <div class="row ">
                    <div class="col-xs-12">

                        <?php
						//pr($projects);

                        if (isset($projects) && !empty($projects)) {
                            ?>
                            <?php
                            if ( isset($typesArr) && !empty($typesArr) && isset($statusArr) && !empty($statusArr) ) {
                                include 'project_filtered.ctp';
							} else if ( isset($typesArr) && !empty($typesArr) && (!isset($statusArr) || empty($statusArr) ) ) {
                                include 'project_filtered.ctp';
							} else {
                                include 'project_unfiltered.ctp';
                            }
                            ?>
                        <?php
                        } else { ?>
						<div class="col-sm-12 partial_data box-borders " style="padding: 10px; min-height: 800px;">
							<div class="col-sm-12 box-borders select-project-sec ">

										<div class="no-data">SELECT PROJECT</div>

</div>
							</div>
						<?php
                            /* echo $this->element('../Projects/partials/error_data', array(
                                'error_data' => [
                                    'message' => "You have not created any project yet.",
                                    'html' => "Click<a class='' href='" . Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE), TRUE) . "'> here </a>to create project now."
                                ]
                            )); */
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <style>
        ul.grid li .panel .panel-body .list-textcontents {
            min-height: 160px;
        }

        .error-inline {
            display: inline-block;
            padding-left: 5px;
            vertical-align: middle;
            font-size: 11px;
            color: #cc0000;
            margin-top: -5px;
        }
        textarea { resize: none; }
    </style>



    <script type="text/javascript" >
        function gototab() {
            window.location.hash = '#mind_maps';
            window.location.reload(true);
        }




        $(function () {

			$('.task-signoff').tooltip({
				 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
			})

			$('.wsp-signoff').tooltip({
				 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
			})

			$('.prj-signoff').tooltip({
				 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
			})

			$('#signoff_comment_show').on('hidden.bs.modal', function(event) {
				$(this).removeData('bs.modal');
				$(this).find('.modal-content').html('');
				$(".reopen-signoff").tooltip("")
			})

			$('#wsp_signoff_comment_show').on('hidden.bs.modal', function(event) {

				$(this).removeData('bs.modal');
				$(this).find('.modal-content').html('');
				 $(".reopen-signoff").tooltip("")
			})

			$('#prj_signoff_comment_show').on('hidden.bs.modal', function(event) {

				$(this).removeData('bs.modal');
				$(this).find('.modal-content').html('');
				 $(".reopen-signoff").tooltip("")
			})



            $('body').delegate(".view_mindmap", "click", function (event) {
                event.preventDefault();
                var $that = $(this),
                        url = $that.data('remote'),
                        eid = $that.data('eid'),
                        id = $that.data('id'),
                        uid = $that.data('uid'),
                        session = $that.data('sessionid');

                window.location.href = "<?php echo MINDMAP_SERVER ?>?eid=" + eid + "&mid=" + id + "&uid=" + window.btoa(uid) + "&sid=" + session + "";

            })


            $(document).on('click', '.RecordDeleteClass', function () {
                id = $(this).attr('rel');
                $('#recordDeleteID').val(id);
                $('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>/users/deleteDoc');
                $('#deleteBox h4').text('Are you sure you want to remove the selected Document? ');

            });
            // $( ".files a" ).draggable();

            $('.elm_docPs').on({
                dragstart: function () {
                    //   $(this).css('opacity', '0.5');

                },
                dragleave: function () {
                    $(this).removeClass('over');

                },
                dragenter: function () {
                    $(this).addClass('over');
                    //alert("Item was Dropped");

                },
                dragend: function () {
                    $(this).css('opacity', '1');


                },
                drop: function () {
                    alert('drop');
                }
            });


            $(".elm_docPs").draggable({helper: 'clone'});

            $(".drgPs").droppable({
                accept: ".elm_docP",
                drop: function (event, ui) {
                    console.log("Item was Dropped");

                    $(this).removeClass('closed expandable lastExpandable');

                    $(this).find('ul').show();
                    $(this).addClass('collapsable lastCollapsable');


                    var crt = $(this);

                    var elem_IDD = $(this).find('.elm_hid').val();

                    var link_href = ui.draggable.find('a').attr('href');

                    var link_id = ui.draggable.find('a').attr('id');

                    var link_tit = ui.draggable.find('a').text();

                    var data = {'id': link_id, 'element_id': elem_IDD, 'link_href': link_href, 'link_tit': link_tit};

                    $.ajax({
                        type: 'POST',
                        data: data,
                        url: '<?php echo SITEURL . 'entities/docElmAdd/' ?>',
                        global: true,
                        success: function (response, status, jxhr) {
                            var obj = jQuery.parseJSON(response);
                            if (obj.msg == 'Success') {
                                crt.find('ul').append($(ui.draggable).clone());
                                //$( "li" ).last()
                                crt.find('li:last-child').find('a.ddn').attr('id', obj.content);
                            }
                            else {
                                console.log('error')
                            }

                        },
                    });

                    //$(this).find('ul').append($(ui.draggable).clone());

                }
            });



            $(".elm_mmPs").draggable({helper: 'clone'});

            $(".drgMMs").droppable({
                accept: ".elm_mmP",
                drop: function (event, ui) {
                    console.log("Item was Dropped");

                    $(this).removeClass('closed expandable lastExpandable');
                    console.log(this);
                    $(this).find('ul').show();
                    $(this).addClass('collapsable lastCollapsable');


                    var crtn = $(this);

                    console.log($(this).find('.elm_mm_hid').val());
                    console.log(ui.draggable.find('a').attr('href'));

                    var elem_IDD = $(this).find('.elm_mm_hid').val();

                    var link_href = ui.draggable.find('a').attr('href');



                    var link_id = ui.draggable.find('a').attr('data-id');
                    //alert(link_id);
                    var link_tit = ui.draggable.find('a').text();

                    var data = {'id': link_id, 'element_id': elem_IDD, 'link_href': link_href, 'link_tit': link_tit};

                    $.ajax({
                        type: 'POST',
                        data: data,
                        url: '<?php echo SITEURL . 'entities/docmmAdd/' ?>',
                        global: true,
                        success: function (response, status, jxhr) {
                            console.log(status);
                            var obj = jQuery.parseJSON(response);
                            console.log(obj.msg);
                            if (obj.msg == 'Success') {
                                crtn.find('ul').append($(ui.draggable).clone());
                                //$( "li" ).last()
                                crtn.find('li:last-child').find('a.ddnsa').attr('id', obj.content);
                                crtn.find('li:last-child').find('a.ddnsa').attr('url', '<?php echo SITEURL . "entities/update_element/"; ?>' + elem_IDD + '#mind_maps');
                            }
                            else {
                                console.log('error')
                            }

                        },
                    });

                    //$(this).find('ul').append($(ui.draggable).clone());

                }
            });




            $(".docdrags").draggable({helper: 'clone'});

            $(".docdps").droppable({
                accept: ".docdrag",
                drop: function (event, ui) {
                    console.log("Item was Dropped");

                    $(this).removeClass('closed expandable lastExpandable');
                    console.log(this);
                    $(this).find('ul').show();
                    $(this).addClass('collapsable lastCollapsable');


                    var crt = $(this);

                    console.log($(this).find('.elm_hid').val());
                    console.log(ui.draggable.find('a').attr('href'));

                    var elem_IDD = $(this).find('.elm_hid').val();

                    var link_href = ui.draggable.find('a').attr('href');

                    var old_elm_id = ui.draggable.find('a.ddn').attr('rel');

                    var link_id = ui.draggable.find('a.ddn').attr('id');

                    var link_tit = ui.draggable.find('a.ddn').text();

                    var data = {'id': link_id, 'element_id': elem_IDD, 'link_href': link_href, 'link_tit': $.trim(link_tit), 'old_elm_id': old_elm_id};

					console.log(data);

                     $.ajax({
                        type: 'POST',
                        data: data,
                        async: false,
                        url: '<?php echo SITEURL . 'entities/docFileAdd/' ?>',
                        global: true,
                        success: function (response, status, jxhr) {
                            console.log(status);
                            var obj = jQuery.parseJSON(response);
                            console.log(obj.msg);
                            if (obj.msg == 'Success') {
                                crt.find('ul').append($(ui.draggable).clone());
                                //$( "li" ).last()
                                crt.find('li:last-child').find('a.ddn').attr('id', obj.content);
                                crt.find('li:last-child').find('a.ddn').attr('rel', obj.rel);
                                crt.find('li:last-child').find('a.ddn').attr('href', obj.href);
                                crt.find('li:last-child').find('a.RecordDeleteClass').attr('rel', obj.content);
                                crt.find('li:last-child').find('a.RecordDeleteClass').attr('url', '<?php echo SITEURL. "/users/deleteDoc/" ?>' + obj.content);
								 $.adjust_resize();

                            }
                            else {
                                console.log('error')
                            }

                        },
                    });

                    //$(this).find('ul').append($(ui.draggable).clone());

                }
            });


            $.expr[":"].contains = $.expr.createPseudo(function (arg) {
                return function (elem) {
                    return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
					 $.adjust_resize();
                };
            });

			$('#create_form_element').on('keyup keypress', function(e) {
			  var keyCode = e.keyCode || e.which;
			  if (keyCode === 13) {
				e.preventDefault();
				return false;
			  }
			});

            $('#search').keyup(function (e) {
				e.preventDefault();

                var searchTerms = $(this).val();
				if(e.keyCode == 13)
				{
					$('#browser li').each(function () {
						var hasMatch = searchTerms.length == 0 || $(this).is(':contains(' + searchTerms + ')');

						$(this).toggle(hasMatch);
						//$(".expd").trigger("click");
						 $.adjust_resize();

					});
				}
            });







        })

    </script>

<div class="box-header" style=" ">
	<!-- MODAL BOX WINDOW -->
	<div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<!-- END MODAL BOX -->
</div>

<!--<div class="box-header" style=" ">

	<div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>

</div>-->



<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-danger fade" id="wsp_signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-danger fade" id="prj_signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="working_in"></div>

        </div>
    </div>
</div>