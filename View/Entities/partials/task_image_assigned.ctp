<?php
$element_assigned = element_assigned( $element_id );

 

$creator = $receiver = false;
$assign_class = 'not-avail';
$assign_tip = 'Unassigned';




if($element_assigned) {
	$creator = ($element_assigned['ElementAssignment']['created_by'] == $current_user_id) ? true : false;
	$receiver = ($element_assigned['ElementAssignment']['assigned_to'] == $current_user_id) ? true : false;
	
	
	$current_org = $this->Permission->current_org($current_user_id);
	$receiver_org = $this->Permission->current_org($element_assigned['ElementAssignment']['assigned_to']);

	$creator_detail = get_user_data($element_assigned['ElementAssignment']['created_by']);
	$creator_name = $creator_detail['UserDetail']['full_name'];

	$receiver_detail = get_user_data($element_assigned['ElementAssignment']['assigned_to']);
	$receiver_name = $receiver_detail['UserDetail']['full_name'];
	
	 $html = '';
		if( $element_assigned['ElementAssignment']['assigned_to'] != $current_user_id ) {
			$html = CHATHTML($element_assigned['ElementAssignment']['assigned_to'],$project_id);
		}
		$style = '';

		 	
		 
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = '';
		$job_title = 'N/A';
		if(isset($receiver_detail) && !empty($receiver_detail)) {
			//$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);

			if( isset($receiver_detail['UserDetail']['first_name']) && !empty($receiver_detail['UserDetail']['first_name']) ){
				$user_name .= htmlentities($receiver_detail['UserDetail']['first_name'],ENT_QUOTES);
			}
			if( isset($receiver_detail['UserDetail']['last_name']) && !empty($receiver_detail['UserDetail']['last_name']) ){
				$user_name .= ' '.htmlentities($receiver_detail['UserDetail']['last_name'],ENT_QUOTES);
			}

			$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
			$job_title = htmlentities($receiver_detail['UserDetail']['job_title'],ENT_QUOTES);

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

		 }
		 if($user_name == ''){
			$user_name = 'N/A';
		 }	
		}	
	
	

	if($element_assigned['ElementAssignment']['reaction'] == 1) {
		$assign_class = 'accepted';
		$assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Accepted';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 2) {
		$assign_class = 'not-accept-start';
		$assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Not Accepted';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 3) {
		$assign_class = 'disengage';
		$assign_tip = "Unassigned <br /> Disengaged By ".$receiver_name;
	}
	else{
		if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
			$assign_tip = "Assigned to ".$receiver_name.'<br /> Schedule Acceptance Pending';
			$assign_class = 'not-react';
		}

	}
}

 if($element_assigned && $element_assigned['ElementAssignment']['reaction'] != 3)  { 
 ?>
						<span class="style-popple-icon-out ">
                                    <a class="style-popple-icon pophoverss" data-content="<div><p><?php echo $receiver_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"     data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo $element_assigned['ElementAssignment']['assigned_to']; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
            							<img src="<?php echo $user_image; ?>" class="user-image tipText" title="<?php echo $receiver_name; ?>" align="left" width="28" height="28">
                                        <?php if($current_org['organization_id'] != $receiver_org['organization_id']){ ?>
                                            <i class="communitygray18 tipText community-g" title="Not In Your Organization" style="cursor: default;"></i>
                                        <?php } ?>
            						</a>
 
									
                        </span>
 <?php  } ?>
 
 <script>
$(function(){
	
 $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })
		
})
</script>		