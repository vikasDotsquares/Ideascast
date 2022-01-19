<?php
$current_user_id = $this->Session->read('Auth.User.id');

function asrt($a, $b) {
    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
    $t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
$current_org = $this->Permission->current_org();
if(isset($project_risks) && !empty($project_risks)){
    foreach ($project_risks as $key => $value) {
        $data = $value['rd'];
        $risk_id = $data['id'];
        $risk_title = $data['title'];
        $risk_date = $data['rdate'];
        $risk_creator_id = $data['creator_id'];

        $project_id = $value['up']['project_id'];
        $ptitle = $value['p']['ptitle'];

        $creator = $value['ud'];
        $creator_pic = $creator['creator_pic'];
        $creator_job = $creator['creator_job'];
        $creator_org = $creator['creator_org'];

        $risk_type = $value['rt']['risk_type'];

        $others = $value[0];
        $rd_status = $others['rd_status'];
        $creator_name = $others['creator_name'];
        $contingency_exists = $others['contingency_exists'];
        $mitigation_exists = $others['mitigation_exists'];
        $residual_exists = $others['residual_exists'];
        $rd_percent = $others['rd_percent'];
        $rd_impact = $others['rd_impact'];
        $rd_exposure = $others['rd_exposure'];
        $ruser_count = $value['ruser']['ruser_count'];
        $rtask_count = $value['rtask']['rtask_count'];

        $assignee = $value['ruser']['assignee'];
        $leaders = $value['rleader']['leaders'];
        $risk_tasks = $value['rtask']['risk_tasks'];
        $assignee = (!empty($assignee)) ? json_decode($assignee, true) : [];
        $leaders = (!empty($leaders)) ? json_decode($leaders, true) : [];
        $risk_tasks = (!empty($risk_tasks)) ? json_decode($risk_tasks, true) : [];
        usort($assignee, 'asrt');
        // $assignee = array_map('trim', $assignee);
        usort($risk_tasks, 'asrt');
        $leaders = (!empty($leaders))  ? Set::extract($leaders, '{n}.id') : [];
        // $leaders[] = $current_user_id;

    ?>
        <div class="rs-col rs-col-1">
            <div class="risks-title-text">
                <?php if( in_array($current_user_id, $leaders) && $rd_status != 3){ ?>
                <a class="risks-title-name" href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", $risk_id, 'admin' => FALSE ), true ); ?>"><?php echo htmlentities($risk_title, ENT_QUOTES, "UTF-8"); ?></a>
                <?php }else{ ?>
                    <span class="risks-title-name"><?php echo htmlentities($risk_title, ENT_QUOTES, "UTF-8"); ?></span>
                <?php } ?>
                <a href="#" class="risks-title-info tipText" title="Project" style="color: #777;"><?php echo htmlentities($ptitle, ENT_QUOTES, "UTF-8"); ?></a>
            </div>
        </div>
        <div class="rs-col rs-col-2">
            <div class="peoplerisk">
                <?php
                    // RISK CREATOR
                    $html = '';
                    if( $risk_creator_id != $current_user_id ) {
                        $html = CHATHTML($risk_creator_id, $project_id);
                    }
                    $user_image = SITEURL . 'images/placeholders/user/user_1.png';
                    $user_name = htmlentities($creator_name, ENT_QUOTES, "UTF-8");
                    $profile_pic = $creator_pic;
                    $job_title = $creator_job;//htmlentities($data['creator_job'], ENT_QUOTES, "UTF-8");

                    if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                        $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
                    }
                ?>
                <div class="community-diff-list">
                    <a href="#" class="pophover-popup1 skill-popple-icon tipText" title="<?php echo $user_name; ?>" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $risk_creator_id, 'admin' => FALSE ), true ); ?>" data-target="#popup_modal" data-content="<div><p><?php echo htmlspecialchars($user_name); ?></p><p><?php echo htmlspecialchars($job_title); ?></p><?php echo $html; ?></div>">
                        <img src="<?php echo ($user_image); ?>" width="36" height="36">
                    </a>
                    <?php if($current_org['organization_id'] != $creator_org){ ?>
                    <i class="communitygray18 tipText" style="cursor: pointer;" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $risk_creator_id, 'admin' => FALSE ), true ); ?>" data-target="#popup_modal" title="Not In Your Organization" ></i>
                    <?php } ?>
                </div>
                <?php
                    // RISK TEAM
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
                <i class="teamblack pophover-popup" data-content="<?php echo $assigned_user_html; ?>"></i>
                <?php } ?>
            </div>
        </div>
        <div class="rs-col rs-col-3">
            <div class="rs-type-info">
                <?php
                // RISK TYPE AND SELECTED TASKS
                    if(!empty($risk_tasks)){
                        $element_html = "<div class='pop-details'>";
                        foreach ($risk_tasks as $ekey => $evalue) {
                            $element_html .= "<span class='el-name'><a href='".Router::Url(['controller' => 'entities', 'action' => 'update_element', $evalue['id'], 'admin' => false], true)."'>".htmlentities($evalue['title'])."</a></span>";
                        }
                        $element_html .= '</div>';
                ?>
                <i class="taskblack18 pophover-popup" style="cursor: default;" title="Related Tasks" data-content="<?php echo $element_html; ?>"></i>
                <?php }else{ ?>
                <span class="risk-type-icon">&nbsp;</span>
                <?php } ?>
                <p class="rs-type-text"><?php echo htmlentities($risk_type, ENT_QUOTES, "UTF-8"); ?></p>
            </div>
        </div>
        <div class="rs-col rs-col-4">
            <?php echo ($rd_status == "Review") ? "In Progress" : $rd_status; ?>
        </div>
        <div class="rs-col rs-col-5">
            <?php echo $rd_impact; ?>
        </div>
        <div class="rs-col rs-col-6">
            <?php echo $rd_percent; ?>
        </div>
        <div class="rs-col rs-col-7">
            <div class="rs-when">
                <span class="deta-info"><?php echo (isset($risk_date) && !empty($risk_date)) ? date( 'd M, Y',strtotime($risk_date)) : 'N/A'; ?></span>
                <?php
                    // RISK EXPOSURE
                    $exposer = $rd_exposure;
                    if(isset($exposer) && !empty($exposer) && $exposer != 'None') {
                        $expClass = 'low';
                        if($exposer == 'Medium') $expClass = 'mid';
                        else if($exposer == 'High') $expClass = 'high';
                        else if($exposer == 'Severe') $expClass = 'severe';
                ?>
                <span class="colorbar tipText " title="" data-original-title="<?php echo 'Risk Exposure: '.$exposer; ?>"> <i class="<?php echo $expClass; ?>">&nbsp;</i></span>
                <?php } else { ?>
                <span class="colorbar navail"> <i class="">&nbsp;</i></span>
                <?php } ?>
            </div>
        </div>
        <div class="rs-col rs-col-8">
            <div class="rs-flag-iocn">
                <?php
                    $profile_popover = '<div class="profile_popover">';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Mitigation</span>';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Contingency</span>';
                    $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Residual</span>';
                    $profile_popover .= '</div>';
                    if(!empty($contingency_exists) || !empty($mitigation_exists) || !empty($residual_exists)){
                        $profile_popover = '<div class="profile_popover">';
                        if( !empty($contingency_exists))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Mitigation</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Mitigation</span>';
                        }
                        if(!empty($mitigation_exists))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Contingency</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Contingency</span>';
                        }
                        if(!empty($residual_exists))
                        {
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-check text-green"></i> Residual</span>';
                        }else{
                            $profile_popover .= '<span class="profile_text"><i class="fa fa-times"></i> Residual</span>';
                        }
                        $profile_popover .= '</div>';
                    }
                    $profile_class = 'btn-danger';
                    if(!empty($mitigation_exists) && !empty($contingency_exists))
                    {
                        $profile_class = 'btn-default';
                    }
                    if(!empty($residual_exists) && (empty($mitigation_exists) || empty($contingency_exists))) {
                        $profile_class = 'btn-primary';
                    }

                    if(!empty($mitigation_exists) && !empty($contingency_exists) && !empty($residual_exists)) {
                         $profile_class = 'btn-default';
                    }
                ?>
                <a href="#" class="risk-profile <?php echo $profile_class; ?>" title="Risk Profile" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "risk_profile", $risk_id, ($key+1), 'response', 'admin' => FALSE ), true ); ?>" data-content='<?php echo $profile_popover; ?>'> <i class="infoblack-icon"></i>
                </a>
                <?php
                    $status_data = ['class' => ' ', 'icon' => 'ps-flag bg-not_started']; // Open
                    $title = 'Open';
                    if($rd_status == 'Review'){
                        $status_data = ['class' => '', 'icon' => 'ps-flag bg-progressing'];
                        $title = 'In Progress';
                    }// Reviewing
                    else if($rd_status == 'Completed'){
                        $status_data = ['class' => 'greens', 'icon' => 'ps-flag  bg-completed'];
                        $title = 'Completed';
                    } // Signed-off
                    else if($rd_status == 'Overdue'){
                        $status_data = ['class' => 'text-red', 'icon' => 'ps-flag bg-overdue'];
                        $title = 'Overdue';
                    } // Overdue
                ?>
                <a href="#" class="tipText <?php echo $status_data['class']; ?>" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "risk_profile", $risk_id, ($key+1), 'status', 'admin' => FALSE ), true ); ?>" title="<?php echo $title; ?>" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "risk_profile", $risk_id, ($key+1), 'status', 'admin' => FALSE ), true ); ?>">
                    <i class="<?php echo $status_data['icon']; ?>"></i>
                </a>
                <?php
                    $btn_status = '';
                    if($rd_status == 'Completed' || !in_array($current_user_id, $leaders) ){ // Signed-off or not a leader
                        $btn_status = 'hide';
                    }
                    if( $risk_creator_id == $current_user_id && $rd_status != 'Completed') $btn_status = ''; // if user is risk creator
                ?>
                <a href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", $risk_id, 'project' => $project_id, 'admin' => FALSE ), true ); ?>" class="rs-show-h tipText <?php echo $btn_status; ?>" title="Edit"> <i class="edit-icon"></i>
                </a>
                <?php
                $del = '';
                if( $risk_creator_id != $current_user_id ) $del = 'hide';
                if( in_array($current_user_id, $leaders) ) $del = '';
                ?>
                <a href="#" class="rs-show-h <?php echo $del; ?> tipText trash_risk delete-an-item" title="Delete" data-risk="<?php echo $risk_id; ?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "risks", "action" => "delete_an_item", $risk_id, '0', 'summary', 'admin' => FALSE ), true ); ?>"> <i class="deleteblack"></i>
                </a>
            </div>
        </div>
    <?php } // loop risks ?>
<?php } ?>

<script type="text/javascript">
    $(function(){
        $('.risks-title-info').on('click', function(event) {
            event.preventDefault();
        });
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
        $('.pophover-popup').popover({
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
            delay: {show: 50, hide: 400}
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
    })

</script>