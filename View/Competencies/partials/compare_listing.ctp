<?php
$current_org = $this->Permission->current_org();
?>


<div class="compare-list-data-inner">
    <div class="comp-bottom-scroll">
        <?php foreach ($users_data as $key => $value) { ?>
            <div class="com-col"> </div>
        <?php } ?>
    </div>
<?php
if(isset($data) && !empty($data)) {
    $result = [];
    foreach ($data as $key => $value) {
        $details = array_chunk($value[0], 4, true);
        $result[] = [ 'competency' => $value['ud'], 'cdetails' => $details];
    }

?>
    <div class="compare-col-1">
        <div class="compare-list-header">
            <div class="com-col">
                <div class="com-h-info">People <span class="com-total total-comp-user">(<?php echo $total_users_found; ?>)</span></div>
                <div class="com-h-info">Competencies <span class="com-total sel-comp-total">(<?php echo $total_comp; ?>)</span></div>
            </div>
        </div>
        <div class="compare-left-cont-outer">
            <?php foreach ($result as $key => $value) {
                $competency = $value['competency'];
                // pr($value);
                $comp_id = $competency['comp_id'];
                $comp_type = $competency['comp_type'];
                $comp_name = htmlentities($competency['comp_name'], ENT_QUOTES, "UTF-8");
                $type_border = $type_icon = '';
                $comp_action = 'view_skills';
                if($comp_type == 'Skill') {$type_border = 'skill-border-left';$type_icon = 'com-skills-icon';$comp_action = 'view_skills';}
                else if($comp_type == 'Subject') {$type_border = 'subjects-border-left';$type_icon = 'com-subjects-icon';$comp_action = 'view_subjects';}
                else if($comp_type == 'Domain') {$type_border = 'domain-border-left';$type_icon = 'com-domain-icon';$comp_action = 'view_domains';}

                $compe_url = Router::Url( array( "controller" => "competencies", "action" => $comp_action, $comp_id, 'admin' => FALSE ), true );

                ?>
            <div class="compare-row">
                <div class="compare-com-list  <?php echo $type_border; ?>">
                    <span class="com-list-bg">
                        <i class="<?php echo $type_icon; ?> tipText" title="<?php echo $comp_type; ?>"></i>
                        <span class="com-sks-title tipText" title="<?php echo $comp_name; ?>" data-html="true" data-remote="<?php echo $compe_url; ?>" data-target="#modal_view_skill" data-toggle="modal"><?php echo $comp_name; ?></span>
                    </span>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="compare-col-2">
        <div class="compare-list-header user-pics-header">
            <?php foreach ($users_data as $key => $value) {
                $udata = $value['u'];
                $uddata = $value['ud'];
                $user_id = $udata['user_id'];
                $profile_pic = $uddata['profile_pic'];
                $full_name = $value[0]['full_name'];
                $job_title = $uddata['job_title'];
                $organization_id = $uddata['organization_id'];
                if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
                    $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
                } else {
                    $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                }

                $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
            ?>
            <div class="com-col sync-hover">
                <span class="style-popple-icon-out">
                    <a class="style-popple-icon tipText" title="<?php echo $full_name; ?>" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
                        <img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($full_name); ?></p><p><?php echo htmlspecialchars($job_title); ?></p><?php //echo $html; ?></div>" class="user-image sender" align="left" >
                    </a>
                    <?php if($current_org['organization_id'] != $organization_id){ ?>
                        <i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>"></i>
                    <?php } ?>
                </span>
            </div>
            <?php } ?>
        </div>
        <div class="compare-right-sec">
            <?php foreach ($result as $key => $value) {
                $comp_id = $value['competency']['comp_id'];
                ?>
            <div class="compare-row sync-hover full-row">
                <?php foreach ($value['cdetails'] as $ckey => $cvalue) {
                    $user_id = array_shift($cvalue);
                    $level = array_shift($cvalue);
                    $experience = array_shift($cvalue);
                    $files = array_shift($cvalue);
                    //tab_competencies
                    $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'tab_competencies', 'admin' => FALSE ), true );
                ?>
                <div class="com-col tipText" title="">
                    <?php if(!empty($level) || !empty($experience) || !empty($files)){ ?>
                    <div class="compare-icon-bg"  data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>">
                        <?php if(!empty($level)){
                            $level_icon = $this->Permission->level_exp_icon($level); ?>
                            <i class="<?php echo $level_icon; ?> tipText" title="" data-original-title="Level: <?php echo $level ?>"></i>
                        <?php } ?>
                        <?php if(!empty($experience)){
                            $exp_icon = $this->Permission->level_exp_icon($experience, false);
                            $exp_num = $this->Permission->exp_number($experience); ?>
                            <i class="<?php echo $exp_icon; ?> tipText" title="" data-original-title="Experience: <?php echo ($exp_num>1)?$experience.' Years':$experience.' Year'; ?>"></i>
                        <?php } ?>
                        <?php if(!empty($files)){ ?>
                            <i class="fas fa-file-pdf tipText" title="<?php echo ($files>1)?$files.' Files':$files.' File'; ?>"></i>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <!-- <div class="com-col">
                    <div class="compare-icon-bg"> <i class="beginner-icon tipText" title="" data-original-title="Level: Beginner"></i> <i class="advanced-icon tipText" title="" data-original-title="Level: Advanced"></i> <i class="sixyears-icon tipText" title="" data-original-title="Experience: 6-10 Years"></i> </div>
                </div>
                <div class="com-col">
                    <div class="compare-icon-bg"> <i class="expert-icon tipText" title="" data-original-title="Level: Expert"></i> <i class="twentyyears-icon tipText" title="" data-original-title="Experience: Over 20 Years"></i> <i class="threeyears-icon tipText" title="Experience: 3 Years"></i> </div>
                </div>
                <div class="com-col">
                    <div class="compare-icon-bg"> <i class="threeyears-icon tipText" title="" data-original-title="Experience: 3 Years"></i> <i class="twentyyears-icon tipText" title="" data-original-title="Experience: Over 20 Years"></i>  </div>
                </div> -->
            </div>
            <?php } ?>
        </div>
    </div>
<?php } else { ?>
<div class="no-res-found">No result</div>
<?php } ?>
</div>

<script type="text/javascript">
    $(()=>{
        $('.comp-bottom-scroll').on('scroll', function(event) {
            event.preventDefault();
            $('.compare-right-sec').scrollLeft($(this).scrollLeft());
            $('.user-pics-header').scrollLeft($(this).scrollLeft());
        });
    })
</script>