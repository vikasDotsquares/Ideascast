<?php
if( isset($element_id) && !empty($element_id) ) {
$elementUsers = $this->ViewModel->elementUsersFromUserPermission($element_id);
$start_date = $end_date = date('Y-m-d');
$element_project_id = element_project($element_id);
$current_org = $this->Permission->current_org();
?>
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
	
	.people .not-react .engage-status:before {
	   content: "\f00c";
		border: solid 1px #444;
		border-radius: 50%;
		background: #444;
		color: #fff;
		padding: 1px 1px 0 0;
		width: 17px;
		height: 17px;
		display: inline-block;
		line-height: 14px;
		font-size: 12px;
		text-align: center;
	}
	.people .not-avail .engage-status:before {
	   display:none;
	}
	.people .accepted .engage-status:before {
	   content: "\f00c";
		border: solid 1px #67a028;
		border-radius: 50%;
		background: #67a028;
		color: #fff;
		padding: 1px 1px 0 0;
		width: 17px;
		height: 17px;
		display: inline-block;
		line-height: 14px;
		font-size: 12px;
		text-align: center;
	}
	.people .not-accept-start .engage-status:before {
	   content: "\f00c";
		border: solid 1px #e3a809;
		border-radius: 50%;
		background: #e3a809;
		color: #fff;
		padding: 1px 1px 0 0;
		width: 17px;
		height: 17px;
		display: inline-block;
		line-height: 14px;
		font-size: 12px;
		text-align: center;
	}
	.people .disengage .engage-status:before {
	   display:none;
	}
	.people .col-sm-2 > i{
		position: absolute;
		right: 6px;
		top: 1px;
		z-index: 1;
	}
</style>
<div class="modal-header">
	<h3 id="modalTitle" class="modal-title" >Task Team Members</h3>
</div>
<div class="modal-body clearfix people" style="max-height:424px; overflow:auto;">
<?php
	if( isset($elementUsers) && !empty($elementUsers) ){
		foreach( $elementUsers as $key => $val ) {
			$user_role = $val['user_permissions']['role'];
			$user_name = $val[0]['fullname'];
			$profile_pic = $val['user_details']['profile_pic'];
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
				$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
			} else {
				$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
			}
			// User assignment status ==============
			$element_assigned = element_assigned($element_id);
			$assign_class = 'not-avail';
			if( $element_assigned['ElementAssignment']['assigned_to'] == $val['user_permissions']['user_id'] ){
				if($element_assigned['ElementAssignment']['reaction'] == 1) {
					$assign_class = 'accepted';
				}
				else if($element_assigned['ElementAssignment']['reaction'] == 2) {
					$assign_class = 'not-accept-start';
				}
				else if($element_assigned['ElementAssignment']['reaction'] == 3)
				{
					$assign_class = 'disengage';
				} else {
					if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
						$assign_class = 'not-react';
					}
				}
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

				$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val['user_permissions']['user_id']);
				if( isset($datelists) && !empty($datelists) ){
					$tooltiphtml .= '<div style="font-weight: 600; font-size: 13px;">Current Unavailability:</div>';
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
			$gpnd = user_groupsbyUser($val['user_permissions']['user_id'], project_upid($element_project_id));
?>
		<div class="row">
			<div class="col-sm-2 <?php echo $assign_class;?>">
				<i class="fa engage-status"></i>
				<div class="noavailwith">
					<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
					<?php if($val['user_details']['organization_id'] != $current_org['organization_id']){ ?>
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