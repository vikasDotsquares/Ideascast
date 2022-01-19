<?php 
if(isset($data) && !empty($data)){
	foreach ($data as $key => $value) {
		//pr($value);
		$project_id = $value['po']['project_id'];
		$user_detail = $value['ud'];
		$project_detail = $value['p'];
		$others = $value[0];
		$other_users = $value['wt'];		 
		//$users = array_unique(explode(',',$others['users']));
		$project_status = $others['project_status'];
		
		$accept_request_count = $others['accept_request_count'];
		$pending_request_count = $others['pending_request_count'];
		$decline_request_count = $others['decline_request_count'];
		
		//project total competencies
		$total_skills = $others['total_skills'];
		$total_subjects = $others['total_subjects'];
		$total_domains = $others['total_domains'];
		//used competencies
		$skill_count = $others['skill_count'];
		$domain_count = $others['domain_count'];
		$subject_count = $others['subject_count'];
		//competencies %
		$skill_match_percent = $others['skill_match_percent'];
		$domain_match_percent = $others['domain_match_percent'];
		$subject_match_percent = $others['subject_match_percent'];
		 
        $total_owners = $other_users['total_owners'];
        $total_shares = $other_users['total_shares'];
        $total_people = $other_users['total_people'];

        $color_code = str_replace("panel-", "project-", $project_detail['color_code']);

        $status_tip = '';
        if($project_status == 'progressing'){
        	$status_tip = 'In Progress';
        }
        else if($project_status == 'overdue'){
        	$status_tip = 'Overdue';
        }
        else if($project_status == 'not_started'){
        	$status_tip = 'Not Started';
        }
	?>			
	<div class="requests-project-info">
	<!-- Active class add here -->
   <div class="opp-project-details">
      <div class="opp-project-left <?php echo $color_code; ?>"> <i class="projectwhite-icon"></i></div>
      <div class="requests-project-info-inner">
         <div class="requests-project-first">
            <div class="opp-project-middle project_request">
               <span class="opp-project-name action " data-project="<?php echo $project_id;?>"><?php echo htmlentities($project_detail['title'], ENT_QUOTES, "UTF-8"); ?></span>
               <span class="opp-project-date"><?php echo date('d M, Y', strtotime($project_detail['start_date'])); ?> → <?php echo date('d M, Y', strtotime($project_detail['end_date'])); ?> </span>
            </div>
			 
            <div class="opp-pss fl-icon">
               <i class="flag <?php echo $project_status; ?> tipText" data-original-title="<?php echo $status_tip; ?>"></i>
               <div class="progress-col-cont">
                  <ul class="workcount">
                     <li class="green-bg tipText <?php if(!isset($accept_request_count) || $accept_request_count == 0){ echo 'zero_class'; }?>" title="" data-original-title="Accepted Requests"><?php echo $accept_request_count;?></li>
                     <li class="red tipText <?php if(!isset($decline_request_count) || $decline_request_count == 0){ echo 'zero_class'; }?>" title="" data-original-title="Declined Requests"><?php echo $decline_request_count;?></li>
                     <li class="yellow tipText <?php if(!isset($pending_request_count) || $pending_request_count == 0){ echo 'zero_class'; }?>" title="" data-original-title="Pending Requests"><?php echo $pending_request_count;?></li>
                  </ul>
               </div>
            </div>
         </div>
         <div class="requests-project-second ">
            <div class="progress-col-cont">
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="blue barTip bar-border ctip tipText" title="Team has <?php echo $skill_count;?> of <?php echo $total_skills;?> Project Skills (<?php echo $skill_match_percent;?>%)" style="width:<?php echo $skill_match_percent; ?>%"></span>
                  </div>
				  <div class="proginfotext ctip tipText" title="<?php if(!empty($total_skills)){ ?><?php echo "Team has $skill_count of $total_skills Project Skills"; if($skill_count > 0){  echo " (".$skill_match_percent."%)";  }   } else{ echo "No Project Skills"; } ?>"><?php echo (!empty($total_skills)) ? $skill_count : 'None'; ?></div>
               </div>
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="red2 barTip bar-border ctip tipText" title="Team has <?php echo $subject_count;?> of <?php echo $total_subjects;?> Project Subjects (<?php echo $subject_match_percent;?>%)" style="width:<?php echo $subject_match_percent; ?>%"></span>
                  </div>                  
				  <div class="proginfotext ctip tipText" title="<?php if(!empty($total_subjects)){ ?><?php echo "Team has $subject_count of $total_subjects Project Subjects"; if($subject_count > 0){  echo " (".$subject_match_percent."%)";  }   } else{ echo "No Project Subjects"; } ?>"><?php echo (!empty($total_subjects)) ? $subject_count : 'None'; ?></div>
				  
				  
               </div>
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="green-bg barTip bar-border ctip tipText" title="Team has <?php echo $domain_count;?> of <?php echo $total_domains;?> Project Domains (<?php echo $domain_match_percent;?>%)" style="width:<?php echo $domain_match_percent; ?>%"></span>
                  </div>
				  <div class="proginfotext ctip tipText" title="<?php if(!empty($total_domains)){ ?><?php echo "Team has $domain_count of $total_domains Project Domains"; if($domain_count > 0){  echo " (".$domain_match_percent."%)";  }   } else{ echo "No Project Domains"; } ?>"><?php echo (!empty($total_domains)) ? $domain_count : 'None'; ?></div>
				  
				  
               </div>
            </div>
            <div class="progress-col-cont">
               <ul class="workcount" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'opppeople', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
                  <li class="dark-gray tipText <?php if(!isset($total_owners) || $total_owners == 0){ echo 'zero_class'; }?>" title="<?php echo $total_owners; if( $total_owners != 1 ){ echo " Owners";} else { echo " Owner"; } ?>"><?php echo $total_owners; ?></li>
                  <li class="light-gray tipText <?php if(!isset($total_shares) || $total_shares == 0){ echo 'zero_class'; }?>" title="<?php echo $total_shares; if( $total_shares != 1 ){ echo " Sharers";} else { echo " Sharer"; } ?> "><?php echo $total_shares; ?></li>
               </ul>
               <div class="proginfotext "><span class="" ><?php echo ($total_people <= 1) ? $total_people.' Person' : number_format($total_people,0) . ' People'; ?></span></div>
            </div>
         </div>
      </div>
   </div>
</div>
	
	<?php }  ?>
<?php } else { ?>

<div class="">
	<div class="no-summary-found">NO PROJECTS</div>
</div>
<?php }   ?>