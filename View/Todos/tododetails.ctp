<?php
echo $this->Html->css('projects/todo');
echo $this->Html->script('projects/todo', array('inline' => true));

echo $this->Html->css('projects/dropdown');

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<div class="row tododetail">
    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    <?php echo $page_heading; ?>
                    <p class="text-muted date-time">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
        </div>
        <div class="row">
            <div class="col-xs-12 msg_box">
                <?php echo $this->Session->flash(); ?>
            </div>
        </div>
        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">




                        <div class="box-header" style="background: #f5f5f5 none repeat scroll 0 0; border-top: 3px solid #d2d6de;" >
                            <div class="col-lg-12 content-header clearfix nopadding-left sb_blog_parent">
                                <div class="btn btn-group action pull-left" style="margin-top: 3px;">
                                    <span class="end_eople bg-black sb_blog" style="cursor:default;">
                                        End:
                                        <?php
                                        if (isset($tododata['DoList']['end_date']) && !empty($tododata['DoList']['end_date'])) {
                                            //echo date("d M Y", strtotime($tododata['DoList']['end_date']));
											echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($tododata['DoList']['end_date'])),$format = 'd M Y');
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>

                                    </span>
                                    <?php
                                    ?>
                                    <span class="end_eople bg-blue sb_blog" id="getAllUserOnThisTodo" data-remote="<?php echo SITEURL; ?>todos/getAllUserOnThisTodo/<?php echo $tododata['DoList']['id']; ?>" data-toggle="modal" data-target="#userModal" data-id="<?php echo $tododata['DoList']['id']; ?>">

                                        People on <?php echo isset($type) && !empty($type) ? $type : ''; ?> To-do <?php
if (isset($todouserdata) && !empty($todouserdata)) {

    if (isset($todouserdata) && !empty($todouserdata)) {
        echo count($todouserdata) + 1;
    }
}
?>
                                    </span>
                                </div>
                                <div class="btn action pull-right" style="cursor:default;">
<?php
$accept = "accept";
$reject = "Decline";
$classAc = '';
$classRej = '';
if (isset($tododata['DoListUser']['approved']) && !empty($tododata['DoListUser']['approved']) && $tododata['DoListUser']['approved'] == 1) {
    $accept = "accepted";
    $classAc = 'disable';
}
if (isset($tododata['DoListUser']['approved']) && !empty($tododata['DoListUser']['approved']) && $tododata['DoListUser']['approved'] == 2) {
    $reject = "Declined";
    $classRej = 'disable';
}
if (isset($todosubdata['DoListUser']['approved']) && !empty($todosubdata['DoListUser']['approved']) && $todosubdata['DoListUser']['approved'] == 1) {
    $accept = "accepted";
    $classAc = 'disable';
}
if (isset($todosubdata['DoListUser']['approved']) && !empty($todosubdata['DoListUser']['approved']) && $todosubdata['DoListUser']['approved'] == 2) {
    $reject = "Declined";
    $classRej = 'disable';
}
?>
                                    <?php
                                    //pr($tododata['DoListUser']);
                                    $status = $this->requestAction(array("action" => "get_status", $tododata['DoList']['id']));
                                    if (isset($tododata) && !empty($tododata)) {
                                        if ($tododata['DoListUser']['approved'] == 1) {
                                            // $status = $this->requestAction(array("action"=>"get_status",$tododata['DoList']['id']));
                                            echo $status;
                                            ?>

                                            <?php
                                        } else if ($tododata['DoListUser']['approved'] == 2) {
                                            // $status = $this->requestAction(array("action"=>"get_status",$tododata['DoList']['id']));
                                            echo 'Declined'; //$status;
                                            ?>

                                            <?php
                                        } else if ($tododata['DoList']['sign_off'] == 1) {
                                            // $status = $this->requestAction(array("action"=>"get_status",$tododata['DoList']['id']));
                                            echo $status;
                                        } else if (($tododata['DoList']['sign_off'] == 0) && ($tododata['DoList']['start_date'] <= date('Y-m-d') || empty($tododata['DoList']['start_date'])) && ($status != 'Overdue')) {
                                            ?>
                                            <button data-original-title="<?php echo ucfirst($accept); ?>" class="btn <?php echo $classAc; ?> btn-sm btn-success request_action tipText" style="" data-title="ToDoUser" type="button" data-remote="<?php echo SITEURL ?>todos/AcceptToDoRequest/<?php echo $tododata['DoListUser']['id']; ?>/1" id="accept" data-id="<?php echo $tododata['DoListUser']['id']; ?>" data-accept="1">Accept</button>
                                            <button data-original-title="<?php echo ucfirst($reject); ?>" class="btn <?php echo $classRej; ?> btn-sm btn-danger request_action tipText" data-title="ToDoUser" type="button" data-remote="<?php echo SITEURL ?>todos/AcceptToDoRequest/<?php echo $tododata['DoListUser']['id']; ?>/2" id="reject" data-id="<?php echo $tododata['DoListUser']['id']; ?>" data-accept="2">Decline</button>

                                            <?php
                                        } else if ($status == 'Overdue' && $tododata['DoList']['sign_off'] == 0) {
                                            echo 'Overdue';
                                        } else if ($status == 'Not Started') {
                                        ?>
                                       <button data-original-title="<?php echo ucfirst($accept); ?>" class="btn <?php echo $classAc; ?> btn-sm btn-success request_action tipText" style="" data-title="ToDoUser" type="button" data-remote="<?php echo SITEURL ?>todos/AcceptToDoRequest/<?php echo $tododata['DoListUser']['id']; ?>/1" id="accept" data-id="<?php echo $tododata['DoListUser']['id']; ?>" data-accept="1">Accept</button>
                                            <button data-original-title="<?php echo ucfirst($reject); ?>" class="btn <?php echo $classRej; ?> btn-sm btn-danger request_action tipText" data-title="ToDoUser" type="button" data-remote="<?php echo SITEURL ?>todos/AcceptToDoRequest/<?php echo $tododata['DoListUser']['id']; ?>/2" id="reject" data-id="<?php echo $tododata['DoListUser']['id']; ?>" data-accept="2">Decline</button>
                                        <?php
                                            //echo $status;
                                        }
                                    }
                                    ?>

                                </div>

                            </div>
                        </div>

                        <div class="box-body">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default" id="panel1">
                                    <div class="panel-heading bg-green">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
<?php echo isset($type) && !empty($type) ? $type : ""; ?> To-do details
                                            </a>


<?php
if ((!empty($tododata['DoListUser']['approved']) && $tododata['DoListUser']['approved'] == 1)) {
    $link_project = '';
    if (isset($tododata['DoList']['project_id']) && !empty($tododata['DoList']['project_id'])) {
        $link_project = '/project:' . $tododata['DoList']['project_id'];
    }

    $link_todo = '';
    if (isset($tododata['DoList']['id']) && !empty($tododata['DoList']['id'])) {
        $link_todo = '/dolist_id:' . $tododata['DoList']['id'];
    }
    ?>
                                                <div style="" class="btn btn-xs btn-default pull-right">
                                                    <a class="opentodo" href="<?php echo SITEURL; ?>/todos/index<?php echo $link_project ?><?php echo $link_todo ?>"><i class="fa fa-folder-open"></i></a>
                                                </div>
                                                <?php
                                            }
                                            ?>


                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" style="border:1px solid #67A028">
                                        <div class="panel-body">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Project Title: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                            <?php
                                            if (isset($tododata) && !empty($tododata)) {
                                                if (isset($tododata['DoList']['project_id']) && !empty($tododata['DoList']['project_id'])) {
                                                    $project = project_detail($tododata['DoList']['project_id']);
                                                    echo ucfirst(strip_tags($project['title']));
                                                } else {
                                                    echo "Unspecified";
                                                }
                                            }
                                            ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">
                                                    <?php echo isset($type) && !empty($type) ? $type : ''; ?>To-do: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                    if (isset($tododata) && !empty($tododata)) {
                                                        if (isset($tododata['DoList']['title']) && !empty($tododata['DoList']['title'])) {
                                                            echo ucfirst(Sanitize::html($tododata['DoList']['title']));
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row"  style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Requested By: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                    if (isset($tododata) && !empty($tododata)) {
                                                        if (isset($tododata['DoList']['user_id']) && !empty($tododata['DoList']['user_id'])) {
                                                            ?>
                                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $tododata['DoList']['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                                <i class="fa fa-user"></i>
                                                            </a>
        <?php
        echo $this->Common->userFullname($tododata['DoList']['user_id']);
    }
}
?>

                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Completed By: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                    if (isset($tododata['DoList'])) {
                                                        if (isset($tododata['DoList']['sign_off']) && $tododata['DoList']['sign_off'] == 1) {
                                                            ?>
                                                            <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $tododata['DoList']['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                                <i class="fa fa-user"></i>
                                                            </a>
        <?php
        //echo $this->Session->read("Auth.User.id").'<br>';
        echo $this->Common->userFullname($tododata['DoList']['user_id']);
    } else {
        echo "N/A";
    }
}
?>

                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Completed On: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">

                                                    <?php
                                                    if (isset($tododata['DoList'])) {
                                                        if (isset($tododata['DoList']['sign_off']) && $tododata['DoList']['sign_off'] == 1) {
                                                            //echo date("d M Y", strtotime($tododata['DoList']['modified']));
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($tododata['DoList']['modified'])),$format = 'd M Y');
                                                        } else {
                                                            echo 'Not Yet';
                                                        }
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Accepted On: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">

                                                    <?php
                                                    if (isset($tododata['DoListUser']['approved']) && $tododata['DoListUser']['approved'] != 0) {
                                                        if (isset($tododata['DoListUser']['modified'])) {
                                                            //echo date("d M Y", strtotime($tododata['DoListUser']['modified']));
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($tododata['DoListUser']['modified'])),$format = 'd M Y');
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    }else{
                                                        echo 'N/A';
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Attachments: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                    if (isset($tododata) && !empty($tododata)) {

                                                        if (isset($tododata['DoListUpload']) && !empty($tododata['DoListUpload'])) {

                                                            if (isset($tododata['DoListUpload'][0]) && !empty($tododata['DoListUpload'][0])) {
                                                                echo '<ul class="list-unstyled doc-type">';
                                                                foreach ($tododata['DoListUpload'] as $key => $upload) {
                                                                    if (isset($upload['file_name_original']) && $upload['file_name_original'] != '') {
                                                                        $output_dir = TODO;
                                                                        $ext = pathinfo($upload['file_name_original']);

                                                                        //pr($ext);
                                                                        echo '<li><span  class="btn btn-xs extension_text">' . $ext['extension'] . '</span><a download  href="' . SITEURL . TODO . $upload['file_name'] . '">' . $ext['filename'] . '</a></li>';
                                                                    }
                                                                }
                                                                echo '</ul>';
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" >
    $(document).ready(function () {
        $("#popup_model_box_profile").on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html("")
        })
        $("body").delegate("#getAllUserOnThisTodo1", 'click', function (event) {
            var $current = $(this);
            $.ajax({
                type: 'POST',
                url: $current.attr("data-url"),
                global: true,
                success: function (response) {
                    $("#userModal .modal-body").html(response);
                    $("#userModal").modal()

                }
            });

        })

        $("body").delegate(".request_action", 'click', function (event) {
            var $t = $(this);
            if ($t.attr("id") == 'reject') {
                BootstrapDialog.show({
                    title: 'To-do Request',
                    message: 'Are you sure you want to decline this <?php echo isset($type) && !empty($type) ? $type : ""; ?> To-do request?',
                    type: BootstrapDialog.TYPE_DANGER,
                    draggable: true,
                    buttons: [{
                            label: 'Decline',
                            cssClass: 'btn-success',
                            autospin: true,
                            action: function (dialogRef) {
                                ajaxdatasend($t);
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                //dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 1500);
                                setTimeout(function () {
                                    window.location.reload()
                                }, 1550);
                            }
                        }, {
                            label: 'Cancel',
                            cssClass: 'btn-danger',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }
                        }]
                });
            } else if ($t.attr("id") == 'accept') {
                BootstrapDialog.show({
                    title: 'To-do Request',
                    message: 'Are you sure you want to accept this <?php echo isset($type) && !empty($type) ? $type : ""; ?> To-do request?',
                    type: BootstrapDialog.TYPE_SUCCESS,
                    draggable: true,
                    buttons: [{
                            //icon: '',
                            label: 'Accept',
                            cssClass: 'btn-success',
                            autospin: true,
                            action: function (dialogRef) {
                                ajaxdatasend($t);
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 1500);
                                setTimeout(function () {
                                    window.location.reload()
                                }, 1550);
                            }
                        }, {
                            label: 'Cancel',
                            cssClass: 'btn-danger',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }
                        }]

                });
            } else if ($t.attr("id") == 'accepted') {
                BootstrapDialog.alert({
                    title: 'Request confirmation info',
                    message: 'You have already accepted this <?php echo isset($type) && !empty($type) ? $type : ""; ?>  To-do request?',
                    type: BootstrapDialog.TYPE_INFO,
                    draggable: true,
                });
            } else if ($t.attr("id") == 'rejected') {
                BootstrapDialog.alert({
                    title: 'Request rejection info',
                    message: 'You have already rejected this <?php echo isset($type) && !empty($type) ? $type : ""; ?>  To-do request?',
                    type: BootstrapDialog.TYPE_INFO,
                    draggable: true,
                });
            }
        });
    })
    function ajaxdatasend($t) {
        var $t = $t,
                dataid = $t.attr('data-id'),
                id = $t.attr('id'),
                data = $t.data(),
                url = data.remote,
                accept = $t.attr('data-accept');
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: url,
            global: false,
            success: function (response) {
                if (response.success) {

                }
            }
        });
    }
</script>

<style>

    .doc-type .txt::before {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .jpg::before, .doc-type .jpeg::before, .doc-type .png::before, .doc-type .gif::before {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .doc::before, .doc-type .docx::before {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .xls::before, .doc-type .xlsx::before {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .pdf::before  {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .rar::before, .doc-type .zip::before  {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type .ppt::before, .doc-type .pptx::before  {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }

    .doc-type .html::before, .doc-type .htm::before, .doc-type .php::before  {
        color: #333;
        content: "";
        font-family: FontAwesome;
        margin: 0 8px 0 0;
    }
    .doc-type   {
        background: none;
        padding: 0;
    }
    .doc-type  {
        margin: 0 !important;
    }
    .btns{
        margin-top: -8px;
    }
    .tododetail a:hover, .tododetail a:active, .tododetail a:focus{ color:#fff;}
    .end_eople{
        cursor: pointer;
        margin-right: 5px;
        padding: 7px 8px;
    }

    .list-unstyled.doc-type > li {
        padding: 3px;
    }
    .extension_text {
        padding: 0 5px 4px 6px;
        background-color: #00c0ef;
        border-color: #00acd6;
        color: #ffffff;
        margin: 0 5px 0 0;
    }
    .opentodo:hover,.opentodo:active,.opentodo:visited{
        color:#000;
    }
</style>
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="popup_model_box_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<!-- END MODAL BOX -->
<!-- Modal -->
<div class="modal fade" id="userModal" role="dialog">
    <div class="modal-dialog modal-people">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header  bg-green">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">People on <?php echo isset($type) && !empty($type) ? "Sub To-do" : "To-do"; ?> </h3>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>