<?php $refer_url = SITEURL.'/boards/opportunity/request';
if(isset($data) && !empty($data)){
	foreach ($data as $key => $value) {
		 // pr($value);

		$pbs_data = $value['pbs'];
		$project_id = $pbs_data['project_id'];
		$others = $value['0'];

		$response_by = $others['response_by'];
		$declined_by = $others['declined_by'];

		$skill_match_percent = $others['skill_match_percent'];
		$domain_match_percent = $others['domain_match_percent'];
		$subject_match_percent = $others['subject_match_percent'];

		$match_tasks_counts = $others['match_tasks_counts'];
        $match_project_counts = $others['match_project_counts'];

		$unvailable_days = $others['unavailable_count'];
        $block_days = $others['block_count'];

		$match_project_counts = (isset($others['match_project_counts']) && !empty($others['match_project_counts'])) ? $others['match_project_counts'] : 0;
		$match_tasks_counts = (isset($others['match_tasks_counts']) && !empty($others['match_tasks_counts'])) ? $others['match_tasks_counts'] : 0;

        $unvailable_days = (isset($others['unavailable_count']) && !empty($others['unavailable_count'])) ? $others['unavailable_count'] : 0;
        $block_days = (isset($others['block_count']) && !empty($others['block_count'])) ? $others['block_count'] : 0;

        $skill_match_percent = (isset($others['skill_match_percent']) && !empty($others['skill_match_percent'])) ? $others['skill_match_percent'] : 0;
        $subject_match_percent = (isset($others['subject_match_percent']) && !empty($others['subject_match_percent'])) ? $others['subject_match_percent'] : 0;
        $domain_match_percent = (isset($others['domain_match_percent']) && !empty($others['domain_match_percent'])) ? $others['domain_match_percent'] : 0;

		$profile_pic = $pbs_data['profile_pic'];
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
			$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
		} else {
			$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
		}
		$current_org = $this->Permission->current_org();
		$fullName = $pbs_data['first_name'].' '.$pbs_data['last_name'];
		$user_login = $this->Session->read('Auth.User.id');
	?>

		<div class="requests-row ssd-data-row ">
      <div class="requests-col req-col-1">
         <div class="style-people-com">
			<?php //echo $pbs_data['sender'];?>
            <a class="style-popple-icons" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $pbs_data['sender'], 'details', 'admin' => false)); ?>" data-target="#popup_modal" data-toggle="modal" >
            <span class="style-popple-icon-out">
				<span class="style-popple-icon">
					<img src="<?php echo $profilesPic ?>" class="user-image" align="left" width="36" height="36">
				</span>
				<?php
				if($pbs_data['organization_id'] != $current_org['organization_id']){ ?>
						<i class="communitygray18 community-g tipText" title="Not In Your Organization"></i>
				<?php } ?>
            </span>
            </a>
            <div class="style-people-info">
               <span class="style-people-name" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $pbs_data['sender'], 'details', 'admin' => false)); ?>" data-target="#popup_modal" data-toggle="modal" ><?php echo $fullName;?> </span>
               <span class="style-people-title"><?php echo $pbs_data['job_title'];?></span>
            </div>
         </div>
      </div>
      <div class="requests-col req-col-2">
         <span class="competencies-list">
		 <span class="competencies-list-bg competencies-list-bg-skill tipText" title="Skills" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppskills', $pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
					<i class="skills-icon"></i>
			<span class="sks-title"><?php echo $skill_match_percent; ?>%</span>
		</span>
		<span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppsubjects',$pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
			<i class="subjects-icon"></i>
			<span class="sks-title"><?php echo $subject_match_percent; ?>%</span>
		</span>
		<span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppdomains',$pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
			<i class="domain-icon"></i>
			<span class="sks-title"><?php echo $domain_match_percent; ?>%</span>
		</span>

         </span>
      </div>
      <div class="requests-col req-col-3">
         <div class="opp-work-info-wrap">
            <div class="oppinfo-eng">
               <div class="opp-work-item"> <a href="<?php echo Router::url(array('controller' => 'searches', 'action' => 'people', 'user' => $pbs_data['sender'], 'project' => $project_id, 'tab' => 'tab_engagements', 'admin' => false)); ?>"><i data-html="true" class="opp-icon projectblack tipText" data-original-title="Project Count In Project Period<br />Click to Go To Engagements"></i><?php echo number_format($match_project_counts,0);?></a> </div>
			   
               <div class="opp-work-item"> <a href="<?php echo Router::url(array('controller' => 'searches', 'action' => 'people', 'user' => $pbs_data['sender'], 'project' => $project_id, 'tab' => 'tab_engagements', 'admin' => false)); ?>"><i data-html="true" class="opp-icon taskblack tipText" data-original-title="Task Count In Project Period<br />Click to Go To Engagements"></i>
                  <?php echo number_format($match_tasks_counts,0);?></a>
               </div>
            </div>
            <div class="oppinfo-match">
               <span class="opp-work-item">
               	<a href="<?php echo Router::url(array('controller' => 'searches', 'action' => 'people', 'user' => $pbs_data['sender'], 'project' => $project_id, 'tab' => 'tab_engagements', 'admin' => false)); ?>">
               		<i data-html="true" class="opp-unavailableBlack tipText" data-original-title="Absence Count In Project Period<br />Click to Go To Engagements"></i> <?php echo number_format($unvailable_days,0); ?>
               	</a>
               </span>
               <span class="opp-work-item">
               	<a href="<?php echo Router::url(array('controller' => 'searches', 'action' => 'people', 'user' => $pbs_data['sender'], 'project' => $project_id, 'tab' => 'tab_engagements', 'admin' => false)); ?>">
               	<i data-html="true" class="blocksmblack18 tipText" data-original-title="Work Block Count In Project Period<br />Click to Go To Engagements"></i> <?php echo number_format($block_days,0); ?>
               	</a> </span>
            </div>
         </div>
      </div>
      <div class="requests-col req-col-4 requests-status">
				<?php
				if($pbs_data['project_status'] == 0){ ?>
					<a style="opacity:1;cursor:default;" class="request-sent tipText" href="#" title="Request: <?php echo $pbs_data['board_msg'] ?>"><i class="requestsent-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($pbs_data['updated'])); ?></a>
				<?php
				}
				if($pbs_data['project_status'] == 1){ ?>
					<a style="opacity:1;cursor:default;" class="request-sent tipText" href="#" title="Request: <?php echo $pbs_data['board_msg'] ?>"><i class="requestsent-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($pbs_data['created'])); ?></a>
					<a style="opacity:1;cursor:default;" class="request-sent tipText" href="#" title="Accepted By: <?php echo $response_by; ?>"><i class="activegreen"></i>&nbsp;<?php echo date('d M, Y', strtotime($pbs_data['updated'])); ?></a>
				<?php }

				if($pbs_data['project_status'] == 2){
				//echo $pbs_data['reason_id'].'=reason_id=';
				?>
					<a style="opacity:1;cursor:default;"  class="request-sent tipText" href="#" title="Request: <?php echo $pbs_data['board_msg'] ?>"><i class="requestsent-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($pbs_data['created'])); ?></a>
					<a data-html="true" style="opacity:1;cursor:default;"  class="request-rejected tipText" href="#" title="Declined By: <?php echo ( isset($pbs_data['reasons']) && !empty($pbs_data['reasons']) ) ? $declined_by.'<br />'.$pbs_data['reasons'] : 'N/A';?>"><i class="inactivered"></i>&nbsp;<?php echo date('d M, Y', strtotime($pbs_data['updated'])); ?></a>
				<?php } ?>


      </div>
      <div class="requests-col req-col-5 requests-actions">
			<?php if( $pbs_data['project_status'] == 0 ){ ?>

			 <a href="javascript:void(0);" class="project_request_accept tipText" data-project="<?php echo $project_id;?>"  data-projectboard="<?php echo $pbs_data['pbs_id'];?>"  data-sender="<?php echo $pbs_data['sender'];?>"  title="Accept Request" data-redirect="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'index', $project_id,$pbs_data['sender'], 1,'refer'=>$refer_url, 'admin' => FALSE ), TRUE); ?>" ><i class="activegreen"></i></a>

			 <a href="javascript:void(0);" title="Decline Request" class="tipText decline_list1" data-id="<?php echo $pbs_data['pbs_id']; ?>" data-toggle="modal" data-target="#popup_model_box_decline" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'decline_opp_interest', 'project_id'=>$project_id, 'board_id'=>$pbs_data['pbs_id'], 'login_user'=>$user_login, 'project_status'=>2, 'id'=>$pbs_data['reason_id'], 'admin' => FALSE ), TRUE); ?>" data-project="<?php echo $project_id;?>" data-sender="<?php echo $pbs_data['sender'];?>" ><i class="inactivered"></i></a>

			 <a class="moreinfo tipText" href="#" title="More Information" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'details',$pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" ><i class="infoblack-icon"></i></a>

			<?php }
			if( $pbs_data['project_status'] == 1 ){ ?>

			 <a class="moreinfo tipText" href="#" title="More Information" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'details',$pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" ><i class="infoblack-icon"></i></a>

			<?php }
			if( $pbs_data['project_status'] == 2 ){

			?>

			 <a href="javascript:void(0);" class="project_request_accept tipText" data-project="<?php echo $project_id;?>"  data-projectboard="<?php echo $pbs_data['pbs_id'];?>"  data-sender="<?php echo $pbs_data['sender'];?>"  title="Accept Request" data-redirect="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'index', $project_id,$pbs_data['sender'], 1, 'admin' => FALSE ), TRUE); ?>" ><i class="activegreen"></i></a>

			 <a class="moreinfo tipText" href="#" title="More Information" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'details',$pbs_data['sender'], 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" ><i class="infoblack-icon"></i></a>

			<?php } ?>


	  </div>
   </div>


	<?php }  ?>
<?php } else { ?>

<div class="">
	<div class="no-summary-found">NO REQUESTS</div>
</div>
<?php } ?>

