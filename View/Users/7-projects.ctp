<?php echo $this->Html->css('jquery.treeview.css'); ?>

<?php echo $this->Html->script('jquery.cookie', array('inline' => true)); ?>
<?php echo $this->Html->script('jquery.treeview', array('inline' => true)); ?>

<?php
echo $this->Html->script('demoTree', array('inline' => true));
$uuid = $this->Session->read('Auth.User.id');
$pid = $project_id;
//$pid = $this->params['pass']['0'];
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
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/r_project:" + current_val);
            }
            if (select_box_type == 'my_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/m_project:" + current_val);
            }
            if (select_box_type == 'group_received_project') {
                $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/g_project:" + current_val);
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
							echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'resources' )); 	
						}
                        ?>
                    </div>


					<?php echo $this->Form->create('Project', array("id" => "create_form_element", "type" => "post")); ?>
                    <div class="radio-family col-sm-12 col-md-12 col-lg-6 nopadding-left nomargin-left radio-family-left-unq">
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

                    <div class="second_row">

                        <div class="box-tools col-sm-12 col-md-8 col-lg-9 box-tools-right-unq" style="padding:0px;">

                            <!--                                    <label>Show List:</label>-->

                            <select name="type" id="multiple_element_type" style="display:none; width:  147px;" multiple="multiple">
                                <option <?php echo isset($links) && !empty($links) && $links == 'links' ? 'selected="selected"' : '' ?> value="links" id="a2">Links</option>
                                <option <?php echo isset($notes) && !empty($notes) && $notes == 'notes' ? 'selected="selected"' : '' ?> value="notes" id="a3">Notes</option>
                                <option <?php echo isset($documents) && !empty($documents) && $documents == 'documents' ? 'selected="selected"' : '' ?> value="documents" id="a4">Documents</option>
                                <option <?php echo isset($mms) && !empty($mms) && $mms == 'mms' ? 'selected="selected"' : '' ?> value="mms" id="a5">Mind Maps</option>
                                <option <?php echo isset($decisions) && !empty($decisions) && $decisions == 'decisions' ? 'selected="selected"' : '' ?> value="decisions" id="a6">Decisions</option>
                                <option <?php echo isset($feedbacks) && !empty($feedbacks) && $feedbacks == 'feedbacks' ? 'selected="selected"' : '' ?> value="feedbacks" id="a7">Feedbacks</option>
                                <option <?php echo isset($votes) && !empty($votes) && $votes == 'votes' ? 'selected="selected"' : '' ?> value="votes" id="a8">Votes</option>
                            </select>
                            <div style="display: inline;">
                                <button type="button" id="multiple_element_type_submit" style="margin-right:9px" class="btn btn-sm btn-success">Apply Filter</button>
                                <button type="button" onclick="window.location.href = $js_config.base_url + 'users/projects/<?php echo $project_type; ?>:<?php echo $project_id; ?>'" id="multiple_element_type_cancel" class="btn btn-danger btn-sm">Reset</button>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <div class="box-tools pull-right col-sm-12 col-md-12  col-lg-6 radio-family-left-unq" style="padding: 3px 0px 10px 10px ">
                            <div class="search_div pull-right">
                                <input placeholder="Search" class="search pull-left" id="search" style="margin: 0 10px 10px 10px; padding: 4px; text-align:right; border: 1px solid #367fa9;">

                                <div id="sidetreecontrol" class="pull-left">
									 <a class="btn btn-primary btn-sm  searchbtn pull-right tipText" title="Collapse" href="?#" style="margin: 0 0 0 5px;"><i class="fa fa-compress"></i></a>
                                    <a class="btn btn-primary btn-sm  searchbtn expd tipText" title="Expand"  href="?#"><i class="fa fa-expand"></i></a>
                                </div>

                                <!-- Project Options -->

                            </div>

                        </div>
                    </div>
                </section>
            </div>


            <div class="box-content">
                <div id="message_box"></div>
                <div class="row ">
                    <div class="col-xs-12">

                        <?php
                        if (isset($projects) && !empty($projects)) {
                            ?>
                            <?php
                            if (isset($typesArr) && !empty($typesArr)) {
                                include 'project_filtered.ctp';
                            } else {
                                include 'project_unfiltered.ctp';
                            }
                            ?>
                        <?php
                        } else {
                            echo $this->element('../Projects/partials/error_data', array(
                                'error_data' => [
                                    'message' => "You have not created any project yet.",
                                    'html' => "Click<a class='' href='" . Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE), TRUE) . "'> here </a>to create project now."
                                ]
                            ));
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

            $('.elm_docP').on({
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


            $(".elm_docP").draggable({helper: 'clone'});

            $(".drgP").droppable({
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



            $(".elm_mmP").draggable({helper: 'clone'});

            $(".drgMM").droppable({
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




            $(".docdrag").draggable({helper: 'clone'});

            $(".docdp").droppable({
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



<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="working_in"></div>

        </div>
    </div>
</div>