<?php
   // echo $this->Html->css(array('styles-inner'));
   echo $this->Html->css(array('projects/custom'));
   echo $this->Html->css('projects/bs-selectbox/bootstrap-select.min');
   echo $this->Html->script('projects/plugins/selectbox/bootstrap-select', array('inline' => true));
   echo $this->Html->css('projects/bootstrap-input');
   	echo $this->Html->css('projects/manage_elements');
   ?>
<style>
  .prd-img {
  	height: 105px;
  	width: 350px;
  	border: 1px solid #ffffff;
  }
  .modal-dialog {

  	 padding: 0px;
       margin-top: 30px;
  }

  .modal-backdrop.in{
  	display: none;

  }

  .assistance a{

  	text-decoration :none;
  }


  .dropdown-menu > li > a:hover {
    background-color: #e1e3e9 !important;
  background-image : none !important;

    color: #333;
  }

  .btn-warning:active, .btn-warning.active {
      background-color: #ec971f;
      border-color: #eb9316;
  }

  @media (min-width:1000px) {
  	.prd-img {
  		position: absolute;
  		right: 15px;
  		top: -40px;
  		z-index: 1;
  	}
  }
  @media (min-width:320px) and (max-width:999px) {
  	.prd-img {
  		position: unset;
  		margin: 0 auto;
  	}
  }
</style>
<script type="text/javascript">
   $('#ajax_overlay').show();
   window.onload=function(){
	$('.selectpicker').selectpicker();
	$('#ajax_overlay').hide();
   };
</script>
<script type="text/javascript" >
   $(document).ready(function () {
       $(".project_type").click(function () {
           var vlu = $(this).val();
           var id = '';
           if (vlu == 'my_r') {
                id = 'my_received_project'
                $(".sect").hide();
                $(".my_r").show();
           } else if(vlu == 'my_p'){
                id = 'my_project';
                $(".sect").hide();
                $(".my_p").show();
           }else if(vlu == 'my_g'){
                id = 'group_received_project';
                $(".sect").hide();
                $(".my_g").show();
           }

           //alert(id);
           var current_val = $('#' + id).val();
           if (id == 'my_received_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/r_project:" + current_val);
           }
           if (id == 'my_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/m_project:" + current_val);
           }
           if (id == 'group_received_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/g_project:" + current_val);
           }
           //alert(current_val);
           if (current_val != ''  && current_val != null) {
               $("#event_calender").submit();
           }else {

                // $("#create_form_element").attr("action", "<?php echo SITEURL; ?>users/projects/" + last);
                // $("#create_form_element").submit();
                //$('#message_box').html('No Project Found.')
                //$("#workspace_id").html('<option value="">-- Select Workspaces --</option>');
                //$('#workspace_id').html("");
                $("#workspace_id").html('<option value="">No Workspace Found!</option>').selectpicker('refresh');
                $(".calender_data").html('<div class="col-xs-12"><section class="content-header clearfix"><div style="max-width: 100%" class="container"><div style=" margin:  0px 0" class="text-center">No Recored Found!</div></div></section></div>');
            }
       })

       $(".getWrkSpaces").change(function () {
           var current_val = $(this).val();
           var select_box_type = $(this).attr("id");
           if (select_box_type == 'my_received_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/r_project:" + current_val);
           }
           if (select_box_type == 'my_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/m_project:" + current_val);
           }
           if (select_box_type == 'group_received_project') {
               $("#event_calender").attr("action", "<?php echo SITEURL; ?>users/event_calender/g_project:" + current_val);
           }
           if (current_val != '') {
               $("#event_calender").submit();
           }
       });

       $(".get_calender_elements").click(function () {
           var project_type = $(".project_type:checked").val();// project type like my project/received project
           var projectid = '';//(project_type == 'my_p') ? $("#my_project").val() : $("#my_received_project").val();// project id get
           if(project_type == 'my_p'){
               projectid = $("#my_project").val();
           }else if(project_type == 'my_r'){
               projectid = $("#my_received_project").val();
           }else if(project_type == 'my_g'){
               projectid = $("#group_received_project").val();
           }

           var workspaceid = $("#workspace_id").val(); // owrkspace id get
           var status = ($(".statusid").val() !='') ? $(".statusid").val() : 'all';// get element status
           if (workspaceid == '') {

			    var srcvalue = "<?php echo SITEURL; ?>users/event_calender/<?php echo $project_type; ?>:<?php echo $project_id ?>"+"/status:" + status + "/mode:<?php echo $mode; ?>";
           } else {
               var srcvalue = "<?php echo SITEURL; ?>users/event_calender/<?php echo $project_type; ?>:<?php echo $project_id ?>/workspace_id:" + workspaceid + '/status:' + status + '/mode:<?php echo $mode; ?>';
           }
           //alert(srcvalue);
                           $.ajax({
                               type: "POST",
                               url: srcvalue,
                               dataType: "html",
                               async: false,
                               global: true,
                               beforeSend: function () {
                                   $(".ajax_overlay_preloader").fadeIn();
                               },
                               success: function (resultes) {
                                   //alert(resultes);
                                   $(".calender_data").html(resultes);
                               },
                               complete: function () {
                                   $(".ajax_overlay_preloader").fadeOut();
                               },
                               data: {"project_id": projectid, "project_type": project_type, "workspace": workspaceid, "status": status}
                           });

                       });



                   });
</script>
<div class="col-xs-12">


   <section class="content-header event-padd clearfix">
      <!--	<h2>Calendar</h2> -->
      <div class="btn-group action pull-right">
         <a class="btn btn-warning tipText btn-sm btn_go_back" href="javascript:history.back()" data-original-title="Go Back" id="btn_go_back"> <i class="fa fa-fw fa-chevron-left"></i> Back</a>
      </div>
      <h1 class="pull-left">
         <?php
            if (isset($project) && !empty($project)) {
                $project_detail = $project;

                //pr($project_detail);
                echo $this->ViewModel->_substr($project_detail['Project']['title'], 60, array('html' => true, 'ending' => '...'));
                ?>
         <?php
            } else {
                echo "Project Summary";
            }
            ?>
      </h1>
      <?php
         if (isset($project) && !empty($project)) {
             $project_detail = $project;
             ?>
      <p class="text-muted date-time pull-left" style="min-width:50%;clear:both">Project :
         <span>Created: <?php
		 //echo date('d M Y h:i:s', $project_detail['Project']['created']);
		 echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s');
		 ?></span>
         <span>Updated: <?php
		 //echo date('d M Y h:i:s', strtotime($project_detail['UserProject']['0']['modified']));
		 echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_detail['UserProject']['0']['modified'])),$format = 'd M Y h:i:s');
		 ?></span>
      </p>
      <?php } ?>
      <?php //pr($mygroupprojectlist);?>
   </section>

	<span id="project_header_image">
		<?php
			if (isset($project) && !empty($project)) {
				$project_detail = $project;
				if( isset( $project_detail['Project']['id'] ) && !empty( $project_detail['Project']['id'] ) ) {
					echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_detail['Project']['id']));
				}
			}
		?>
	</span>
</div>

<div class="col-xs-12">
   <section class="content-header event-padd clearfix">
      <div id="calendar">  </div>
   </section>
</div>
<div class="col-xs-12">
   <section class="content-header event-padd clearfix">
      <div class="form-group">
         <div class="radio-family">
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
         </div>
         <div class="pull-right no-direct">
            <div class="btn-group">
               <?php
                  // Received project conditions
                  $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
                  $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
                  // Group project conditions
                  $grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
                  if (isset($grp_id) && !empty($grp_id)) {
                      $group_permission = $this->Group->group_permission_details($project_id, $grp_id);

                      if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
                          $project_level = $group_permission['ProjectPermission']['project_level'];
                      }
                  }
                  //echo $project_id.' = '. $user_id;
                  //pr($grp_id);
                  if ((isset($user_project) && !empty($user_project))) {

                      echo $this->Html->link("Gantt", array("action" => "event_gantt", "m_project" => $project['Project']['id']), array("class" => "btn  btn-sm btn-success"));
                  } else if (((isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($p_permission['ProjectPermission']) )) && (isset($grp_id) && empty($grp_id))) {


                      echo $this->Html->link("Gantt", array("action" => "event_gantt", "r_project" => $project['Project']['id']), array("class" => "btn  btn-sm btn-success"));
                  }else if (isset($grp_id) && !empty($grp_id)) {

                      echo $this->Html->link("Gantt", array("action" => "event_gantt", "g_project" => $project['Project']['id']), array("class" => "btn  btn-sm btn-success"));
                  }
                  ?>
            </div>
            <div class="btn-group">
               <button class="btn  btn-sm btn-primary" data-calendar-nav="prev"><< Prev</button>
               <button class="btn  btn-sm btn-default" data-calendar-nav="today">Today</button>
               <button class="btn  btn-sm btn-primary" data-calendar-nav="next">Next >></button>
            </div>
            <div class="btn-group">
               <button class="btn  btn-sm btn-warning" data-calendar-view="year">Year</button>
               <button class="btn  btn-sm btn-warning active" data-calendar-view="month">Month</button>
               <button class="btn  btn-sm btn-warning" data-calendar-view="week">Week</button>
               <!--<button class="btn btn-warning" data-calendar-view="day">Day</button> -->
            </div>
         </div>
      </div>
   </section>
</div>
<div class="col-xs-12">
   <section class="content-header event-padd clearfix">
      <form id="event_calender" name="search_filter" action="<?php echo SITEURL; ?>users/event_calender/<?php echo $project_type; ?>:<?php echo $project_id; ?>" method="post">
         <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6">
               <div class="form-group my_p sect" style="display:<?php echo isset($project_type) && $project_type == "m_project" ? "block;" : "none;" ?>">
                  <label for="my_project">
                     <!--My Projects:-->&nbsp;
                  </label>
                <?php
                    if(isset($myprojectlist) && !empty($myprojectlist)){
                        echo $this->Form->input("project_id", array("id" => "my_project", "div" => false, "selected" => $project_id, "label" => false, "class" => "form-control getWrkSpaces selectpicker", "data-style"=>"aqua" ,"style"=>"display: none;","type" => "select", "options" => $myprojectlist));
                    }else{
                    ?>
                    <select style="display: none;" data-style="aqua" class="form-control getWrkSpaces selectpicker" id="my_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>

                    <?php

                    }
                ?>
               </div>
               <div class="form-group my_g sect" style="display:<?php echo isset($project_type) && $project_type == "g_project" ? "block;" : "none;" ?>">
                  <label for="group_received_project">
                     <!--Group Received Projects:-->&nbsp;
                  </label>
                <?php //
                    //$mygroupprojectlist = null;
                    if(isset($mygroupprojectlist) && !empty($mygroupprojectlist)){
                  echo $this->Form->input("project_id", array("id" => "group_received_project", "div" => false, "selected" => $project_id, "label" => false, "class" => "form-control getWrkSpaces selectpicker", "data-style"=>"aqua" ,"style"=>"display: none;","type" => "select", "options" => $mygroupprojectlist));
                    }else{
                    ?>
                   <select style="display: none;" data-style="aqua" class="form-control getWrkSpaces selectpicker" id="group_received_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>
                    <?php
                    }
                ?>
               </div>
               <div class="form-group my_r sect" style="display:<?php echo isset($project_type) && $project_type == "r_project" ? "block;" : "none;" ?>">
                  <label for="my_received_project">
                     <!--Received Projects:-->&nbsp;
                  </label>
                <?php
                   // $myreceivedprojectlist = null;
                    if(isset($myreceivedprojectlist) && !empty($myreceivedprojectlist)){
                  echo $this->Form->input("project_id", array("id" => "my_received_project", "selected" => $project_id, "div" => false, "label" => false, "class" => "form-control getWrkSpaces selectpicker", "type" => "select","data-style"=>"aqua" ,"style"=>"display: none;", "options" => $myreceivedprojectlist));
                    }else{
                ?>
                   <select style="display: none;" data-style="aqua" class="form-control getWrkSpaces selectpicker" id="my_received_project" name="data[Project][project_id]"><option value="">No Project Found!</option></select>

                <?php
                    }
                ?>
               </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6">
               <div class="form-group change_workspaces">
                  <label for="workspaces">
                     <!--Workspaces:-->&nbsp;
                  </label>
                  <?php echo $this->Form->input("workspace_id", array("data-style"=>"aqua" ,"style"=>"display: none;","empty" => "Select Workspace", "selected" => $workspace_id, "div" => false, "label" => false, "class" => "form-control selectpicker", "type" => "select", "options" => $myWorkspaceslistByproject)); ?>
               </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6">
               <div class="form-group change_workspaces">
                  <label for="workspaces">
                     <!--Status:-->&nbsp;
                  </label>
                  <?php
                     $statusArray = array("PND" => "Not Started", "PRG" => "In Progress", "OVD" => "Overdue", "CMP" => "Completed");
                     echo $this->Form->input("status_id", array("data-style"=>"aqua" ,"style"=>"display: none;","empty" => "Select Status", "div" => false, "label" => false, "class" => "form-control selectpicker statusid", "type" => "select", "options" => $statusArray));
                     ?>
               </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6">
               <div class="button-gant-css">
                  <button class="btn  btn-sm btn-success get_calender_elements"  type="button" >Apply Filter</button>
                  <button type="button" class="btn  btn-sm btn-danger" onclick="window.location.href = '<?php echo SITEURL; ?>users/event_calender/<?php echo $project_type; ?>:<?php echo $project_id; ?>'">Reset</button>
               </div>
            </div>
         </div>
      </form>
   </section>
</div>
<div class="col-xs-12">
   <section class="content-header event-padd clearfix">
      <div id="calendar">  </div>
   </section>
</div>

<div class="clearfix"></div>
<div class="calender_data">
   <?php
      include 'get_workspace_by_project.ctp';
    ?>
</div>
<div class="clearfix"></div>
<br><br>
<div id="disqus_thread"></div>
<noscript>Please enable JavaScript to view the calendar.</noscript>
<div class="modal fade" id="events-modalsss" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title">Event</h3>
         </div>
         <div class="modal-body" style="height: 400px">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

<style>
#event_calender .form-group{ margin-bottom:0px;}
.radio label{ width:auto;}
.content { margin-left: auto;  margin-right: auto;  min-height: 250px;  padding: 5px 0 15px; }
</style>
 <style>

    .fc-time{display:none}
    .fc-day-grid-event{cursor : pointer !important }
    #calendar {
        background: #fff none repeat scroll 0 0;
        border-top: 2px solid #048204;
        margin: 3px 0 0;
    }

	#event_calender{ margin : -7px 0 0 0 ;}

    .fc-day-number {
        text-align: center !important;
    }
    .fc-center h2 {color: peru;}

    .modal.modal-success.fade.in .modal-backdrop {
        height: auto !important;
        position: fixed;
        bottom: 0;
        z-index: 0;
    }
    .page-header {
        border-bottom: 1px solid #eee;
        margin: auto !important;
        padding-bottom: 9px;
        display: block;
        overflow: hidden;
		text-align :center;
        /* float: right; */

    }

    /* .page-header h3 { text-transform:capitalize; } */
    .container {
        margin-left: auto;
        margin-right: auto;
        padding-left: 0px;
        padding-right: 0px;
    }

    .nav .caret {
        border-bottom-color: #fff;
        border-top-color: #fff;
    }

    .nav a:hover .caret {
        border-bottom-color: #fff;
        border-top-color: #fff;
    }
    .text-muted {
        color: #777;
    }
    .button-gant-css button{margin-top: 25px;}

	.btn-gray {
		background-color: #d9dde2;
		border-color: #b7bbc0;
	}

	.bootstrap-select.btn-group:not(.input-group-btn), .bootstrap-select.btn-group[class*="span"]{ background:none;}

.border-alt {
    border: 1px solid #fff !important;
    border-radius: 2px;
    font-size: 10px;
    margin: -20px 0 0 !important;
    padding: 1px;
    position: relative;
    top: -2px;
}
#event_calender .btn-group .aqua {
    border-color: #00c0ef;
	background:#fff;
	border-radius:0px;
}
#event_calender .open ul.dropdown-menu > li{
border: medium none !important;
}

#event_calender ul.multiselect-container.dropdown-menu {
  border-radius: 0 !important;
  box-shadow: none !important;
  height: auto;
  max-height: 300px;
  overflow: auto;
  width: 100%;
}

#event_calender .open ul.dropdown-menu > li a {
    border: medium none !important;
    padding: 0 0 0 2px;
	color:#000;
}

#event_calender .open ul.dropdown-menu > li > a > label {
  cursor: pointer;
  font-weight: 400;
  height: 100%;
  margin: 0;
  padding: 0 !important;
}

#event_calender .multiselect-container > li > a > label {
    cursor: pointer;
    font-weight: 400;
    height: 100%;
    margin: 0;
    padding: 0 !important;
}

#event_calender .open ul.dropdown-menu > li a:hover {
    background: #3399FF;
	color:#fff;
}

#event_calender .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
    background: #3399FF !important;
  color: #fff !important;
  outline: 0 none;
  text-decoration: none;
}

#event_calender .dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, #event_calender .dropdown-menu > .active > a:focus {
   background: #3399FF !important;
  color: #fff !important;
  background-image: linear-gradient(to bottom, #428bca 0%, #357ebd 100%);
  background-repeat: repeat-x;
}

#event_calender .btn-group.open .dropdown-toggle {
  box-shadow: none;
}

#event_calender .bootstrap-select > .dropdown-menu{
border-radius:0px;
}

#event_calender .sect .caret{
border-top-color:#b7b7b7;
}
.aqua .caret{
border-top-color:#b7b7b7;
}

@media (min-width:768px) and (max-width:991px) {
	#events-modal .modal-sm {
		width: 340px;
	}
}

@media screen and (max-width:1366px) {
	#events-modal .modal-sm {
		width: 340px;
	}
}

</style>
