<?php
$workspace_id = element_workspace($element_id);

$current_user_id = $this->Session->read("Auth.User.id");
$current_org = $this->Permission->current_org();
$element_assigned = element_assigned($element_id);
$assigned_user = null;
if(isset($element_assigned) && !empty($element_assigned)) {
	$assigned_user = $element_assigned['ElementAssignment']['assigned_to'];
}
$taskRole = taskRole($element_id, $current_user_id);
$taskPermit = $owner = ($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner') ? true : false;

$iconPermit = false;
if(!$owner) {
	if($assigned_user == $current_user_id){
		$iconPermit = true;
	}
}
else{
	$iconPermit = true;
}
?>

<?php if(isset($team_data) && !empty($team_data)){

	foreach ($team_data as $key => $value) {
		$edata = $value['ed'];
		$unavail = $value['unavail'];
		$efforts = $value['ef'];
		$cost = $value['cd'];
		$activity = $value['ac'];
		$grp_data = $value['pg'];
		$others = $value[0];
?>
<?php
		/*---------------Efforts----------------------------------*/
	 	$effort_bar_completed_hours = (isset($efforts) && !empty($efforts['completed_hours'])) ? $efforts['completed_hours'] : 0;

	 	$effort_bar_remaining_hours = (isset($efforts) && !empty($efforts['remaining_hours'])) ? $efforts['remaining_hours'] : 0;

	 	$remaining_hours_color = (isset($others) && !empty($others['remaining_hours_color'])) ? strtolower($others['remaining_hours_color']) : '';

	 	$effort_bar_total_hours = $effort_bar_remaining_hours + $effort_bar_completed_hours;

		if($effort_bar_total_hours > 0){

		$effort_bar_top_percentage = round($effort_bar_completed_hours /	($effort_bar_total_hours ) * 100) ;

		$effort_bar_blue_percentage = round($effort_bar_completed_hours /	($effort_bar_total_hours ) * 100) ;

		$effort_bar_secondbar_percentage = round($effort_bar_remaining_hours /	($effort_bar_total_hours ) * 100) ;

		}
		else{
			$effort_bar_top_percentage = 0;
			$effort_bar_blue_percentage = 0;
			$effort_bar_secondbar_percentage = 0;
		}

		if($effort_bar_completed_hours == 1){
			$effort_bar_completed_hours_text = $effort_bar_completed_hours.' Hr';
		}
		else{
			$effort_bar_completed_hours_text = $effort_bar_completed_hours.' Hrs';
		}

		if($effort_bar_total_hours ==1){
			$effort_bar_total_hours_text = $effort_bar_total_hours.' Hr';
		}
		else{
			$effort_bar_total_hours_text = $effort_bar_total_hours.' Hrs';
		}

		$remaining_color_tip = 'None';


		if($effort_bar_remaining_hours ==1){
			$effort_bar_remaining_hours_text = $effort_bar_remaining_hours.' Hr';
		}else{
			$effort_bar_remaining_hours_text = $effort_bar_remaining_hours.' Hrs';
		}

		if($remaining_hours_color == 'red'){
			//$remaining_color_tip = "$effort_bar_remaining_hours_text Off Track Remaining Of $effort_bar_total_hours_text ($effort_bar_secondbar_percentage%)";
			$remaining_color_tip = "Remaining: $effort_bar_remaining_hours Of $effort_bar_total_hours Hours Off Track ($effort_bar_secondbar_percentage%)";

		}
		else if($remaining_hours_color == 'amber'){
			//$remaining_color_tip = "$effort_bar_remaining_hours_text At Risk Remaining Of $effort_bar_total_hours_text ($effort_bar_secondbar_percentage%)";
			$remaining_color_tip = "Remaining: $effort_bar_remaining_hours Of $effort_bar_total_hours Hours At Risk ($effort_bar_secondbar_percentage%)";
		}
		else if($remaining_hours_color == 'green'){
			//$remaining_color_tip = "$effort_bar_remaining_hours_text On Track Remaining Of $effort_bar_total_hours_text ($effort_bar_secondbar_percentage%)";
			$remaining_color_tip = "Remaining: $effort_bar_remaining_hours Of $effort_bar_total_hours Hours On Track ($effort_bar_secondbar_percentage%)";
		}
		else if( $remaining_hours_color == 'none'){
			$remaining_hours_color = 'grey';
			$per = round($effort_bar_remaining_hours /	($effort_bar_total_hours ) * 100) ;
			$rem = $effort_bar_total_hours - $effort_bar_completed_hours;
			//$remaining_color_tip = "$rem Hrs Remaining Of $effort_bar_total_hours_text Total ($per%)";
			$remaining_color_tip = "Remaining: $rem Of $effort_bar_total_hours Hours No Schedule ($per%)"; 
		}

		$remaining_color_tip_blue = 'None';

		if(isset($effort_bar_blue_percentage) && !empty($effort_bar_blue_percentage)){
			//$remaining_color_tip_blue = "$effort_bar_completed_hours_text Completed of $effort_bar_total_hours_text Total ($effort_bar_blue_percentage%)";
			$remaining_color_tip_blue = "Completed:  $effort_bar_completed_hours Of $effort_bar_total_hours Hours ($effort_bar_blue_percentage%)";
		}
		else if(!empty($effort_bar_remaining_hours) ){
			$remaining_color_tip_blue = "Completed: 0 Hours of $effort_bar_total_hours Hours (0%)";
		}

		$effort_changed_hours = (isset($efforts) && !empty($efforts['change_hours'])) ? $efforts['change_hours'] : 0;

		$effort_changed_icon = '';

		if($effort_changed_hours == 1){
			$effort_changed_hours_text = '+'.$effort_changed_hours.' Hour Change';
			$effort_changed_icon = 'increasegrey';
		}else if($effort_changed_hours == -1){
			$effort_changed_hours_text = $effort_changed_hours.' Hour Change';
			$effort_changed_icon = 'decreasegrey';
		}else if($effort_changed_hours !=0 && $effort_changed_hours != 1 ){

			if($effort_changed_hours > 0){
				$effort_changed_icon = 'increasegrey';
				$effort_changed_hours_text = '+'.$effort_changed_hours.' Hours Change';
			}
			else{
				$effort_changed_icon = 'decreasegrey';
				$effort_changed_hours_text = $effort_changed_hours.' Hours Change';
			}
		}else if($effort_changed_hours == 0 && isset($efforts) && !empty($efforts['remaining_hours'])){
			$effort_changed_hours_text = 'Unchanged';
			$effort_changed_icon = 'notsetgrey';
		}else{
			$effort_changed_hours_text = '';
			$effort_changed_icon = '';
		}
		/*---------------Efforts----------------------------------*/
?>
<?php
		$userRole = taskRole($element_id, $edata['user_id']);
		$role = ($userRole == 'Creator' || $userRole == 'Owner' || $userRole == 'Group Owner') ? 'owner-user' : 'sharer-user';
		$user_permit = $taskPermit;



		if(!$taskPermit){
			if($current_user_id == $edata['user_id'] ){
				$user_permit = true;
			}
		}

		$icon_permit = $iconPermit;
		if($edata['user_id'] == $current_user_id){
			$icon_permit = true;
		}


		// USER DETAIL
		$user_name = htmlentities($edata['full_name'], ENT_QUOTES, "UTF-8");
		$profile_pic = $edata['profile_pic'];
		$job_title = $edata['job_title'];//htmlentities($edata['job_title'], ENT_QUOTES, "UTF-8");

		$html = '';
		if( $edata['user_id'] != $current_user_id ) {
			$html = CHATHTML($edata['user_id'], $project_id);
		}

		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
			$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
		}

		// COST
		$estimatedcost = (isset($cost['cost_estimate']) && !empty($cost['cost_estimate'])) ? $cost['cost_estimate'] : 0;
		$spendcost = (isset($cost['cost_spend']) && !empty($cost['cost_spend'])) ? $cost['cost_spend'] : 0;

		$max_budget = max( $estimatedcost, $spendcost );
		$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
		$spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;

		$projectCurrencyName = $this->Common->getCurrencySymbolName($project_id);

		$force_cost_percentage = 2;

		// COMPETENCY
		$project_skills = $edata['project_skills'];
		$project_subjects = $edata['project_subjects'];
		$project_domains = $edata['project_domains'];
		$user_skills = $others['user_skills'];
		$user_subjects = $others['user_subjects'];
		$user_domains = $others['user_domains'];
		$skill_percent = (!empty($project_skills) && !empty($user_skills)) ?  round(($user_skills/$project_skills)*100) : 0;
		$subject_percent = (!empty($project_subjects) && !empty($user_subjects)) ? round(($user_subjects/$project_subjects)*100) : 0;
		$domain_percent = (!empty($project_domains) && !empty($user_domains)) ? round(($user_domains/$project_domains)*100) : 0;


		// pr(number_format($skill_percent, 2, '', '.'));
?>
<div class="ps-data-row" data-user="<?php echo $edata['user_id']; ?>">
	<div class="ps-col tm-col-1 team-col-info">
		<div class="style-people-com">
			<a class="style-popple-icons" data-remote="<?php echo Router::url(['controller' => 'shares', 'action' => 'show_profile', $edata['user_id'], 'admin' => FALSE], TRUE) ?>" data-target="#popup_modal" data-toggle="modal">
				<span class="style-popple-icon-out">
					<span class="style-popple-icon <?php echo $role; ?>" >
						<img src="<?php echo $user_image; ?>" class="user-image" align="left" width="36" height="36" title="Role: <?php echo $userRole; ?><br />Click To View Profile" align="left" width="36" height="36">
					</span>
					<?php if($current_org['organization_id'] != $edata['organization_id']){ ?>
					<i class="communitygray18 tipText community-g" title="Not In Your Organization"></i>
					<?php } ?>
				</span>
			</a>
			<div class="style-people-info">
				<span class="style-people-name" data-remote="<?php echo Router::url(['controller' => 'shares', 'action' => 'show_profile', $edata['user_id'], 'admin' => FALSE], TRUE) ?>" data-target="#popup_modal" data-toggle="modal"><?php echo $user_name; ?></span>
				<span class="style-people-title"><?php echo $job_title; ?></span>
			</div>
		</div>
		<div class="team-user-status">
			<?php
			$grp_tip = '';
			if(!empty($grp_data['grp_owner']) && $grp_data['grp_owner'] == $current_user_id){
				//grp creator
				$grp_tip = 'Team Member Is Part Of A Group<br>Click to View Group';
			?>
				<span><a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'my_groups', $grp_data['grp_id'], 'admin' => FALSE ), TRUE ); ?>" class="grp-title"  title="<?php echo $grp_tip; ?>"><i class="groupblue18"></i></a></span>
			<?php
			}
			else if(($userRole == 'Group Owner' || $userRole == 'Group Sharer') && !empty($grp_data['grp_owner'])){
				//grp user
				$grp_tip = 'Team Member Is Part Of A Group<br>Created By '.$grp_data['grp_user_name'];
			?>
				<span><a  class="grp-title grp-member" title="<?php echo $grp_tip; ?>"><i class="groupgrey18"></i></a></span>
			<?php
			}
			?>
			<?php if(isset($unavail['tdays']) && !empty($unavail['tdays'])){ ?>
			<span><a href="<?php echo Router::Url( array( 'controller' => 'resources', 'action' => 'people', 'user' => $edata['user_id'], 'task' => $element_id, 'tab' => 'tab_engagements', 'admin' => FALSE ), TRUE ); ?>" class="grp-title" title="Team Member Is Currently Absent<br>Click To Go To Engagements"><i class="absenceblack18"></i></a></span>
			<?php } ?>
		</div>
	</div>
	<div class="ps-col tm-col-2">
		<div class="progress-col-cont team-prog">
			<div class="team-prog-inner">
				<div class="schedule-bar" data-original-title="" title="">
					<span class="blue tipText bar-border" title="" style="width: <?php echo $effort_bar_blue_percentage; ?>%" data-original-title="<?php echo $remaining_color_tip_blue; ?>"></span>
					<span class="<?php echo strtolower($remaining_hours_color); ?> tipText bar-border" title="" style="width: <?php echo $effort_bar_secondbar_percentage; ?>%" data-original-title="<?php echo $remaining_color_tip; ?>"></span>
				</div>
				<i class="level-ts tipText <?php echo $effort_changed_icon; ?>" title="<?php echo $effort_changed_hours_text; ?>"></i>
			</div>

			<?php if($effort_bar_total_hours_text > 0) { ?>
			<div class="teaminfotext " >
			<?php //if($effort_bar_completed_hours > 0) { ?>
			<span class="tipText" title="Completed Hours"><?php echo ($effort_bar_completed_hours == 1) ? $effort_bar_completed_hours.' Hr' : $effort_bar_completed_hours.' Hrs'; ?></span>
			<?php //} ?>
			<?php if($effort_bar_remaining_hours > 0) { ?>
			<span class="tipText" title="Remaining Hours"><?php echo ($effort_bar_remaining_hours == 1) ? $effort_bar_remaining_hours.' Hr' : $effort_bar_remaining_hours.' Hrs'; ?></span>
			<?php } ?>
			</div>
			<?php }else{ ?>
			<div class="teaminfotext"><span>Not Set</span></div>
			<?php } ?>
		</div>
	</div>
	<div class="ps-col tm-col-3">
		<div class="progress-col-cont">
			<?php if($user_permit){ ?>
			<div class="progress-col-cont-min">
				<div class="cost-bar cost-tooltip ctip" title="Budget (<?php echo $projectCurrencyName; ?>): <?php echo number_format($estimatedcost, 2, '.', ','); ?>">
					<?php $estimate_used = ($estimate_used < $force_cost_percentage && $estimate_used > 0) ? $force_cost_percentage : $estimate_used; ?>
					<span class="blue" style="width: <?php echo round($estimate_used); ?>%"></span>
				</div>
				<div class="cost-bar cost-tooltip ctip" title="Actual (<?php echo $projectCurrencyName; ?>): <?php echo number_format($spendcost, 2, '.', ','); ?>">
					<?php $spendcost = ($spendcost < $force_cost_percentage && $spendcost > 0) ? $force_cost_percentage : $spendcost; ?>
					<span class="<?php if($spendcost > $estimatedcost) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo (!empty($estimatedcost) && !empty($spendcost)) ? round($spend_used) : ((empty($estimatedcost) && !empty($spendcost)) ? '100' : '0'); ?>%"></span>
				</div>
			</div>
			<div class="proginfotext"><?php echo $others['cost_status']; ?></div>
			<?php }else{ ?>
			<i class="visible-off tipText" title="This Information Is Only Available To Owners"></i>
			<?php } ?>
		</div>
	</div>
	<div class="ps-col tm-col-4">
		<div class="progress-col-cont">
			<ul class="workcount">
				<li class="darkred tipText <?php if(!isset($others['high_pending_risks']) || $others['high_pending_risks'] == 0){ echo 'zero_class'; }?>" title="High Pending Risks" ><?php echo $others['high_pending_risks']; ?></li>
				<li class="red tipText <?php if(!isset($others['severe_pending_risks']) || $others['severe_pending_risks'] == 0){ echo 'zero_class'; }?>" title="Severe Pending Risks"><?php echo $others['severe_pending_risks']; ?></li>
			</ul>
			<div class="proginfotext"><?php echo $others['total_risks']; ?> Total</div>
		</div>
	</div>
	<div class="ps-col tm-col-5">
		<div class="progress-col-cont">
			<div class="compet-proj-bar-col">
				<div class="schedule-bar" data-original-title="" title="">
					<span class="blue barTip bar-border ctip" title="" style="width:<?php echo $skill_percent; ?>%" data-original-title="Team Member has <?php echo $user_skills; ?> of <?php echo $project_skills; ?> Project Skills (<?php echo $skill_percent; ?>%)"></span>
				</div>
				<div class="proginfotext ctip" title="<?php if($project_skills > 0){ ?>Team Member has <?php echo $user_skills; ?> of <?php echo $project_skills; ?> Project Skills (<?php echo $skill_percent; ?>%)<?php }else{ ?>No Project Skills<?php } ?>"><?php echo $user_skills; ?></div>
			</div>
			<div class="compet-proj-bar-col">
				<div class="schedule-bar" data-original-title="" title="">
					<span class="red2 barTip bar-border ctip" title="" style="width: <?php echo $subject_percent; ?>%" data-original-title="Team Member has <?php echo $user_subjects; ?> of <?php echo $project_subjects; ?> Project Subjects (<?php echo $subject_percent; ?>%)"></span>
				</div>
				<div class="proginfotext ctip" title="<?php if($project_subjects > 0){ ?>Team Member has <?php echo $user_subjects; ?> of <?php echo $project_subjects; ?> Project Subjects (<?php echo $subject_percent; ?>%)<?php }else{ ?>No Project Subjects<?php } ?>"><?php echo $user_subjects; ?></div>
			</div>
			<div class="compet-proj-bar-col">
				<div class="schedule-bar" data-original-title="" title="">
					<span class="green-bg barTip bar-border ctip" title="" style="width:<?php echo $domain_percent; ?>%" data-original-title="Team Member has <?php echo $user_domains; ?> of <?php echo $project_domains; ?> Project Domains (<?php echo $domain_percent; ?>%)"></span>
				</div>
				<div class="proginfotext ctip" title="<?php if($project_domains > 0){ ?>Team Member has <?php echo $user_domains; ?> of <?php echo $project_domains; ?> Project Domains (<?php echo $domain_percent; ?>%)<?php }else{ ?>No Project Domains<?php } ?>"><?php echo $user_domains; ?></div>
			</div>
		</div>
	</div>
	<div class="ps-col tm-col-6">
		<div class="text-ellipsis lineheight17"><?php echo (isset($activity['message']) && !empty($activity['message'])) ? $activity['message'] : 'None'; ?></div>
		<div class="team-date">
			<?php echo (!empty($activity['updated'])) ? date('d M, Y',strtotime($activity['updated'])) : ''; ?>
		</div>
	</div>
	<div class="ps-col tm-col-7">
		<?php if($owner || $icon_permit){ ?>
		<a href="#" class="tipText" title="Set Effort" data-toggle="modal" data-target="#element_level" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'set_efforts', $element_id, $edata['user_id'], 'admin' => FALSE ), TRUE ); ?>"><i class="effortblack"></i></a>
		<?php } ?>

		<a href="#" class="tipText" title="Tag Team Member" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::Url( array( 'controller' => 'tags', 'action' => 'add_tags_team_members', 'project' => $project_id, 'workspace'=> $workspace_id, 'task' => $element_id, 'type' => 'task_team', 'selected' => $edata['user_id'], 'admin' => FALSE ), TRUE ); ?>"><i class="tagblack"></i></a>

		<a href="#" class="tipText" title="Nudge Team Member" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'workspace' => $workspace_id, 'task' => $element_id, 'type' => 'task_team', 'selected' => $edata['user_id'], 'admin' => false)); ?>"><i class="nudgeblack"></i></a>

		<?php if($taskPermit && ($edata['user_id'] != $current_user_id) ){
			if($userRole != 'Group Owner' && $userRole != 'Group Sharer') {
			$typeShare = ($userRole == 'Creator' || $userRole == 'Owner' || $userRole == 'Group Owner') ? null : 2; ?>
		<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'index', $project_id, $edata['user_id'], $typeShare, 'admin' => FALSE ), TRUE ); ?>" class="tipText edit-sharing" title="Edit Sharing Permissions"><i class="mysharingblack18"></i></a>
		<?php } ?>
		<?php } ?>

		<?php
		if(chat_enabled()){  
		if($current_user_id != $edata['user_id']){ ?>
		<a href="#" class="tipText start_chat_7" title="Talk Now" data-project="<?php echo $project_id; ?>" data-member="<?php echo $edata['user_id']; ?>"><i class="talknowblack"></i></a>
		<?php } } ?>
	</div>
</div>
<?php } ?>
<?php }else{ ?>
	<div class="no-summary-found">No Team</div>
<?php } ?>

<style type="text/css">
	.tooltip-comp{
		text-transform: none;
	}
</style>
<script type="text/javascript">
	$(function(){
		/*$('.user-image').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })*/
        $('.ctip').tooltip({
        	placement: 'top',
        	container: 'body',
        	template: '<div class="tooltip tooltip-comp"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        })
        $('.user-image,.grp-title').tooltip({
        	placement: 'top',
        	container: 'body',
        	html: true,
        	template: '<div class="tooltip tooltip-comp"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        })

        $('.grp-member').on('click', function(event) {
        	event.preventDefault();
        });
	})
</script>