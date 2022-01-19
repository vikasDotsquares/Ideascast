<?php
echo $this->Html->css('projects/todo');
echo $this->Html->script('projects/todo', array('inline' => true));

echo $this->Html->css('projects/dropdown');


echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<style>
#userModal .modal-dialog{ max-width:450px;  }
</style>
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




                        <div class="box-header" style="background: #f1f3f4 none repeat scroll 0 0; border-top: 3px solid #d2d6de;" >
                            <div class="col-lg-12 content-header clearfix nopadding-left sb_blog_parent">
                                <?php
                                $project_people = $this->requestAction(array("action"=>"get_all_user_on_this_wiki_count",$data['Wiki']['project_id'],$this->Session->read("Auth.User.id"),$data['Wiki']['id']));

                                $allwikipageusers = $this->Wiki->getWikiAllUserLists($data['Wiki']['project_id'], $this->Session->read("Auth.User.id"),$data['Wiki']['id']);

                                $allwikipageusers = array_merge($allwikipageusers,$project_people);
                                $allwikipageusers = array_unique($allwikipageusers);

                                ?>
                                <div class="btn btn-group action pull-left bg-blue sb_blog" style="">
                                    <span class="end_eople" id="getAllUserOnThisWiki" data-url="<?php echo SITEURL; ?>wikies/get_all_user_on_this_wiki/<?php echo $data['Wiki']['project_id']."/".$this->Session->read("Auth.User.id")."/".$data['Wiki']['id']; ?>" data-id="<?php echo $data['Wiki']['id']; ?>">

                                       People on Wiki <?php echo ( isset($allwikipageusers) && !empty($allwikipageusers) ) ? count($allwikipageusers) : 0;?>
                                    </span>
                                </div>
                                <div class="btn  action pull-right">
                                    <?php
                                    $accept = "accept_request";
                                    $reject = "reject_reject";
                                    $classAc = '';
                                    $classRej = '';

                                    if (isset($data['WikiUser']['approved']) && !empty($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 1) {
                                        $reject = "accepted";
                                        $classRej = 'disable';
                                    }
                                    if (isset($data['WikiUser']['approved']) && !empty($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 2) {
                                        $accept = "rejected";
                                        $classAc = 'disable';
                                    }

                                    ?>


                                </div>

                            </div>
                        </div>

                        <div class="box-body">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default" id="panel1">
                                    <div class="panel-heading bg-green">
                                        <h4 class="panel-title wikidetails_page">
                                            <a class="">Wiki details</a>

                                            <?php
                                            if(isset($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 1){
                                            ?>
                                            <div style="" class="btn btn-xs btn-default pull-right">
                                                <a class="opentodo" href="<?php echo SITEURL; ?>/wikies/index/<?php echo $data['Wiki']['project_id'];?>"><i class="fa fa-folder-open"></i></a>
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
                                                    if (isset($data) && !empty($data)) {
                                                        if (isset($data['Wiki']['project_id']) && !empty($data['Wiki']['project_id'])) {
                                                            $project = project_detail($data['Wiki']['project_id']);
                                                            echo ucfirst(strip_tags($project['title']));
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Wiki Title: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                        if (isset($data['Wiki']['title']) && !empty($data['Wiki']['title'])) {
                                                            echo ucfirst(Sanitize::html($data['Wiki']['title']));
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Wiki Description: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                        if (isset($data['Wiki']['description']) && !empty($data['Wiki']['description'])) {
                                                            //echo ucfirst(Sanitize::html($data['Wiki']['description']));
                                                            echo $data['Wiki']['description'];
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Requested by: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">

                                                    <?php
                                                    if (isset($data['WikiUser']['user_id']) && !empty($data['WikiUser']['user_id'])) {
                                                        ?>
                                                        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['WikiUser']['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                            <i class="fa fa-user"></i>
                                                        </a>
                                                        <?php
                                                        echo $this->Common->userFullname($data['WikiUser']['user_id']);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Accepted On: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">
                                                    <?php
                                                    if(isset($data['WikiUser']['updated']) && !empty($data['WikiUser']['updated']) && $data['WikiUser']['approved'] != 0){
                                                        //echo date("d M Y", $data['WikiUser']['updated']);
														echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$data['WikiUser']['updated']),$format = 'd M Y');
                                                    }else{
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-sm-4 col-md-3 col-lg-2 text-bold">Request: </div>
                                                <div class="col-sm-8 col-md-9 col-lg-10">

                                                    <?php

                                                    if(isset($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 0){
                                                    ?>
                                                    <a href="" class="btn btn-success btn-xs tipText request_action" data-remote="<?php echo SITEURL;?>wikies/requestsave/<?php echo $data['WikiUser']['id'];?>/1" id="1" title="Accept Request" data-id="<?php echo $data['WikiUser']['id'];?>" ><i class="fa fa-check"></i></a>
                                                    <a href="" class="btn btn-danger btn-xs tipText request_action" data-remote="<?php echo SITEURL;?>wikies/requestsave/<?php echo $data['WikiUser']['id'];?>/2" id="2" title="Decline Request" data-id="<?php echo $data['WikiUser']['id'];?>"  ><i class="fa fa-close"></i></a>
                                                    <?php
                                                    }
                                                    if(isset($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 1){
                                                        echo 'Accepted';
                                                    }
                                                    if(isset($data['WikiUser']['approved']) && $data['WikiUser']['approved'] == 2){
                                                         echo 'Declined';
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
<div class="modal fade" id="userModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header  bg-green">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">People on Wiki </h4>
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
<script type="text/javascript" >
    $(document).ready(function () {
        $("#popup_modal_profile").on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        })

        $("body").delegate("#getAllUserOnThisWiki", 'click', function (event) {
            var $current = $(this);
            $.ajax({
                type: 'POST',
                url: $current.attr("data-url"),
                global: true,
                success: function (response) {
                    $("#userModals .modal-body").html(response);
                    $("#userModals").modal();
                    //$("#userModal .modal-dialog").addClass('modal-sm');

                }
            });

        })

        $(".request_action").click(function (event) {
            event.preventDefault();
            var $t = $(this),url = $t.data("remote");
            var type = $t.attr("id") == 1 ? "accept" : "decline";

            if ( $t.attr("id") != '' && type != '' ) {

                BootstrapDialog.show({
                    title: 'Confirmation',
                    message: 'Are you sure you want to '+type+' Wiki request?',
                    type: BootstrapDialog.TYPE_SUCCESS,
                    draggable: true,
                    buttons: [{
                            label: 'Yes',
                            cssClass: 'btn-success',
                            autospin: true,
                            action: function (dialogRef) {
                                $.ajax({
                                    type: 'POST',
                                    dataType: 'JSON',
                                    url: url,
                                    global: false,
                                    success: function (response) {
                                        if (response.success) {
                                            dialogRef.enableButtons(false);
                                            dialogRef.setClosable(false);
                                            setTimeout(function () {
                                                dialogRef.close();
                                            }, 1500);
                                            setTimeout(function () {
                                                window.location.reload()
                                            }, 1550);
                                        }
                                    }
                                });

                            }
                        }, {
                            label: 'Close',
                            cssClass: 'btn-danger',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }
                        }]
                });
            }
        });
    })

</script>


<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="popup_modal_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
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

<div class="modal fade" id="userModals" role="dialog">
    <div class="modal-dialog modal-people">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header  bg-green">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">People on Wiki</h3>
            </div>
            <div class="modal-body people">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>