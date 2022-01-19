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
 
if( isset($data) && !empty($data) ) {
$start_date = $end_date = date('Y-m-d');		
?>
	<div class="modal-header"> 
		<h3 id="modalTitle" class="modal-title" >Workspace Team Members</h3> 	
	</div>
	<div class="modal-body clearfix people" style="max-height:424px; overflow:auto;">
	

		
<?php /********************************** participants owners *****************************************************/  ?>


<?php
if(isset($owner) && !empty($owner)){
$userDetail = $this->ViewModel->get_user( $owner, null, 1 ); 
					// pr($userDetail, 1);

$currentavail =  $this->ViewModel->currentAvaiability($userDetail['UserDetail']['user_id']);
$start_date = $end_date = date('Y-m-d');
if( isset($currentavail) && !empty($currentavail) ){
	// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
	$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
	// $start_date = $start;
	$start_date = date('Y-m-d');
	$end_date = $endd;
}
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
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
						//data-click-content='echo $tooltiphtml;'	
					
				?>
				<div class="row">
					<div class="col-sm-2">
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip tipText" title="Absent"></i><?php } ?>
							<!--<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>-->
						</div>
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name">Creator: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p>
					</div>
					<!-- <div class="col-sm-2">
						<i class="fa fa-comment"></i> 
					</div>-->
				</div>
<?php } } ?>


<?php
$owner = isset($owner) ? $owner : 0;
 // pr($owner);
// pr($data );
 ?>


		<?php if(isset($data['participants_owners']) && !empty($data['participants_owners'])) { ?>
			<?php foreach( $data['participants_owners'] as $key => $val ) { ?>
				
				<?php 
				if(isset($val) && !empty($val)){
				  if($owner != $val){
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					 
					$currentavail =  $this->ViewModel->currentAvaiability($val);				 
					$start_date = $end_date = date('Y-m-d');
					if( isset($currentavail) && !empty($currentavail) ){
						// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
						$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
						// $start_date = $start;
						$start_date = date('Y-m-d');
						$end_date = $endd;
					}					
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
					// Not Avaiablity ==================================================================
						$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
						$noAvailDates = $this->ViewModel->not_available_dates_range($val,$start_date,$end_date);
						$showIcon = false;
						if( isset($noAvailDates) && !empty($noAvailDates) ){

							$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val);
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
						//data-click-content='echo $tooltiphtml;'
				?>
				<div class="row">
					<div class="col-sm-2">
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
						</div>
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name">Owner: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p>
					</div>
					<!-- <div class="col-sm-2">
						<i class="fa fa-comment"></i> 
					</div>-->
				</div>
				<?php } } } ?>
			
			<?php } ?>
		<?php } ?>
		
<?php /********************************** participants group owners *****************************************************/  ?>

		<?php if(isset($data['participantsGpOwner']) && !empty($data['participantsGpOwner'])) { ?>
			<?php foreach( $data['participantsGpOwner'] as $key => $val ) { ?>
				
				<?php 
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					$currentavail =  $this->ViewModel->currentAvaiability($val);
					$start_date = $end_date = date('Y-m-d');
					if( isset($currentavail) && !empty($currentavail) ){
						// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
						$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
						// $start_date = $start;
						$start_date = date('Y-m-d');
						$end_date = $endd;
					}
					
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
						
					// Not Avaiablity ==================================================================
						$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
						$noAvailDates = $this->ViewModel->not_available_dates_range($val,$start_date,$end_date);
						$showIcon = false;
						if( isset($noAvailDates) && !empty($noAvailDates) ){

							$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val);
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
						//data-click-content='echo $tooltiphtml;'	
					
				?>
				<div class="row">
					<div class="col-sm-2">
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
						</div>
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name">Group Owner: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><b>Group:&nbsp;
							<?php 
								$gpnd = user_groupsbyUser($userDetail['User']['id'], project_upid($project_id));   
								 
								 echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
							?>
						</b></p>
						<p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p> 
						
					</div>
					<!-- <div class="col-sm-2">
						<i class="fa fa-comment"></i> 
					</div>-->
				</div>
				<?php } ?>
			
			<?php } ?>
		<?php } ?>
		
<?php /********************************** participants *****************************************************/  ?>

		<?php
		 
		if(isset($data['participants']) && !empty($data['participants'])) { ?>
			<?php foreach( $data['participants'] as $key => $val ) { 
			$hspermit = $this->Common->project_permission_details($this->params['pass']['1'], $val);
			if(isset($hspermit['ProjectPermission']['id']) && $hspermit['ProjectPermission']['project_level'] !=1){
			?>
				
				<?php 
				if( $val != $owner ) {
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					$currentavail =  $this->ViewModel->currentAvaiability($val);
					$start_date = $end_date = date('Y-m-d');
					if( isset($currentavail) && !empty($currentavail) ){
						// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
						$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
						// $start_date = $start;
						$start_date = date('Y-m-d');
						$end_date = $endd;
					}
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
					// Not Avaiablity ==================================================================
						$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
						$noAvailDates = $this->ViewModel->not_available_dates_range($val,$start_date,$end_date);
						$showIcon = false;
						if( isset($noAvailDates) && !empty($noAvailDates) ){

							$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val);
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
						//data-click-content='echo $tooltiphtml;'
					
				?>
				<div class="row">
					<div class="col-sm-2">
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
						</div>
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name">Sharer: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p>
					</div>
					<!-- <div class="col-sm-2">
						<i class="fa fa-comment"></i> 
					</div>-->
				</div>
				<?php } ?>
			
			<?php } ?>
			<?php } } ?>
		<?php } ?>		
		
<?php /********************************** participants group sharers *****************************************************/  ?>

		<?php if(isset($data['participantsGpSharer']) && !empty($data['participantsGpSharer'])) { ?>
			<?php foreach( $data['participantsGpSharer'] as $key => $val ) { ?>
				
				<?php 
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					
					$currentavail =  $this->ViewModel->currentAvaiability($val);
					$start_date = $end_date = date('Y-m-d');
					if( isset($currentavail) && !empty($currentavail) ){
						// $start = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_start_date']));
						$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
						// $start_date = $start;
						$start_date = date('Y-m-d');
						$end_date = $endd;
					}
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
					// Not Avaiablity ==================================================================
						$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';
						$noAvailDates = $this->ViewModel->not_available_dates_range($val,$start_date,$end_date);
						$showIcon = false;
						if( isset($noAvailDates) && !empty($noAvailDates) ){

							$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$val);
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
						//data-click-content='echo $tooltiphtml;'
					
				?>
				<div class="row">
					<div class="col-sm-2"> 
						
						<div class="noavailwith">
							<img class="myaccountPic" <?php if( $showIcon ){ ?>data-title="<?php echo $user_name; ?>" data-content='<?php echo $tooltiphtml;?>' <?php } ?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" /><?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
						</div>
						
					</div>
					<div class="col-sm-10 user-detail">
						<p class="user_name">Group Sharer: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><b>Group:&nbsp;
							<?php 
								$gpnd = user_groupsbyUser($userDetail['User']['id'], project_upid($project_id));   
								 
								 echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
							?>
						</b></p>
						<p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p> 
						
					</div>
					<!-- <div class="col-sm-2">
						<i class="fa fa-comment"></i> 
					</div>-->
				</div>
				<?php } ?>
			
			<?php } ?>
		<?php } ?>
		
		
	</div>
	
	<div class="modal-footer"> 
		<button class="btn btn-danger" data-dismiss="modal">Close</button> 	
	</div>	 

<?php
}
 ?>
        
     

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
	
/* 	$('.myaccountPic11').popover({
        placement: "right",
        container: 'body',
        trigger: 'manual',
        html: true,       
    }).on('click', onEnter)
	.on('mouseleave', onLeave); */
	
	
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