<?php if(isset($data) && !empty($data)){ ?>
	<?php foreach ($data as $key => $value) {
		$project_id = $value['po']['project_id'];
		$user_detail = $value['ud'];
		$project_detail = $value['p'];
		$others = $value[0];
		$others_wt = $value['wt'];
		$users = array_unique(explode(',',$others_wt['users']));
		$project_status = $others['project_status'];
        $total_tasks = $others_wt['total_tasks'];
        $total_workspaces = $others_wt['total_workspaces'];
        $total_owners = $others_wt['total_owners'];
        $total_shares = $others_wt['total_shares'];
        $skill_match_percent = $others['skill_match_percent'];
        $domain_match_percent = $others['domain_match_percent'];
        $subject_match_percent = $others['subject_match_percent'];
        $match_tasks_counts = $others['match_tasks_counts'];
        $match_project_counts = $others['match_project_counts'];
        $unvailable_days = $others['unavailable_count'];
        $block_days = $others['block_count'];

        $total_workspaces = (isset($others_wt['total_workspaces']) && !empty($others_wt['total_workspaces'])) ? $others_wt['total_workspaces'] : 0;
        $total_tasks = (isset($others_wt['total_tasks']) && !empty($others_wt['total_tasks'])) ? $others_wt['total_tasks'] : 0;
        $match_project_counts = (isset($others['match_project_counts']) && !empty($others['match_project_counts'])) ? $others['match_project_counts'] : 0;
        $unvailable_days = (isset($others['unavailable_count']) && !empty($others['unavailable_count'])) ? $others['unavailable_count'] : 0;
        $block_days = (isset($others['block_count']) && !empty($others['block_count'])) ? $others['block_count'] : 0;
        $skill_match_percent = (isset($others['skill_match_percent']) && !empty($others['skill_match_percent'])) ? $others['skill_match_percent'] : 0;
        $subject_match_percent = (isset($others['subject_match_percent']) && !empty($others['subject_match_percent'])) ? $others['subject_match_percent'] : 0;
        $domain_match_percent = (isset($others['domain_match_percent']) && !empty($others['domain_match_percent'])) ? $others['domain_match_percent'] : 0;

        $total_owners = (isset($others_wt['total_owners']) && !empty($others_wt['total_owners'])) ? $others_wt['total_owners'] : 0;
        $total_shares = (isset($others_wt['total_shares']) && !empty($others_wt['total_shares'])) ? $others_wt['total_shares'] : 0;
        $total_people = $total_owners + $total_shares;

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
		<div class="loc-col opp-col opp-col-1">
		  	<div class="opp-project-details">
				<div class="opp-project-left <?php echo $color_code; ?>"> <i class="projectwhite-icon"></i></div>
				<div class="opp-project-middle">
					<span class="opp-project-name" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'details', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" ><?php echo htmlentities($project_detail['title'], ENT_QUOTES, "UTF-8"); ?></span>
					<span class="opp-project-date"><?php echo date('d M, Y', strtotime($project_detail['start_date'])); ?> → <?php echo date('d M, Y', strtotime($project_detail['end_date'])); ?> </span>
				</div>
				<div class="opp-pss"> <i class="flag <?php echo $project_status; ?> tipText" data-original-title="<?php echo $status_tip; ?>"></i> </div>
		  	</div>
		</div>  
		<div class="loc-col opp-col opp-col-2">
		  	<div class="team-col-cont">
				<ul class="peoplecount" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'opppeople', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal">
			  		<li class="dark-gray tipText <?php if(!isset($total_owners) || $total_owners == 0){ echo 'zero_class'; }?>" title="<?php if( $total_owners !=1){ echo $total_owners." Owners";} else { echo $total_owners." Owner";}?>"><?php echo $total_owners; ?></li>
			  		<li class="light-gray tipText <?php if(!isset($total_shares) || $total_shares == 0){ echo 'zero_class tbdr'; }?>" title="<?php if( $total_shares !=1){ echo $total_shares." Sharers";} else { echo $total_shares." Sharer";}?>"><?php echo $total_shares; ?></li>
				</ul>
				<div class="teaminfotext "><?php echo ($total_people <= 1) ? $total_people.' Person' : number_format($total_people,0) . ' People'; ?></div>
		  	</div> 
		</div> 
		<div class="loc-col opp-col opp-col-3">
		  	<div class="opp-work-info">
				<div class="opp-work-item"> <i class="opp-icon workspaceblack"></i><?php echo ($total_workspaces == 1) ? $total_workspaces . ' Workspace' : number_format($total_workspaces,0) . ' Workspaces'; ?></div>
				<div class="opp-work-item"> <i class="opp-icon taskblack"></i><?php echo ($total_tasks == 1) ? $total_tasks . ' Task' : number_format($total_tasks,0) . ' Tasks'; ?> </div>
		  	</div>
		</div>
		<div class="loc-col opp-col opp-col-4">
			<span class="competencies-list">
				<span class="competencies-list-bg competencies-list-bg-skill tipText" title="Skills" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppskills', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
					<i class="skills-icon"></i>
					<span class="sks-title"><?php echo $skill_match_percent; ?>%</span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppsubjects', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
					<i class="subjects-icon"></i>
					<span class="sks-title"><?php echo $subject_match_percent; ?>%</span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'oppdomains', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" >
					<i class="domain-icon"></i>
					<span class="sks-title"><?php echo $domain_match_percent; ?>%</span>
				</span>
			</span>
		</div>
		<div class="loc-col opp-col opp-col-5">
			<div class="opp-work-info-wrap">
				<div class="oppinfo-eng">
				<div class="opp-work-item"> <i class="opp-icon projectblack tipText" data-original-title="Your Project Count In Project Period"></i><?php echo number_format($match_project_counts,0);  //echo ($match_project_counts <= 1) ? $match_project_counts . ' Project' : number_format($match_project_counts,0) . ' Projects'; ?> </div>
				<div class="opp-work-item"> <i class="opp-icon taskblack tipText" data-original-title="Your Task Count In
Project Period"></i> </i>
				<?php //echo ($unvailable_days <= 1) ? $unvailable_days . ' Day' : number_format($unvailable_days,0) . ' Days'; ?>
				<?php echo number_format($match_tasks_counts,0); //echo ($match_tasks_counts <= 1) ? $match_tasks_counts . ' Task' : number_format($match_tasks_counts,0) . ' Tasks'; ?>
				</div>
			</div>
				<div class="oppinfo-match">
					<span><i class="absenceblack18 tipText" data-original-title="Your Absence Count
In Project Period"></i> <?php echo number_format($unvailable_days,0); ?></span>
					<span><i class="blockblack18 tipText" data-original-title="Your Work Block Count
In Project Period"></i> <?php echo number_format($block_days,0); ?> </span>
				</div>
			</div>
			</div>
		<div class="loc-col opp-col opp-col-6 opp-actions">
			<?php $reqData = boardRequestSent($project_id); ?>
			<?php if($reqData){ 
				
				$declinereasons = array();
				$reasonresponse = $this->Common->board_data_by_project_sender($project_id,$reqData['sender']);
				if( isset($reasonresponse) && !empty($reasonresponse) ){
					$declinereasons = $this->Common->show_reason($reasonresponse['BoardResponse']['reason']);
				}				
			?>				
				<?php if($reqData['project_status'] == 0){ ?>
					
					
					<a style="opacity:1;cursor:default;" class="request-sent tipText" href="#" title="Request: <?php echo $reqData['board_msg'] ?>"><i class="requestsent-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($reqData['updated'])); ?></a>

				<?php }
				if($reqData['project_status'] == 2){ ?>
					
					<a style="opacity:1;cursor:default;"  class="request-sent tipText" href="#" title="Request: <?php echo $reqData['board_msg'] ?>"><i class="requestsent-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($reqData['created'])); ?></a>
					<a style="opacity:1;cursor:default;"  class="request-rejected tipText" href="#" title="Response: <?php echo ( isset($declinereasons['DeclineReason']['reasons']) && !empty($declinereasons['DeclineReason']['reasons']) ) ? $declinereasons['DeclineReason']['reasons'] : 'N/A';?>"><i class="request-received-icon"></i>&nbsp;<?php echo date('d M, Y', strtotime($reqData['updated'])); ?></a>
					
					
				<?php } ?>
				
			<?php } else { ?>
				
				<?php 
				//pr($users);
				if(isset($users) && !in_array($this->Session->read('Auth.User.id'),$users)){ ?>
				<a class="requestjoin tipText" href="#" title="Request To Join Project" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'join_req', $project_id, 'details', 'admin' => false)); ?>" data-target="#modal_request" data-toggle="modal" data-project="<?php echo $project_id; ?>"><i class="requestjoin-icon"></i></a>
				<?php } ?>
				<?php
					$opacity = '';				
				//pr($users);
				if(isset($users) && in_array($this->Session->read('Auth.User.id'),$users)){ 
				   $opacity = 'style="opacity:1; ';
				}
				?>
				<a <?php echo $opacity; ?>  class="moreinfo tipText" href="#" title="More Information" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'more_information', $project_id, 'details', 'admin' => false)); ?>" data-target="#modal_information" data-toggle="modal" ><i class="infoblack-icon"></i></a>
				
				<span class="team_member">
				<?php 
				//pr($users);
				if(isset($users) && in_array($this->Session->read('Auth.User.id'),$users)){ 
					echo "Team Member";
				}?>
				</span>
				
				
				
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>
<style>
 
.tbdr{ border-right: 1px solid #bcbcbc !important;}
</style>
