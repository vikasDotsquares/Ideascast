<style>
.workinfo-wrap .workinfo {
    display: block;
	font-size:13px;
}
.popover .workinfo-wrap span {
    margin-bottom: 0px !important;
}
.popover-content p{
	word-wrap: break-word;
}
</style>
<?php
$current_org = $this->Permission->current_org();

if( isset($data) && !empty($data) ) {
$start_date = $end_date = date('Y-m-d');
?>
	<div class="modal-header">
		<h3 id="modalTitle" class="modal-title" >Workspace Team Members</h3>
	</div>
	<div class="modal-body clearfix people" style="max-height:424px; overflow:auto;">
<?php

					foreach( $data as $user_lists  ){

						$user_name = $user_lists[0]['fullname'];
						$userDetail['UserDetail']['user_id'] = $user_lists['user_details']['user_id'];
						$userDetail['UserDetail']['profile_pic'] = $user_lists['user_details']['profile_pic'];
						$userDetail['UserDetail']['org_name'] = $user_lists['user_details']['org_name'];
						$userDetail['UserDetail']['job_role'] = $user_lists['user_details']['job_role'];
						$userDetail['UserDetail']['user_type'] = $user_lists['user_permissions']['role'];
						$userDetail['User']['email'] = $user_lists['users']['email'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

						$currentavail =  $this->ViewModel->currentAvaiability($userDetail['UserDetail']['user_id']);
						$start_date = $end_date = date('Y-m-d');
						if( isset($currentavail) && !empty($currentavail) ){
							$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
							$start_date = date('Y-m-d');
							$end_date = $endd;
						}

						// Not Avaiablity ==================================================================
						$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
						$noAvailDates = $this->ViewModel->not_available_dates_range($userDetail['UserDetail']['user_id'],$start_date,$end_date);
						$showIcon = false;
						if( isset($noAvailDates) && !empty($noAvailDates) ){

							$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$userDetail['UserDetail']['user_id']);
							if( isset($datelists) && !empty($datelists) ){
								$tooltiphtml .= '<div style="font-weight: 600; font-size: 13px;">Absent:</div>';
								$tooltiphtml .=$datelists;
								$showIcon = true;
							} else {
								$tooltiphtml .= '<span class="workinfo">N/A</span>';
							}
						} else {
							$tooltiphtml .= '<span class="workinfo">N/A</span>';
						}
						$tooltiphtml .= '</div>';
					//===================================================================================
				?>
				<div class="row">
					<div class="col-sm-2">
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip tipText" title="Absent"></i><?php } ?>
							<?php if($user_lists['user_details']['organization_id'] != $current_org['organization_id']){ ?>
							<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
							<?php } ?>
						</div>
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name"><?php echo $userDetail['UserDetail']['user_type']; ?>: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($user_lists['organizations']['name']) && !empty($user_lists['organizations']['name']) )? trim($user_lists['organizations']['name']) : 'None'; ?></p>
						<p><span class="jobrole">Role: </span><?php
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'None';
							?></p>
					</div>
				</div>
<?php	} ?>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<?php } ?>
<script type="text/javascript" >
$(function(){
    $('#modal_medium').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
	var clicked = false;
    var onLeave = function() {
		if (!clicked) {
			$(this).popover('hide');
		}
    };
    var onEnter = function () {
        $(this).popover('show');
    };

    var clickToggle = function() {
        if (clicked) {
			$(this).popover('hide');
		}
        clicked = !clicked;
    }

	var clicked = false;
    var onLeave = function() {
		if (!clicked) {
			$(this).popover('hide');
		}
    };

    var onEnter = function () {
        $(this).popover('show');
    };

    var clickToggle = function() {
        if (clicked) {
			$(this).popover('hide');
		}
        clicked = !clicked;
    }
	$('.noavaildatausertip').on('click', function(e){
		$(this).parents('.noavailwith').find('.myaccountPic').popover('show');
	})
	.on('mouseout', function(e){
		$(this).parents('.noavailwith').find('.myaccountPic').popover('hide');
	})
	$('.myaccountPic').popover({
		 placement: "right",
        container: 'body',
        trigger: 'manual',
        html: true,
		delay: {show: "50", hide: "400"}
    })
    .on('click', onEnter)
	.on('mouseleave', onLeave);

});
</script>