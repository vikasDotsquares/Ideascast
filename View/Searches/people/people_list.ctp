
<?php
// pr($all_people);
$current_org = $this->Permission->current_org();
?>
<!-- <ul class="people-cont-list"> -->
<?php if(isset($all_people) && !empty($all_people)){
    foreach ($all_people as $key => $value) {
        $userdata = $value['udata'];
        $others = $value[0];
        $rt_user = $value['rt_user']['reports_to_user'];

        $user_id = $userdata['user_id'];
        $fullname = ( isset($others['full_name']) && !empty($others['full_name'])) ? $others['full_name'] : ( (isset($userdata['full_name']) && !empty($userdata['full_name'])) ? $userdata['full_name'] : '' );

        $profile_pic = $userdata['profile_pic'];
        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
            $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
        } else {
            $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
        }

        $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
        $html = CHATHTML($user_id);
?>

    <li>
        <div class="people-cont-list-inner">
            <div class="people-cont-left">
                <div class="style-people-com ">
                    <span class="style-popple-icon-out">
                        <a class="style-popple-icons ">
                            <span class="style-popple-icon-out">
                                <span class="style-popple-icon" style="cursor: default;">
                                    <img src="<?php echo $profilesPic; ?>" class="user-wpop" style="cursor:pointer;" align="left" width="46" height="46" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($fullname); ?></p><p><?php echo htmlspecialchars($userdata['job_title']); ?></p><?php echo $html; ?></div>">
                                </span>
                                <?php if($current_org['organization_id'] != $userdata['organization_id']){ ?>
                                <i class="communitygray18 community-g tipText" title="Not In Your Organization" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"></i>
                                <?php } ?>
                            </span>
                        </a>
                    </span>
                    <div class="style-people-info">
                        <a href="#">
                            <span class="style-people-name" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><?php echo $fullname; ?></span>
                            <span class="style-people-title" style="cursor: default;"><?php echo (!empty($userdata['job_title'])) ? $userdata['job_title'] : 'Not Set'; ?></span>
                        </a>
                    </div>
                </div>
                <div class="people-cont-show">
					<div class="pp-user-office-name pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><i class="reportstoblack18"></i> <?php echo (!empty($rt_user)) ? $rt_user : 'Not Set'; ?></div>
					<div class="pp-user-office-name">
                        <i class="reportsfromblack18"></i> <span class="pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><?php echo $others['count_report_to']; ?></span>
                        <i class="dottedtoblack18"></i>  <span class="pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><?php echo $others['count_dline']; ?></span>
                        <i class="dottedfromblack18"></i>  <span class="pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><?php echo $others['count_dotted_line']; ?></span>
                    </div>

                    <div class="pp-user-office-name pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><i class="skill-menu-icon organizationgreenicon"></i> <?php echo (!empty($userdata['organization'])) ? htmlentities($userdata['organization'], ENT_QUOTES, "UTF-8") : 'Not Set'; ?></div>
                    <div class="pp-user-office-name pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><i class="skill-menu-icon departmentsgreenicon"></i> <?php echo (!empty($userdata['department'])) ? htmlentities($userdata['department'], ENT_QUOTES, "UTF-8") : 'Not Set'; ?></div>
                </div>
            </div>
            <div class="pp-user-info">
                <div class="pp-user-office-name pointer" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><i class="globlegreen"></i> <?php echo (!empty($userdata['location'])) ? htmlentities($userdata['location'], ENT_QUOTES, "UTF-8") : 'Not Set'; ?></div>
                <div class="pp-user-tag" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo $profile_url; ?>"><i class="taggreen"></i>
                    <span class="pp-tag">Tags</span> <span><?php echo $others['count_tag']; ?></span>
                </div>
                <div class="pp-user-tag-show">
                    <div class="pp-user-tag" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_competencies', 'admin' => FALSE ), true ); ?>"><i class="skill-menu-icon skillsgreenicon"></i> <span class="pp-tag"> Skills </span> <span><?php echo $others['count_skill']; ?></span></div>
                    <div class="pp-user-tag" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_competencies', 'admin' => FALSE ), true ); ?>"><i class="skill-menu-icon subjectsgreenicon"></i> <span class="pp-tag"> Subjects </span> <span><?php echo $others['count_subject']; ?></span></div>
                    <div class="pp-user-tag" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_competencies', 'admin' => FALSE ), true ); ?>"><i class="skill-menu-icon domaingreenicon"></i> <span class="pp-tag"> Domains </span> <span><?php echo $others['count_domain']; ?></span></div>
                    <div class="pp-user-tag" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_stories', 'admin' => FALSE ), true ); ?>"><i class="skill-menu-icon storygreenicon"></i> <span class="pp-tag"> Stories </span> <span><?php echo $others['count_story']; ?></span></div>
                </div>
            </div>
        </div>
    </li>
<?php } ?>
<?php }else{ ?>
    <div class="no-summary-found">No People</div>
<?php } ?>
<!-- </ul> -->
<script type="text/javascript">
    /*$(()=>{
        $('.user-wpop').popover({
            placement: function(context, element) {
                var position = $(element).offset();
                if (position.top > ($(document).height() - 270)) {
                    return "top";
                }
                return "bottom";
            },
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });
    })*/
</script>