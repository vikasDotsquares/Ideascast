
<?php
$result = [];
if(isset($data) && !empty($data)){
    foreach ($data as $key => $value) {
        $dates = array_chunk($value[0], 6, true);
        $result[] = ['udata' => $value['util'], 'dates' => $dates];
    }
}
    // pr( ($result));
$current_org = $this->Permission->current_org();
?>

    <?php if(isset($data) && !empty($data)){
    ?>


        <?php
        if($section && $section == 'left'){
            foreach ($result as $key => $value) {
                $udata = $value['udata'];
                $user_id = $udata['user_id'];
                $profile_pic = $udata['profile_pic'];
                if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
                    $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
                } else {
                    $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                }

                $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
                $html = CHATHTML($user_id);

            ?>
            <div class="util-row">
                <div class="util-col util-people-list">
                    <div class="style-people-com">
                        <span class="style-popple-icon-out">
                            <a class="style-popple-icon" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
                                <img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($udata['full_name']); ?></p><p><?php echo htmlspecialchars($udata['job_title']); ?></p><?php echo $html; ?></div>" class="user-image sender" align="left" data-original-title="" title="">
                            </a>
                            <?php if($current_org['organization_id'] != $udata['organization_id']){ ?>
                                <i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>"></i>
                            <?php } ?>
                        </span>
                        <div class="style-people-info">
                            <span class="style-people-name" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal"><?php echo $udata['full_name']; ?></span>
                            <span class="style-people-title"><?php echo (!empty($udata['job_title'])) ? $udata['job_title'] : 'Not Set'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php } ?>
        <?php }
        else if($section && $section == 'right'){ ?>

        <?php foreach ($result as $key => $value) {
                ?>
                <div class="util-row <?php echo (isset($date_type) && $date_type != 'daily') ? 'full-wd' : ''; ?>">
                <?php
                $user_id = $value['udata']['user_id'];
                $dates = $value['dates'];
                foreach ($dates as $keys => $values) {
                    $dt = array_shift($values);
                    $da = (array_shift($values));
                    $de = (array_shift($values));
                    $ad = (array_shift($values));
                    $ab = (array_shift($values));
                    $wb = (array_shift($values));

                    $de_class = 'plan-gray';
                    if( round($de) <= 0 ){
                        $de_class = 'plan-gray';
                    }
                    else if( ($de) < ( ( ($da)*90)/100 )){
                        $de_class = 'plan-green';
                    }
                    else if( ( ($da) >  ($de)) && (  ($de) > ( ( ($da)*90)/100 ))){
                        $de_class = 'plan-amber';
                    }
                    else if( ($de) ==  ($da)){
                        $de_class = 'plan-blue';
                    }
                    else if( ($de) >  ($da)){
                        $de_class = 'plan-red';
                    }

                    $da_class = 'plan-gray';
                    if(round($da) > 0){
                        $da_class = 'plan-blue';
                    }

                    $ad_class = '';
                    $ad_tip = '';
                    $ad_tab = '';
                    $ad_modal = '';
                    if(round($ad) > 0){
                        $ad_class = 'adjustblack-icon';
                        $ad_tip = 'Adjusted';
                        $ad_tab = 'util_adjustment';
                        $ad_modal = ' data-toggle="modal"';
                    }

                    $ab_wb_class = ''; // both 0
                    $ab_wb_tip = ''; // both 0
                    $ab_wb_tab = '';
                    $ab_wb_modal = '';
                    if(round($ab) > 0 && round($wb) > 0){
                        $ab_wb_class = 'blockabsenceblack18'; // both 1
                        $ab_wb_tip = 'Absence with Work Block';
                        $ab_wb_tab = 'util_workblock';
                        $ab_wb_modal = ' data-toggle="modal"';
                    }
                    else if(round($ab) > 0 && round($wb) <= 0){
                        $ab_wb_class = 'absenceblack18'; // ab 1
                        $ab_wb_tip = 'Absence';
                        $ab_wb_tab = 'util_absences';
                        $ab_wb_modal = ' data-toggle="modal"';
                    }
                    else if(round($ab) <= 0 && round($wb) > 0){
                        $ab_wb_class = 'blockblack18'; // wb 1
                        $ab_wb_tip = 'Work Block';
                        $ab_wb_tab = 'util_workblock';
                        $ab_wb_modal = ' data-toggle="modal"';
                    }

                    $de_tip = "Allocated (Hrs)";
                    if(round($da) > 0){
                        $de_data = ($de*100)/$da;
                        $de_tip = (!empty($de_tip)) ? number_format($de_data, 2, '.', '') . '% Utilization (Hrs)' :  '0% Utilization (Hrs)';
                    }

                    $dtyp = ($date_type == 'daily') ? 'd' : ( ($date_type == 'weekly') ? 'w' : 'm' );
            ?>

                <div class="util-col">
                    <div class="days-outer">
                        <div class="days-inner">
                            <span class="days-num <?php echo $de_class; ?> tipText" title="<?php echo $de_tip; ?>" data-toggle="modal" data-target="#modal_util" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "util_details", 'util_work', $user_id, $dt, $dtyp, 'admin' => FALSE ), true ); ?>"><?php echo (round($de) <= 0) ? round($de) : $de; ?></span>
                            <span class="days-num <?php echo $da_class; ?> tipText" title="Available (Hrs)" data-toggle="modal" data-target="#modal_util" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "util_details", "util_availability", $user_id, $dt, $dtyp, 'admin' => FALSE ), true ); ?>"><?php echo (round($da) <= 0) ? round($da) : $da; ?></span>
                        </div>
                        <div class="days-icon">
                            <i class="dayicon-blank tipText <?php echo $ad_class; ?>" title="<?php echo $ad_tip; ?>" <?php echo $ad_modal; ?> data-target="#modal_util" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "util_details", 'util_adjustment', $user_id, $dt, $dtyp, 'admin' => FALSE ), true ); ?>"></i>
                            <i class="dayicon-blank <?php echo $ab_wb_class; ?> tipText" title="<?php echo $ab_wb_tip; ?>" <?php echo $ab_wb_modal; ?> data-target="#modal_util" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "util_details", $ab_wb_tab, $user_id, $dt, $dtyp, 'admin' => FALSE ), true ); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php }// first foreach ?>
            </div>
            <?php }// sec foreach ?>
    <?php } ?>
<?php } ?>
<?php /*if(isset($data) && !empty($data)){ ?>
<script type="text/javascript">
    $(()=>{
        $.adjust_resize();
        $('.user-image.sender').popover({
            placement:  function(context, element) {
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
        $('.abs').on('scroll', function(event) {
            event.preventDefault();
            $('.utilization-list-header').scrollLeft($(this).scrollLeft());
            $('.util-right-sec').scrollLeft($(this).scrollLeft());
        });
    })
</script>
<?php }*/ ?>