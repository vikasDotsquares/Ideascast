<?php
echo $this->Html->css('projects/todo');
echo $this->Html->script('projects/todo', array(
    'inline' => true
));
echo $this->Html->css('projects/dropdown');

echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array(
    'inline' => true
));

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array(
    'inline' => true
));

?>
<style type="text/css">
    .calendar-trigger {
        cursor: pointer !important;
    }
	.create-to-do input.fancy_input[type=checkbox]:checked+label.fancy_label {
   	 background-position: left -24px;
	}
	.create-to-do input.fancy_input[type=checkbox] {
		margin: 0px 0 0 6px;
	}

    label.fancy_label {
        /* background: rgba(0, 0, 0, 0) url("../../images/checks.png") no-repeat scroll left 1px;  */
        font-size: 14px;
        font-weight: bold;
        line-height: 24px;
        margin:-2px 10px 0 0;
    }

    .ui-datepicker {
        font-size: 14px !important; /*what ever width you want*/
    }

    .docUpload input.upload-input {
        cursor: pointer;
        font-size: 20px;
        margin: 0;
        opacity: 0;
        padding: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

	.open_sub_panel{ font-size: 14px; color: #000;}
	#create_sub_panel h4{ font-size: 14px; color: #000;}
	#create_sub_panel h4:hover, .open_sub_panel:hover{ font-size: 14px; color: #000;}
	#create_sub_panel h4:visited, .open_sub_panel:visited{ font-size: 14px; color: #000;}
	#create_sub_panel h4:focus,.open_sub_panel:focus{ font-size: 14px; color: #000;}
	#create_sub_panel h4:active, .open_sub_panel:active{ font-size: 14px; color: #000;}

    .docUpload input.upload-input {
        width: 100%;
    }

    .input-group .form-control-input {
        float: left;
        z-index: 2;
    }
    .list-inline.ideacast-assign-list select.aqua {
        margin: 0 !important;
    }
	a.icon-round-redbg{
		border-radius: 50%;
		padding: 0px 5px 2px 5px;
		background-color: #dd4b39;
		border-color: #d73925;
		color: #fff !important;
		display: inline-block;
	}

	.noprojectlabel .fancy_label {
		font-weight:normal;
	}
</style>
<?php
$project_end_date = "";
if (isset($this->data["Parent"]['id']) && !empty($this->data["Parent"]['id'])) {

    $start = (isset($this->data["Parent"]["start_date"]) && !empty($this->data["Parent"]["start_date"])) ? date("d M Y", strtotime($this->data ["Parent"] ["start_date"])) : "";
	$end = (isset($this->data["Parent"]["end_date"]) && !empty($this->data["Parent"]["end_date"])) ? date("d M Y", strtotime($this->data ["Parent"] ["end_date"])) : "";

	if ((isset($this->data["DoList"]['project_id']) && !empty($this->data["DoList"]['project_id'])) ) {

	 $project_edate = project_detail($this->data["DoList"]['project_id']) ;

	 $project_end_date = (isset($project_edate["end_date"]) && !empty($project_edate["end_date"])) ? date("d M Y", strtotime($project_edate["end_date"])) : $end ;

	}

} else {

    $start = (isset($this->data["DoList"]["start_date"]) && !empty($this->data["DoList"]["start_date"])) ? date("d M Y", strtotime($this->data["DoList"]["start_date"])) : "";

	$end = (isset($this->data["DoList"]["end_date"]) && !empty($this->data["DoList"]["end_date"])) ? date("d M Y", strtotime($this->data["DoList"]["end_date"])) : "";

	if ((isset($this->data["DoList"]['project_id']) && !empty($this->data["DoList"]['project_id'])) ) {

	 $project_edate = project_detail($this->data["DoList"]['project_id']) ;

	 $project_end_date = (isset($project_edate["end_date"]) && !empty($project_edate["end_date"])) ? date("d M Y", strtotime($project_edate["end_date"])) : $end ;

	}

}


if ((isset($this->request->params['named']) && !empty($this->request->params['named']['project'])) ) {

	 $project_edate = project_detail($this->request->params['named']['project']) ;

	 $project_end_date = (isset($project_edate["end_date"]) && !empty($project_edate["end_date"])) ? date("d M Y", strtotime($project_edate["end_date"])) : $end ;

	 $start = (isset($project_edate["start_date"]) && !empty($project_edate["start_date"])) ? date("d M Y", strtotime($project_edate["start_date"])) : $end ;

}


?>
<script type="text/javascript">
    $(function () {

		//$('.project_id').trigger('change').val();

        $('#users').multiselect({
            maxHeight: '400',
            buttonWidth: '100%',
            buttonClass: 'btn btn-info',
            // checkboxName: 'data[DoListUser][user_id][]',
            enableFiltering: true,
            filterBehavior: 'text',
            includeFilterClearBtn: true,
            enableCaseInsensitiveFiltering: true,
            // numberDisplayed: 3,
            includeSelectAllOption: true,
            includeSelectAllIfMoreThan: 5,
            selectAllText: ' Select all',
			nonSelectedText: 'Myself',
            // disableIfEmpty: true
            onInitialized: function () {
            }
        });

        $('#sub-users').multiselect({
            maxHeight: '400',
            buttonWidth: '100%',
            buttonClass: 'btn btn-info',
            // checkboxName: 'data[DoListUser][user_id][]',
            enableFiltering: true,
            filterBehavior: 'text',
            includeFilterClearBtn: true,
            enableCaseInsensitiveFiltering: true,
            // numberDisplayed: 3,
            includeSelectAllOption: true,
            includeSelectAllIfMoreThan: 5,
            selectAllText: ' Select all',
			nonSelectedText: 'Myself',
            // disableIfEmpty: true
            onInitialized: function () {
            }
        });

        $('.sub_user_selection').multiselect({
            maxHeight: '400',
            buttonWidth: '100%',
            buttonClass: 'btn btn-info',
            // checkboxName: 'data[DoListUser][user_id][]',
            enableFiltering: true,
            filterBehavior: 'text',
            includeFilterClearBtn: true,
            enableCaseInsensitiveFiltering: true,
            // numberDisplayed: 3,
            includeSelectAllOption: true,
            includeSelectAllIfMoreThan: 5,
            selectAllText: ' Select all',
            // disableIfEmpty: true
            onInitialized: function () {
            }
        });

        var selectedDate = '';


		$('.date-picker:not(#datepicker_00)').each(function () {
			 
            var $thev = $(this);
            var enddate = '<?php echo $end; ?>';
            var start = '<?php echo $start; ?>';
			if( $('#datepicker_00').length > 0){
				var strs = $("#datepicker_00").val().split("-")

				if( strs.length < 2 && ($("#" + $thev.attr("id")) != 'datepicker_00') ){
					$("#" + $thev.attr("id")).css("pointer-events", "none");
					return
				}
			}

            $("#" + $thev.attr("id")).daterange({
                numberOfMonths: 2,
                dateFormat: 'dd M yy',
                //minDate: 0,
                showButtonPanel: false,
                autoUpdateInput: false,
				showOptions: { direction: "down" },
                onSelect: function (selected, inst) {
                },
               beforeShow: function (input, inst) {
                    inst.dpDiv.addClass('SplCAl');
					var divPane = $(input).datepicker("widget")
					setTimeout(function(){
						divPane.offset({ top: (input.offsetHeight) + $("#" + inst.id).offset().top});
					},0)

					if( $('#datepicker_00').length > 0){
						var strs = $("#datepicker_00").val().split("-");
						var strs = $("#datepicker_00").val().split("-");
						 $("#" + $thev.attr("id")).datepicker("option", "minDate", strs[0]);
						if( strs[1].length > 0 ){
							 $("#" + $thev.attr("id")).datepicker("option", "maxDate", strs[1].trim());
						}else{
							$("#" + $thev.attr("id")).css("pointer-events", "none");
							console.log("dater3");
						}
					} else {
						$("#" + $thev.attr("id")).datepicker("option", "minDate", '<?php echo $start; ?>');
						$("#" + $thev.attr("id")).datepicker("option", "maxDate", '<?php echo $end; ?>');
					}
                },
                init: function (input, inst) {

                },
            });
		});




		//$("#datepicker_00").datepicker("option", "maxDate", new Date("<?php echo $project_end_date ; ?>"));

        $('.calendar-trigger').on('click', function (event) {
            event.preventDefault();
            var $thev = $(this);

         console.log($thev.prev().prev().attr("id"));
              $(this).parent('.input-group').find('input').trigger('click').trigger('focus');
        });



    })

    function date_message() {
        BootstrapDialog.show({
            title: '<i class="fa fa-warning"></i> Warning',
            type: BootstrapDialog.TYPE_DANGER,
            message: "Please schedule Main To-do's date before update.",
            draggable: true,
            buttons: [
                {
                    label: 'Close',
                    cssClass: 'btn-danger',
                    action: function (dialogRef) {
                        dialogRef.close();
                    }
                }
            ]
        });
    }

	function submitfrm(){
		$("#filter_list").addClass('disable');
		$("#filter_list").attr('type','button');
		$("#filter_list").attr('disabled','disabled');
	}

    $("#filter_list").on('click', function(event) {
        $(this).addClass('disabled');
        /* Act on the event */
    });

	function submitfrmsub(){
		$("#sub_todosave").addClass('disable');
		$("#sub_todosave").attr('type','button');
		$("#sub_todosave").attr('disabled','disabled');
		$("#subtodocancel").addClass('disable');
		$("#subtodocancel").attr('type','button');
		$("#subtodocancel").attr('disabled','disabled');
	}

</script>

<?php

if((isset($this->data ['DoList'] ['id']) && !empty($this->data ['DoList']['id'])))
{

?>
<script>

 $(function () {

        $("#datepicker_00").daterange({
            numberOfMonths: 2,
            dateFormat: 'dd M yy',
            minDate: 0,
            showButtonPanel: false,
            autoUpdateInput: false,
			orientation: 'bottom auto',
            onSelect: function (selected, inst) {
            },
            beforeShow: function (input, inst) {
                inst.dpDiv.addClass('SplCAl');
				 $("#datepicker_00").datepicker("option", "maxDate", "<?php echo $project_end_date ; ?>");
				 console.log("aay")
            },
			onClose: function(selectedDate){
				var strs = selectedDate.split("-")

				$('.date-picker').each(function (input, inst) {
					var $thev = $(this);
					var strs = $("#datepicker_00").val().split("-")
					if( strs.length < 2 && (inst.id != 'datepicker_00') ){

						$("#" + $thev.attr("id")).css("pointer-events", "none");
						return
					}

					if(inst.id != 'datepicker_00'){
						console.log("hello there...");
						$("#" + inst.id).datepicker("option", "minDate", strs[0]);
						$("#" + inst.id).datepicker("option", "maxDate", strs[1]);
						$("#" + inst.id).datepicker("option", "maxDate", strs[1].trim());
					}

				});
			}
        });
        });
</script>
<?php

}else{
?>
<script>
 $(function () {

	         $("#datepicker_00").daterange({
            numberOfMonths: 2,
            dateFormat: 'dd M yy',
            minDate: 0,
            showButtonPanel: false,
            autoUpdateInput: false,
			orientation: 'bottom auto',

        });

		$("#datepicker_00").datepicker("option", "maxDate", "<?php echo $project_end_date ; ?>");
});
</script>
<?php

}

?>
<div class="row create-to-do">
    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $page_heading; ?>
                    <p class="text-muted date-time">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php
                 echo $this->Session->flash();

                // $time = date("30/11/2010");
                //
				// $new_time = preg_replace("!([01][0-9])/([0-9]{2})/([0-9]{4})!", "$3-$1-$2", $time);
                // echo $time.'<br>';
                // echo date("Y-m-d",strtotime($new_time));
                // pr(strtotime($new_time));
                ?>
            </div>
        </div>
        <div class="box-content">
            <div class="row ">

                <div class="col-xs-12">

                    <div class="box margin-top noborder">

                        <?php
                        echo $this->Form->create("DoList", array(
                            "url" => array(
                                "controller" => "todos",
                                "action" => "manage"
                            ),
                            "id" => "formManageDoList",
							"onsubmit"=>"submitfrm()"
                        ));
                        echo $this->Form->input("DoList.id", array(
                            "type" => "hidden"
                        ));
                        ?>

                        <div class="box-header filters" style="border-top-left-radius: 3px;background-color: #f5f5f5;border: 1px solid #ddd;border-top-right-radius: 3px;">
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large"
                                 tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                            <div class="col-sm-12 ideacast-tophead create-to-do">
                                <ul class="list-inline ideacast-assign-list" style="margin-bottom: 0">
                                    <li><label class="text-bold" for="ProjectId"><span class="hidden-sm hidden-md">Assigned to</span>
                                            Project:</label></li>
                                    <li class="assigned-manage-todo"><label class="custom-dropdown" style="width: 100%;">
										 <?php
										 $disable = "";
										 if (isset($this->data ['DoList'] ['id']) && !empty($this->data ['DoList'] ['id'])) {
											 $disable = 'disabled';
											 echo $this->Form->input("DoList.project_id", array(
												 "type" => "hidden"
											 ));
										 }
										 $projectArr = array();
										 if (isset($projects) && !empty($projects)) {
											 foreach ($projects as $k => $v) {
												 $projectArr [$k] = html_entity_decode($v);
											 }
										 }
										 ?>

                                            <?php echo $this->Form->input("DoList.project_id", array("required" => false, "empty" => "Select Project", "id" => "ProjectId", "class" => "form-control aqua project_id", $disable, "options" => $projectArr, "label" => false, "div" => false, 'default' => $project)); ?>

                                        </label></li>
                                    <li class="noprojectlabel">
                                        <?php
                                        if (!isset($this->data ['DoList'] ['project_id']) && empty($this->data ['DoList'] ['project_id'])) {
                                            ?>
                                            <script
                                                type="text/javascript">
												$(function () {
													$("label[for=NoProject]").trigger('click');
												})
                                            </script>
                                            <?php
                                        }
                                        ?>
                                        <input
                                        <?php echo (isset($this->data['DoList']['project_id']) && empty($this->data['DoList']['project_id'])) ? 'checked="checked"' : ''; ?>
                                            type="checkbox" <?php //echo $disable; ?> id="NoProject"
                                            name="data[DoList][no_project]" class="fancy_input"   />
                                        <label class="fancy_label" for="NoProject">No Project</label>
                                    </li>
                                    <li class="noprojectlabel">
                                        <?php if(isset($this->data ['DoList']['id']) && !empty($this->data ['DoList']['id'])){
                                            $nudge_type = 'todo';
                                            if (isset($this->data['DoList']['parent_id']) && !empty($this->data['DoList']['parent_id'])) {
                                                $nudge_type = 'sub-todo';
                                            }
                                            ?>
                                            <span class="ico-nudge ico-todo tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $this->data['DoList']['project_id'], 'todo' => $this->data ['DoList']['id'], 'type' => $nudge_type, 'admin' => false)); ?>"></span>
                                        <?php } ?>
                                    </li>
                                </ul>
                                <div class="ideacast-button-right">
                                    <?php if (isset($this->data['DoList']['parent_id']) && empty($this->data['DoList']['parent_id'])) { ?>
                                        <a
                                            class="btn btn-warning collepse btn-sm btn_sub_top"
                                            role="button" href="#create_sub_panel" aria-expanded="false"
                                            aria-controls="create_sub_panel" id="btn_sub_top"><i
                                                class="fa fa-plus"></i> Create Sub To-do</a>
                                        <?php } ?>
                                        <?php

                                        $doid = (isset($this->data ['DoList'] ['id']) && !empty($this->data ['DoList'] ['id'])) ? $this->data ['DoList'] ['id'] : '';
                                        $dopid = (isset($this->data ['DoList'] ['project_id']) && !empty($this->data ['DoList'] ['project_id'])) ? $this->data ['DoList'] ['project_id'] : '';
                                        $button_text = (isset($this->data ['DoList'] ['id']) && !empty($this->data ['DoList'] ['id'])) ? 'Update' : 'Create';
                                        echo $this->Form->submit($button_text, array(
                                            "div" => false,
                                            "label" => false,
                                            "data-project-id" => $dopid,
                                            "data-do-id" => $doid,
                                            "class" => "btn btn-success btn-sm",
                                            "id" => "filter_list"
                                        ));

                                        ?>
                                    <a class="btn btn-danger btn-sm" href="<?php echo SITEURL ?>todos/index<?php if (isset($this->data['DoList']['project_id']) && !empty($this->data['DoList']['project_id'])) {
                                            echo '/project:' . $this->data['DoList']['project_id'];
											if (isset($doid) && !empty($doid)) {
											echo '/dolist_id:' . $doid;
											}
                                        }   else if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
                                            echo '/project:' . $this->params['named']['project'];
											if (isset($doid) && !empty($doid)) {
											echo '/dolist_id:' . $doid;
											}
                                        }else{

											if (isset($doid) && !empty($doid)) {
											echo '/dolist_id:' . $doid;
											}
										}  ?>"id=" "> Cancel </a>
                                </div>
                            </div>


                        </div>

                        <div class="box-body clearfix" style="min-height: 200px">
                            <div class=" ">
                                <div class="form-group col-lg-12">
                                    <label for="title"><?php echo $sub; ?>To-do:</label>
<?php echo $this->Form->input("DoList.title", array("required" => false, "placeholder" => "Max chars allowed 100", "id" => "title", "class" => "form-control editor_title", "label" => false, "div" => false)); ?>
                                    <span id="counter"
                                          class="error-message error text-danger"></span>

                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="users">Assigned To:</label>
                                    <?php

	if( isset($this->request->data ['DoList']['project_id']) && !empty($this->request->data ['DoList']['project_id']) ){
		$pd = $this->request->data ['DoList']['project_id'];
	} else if( isset($this->request->params['named']['project']) && !empty($this->request->params['named']['project'])  ){
		$pd = $this->request->params['named']['project'];
	} else {
		$pd ='';
	}

									$users = $this->ViewModel->todo_People_all($pd,$this->Session->read("Auth.User.id"));


									$users = Hash::extract($users, '{n}.{n}');
									$users = json_encode($users);


                                    $users = json_decode($users, true);

                                    $userArr = array();
                                    if (isset($users) && !empty($users)) {
                                        foreach ($users as $val_u) {
                                            $userArr [$val_u["id"]] = $val_u["name"];
                                        }
                                    }
                                    ?>

                                    <?php
                                    //$selected_users = array( $this->Session->read('Auth.User.id'));
                                    $selected_users =  array();
                                    if (isset($this->request->data ['DoListUser']) && !empty($this->request->data ['DoListUser'])) {
                                        $selected_users = Set::extract($this->request->data, '/DoListUser/user_id');
                                    }

                                    // echo $this->Form->select("DoListUser.user_id", $userArr, array("title" => "Select User", "selected" => $selected_users, "multiple" => "multiple", "id" => "users", "class" => "form-control aqua", "label" => false, "div" => false, "style" => "display: none;", "data-width" => "100%"));
									//pr( $selected_users);

                                    echo $this->Form->input('DoListUser.user_id', array(
                                        'type' => 'select',
                                        'options' => $userArr,
                                        'selected' => $selected_users,
                                        "multiple" => "multiple",
                                        "id" => "users",
                                        "class" => "form-control aqua",
                                        "label" => false,
                                        "div" => false,
                                        "style" => "display: none;",
                                        "data-width" => "100%"
                                    ));
                                    ?>

                                    <span
                                        class="error-message text-danger"></span>

                                </div>

                                <?php
                                if (!empty($this->request->data ["DoList"] ["start_date"]) && !empty($this->request->data ["DoList"] ["end_date"])) {
                                    $this->request->data ["DoList"] ["dateby"] = date("d M Y", strtotime($this->request->data ["DoList"] ["start_date"])) . ' - ' . date("d M Y", strtotime($this->request->data ["DoList"] ["end_date"]));
                                }
                                ?>
                                <div class="form-group col-lg-6">
                                    <label for="dateby_parent">Date From - To:</label>
                                    <div class="input-group">
                                        <?php
                                        $dateid = "datepicker_00";

                                        if (isset($this->request->data["DoList"]["parent_id"]) && $this->request->data["DoList"]["parent_id"] > 0) {
                                            $dateid = "datepicker_01";
                                        }

                                        echo $this->Form->input("DoList.dateby", array(
                                            "readonly" => "readonly",
                                            "id" => $dateid,
                                            "class" => "date-picker form-control",
                                            "label" => false,
                                            "div" => false,
                                            "style" => "cursor: pointer !important;"
                                        ));
                                        ?>


                                        <div
                                            class="input-group-addon  open-end-date-picker calendar-trigger">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
									<span style=""
                                              class="error-message text-danger"></span>
                                </div>

                                <div class="form-group col-lg-12">
                                    <label for="doc_file">Upload:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="uploadblackicon"></i>
                                        </div>
                                        <span title=""
                                              class="docUpload icon_btn bg-white border-radius-right tipText"
                                              data-original-title="Click to upload multiple files">
												<?php echo $this->Form->input('DoListUpload.file_name.', [ 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload-input dolist-uploads', 'id' => '', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]); ?> <span
                                                class="text-blue" id="upText">Upload Multiple Documents</span>
                                        </span> <span class="error-message text-danger"></span>

										<input type="hidden" name="upload_files" id="uploaded_files" value="" style="width: 50%;height: 250px;" >

                                    </div>

                                    <ul class="list-group" id="dolist_uploads_list">
                                        <?php
                                        if (isset($this->data ['DoListUpload']) && !empty($this->data ['DoListUpload'])) {
                                            foreach ($this->data ['DoListUpload'] as $k => $up) {
                                                if (isset($up ['id']) && !empty($up ['id'])) {
                                                    ?>
                                                    <li
                                                        class="todoimg list-group-item">
															<a href="<?php echo SITEURL . TODO; ?><?php echo $up['file_name'] ?>" class="todoimglink" download ><?php echo $up['file_name'] ?></a>
                                                        <span class="del-img-todo pull-right">
															<a data-id="<?php echo $up['id'] ?>" data-type="dolist" title="Click Here To Remove" class="text-red tipText doc_delete icon-round-redbg" href="javascript:void(0);"> <i class="fa fa-trash"></i>
                                                            </a>
                                                        </span></li>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>




                            </div>
                        </div>

<?php echo $this->Form->end(); ?>


                        <div class="box-body clearfix"
                             style="min-height: 700px">
<?php if (isset($this->data['Children']) && !empty($this->data['Children'])) { ?>
                                <div
                                    class="thired col-lg-12 created_sublists"
                                    style="margin-bottom: 15px;">
                                <?php echo $this->element('../Todos/partials/sublist_panel', ['data' => $this->data['Children']]); ?>
                                </div>
                            <?php } ?>
<?php if (isset($this->data['DoList']['parent_id']) && empty($this->data['DoList']['parent_id'])) { ?>
                                <form class="subtodoas" method="post"
                                      enctype="multipart/form-data" action="" onsubmit="submitfrmsub" >

                                    <div class="col-lg-12 margin-bottom">
                                        <a class="btn btn-warning btn-sm create_sub_panel btn_sub_top"
                                           role="button" href="#create_sub_panel" id="btn_sub_down"><i
                                                class="fa fa-plus"></i> Create Sub To-do</a>
                                    </div>

                                    <div class="col-lg-12" style="display: none;"
                                         id="create_sub_panel">
                                        <div class="panel panel-default" id="panel_sub">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Create Sub To-do</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group col-lg-12">
                                                    <label for="title">Sub To-do:</label>
                                                    <?php
                                                    echo $this->Form->input("DoList.parent_id", array(
                                                        "value" => $this->request->data ['DoList'] ['id'],
                                                        "type" => "hidden"
                                                    ));
                                                    echo $this->Form->input("DoList.project_id", array(
                                                        "value" => $this->request->data ['DoList'] ['project_id'],
                                                        "type" => "hidden"
                                                    ));

                                                    echo $this->Form->input("DoList.title", array(
                                                        "placeholder" => "Max chars allowed 100",
                                                        "value" => '',
                                                        "id" => "title",
                                                        "class" => "form-control editor_title",
                                                        "label" => false,
                                                        "div" => false
                                                    ));
                                                    ?>
                                                    <span
                                                        id="counter_01" class="error-message error text-danger"></span>

                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="users">Assigned To:</label>
                                                    <?php


													$users = $this->ViewModel->todo_People_all($this->request->data ['DoList'] ['project_id'],$this->Session->read("Auth.User.id"));


													$users = Hash::extract($users, '{n}.{n}');
													$users = json_encode($users);
                                                    $users = json_decode($users, true);
                                                    $userArr = array();
                                                    if (isset($users) && !empty($users)) {
                                                        foreach ($users as $val_u) {
                                                            $userArr [$val_u ["id"]] = $val_u["name"];
                                                        }
                                                    }
                                                    ?>

    <?php echo $this->Form->input("DoListUser.user_id", array("title" => "Select User", "multiple" => "multiple", "id" => "sub-users", "class" => "form-control aqua", "options" => $userArr, "type" => 'select', "label" => false, "div" => false, "style" => "display: none;", "data-width" => "100%")); ?>

                                                    <span
                                                        class="error-message text-danger"></span>

                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <label for="dateby_parent">Date By:</label>
                                                    <div class="input-group">
                                                        <?php
                                                        echo $this->Form->input("DoList.dateby", array(
                                                            "readonly" => "readonly",
                                                            "id" => "datepicker_01",
                                                            "value" => '',
                                                            "class" => "date-picker form-control",
                                                            "label" => false,
                                                            "div" => false,
                                                            "style" => "cursor: pointer !important;"
                                                        ));
                                                        ?>
                                                        <div
                                                            class="input-group-addon  open-end-date-picker calendar-trigger">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
													<span style=""
                                                              class="error-message text-danger"></span>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label for="doc_file">Upload:</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="uploadblackicon"></i>
                                                        </div>
                                                        <span title=""
                                                              class="docUpload icon_btn bg-white border-radius-right tipText"
                                                              data-original-title="Click to upload multiple files">
                                                            <?php echo $this->Form->input('DoListUpload.file_name.', [ 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload-input sub-do-create-dolist-uploads', 'id' => '', 'placeholder' => 'Upload Multiple Files', "multiple" => "multiple"]); ?> <span
                                                                class="text-blue" id="upText">Upload Multiple Documents</span>
                                                        </span> <span class="error-message text-danger"></span>
                                                    </div>

                                                    <ul class="list-group" id="dolist_uploads_list">
                                                    </ul>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <?php
                                                    // #vikas $button_text = (isset($this->data) && !empty($this->data)) ? 'Update' : 'Add';
                                                    echo $this->Form->submit("Save", array(
                                                        "div" => false,
                                                        "label" => false,
                                                        "class" => "btn btn-success btn-sm submit_subtodo",
                                                        "id" => "sub_todosave"
                                                    ));
                                                    ?>
                                                    <a
                                                        class="btn btn-danger btn-sm "
                                                        href="<?php echo SITEURL ?>todos/index<?php if (isset($this->data['DoList']['project_id']) && !empty($this->data['DoList']['project_id'])) {
                                            echo '/project:' . $this->data['DoList']['project_id'];
                                        } if (isset($doid) && !empty($doid)) {
											echo '/dolist_id:' . $doid;
											} ?>" id="subtodocancel"> Cancel </a>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>

                        </div>




                    </div>
                </div>

            </div>

        </div>

        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog"
             tabindex="-1" id="popup_model_box" class="modal modal-success fade">
            <div class="modal-dialog">
                <div class="modal-content"></div>
            </div>
        </div>

    </div>
</div>


<div id="confirm_box_img_del" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">&times;</button>
                <h4 class="modal-title">Delete confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Do you want to delete To-do attachment you sure to before click
                    delete?</p>
                <p class="text-warning">
                    <small>If you click on delete, your To-do attachment will be lost.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete-yes" class="btn btn-success">Delete</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
