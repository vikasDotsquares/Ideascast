<?php
$current_user_id = $this->Session->read('Auth.User.id');
$html = '';
if( $user_id != $current_user_id ) {
    $html = CHATHTML($user_id, null);
}
$userDetail = $this->ViewModel->get_user_data($user_id, -1, 'taskcenter');
$user_image = SITEURL . 'images/placeholders/user/user_1.png';
$user_name = 'Not Available';
$job_title = 'Not Available';
if(isset($userDetail) && !empty($userDetail)) {
	$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
	$profile_pic = $userDetail['UserDetail']['profile_pic'];
	$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

    if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
        $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
    }
}
?>

<div class="col-sec-img">
    <a href="#"
        class="pophover-popup user-image"
        data-toggle="modal"
        data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>"
        data-target="#popup_modal"  data-user="<?php echo $user_id; ?>"
        data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
        <img src="<?php echo $user_image; ?>" >
    </a>
</div>
<?php


$own_projects = $shr_projects = 0;
if($current_user_id != $user_id) {
    $inter = array_intersect($user_projects, $my_projects);
    if(isset($inter) && !empty($inter)) {
        // pr($inter);
        foreach ($inter as $key => $value) {
            // $ProjectPermit = $this->ViewModel->projectPermitType($key, $current_user_id);
            $projectRole = projectRole($key, $user_id);
            // pr($projectRole, 1);
            if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner') {
                $own_projects += 1;
            }
            else{
                $shr_projects += 1;
            }

        }
    }
}
else{
    // $own_projects = (isset($ownerSharer['owner']) && !empty($ownerSharer['owner'])) ? count($ownerSharer['owner']) : 0;
    // $shr_projects = (isset($ownerSharer['sharer']) && !empty($ownerSharer['sharer'])) ? count($ownerSharer['sharer']) : 0;

    $own_projects = projectOwnerSharerTotal(1, $current_user_id);
    $shr_projects = projectOwnerSharerTotal(null, $current_user_id);
}
$total = $own_projects + $shr_projects;

?>
<div class="col-sec-detail">
    <div class="pdata <?php if(empty($total)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="all">
        <span>Projects: </span><span><?php echo $total; ?></span>
    </div>
    <div class="pdata txt-light-grn <?php if(empty($own_projects)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="owner">
        <span>Owner: </span><span><?php echo $own_projects; ?></span>
    </div>
    <div class="pdata <?php if(empty($shr_projects)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="sharer" >
        <span>Sharer: </span><span><?php echo $shr_projects; ?></span>
    </div>
</div>

<script type="text/javascript">
    $(function(){
/*         $('.pophover-popup').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        }); */
    })
</script>
<style type="text/css">
    .no-pointer {
        cursor: default !important;
    }
</style>