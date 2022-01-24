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
	.popover.in{ z-index : +99999;}
</style>
<?php
$logged_user = $this->Session->read('Auth.User.id');

$projectUsers = $this->ViewModel->projectUsersFromUserPermission($project_id);

$current_org = $this->Permission->current_org();

?>
<div class="modal-header">
	<h3 id="modalTitle" class="modal-title" >Project Team Members</h3>
</div>
<div class="modal-body clearfix people" style="max-height:424px; overflow:auto;">
<?php
	if( isset($projectUsers) && !empty($projectUsers) ){
		foreach( $projectUsers as $key => $val ) {
			$user_role = $val['user_permissions']['role'];
			$user_name = $val[0]['fullname'];
			$profile_pic = $val['user_details']['profile_pic'];
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
				$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
			} else {
				$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
			}
			///==========================================
			$currentavail =  $this->ViewModel->currentAvaiability($val['user_permissions']['user_id']);

			$start_date = $end_date = date('Y-m-d');
			if( isset($currentavail) && !empty($currentavail) ){
				// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
				$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
				// $start_date = $start;
				$start_date = date('Y-m-d');
				$end_date = $endd;
			}
			// Not Avaiablity ==================================================================
			$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
			$noAvailDates = $this->ViewModel->not_available_dates_range($val['user_permissions']['user_id'],$start_date,$end_date);
			$showIcon = false;
			if( isset($noAvailDates) && !empty($noAvailDates) ){
				// pr($noAvailDates);
				$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val['user_permissions']['user_id']);
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
			$gpnd = user_groupsbyUser($val['user_permissions']['user_id'], project_upid($project_id));
			
?>
		<div class="row">
			<div class="col-sm-2">
				<div class="noavailwith">
					<img class="myaccountPic prj-people-pic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image"   src="<?php echo $profilesPic ?>" alt="Profile Image" />
				<?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip tipText" title="Absent"></i><?php } ?>
					<?php if( ($val['user_details']['organization_id'] != $current_org['organization_id']) &&  ($val['user_details']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>	
						<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
					<?php } ?>
				</div>
			</div>
			<div class="col-sm-10 user-detail">
				<p class="user_name"><?php echo $user_role;?>: <?php echo $user_name; ?> </p>
				<p><?php echo $val['users']['email']; ?></p>
				<p><span class="ucompany">Organization: </span><?php echo ( isset($val['organizations']['name']) && !empty($val['organizations']['name']) )? trim($val['organizations']['name']) : 'None'; ?></p>
				<?php if( $user_role == 'Group Owner' ){ ?>
				<p>
					Group:&nbsp;
						<?php
							echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
						?>
					
				</p>
				<?php } ?>
				<?php if( $user_role == 'Group Sharer' ){ ?>
				<p>
					Group:&nbsp;
						<?php
							echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
						?>
					
				</p>
				<?php } ?>
				<p><span class="jobrole">Role: </span><?php echo ( isset($val['user_details']['job_role']) && !empty($val['user_details']['job_role']) )? trim($val['user_details']['job_role']) : 'None'; ?></p>
			</div>
		</div>
<?php }
} ?>
</div>
<div class="modal-footer">
	<button class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<script type="text/javascript" >
$(function(){
    $('#modal_medium').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

/*
	$('.prj-people-pic').popover({
        placement: "right",
        container: 'body',
        trigger: 'click',
        html: true,
    })*/


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
		$(this).parents('.noavailwith').find('.prj-people-pic').popover('show');
	})
	.on('mouseout', function(e){
		$(this).parents('.noavailwith').find('.prj-people-pic').popover('hide');
	})
	$('.prj-people-pic').popover({
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