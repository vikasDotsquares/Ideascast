<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<!--contant-->

<!-- <div class="risk-data-page"> -->
<?php
function asrt($a, $b) {
    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
    $t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
$current_org = $this->Permission->current_org();
if(isset($list) && !empty($list)){
    foreach ($list as $key => $value) {
        // pr($value);
        $data = $value['risk'];
        $mcr = $value['exp'];
        $others = $value[0];
        $assignee = $value['ruser']['assignee'];
        $leaders = $value['rleader']['leaders'];
        $risk_tasks = $value['rtask']['risk_tasks'];
        $assignee = (!empty($assignee)) ? json_decode($assignee, true) : [];
        $leaders = (!empty($leaders)) ? json_decode($leaders, true) : [];
        $risk_tasks = (!empty($risk_tasks)) ? json_decode($risk_tasks, true) : [];
        usort($assignee, 'asrt');
        // $assignee = array_map('trim', $assignee);
        usort($risk_tasks, 'asrt');
        $project_id = $data['project_id'];
        // pr($leaders);
        $leaders = (!empty($leaders))  ? Set::extract($leaders, '{n}.id') : [];
        $leaders[] = $current_user_id;

        $rd_status = (isset($data['rd_status']) && !empty($data['rd_status'])) ? $data['rd_status'] : ( (isset($others['rd_status']) && !empty($others['rd_status'])) ? $others['rd_status'] : '' );
        $creator_name = (isset($data['creator_name']) && !empty($data['creator_name'])) ? $data['creator_name'] : ( (isset($others['creator_name']) && !empty($others['creator_name'])) ? $others['creator_name'] : '' );
        // pr($leaders);
    ?>
            <!-- <div class="col-nomber">
                <div class="col-contant"><?php echo $key + 1; ?></div>
            </div> -->
            <div class="col-title" style="width: 27%;">
                <div class="col-contant ">
                    <span class="risk-title"  >

                        <?php if( in_array($current_user_id, $leaders) && $rd_status != 3){ ?>
                            <a class="title-link" href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", $data['id'], 'admin' => FALSE ), true ); ?>"><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?>
                            </a>
                        <?php }else{ ?>
                            <?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?>
                        <?php } ?>
                    </span>
                    <div class="loc-cc-name">
                        <a href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $project_id )); ?>" class="com-single-name tipText" title="Open Project" style="color: #777;"><?php echo htmlentities($data['ptitle'], ENT_QUOTES, "UTF-8"); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-people">
                <div class="col-contant">
                    <div class="peoplethumb">
                        <?php
                        $html = '';
                        if( $data['creator_id'] != $current_user_id ) {
                            $html = CHATHTML($data['creator_id'], $project_id);
                        }
                        $user_image = SITEURL . 'images/placeholders/user/user_1.png';
                        $user_name = htmlentities($creator_name, ENT_QUOTES, "UTF-8");
                        $profile_pic = $data['creator_pic'];
                        $job_title = htmlentities($data['creator_job'], ENT_QUOTES, "UTF-8");

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                            $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
                        }
                         ?>
						<div class="community-diff-list">
                        <a
                            href="#"
                            class="pophover-popup skill-popple-icon"
                            data-toggle="modal"
                            data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $data['creator_id'], 'admin' => FALSE ), true ); ?>"
                            data-target="#popup_modal"
                            data-content="<div><p><?php echo htmlspecialchars($user_name); ?></p><p><?php echo htmlspecialchars($job_title); ?></p><?php echo $html; ?></div>">
                            <img src="<?php echo ($user_image); ?>"  width="36" height="36">
                        </a>
                        <?php if($current_org['organization_id'] != $data['creator_org']){ ?>
                            <i class="communitygray18 tipText" style="cursor: pointer;" title="Not In Your Organization" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $data['creator_id'], 'admin' => FALSE ), true ); ?>" data-target="#popup_modal"></i>
                        <?php } ?>
						</div>
                        <?php
                        // $risk_leaders = risk_leader($data['id']);
                        // $risk_users = $this->ViewModel->risk_users($data['id']);

                        if(isset($assignee) && !empty($assignee)) {
                            $assigned_user_html = "<div class='el_users'>";
                            foreach ($assignee as $ukey => $users) {

                                $user_name = $users['title'];
                                $assigned_user_html .= "<span style='display: block; width: 100%;'>";
                                $assigned_user_html .= "<a data-toggle='modal' data-target='#popup_modal' data-remote='".Router::Url( array( "controller" => "shares", "action" => "show_profile", $users['id'], 'admin' => FALSE ), true )."' href='#'>";
                                $assigned_user_html .= "<i class='fa fa-user text-maroon'></i> ";
                                $assigned_user_html .= $user_name;
                                $assigned_user_html .= '</a>';
                                if(isset($leaders) && !empty($leaders)) {
                                    if(in_array($users['id'], $leaders)) {
                                        $assigned_user_html .= " <i class='fa fa-check text-blue' style='cursor: default;'></i> ";
                                    }
                                }
                                $assigned_user_html .= '</span>';
                            }
                            $assigned_user_html .= '</div>';
                        ?>
                        <a class="pophover-popup" data-content="<?php echo $assigned_user_html; ?>" style="cursor: default;">
                            <i class="teamblack"></i>
                        </a>
                        <?php }else{ ?>
                        <span class="spacing"></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-type">
                <div class="col-contant">
                    <?php
                    if(!empty($risk_tasks)){
                        $element_html = "<div class='pop-details'>";
                        foreach ($risk_tasks as $ekey => $evalue) {
                            $element_html .= "<span class='el-name'><a href='".Router::Url(['controller' => 'entities', 'action' => 'update_element', $evalue['id'], 'admin' => false], true)."'>".htmlentities($evalue['title'])."</a></span>";
                        }
                        $element_html .= '</div>';
                    ?>
                        <span class="type-iocn pophover-popup" title="Related Tasks" data-content="<?php echo $element_html; ?>"><i class="taskblack18" style="cursor: default;"></i></span>
                    <?php
                    }else{ ?>
                        <span class="type-iocn">&nbsp;</span>
                    <?php } ?>
                    <p class="rp_type"><?php echo htmlentities($data['risk_type'], ENT_QUOTES, "UTF-8"); ?></p>
                </div>
            </div>
            <div class="col-status">
                <div class="col-contant">
                    <?php echo $rd_status; ?>
                </div>
            </div>
            <div class="col-impect">
                <div class="col-contant">
                    <?php echo $others['rd_impact']; ?>
                </div>
            </div>
            <div class="col-parcent">
                <div class="col-contant">
                    <?php echo $others['rd_prob']; ?>
                </div>
            </div>
            <div class="col-when">
                <div class="col-contant">
                    <span class="deta"><?php echo (isset($data['rdate']) && !empty($data['rdate'])) ? date( 'd M, Y',strtotime($data['rdate'])) : 'N/A'; ?></span>
                    <?php
                    $exposer = $others['rd_exposure'];
                    if(isset($exposer) && !empty($exposer) && $exposer != 'None') {
                        $expClass = 'low';
                        if($exposer == 'Medium') $expClass = 'mid';
                        else if($exposer == 'High') $expClass = 'high';
                        else if($exposer == 'Severe') $expClass = 'severe';
                    ?>
                    <span class="colorbar tipText " title="<?php echo 'Risk Exposure: '.$exposer; ?>"> <i class="<?php echo $expClass; ?>">&nbsp;</i></span>
                    <?php
                    }
                    else {
                    ?>
                      <span class="colorbar navail"> <i class="">&nbsp;</i></span>
                    <?php } ?>
                </div>
            </div>
            <div class="col-action">
                <div class="col-contant col-actions flag-iocn-p">
                    <!-- <a class="risk-detail tipText" title="Risk Detail" href="#" data-toggle="modal" data-target="#modal_small" data-remote="<?php //echo Router::Url( array( "controller" => "risks", "action" => "risk_detail", $data['id'], 'admin' => FALSE ), true ); ?>" > <i class="view-icon"></i>
                    </a> -->
                    <?php
                    $profile_popover = '<div class="profile_popover">';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Mitigation</span>';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Contingency</span>';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Residual</span>';
                    $profile_popover .= '</div>';
                    if(!empty($mcr['contingency_exists']) || !empty($mcr['mitigation_exists']) || !empty($mcr['residual_exists'])){
                        $profile_popover = '<div class="profile_popover">';
                        if( !empty($mcr['contingency_exists']))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Mitigation</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Mitigation</span>';
                        }
                        if(!empty($mcr['mitigation_exists']))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Contingency</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Contingency</span>';
                        }
                        if(!empty($mcr['residual_exists']))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Residual</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Residual</span>';
                        }
                        $profile_popover .= '</div>';
                    }
                    $profile_class = 'btn-danger';
                    if(!empty($mcr['mitigation_exists']) && !empty($mcr['contingency_exists']))
                    {
                        $profile_class = 'btn-default';
                    }
                    if(!empty($mcr['residual_exists']) && (empty($mcr['mitigation_exists']) || empty($mcr['contingency_exists']))) {
                        $profile_class = 'btn-primary';
                    }

                    if(!empty($mcr['mitigation_exists']) && !empty($mcr['contingency_exists']) && !empty($mcr['residual_exists'])) {
                         $profile_class = 'btn-default';
                    }
                    ?>

                    <a class="<?php echo $profile_class; ?> risk-profile" title="Risk Profile" href="#" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "risk_profile", $data['id'], ($key+1), 'response', 'admin' => FALSE ), true ); ?>" data-content='<?php echo $profile_popover; ?>'> <i class="infoblack-icon"></i>
                    </a>
                    <?php
                    $btn_status = '';
                    if($rd_status == 'SignOff' || !in_array($current_user_id, $leaders) ){ // Signed-off or not a leader
                        $btn_status = 'disabled';
                    }
                    ?>
                    <a class="<?php echo $btn_status; ?> tipText edit-risk" title="Update Risk" href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", $data['id'], 'admin' => FALSE ), true ); ?>"> <i class="edit-icon"></i>
                    </a>

                    <?php
                    $status_data = ['class' => ' ', 'icon' => 'ps-flag bg-not_started']; // Open
                    $title = 'Open';
                    if($rd_status == 'Review'){
                        $status_data = ['class' => '', 'icon' => 'ps-flag bg-progressing'];
                        $title = 'In Progress';
                    }// Reviewing
                    else if($rd_status == 'SignOff'){
                        $status_data = ['class' => 'greens', 'icon' => 'ps-flag  bg-completed'];
                        $title = 'Completed';
                    } // Signed-off
                    else if($rd_status == 'Overdue'){
                        $status_data = ['class' => 'text-red', 'icon' => 'ps-flag bg-overdue'];
                        $title = 'Overdue';
                    } // Overdue
                    ?>
                    <a href="" class="<?php echo $status_data['class']; ?> tipText" title="<?php echo $title; ?>" data-id="<?php //echo $data['id']; ?>" data-status="<?php //echo $data['status']; ?>" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "risk_profile", $data['id'], ($key+1), 'status', 'admin' => FALSE ), true ); ?>" >
                        <i class="<?php echo $status_data['icon']; ?>"></i>
                    </a>
                    <!-- // PASSWORD DELETE -->
                    <a href="" class="<?php if( $data['creator_id'] != $current_user_id ) echo 'disabled'; ?> tipText trash_risk delete-an-item" title="Delete" data-risk="<?php echo $data['id']; ?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "delete_an_item", $data['id'], 'admin' => FALSE ), true ); ?>"> <i class="deleteblack"></i>
                    </a>
                </div>
            </div>
    <?php } // loop risks ?>
<?php } // if risks exists ?>
<!-- </div> -->

<script type="text/javascript">
    $(function(){

        $('.risk-profile').popover({
            placement: function(context, element) {
                var position = $(element).offset();
                if (position.top > ($(document).height() - 340)) {
                    return "top";
                }
                return "bottom";
            },
            // placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
        });
        /* $('.pophover-popup').popover({
            placement: function(context, element) {
                var position = $(element).offset();
                if (position.top > ($(document).height() - 340)) {
                    return "top";
                }
                return "bottom";
            },
            // placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {s */how: 50, hide: 400}
        });
        $('.risk-title').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
        .on('show.bs.popover', function(){

        })


        $('body').on('click', '.sign-off', function(event) {
            event.preventDefault();
            var $this = $(this),
                $parent = $(this).parents('.risk-contant:first'),
                key = $parent.data('key'),
                $prev = $parent.prev('.risk-contant:first'),
                risk_id = $(this).data('id'),
                status = $(this).data('status');

            BootstrapDialog.show({
                title: 'Confirmation',
                message: 'Are you sure you want to sign-off this risk?',
                type: BootstrapDialog.TYPE_DANGER,
                draggable: true,
                buttons: [{
                        icon: 'fa fa-check',
                        label: ' Yes',
                        cssClass: 'btn-success',
                        autospin: true,
                        action: function(dialogRef) {
                            $.when($.risk_status({id: risk_id, status: 3}))
                                .then(function(data, textStatus, jqXHR) {
                                    $.ajax({
                                        url: $js_config.base_url + 'risks/risk_list_one',
                                        type: 'POST',
                                        dataType: 'JSON',
                                        data: {'id': risk_id, 'key': key},
                                        success: function(response) {
                                            $parent.fadeOut(100, function(){
                                                if($prev.length > 0) {
                                                    $prev.after(response);
                                                }
                                                else{
                                                    $('.risk-heading-top').after(response);
                                                }
                                                $parent.remove();
                                                $('.tooltip').remove();
                                                $('.popover').remove();
                                            })
                                        }
                                    })

                                    dialogRef.enableButtons(false);
                                    dialogRef.setClosable(false);
                                    dialogRef.getModalBody().html('<div class="loader"></div>');
                                    setTimeout(function() {
                                        dialogRef.close();
                                        $.risk_projects();
                                    }, 500);
                                })
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
        });

    })

</script>