<link href="http://cdn.syncfusion.com/15.4.0.20/js/web/flat-azure/ej.web.all.min.css" rel="stylesheet" /> 
<script src="http://cdn.syncfusion.com/js/assets/external/jsrender.min.js"></script>
<script src="http://cdn.syncfusion.com/js/assets/external/jquery.globalize.min.js"></script>
<script src="http://cdn.syncfusion.com/js/assets/external/jquery.easing.1.3.min.js"></script>
<script src="http://cdn.syncfusion.com/15.4.0.20/js/web/ej.web.all.min.js "></script>

<!-- Include the plugin's CSS and JS: -->
<script type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/selectbox/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>css/projects/bs-selectbox/bootstrap-multiselect.css" type="text/css"/>
<script type="text/javascript" >
	var data = [{
        taskID: 1,
        taskName: "Design",
        startDate: "02/10/2014",
        endDate: "02/14/2018",
        duration: 6,          
        subtasks: [
            {
                taskID: 2,
                taskName: "Software Specification",
                startDate: "02/10/2014",
                endDate: "02/12/2018",
                duration: 4,
                progress: "60",
				predecessor: "5FS",               
            },
            {
                taskID: 3,
                taskName: "Develop prototype",
                startDate: "02/10/2014",
                endDate: "02/12/2018",
                duration: 4,
                progress: "70",
                resourceId: [3]
            },
            {
                taskID: 4,
                taskName: "Get approval from customer",
                startDate: "02/12/2014",
                endDate: "02/14/2018",
                duration: 2,
                progress: "80",
                predecessor: "3FS",
                resourceId: [1]
            },
            {
                taskID: 5,
                taskName: "Design complete",
                startDate: "02/14/2014",
                endDate: "02/14/2018",
                duration: 0,
                predecessor: "4FS"
            }
        ]
    }];
	
	$(function() {
        $("#GanttContainer").ejGantt({
            dataSource: data, //Provides data source for Gantt
            taskIdMapping: "taskID", //Provide name of the property which contains task id in the data source
            taskNameMapping: "taskName", //Provide name of the property which contains task name in the data source
            scheduleStartDate: "02/01/2014", //Provide schedule header start date
            scheduleEndDate: "03/14/2018", //Provide schedule header end date
            startDateMapping: "startDate", //Provide name of the property which contains start date of the task in the data source            
			endDateMapping: "endDate", //Provide name of the property which contains start date of the task in the data source
            durationMapping: "duration", //Provide name of the property which contains duration of the task in the data source
            progressMapping: "progress", //Provide name of the property which contains progress of the task in the data source
            childMapping: "subtasks", //Provide name of the property which contains subtask of the task in the data source
			 toolbarSettings: {
				showToolbar: true,
				toolbarItems: [
					ej.Gantt.ToolbarItems.Add,
					ej.Gantt.ToolbarItems.Edit,
					ej.Gantt.ToolbarItems.Delete,
					ej.Gantt.ToolbarItems.Update,
					ej.Gantt.ToolbarItems.Cancel,
					ej.Gantt.ToolbarItems.Indent,
					ej.Gantt.ToolbarItems.Outdent,
					ej.Gantt.ToolbarItems.ExpandAll,
					ej.Gantt.ToolbarItems.CollapseAll,
					ej.Gantt.ToolbarItems.Search
				],
			},
			allowGanttChartEditing:true, //enable the taskbar editing 
			predecessorMapping:"predecessor" ,// Predecessor editing 
			editSettings: {
				allowEditing: true,
				allowAdding: true,
				allowDeleting: true,
				editMode:"normal",
			},
		}
		
	);
});
</script>

<?php
$uuid = $this->Session->read('Auth.User.id');
$pid = $project_id;
?>
<!-- Include the plugin's CSS and JS: -->
<script type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/selectbox/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>css/projects/bs-selectbox/bootstrap-multiselect.css" type="text/css"/>
<style>
    .second_row{
    	clear: both;
    	display: inline-block;
    	width: 100%;
    }
    .radio label{
    	width: auto;
    }
    #message_box {
    	color: #f00;
    	display: block;
    	font-size: 12px;
    	width: 100%;
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
</style>
<script type="text/javascript" >

    $(function () {

        var href = window.location.href,
                splited = href.split('/'),
                last = splited[splited.length - 1],
                last_split = last.split(':');


        $(".getWrkSpaces").change(function () {
            var current_val = $(this).val();
            var select_box_type = $(this).attr("id");
            //alert(current_val+' ----- '+select_box_type);
            if (select_box_type == 'my_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/r_project:" + current_val);
            }
            if (select_box_type == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/m_project:" + current_val);
            }
            if (select_box_type == 'group_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/g_project:" + current_val);
            }
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
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/r_project:" + current_val);
            }
            if (id == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/m_project:" + current_val);
            }
            if (id == 'group_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>projects/ganttchart/g_project:" + current_val);
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
        setTimeout(function () {
            $('#multiple_element_type').multiselect({
                //enableFiltering: true,
				buttonClass	: 'btn  aqua new-extra',
                includeSelectAllOption: true,
                buttonWidth: '280px',
            });
            $('.span_profile').hide()
        }, 1)
        $("#multiple_element_type_submit").click(function () {
            var typesArr = [];
            $.each($("#multiple_element_type option:selected"), function () {
                typesArr.push($(this).val());
            });
            typesArr.join(", ");
            //alert(typesArr);
            if (typesArr != '') {
                var url_str = $js_config.base_url + "users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>?types=" + typesArr;
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
                        <p class="text-muted date-time" style="padding: 6px 0">
                            <span>Hierarchical view of Project resources</span>
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

                    <div class="btn-group action more-dropdown ">
                        <!-- Project Options -->
                        <?php
                        if (isset($project_id) && !empty($project_id)) {                           
                            $p_permission = $this->Common->project_permission_details($pid, $this->Session->read('Auth.User.id'));
                            $user_project = $this->Common->userproject($pid, $this->Session->read('Auth.User.id'));
                            if (isset($gpid) && !empty($gpid)) {
                                $p_permission = $this->Group->group_permission_details($pid, $gpid);
                                //pr($wwsid); die;
                            } 
							
                            //********************* More Button ************************ 
							echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'projects' ));
                        }
                        ?>
                    </div>


					<?php echo $this->Form->create('Project', array("id" => "create_form_element", "type" => "post")); ?>
                    <div class="radio-family col-sm-6 col-md-6 col-lg-6 nopadding-left nomargin-left radio-family-left-unq">
                        <div class="radio radio-warning">
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

                        <div class="box-tools pull-left box-tools-left-unq" style="padding: 0px; margin-right: 10px;">
                            <div class="my_p sect" style="display:<?php echo isset($project_type) && $project_type == "m_project" ? "block;" : "none;" ?>">

								<?php
								if(isset($myprojectlist) && !empty($myprojectlist)){

								echo $this->Form->input("project_id", array("id" => "my_project", "div" => false, "selected" => $project_id, "label" => false, "class" => "getWrkSpaces selectpicker", "data-style" => "aqua", "style" => "display: none;", "type" => "select", "options" => $myprojectlist));
								} else {
                                    ?>
                                <select style="display: none;" data-style="aqua" class="getWrkSpaces selectpicker" id="my_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>
                                    <?php
                                }
                                ?>
                            </div>
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

                    </div>

                    

                        <div class="box-tools col-sm-6 col-md-4 col-lg-9 box-tools-right-unq" style="padding:0px;">
                            
                            <div style="display: inline;">
                                <button type="button" id="multiple_element_type_submit" style="margin-right:9px" class="btn btn-sm btn-success">Apply Filter</button>
                                <button type="button" onclick="window.location.href = $js_config.base_url + 'users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>'" id="multiple_element_type_cancel" class="btn btn-danger btn-sm">Reset</button>
                            </div>
                        </div>
						<?php echo $this->Form->end(); ?>
                    
                </section>
            </div>
		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">
					<div id="GanttContainer"></div>    
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

        $(function () {

            $.expr[":"].contains = $.expr.createPseudo(function (arg) {
                return function (elem) {
                    return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
                };
            });

            $('#search').keyup(function (e) {
                var searchTerms = $(this).val();

                $('#browser li').each(function () {
                    var hasMatch = searchTerms.length == 0 || $(this).is(':contains(' + searchTerms + ')');

                    $(this).toggle(hasMatch);
                    $(".expd").trigger("click");
                    e.preventDefault();

                });
            });

        })
</script>