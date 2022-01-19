<?php

$current_user_id = $this->Session->read('Auth.User.id');
$post_user_id = $user_id;

$user_opt_status = user_opt_status($post_user_id);
$opt_reward_table = user_table_opt_status($post_user_id);

/*$user_opt_status = user_opt_status($current_user_id);
$opt_reward_table = user_table_opt_status($current_user_id);*/
?>

<?php if(isset($allProjects) && !empty($allProjects)) {

    	// get all those projects in which logged in user is owner
    	if($current_user_id != $post_user_id) {
    		$inter_projects = [];
		    $inter = array_intersect($user_projects, $my_projects);
		    if(isset($inter) && !empty($inter)) {
		    	foreach ($inter as $key => $value) {
		    		$ProjectPermit = $this->ViewModel->projectPermitType($key, $current_user_id);
		    		if($ProjectPermit){
		    			$inter_projects[$key] = ['title' => $value, 'permit' => $ProjectPermit];
		    		}
		    	}
		    	$allProjects = $inter_projects;
		    }
		}
		else{
			$inter_projects = [];
			foreach ($allProjects as $key => $value) {
				$ProjectPermits = $this->ViewModel->projectPermitType($key, $current_user_id);
	   //  		if($ProjectPermits){
	    			$inter_projects[$key] = ['title' => $value, 'permit' => $ProjectPermits];
	    		// }
			}
			$allProjects = $inter_projects;
		}
	}
	// pr($allProjects, 1);
if(isset($allProjects) && !empty($allProjects)) {
?>

<div class="row">
    <div class="col-xs-12 rewards-listing-btn" <?php if(!$user_opt_status) { ?> style="display: none;" <?php } ?>>
        <div class="pull-right common-menus">
            <a href="#" class="btn btn-success btn-sm tipText all-project-ov-achieved <?php if(!$user_opt_status) { ?> not-editable <?php } ?>" title="All Projects - OV Achieved" <?php if(!$opt_reward_table) { ?> style="display: none;" <?php } ?>>
                <i class="icon-achieved-white"></i>
            </a>
            <a href="#" class="btn btn-primary btn-sm toggle-accordion <?php if(!$user_opt_status) { ?> not-editable <?php } ?>" title="Expand All">
                <i class="fa fa-expand"></i>
            </a>
        </div>
    </div>
</div>
<?php
}

if($user_opt_status) {
?>

    <?php if(isset($allProjects) && !empty($allProjects)) {
// pr($allProjects);
    	// get all those projects in which logged in user is owner
    	/*if($current_user_id != $post_user_id) {
    		$inter_projects = [];
		    $inter = array_intersect($user_projects, $my_projects);
		    if(isset($inter) && !empty($inter)) {
		    	foreach ($inter as $key => $value) {
		    		$ProjectPermit = $this->ViewModel->projectPermitType($key, $current_user_id);
		    		if($ProjectPermit){
		    			$inter_projects[$key] = $value;
		    		}
		    	}
		    	$allProjects = $inter_projects;
		    }
		}*/
        foreach ($allProjects as $project_id => $pdata) {
        	// if selected user is not me.
        	$project_title = $pdata['title'];
        	$ProjectPermit = $pdata['permit'];
            if($current_user_id != $post_user_id) {
            	// get all those projects in which logged in user is owner
	            // $ProjectPermit = $this->ViewModel->projectPermitType($project_id, $current_user_id);
	            if($ProjectPermit){
	            	// get all those projects in which selected user is owner or sharer
	                $UserProjectPermit = $this->ViewModel->projectPermitType($project_id, $post_user_id);
	                if($UserProjectPermit) {
	                    $projectPermitType = 'owner';
	                }
	                else{
	                    $projectPermitType = 'sharer';
	                }
	            }
	        }
	        else{
	        	// if selected user is logged in user than show his all projects.
	        	// $ProjectPermit = $this->ViewModel->projectPermitType($project_id, $current_user_id);
	        	if($ProjectPermit){
	        		$projectPermitType = 'owner';
	        	}
	        	else{
	        		$projectPermitType = 'sharer';
	        	}
	        }

	        $show_owner_data = true;
	        if($current_user_id == $post_user_id) {
		        // $cPermit = $this->ViewModel->projectPermitType($project_id, $current_user_id);
		        if($ProjectPermit) {
		        	$show_owner_data = true;
		        }
		        else{
		        	$show_owner_data = false;
		        }
		    }
            if(dbExists('Project', $project_id)) {
	            $offer_exists = project_offers($project_id, true);
	            $project_charity = project_charity($project_id);
	            $project_reward_setting = project_reward_setting($project_id, 1);

	            $project_reward_assignments = project_reward_assignments($project_id);
	            $project_total_assign = 0;
	            if($project_reward_assignments) {
	            	foreach ($project_reward_assignments as $key => $value) {
	            		$project_total_assign += $value['RewardAssignment']['allocated_rewards'];
	            	}
	            }

	            $user_earned_points = project_reward_assignments($project_id, $post_user_id );

	            $total_earned = 0;
                if($user_earned_points) {
                    foreach ($user_earned_points as $rdKey => $rdVal) {
                        $total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
                    }
                }

	            $project_redeemed = project_redeemed_data($project_id, $post_user_id);
            	$total_redeem = 0;
	            if($project_redeemed) {
	            	foreach ($project_redeemed as $rdKey => $rdVal) {
	            		$total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
	            	}
	            }

	            $project_accelerated_points = project_accelerated_points($project_id, $post_user_id);

	            if($project_accelerated_points){
	            	$total_earned += $project_accelerated_points;
	            }

	        	$total_remaining = $total_earned - $total_redeem;

	            $total_remaining_percent = $total_redeem_percent = 0;
				if( (isset($total_earned) && !empty($total_earned)) && (isset($total_redeem) && !empty($total_redeem)) ) {
				    $total_redeem_percent = ($total_redeem / $total_earned) * 100;
				}
				if( (isset($total_earned) && !empty($total_earned)) && (isset($total_remaining) && !empty($total_remaining)) ) {
				    $total_remaining_percent = ($total_remaining / $total_earned) * 100;
				}

     ?>
    <div class="panel panel-default panel-parent" data-project="<?php echo $project_id; ?>" data-permit="<?php echo $projectPermitType; ?>">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="project-name" style="font-weight: 600;" data-toggle="collapse" data-parent="#rewards_accordion" href="#collapse<?php echo $project_id; ?>"><i class="fa fa-briefcase"></i> <?php echo htmlentities( $project_title ); ?></span>
                <div class="pull-right right-menus">
                	<span class="ico-nudge ico-reward tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'type' => 'reward', 'admin' => false)); ?>"></span>
                    <div class="project-summary">
                        <span class="txt-grn tipText" title="Achieved"><?php echo $total_earned; ?></span>
                        <span class="txt-prpl tipText" title="Redeemed"><?php echo $total_redeem; ?></span>
                        <span class="txt-red tipText" title="Available"><?php echo $total_remaining; ?></span>
                    </div>
                    <?php if($offer_exists) { ?>
                    <a class="btn btn-white btn-xs offer-tag-wrap tipText" title="Project - Offers" style="padding: 3px 5px 0 5px;">
                        <i class="fa fa-tag" style="font-size: 16px;"></i>
                    </a>
                    <?php } ?>
                    <?php if($project_charity) { ?>
                    <a class="btn btn-white btn-xs charity-wrap tipText" title="Project - Charity OV">
                        <i class="icon-charity"></i>
                    </a>
                    <?php } ?>
                    <?php //if($show_owner_data){ ?>
                    <a class="btn btn-white btn-xs achieved-wrap tipText " title="Project - OV Achieved" <?php if(!$opt_reward_table ) { ?> style="display: none;" <?php } ?> >
                        <i class="icon-achieved"></i>
                    </a>
                    <?php //} ?>
                    <a class="collapse-trigger btn btn-white btn-xs" title="Expand" data-toggle="collapse" data-parent="#rewards_accordion" href="#collapse<?php echo $project_id; ?>">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </h4>
        </div>
        <div id="collapse<?php echo $project_id; ?>" class="panel-collapse collapse">
            <div class="panel-body">
            	<?php if($show_owner_data) { ?>
                <div class="col-sm-12">
                    <div class="row border-sec">
                        <div class="colume-1 border-right">
							<div class="project-info-padd">
	                            <div class="col-section" style="width: 100%; text-align: center;">
	                                <div class="total-numbers"><?php if($project_reward_setting){ echo $project_reward_setting['RewardSetting']['ov_allocation'];}else{echo '0';} ?></div>
	                            </div>
	                            <div class="col-section" style="width: 100%;">
	                                <div class="icon-ov-reward" style="float: left;"></div>
	                                <div class="total-text" style="float: left; width: 49%;">Project<br />Allocation</div>
	                            </div>
							</div>
                        </div>
                        <div class="colume-2 border-right">
                        	<?php
                        	$total_dis_percent = 0;
                        	$project_total_accelerated = $project_total_assign + project_accelerated_points($project_id);
                        	if($project_reward_setting){
                        		$total_dis_percent = ($project_total_accelerated/$project_reward_setting['RewardSetting']['ov_allocation'])*100;
                        		$total_dis_percent = round($total_dis_percent);
                        	}
                        	?>
							<div class="project-info-padd">
	                            <div class="col-section">
	                                <div class="total-numbers txt-aqua"><?php echo $project_total_accelerated; ?></div>
	                                <div class="total-text">Project Distributed</div>
	                            </div>
	                            <div class="col-section">
	                                <div class="distributed-right">
	                                    <div class="c100 p<?php echo ($total_dis_percent <= 100) ? $total_dis_percent : 100; ?> aqua-bar mid">
	                                        <span></span>
	                                        <div class="icon-ov mid"></div>
	                                        <div class="slice">
	                                            <div class="bar"></div>
	                                            <div class="fill"></div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
						</div>
                        <div class="colume-3">
                        	<?php
                        	// $project_users = project_users($project_id);
                        	$project_users = $this->Permission->project_all_users($project_id);


                        	if(isset($project_users) && !empty($project_users)) { ?>
                        		<div class="container1 project-thumb-slider">
                        			<div class="owl-carousel">
                				<?php
                        		foreach ($project_users as $key => $pu_data) {
                        			$ud = $pu_data['user_details'];
                        			$userId = $ud['user_id'];
                        			// pr($userId);
                        			$user_opt_status = user_opt_status($userId);
                                    if($user_opt_status) {
                                    	$uimage = $ud['profile_pic'];
                                    	$ujob = $ud['job_title'];
                                    	$uname = $pu_data[0]['fullname'];
                                    	$user_image = SITEURL . 'images/placeholders/user/user_1.png';
										$job_title = 'Not Available';
										if (isset($uimage) && !empty($uimage)) {
											$profile_pic = $ud['profile_pic'];

											if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
												$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
											}
										}
										if(isset($ujob) && !empty($ujob)){
											$job_title = $ud['job_title'];
										}
										$chat_btns = CHATHTML($userId, $project_id);
	                        			// $user_chat_popover = user_chat_popover($userId, $project_id);

	                        			$project_rewards = project_reward_assignments($project_id, $userId);
	                        			$reward_popover = '';
							  			$total_allocated = 0;
							  			$by_me = $by_others = 0;
							  			$show_reward_popover = false;
										$total_amount = 0;
										if($project_rewards) {
											foreach ($project_rewards as $key => $value) {
												$data = $value['RewardAssignment'];
												$amount = $data['allocated_rewards'];
												$total_allocated += $amount;
												if($data['given_by'] == $current_user_id) {
													$by_me += $amount;
												}
												else{
													$by_others += $amount;
												}
											}
											$by_me = (isset($by_me) && !empty($by_me)) ? $by_me : '0';
											$by_others = (isset($by_others) && !empty($by_others)) ? $by_others : '0';
											$by_acclerate = project_accelerated_points($project_id, $userId);
											$total_amount = $by_me + $by_others + $by_acclerate;
											$reward_popover .= '<div class="reward_popover">';
												$reward_popover .= '<span class="popover_amount_wrap">';
													$reward_popover .= '<span class="popover_label">Given by Me:</span> ';
													$reward_popover .= '<span class="popover_amount">'.$by_me.'</span> ';
												$reward_popover .= '</span>';
												$reward_popover .= '<span class="popover_amount_wrap">';
													$reward_popover .= '<span class="popover_label">Received from Members:</span> ';
													$reward_popover .= '<span class="popover_amount">'.$by_others.'</span> ';
												$reward_popover .= '</span>';
												$reward_popover .= '<span class="popover_amount_wrap">';
													$reward_popover .= '<span class="popover_label">By Accelerators:</span> ';
													$reward_popover .= '<span class="popover_amount">'.$by_acclerate.'</span> ';
												$reward_popover .= '</span>';
											$reward_popover .= '</div>';
											$show_reward_popover = true;
										}
										// pr($user_chat_popover['user_name']);
	                    			?>
		                    			<div class="item" data-total="<?php echo $total_amount; ?>">
		                    				<img class="user_popover" data-user="<?php echo $userId; ?>" src="<?php echo $user_image; ?>" data-content="<div class=''><p><?php echo htmlentities($uname,ENT_QUOTES); ?></p><p><?php echo htmlentities($job_title,ENT_QUOTES); ?></p><?php echo $chat_btns; ?></div>" >
		                    				<span class="txt-grn txt-600 user-project-reward-total" <?php if(empty($total_amount)){ ?> style="cursor: default !important;"<?php } ?> data-content='<?php echo $reward_popover; ?>' <?php if($show_reward_popover){ ?>data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url( array( "controller" => "rewards", "action" => "user_reward_detail", $userId, $project_id, 'admin' => FALSE ), true ); ?>"<?php } ?>><?php echo $total_amount; ?></span>
		                    			</div>
	                    			<?php }// END FOREACH ?>
                    			<?php }// END opt setting check ?>
                        		</div>
                        	</div>
                        	<?php }else{ ?>
                        		<div class="info-msg">No data found</div>
                        	<?php } ?>
                        </div>
                    </div>
                </div>
            	<?php } ?>

				<div class="ov-project-status">
					<?php
						$reward_acceleration = reward_acceleration($post_user_id, $project_id);

						$rewards_by_acceleration = 0;
						if(isset($reward_acceleration) && !empty($reward_acceleration)) {
							$rewards_by_acceleration = array_sum(array_map(function($item) {
							    return $item['RewardHistory']['accelerated_rewards'];
							}, $reward_acceleration));
						}
				 	?>
				    <div class="ov-project-col border-right1 padd15">
				        <div class="ov-project-status-in">
				            <div class="ov-logo-status">
				                <div class="icon-ov-reward"></div>
				            </div>
				            <div class="ov-achieved-cont">
				                <h5>Project <br> Achieved</h5>
				                <p>By Accelerations: <?php echo $project_accelerated_points; ?></p>
				            </div>
				            <div class="ov-status-info">
				                <div class="c100 p<?php echo (!empty($total_earned)) ? '100' : '0'; ?> green-bar mid">
				                    <div class="number mid"></div>
				                    <div class="slice">
				                        <div class="bar"></div>
				                        <div class="fill"></div>
				                    </div>
				                    <div class="nub-status txt-grn"><?php echo $total_earned; ?></div>
				                </div>
				            </div>
				        </div>
				    </div>
				    <div class="ov-project-col border-right2 padd15">
				        <div class="ov-project-status-in">
				            <div class="ov-logo-status">
				                <div class="icon-ov-reward"></div>
				            </div>
				            <div class="ov-achieved-cont">
				                <h5>Project <br> Redeemed</h5>
				            </div>
				            <div class="ov-status-info">
				                <div class="c100 p<?php echo round($total_redeem_percent); ?> purple-bar mid">
				                    <div class="number mid"></div>
				                    <div class="slice">
				                        <div class="bar"></div>
				                        <div class="fill"></div>
				                    </div>
				                    <div class="nub-status txt-prpl"><?php echo $total_redeem; ?></div>
				                </div>
				            </div>
				        </div>
				    </div>
				    <div class="ov-project-col border-right3 padd15">
				        <div class="ov-project-status-in">
				            <div class="ov-logo-status">
				                <div class="icon-ov-reward"></div>
				            </div>
				            <div class="ov-achieved-cont">
				                <h5>Project <br> Available</h5>
				            </div>
				            <div class="ov-status-info">
				                <div class="c100 p<?php echo round($total_remaining_percent); ?> red-bar mid">
				                    <div class="number mid"></div>
				                    <div class="slice">
				                        <div class="bar"></div>
				                        <div class="fill"></div>
				                    </div>
				                    <div class="nub-status txt-red"><?php echo $total_remaining; ?></div>
				                </div>
				            </div>
				        </div>
				    </div>
				    <div class="ov-project-col padd15">
				    	<?php
					    	$project_accelerate = project_accelerate_setting($project_id, 0, 1);
				    	?>
				        <div class="ov-project-status-in">
				            <div class="ov-logo-status">
				                <div class="icon-ov-reward"></div>
				            </div>
				            <div class="ov-achieved-cont">
				                <h5 class="accelerator-padd">Accelerator</h5>
				                <?php if($project_accelerate){ ?>
					                <p><?php echo date('d M Y', strtotime($project_accelerate['RewardAccelerate']['accelerate_date'])); ?></p>
					                <?php if(can_accelerate($project_id, false)){ ?>
						                <?php if($show_owner_data){ ?>
					                		<span class="allocation-issue">Allocation Required</span>
					                	<?php } ?>
				                	<?php } ?>
					            <?php }  ?>
				            </div>
				            <div class="ov-status-info accelerator-popover" <?php if($project_accelerate){ ?> data-content="<div class='com-user' style='font-size: 12px;'><?php echo htmlentities($project_accelerate['RewardAccelerate']['reason'], ENT_QUOTES, "UTF-8"); ?></div>" <?php } ?> style="margin-top: 16px;">
				                	<span class="accelerator-info"><?php if($project_accelerate){ echo $project_accelerate['RewardAccelerate']['accelerate_percent'].'%';}else{echo 'N/A';} ?></span>
				            </div>
				        </div>
				    </div>
				</div>

				<div class="ov-table-list">
				    <table class="table table-bordered list-table-accelerate table-fixed">
				        <thead>
				            <tr>
				                <th class="col-ov-1">Accelerator Created</th>
				                <th class="col-ov-2">Title</th>
				                <th class="col-ov-3" align="center" style="text-align: center;">%</th>
				                <th class="col-ov-4" align="center" style="text-align: center;">Accelerated By</th>
				                <th class="col-ov-5" style="position: relative;">
				                	<span>Date</span>
				                </th>
				                <th class="col-ov-6">Reason</th>
				            </tr>
				        </thead>
				        <tbody>
				        	<?php
				        	$accelerate_history = project_accelerate_history($project_id, 1);
				        	$accelerate_popover = '<div class="acce-history-pop">';
	        				$accelerate_popover .= '<span class="his-row"><span>Pre-Acceleration Available OV:</span> 0</span>';
	        				$accelerate_popover .= '<span class="his-row"><span>Post Acceleration Available OV:</span> 0</span>';
	        				$accelerate_popover .= '</div>';
	        				$accelerate_popover_title = 'Acceleration';
				        	// pr($accelerate_history);
				        	if($accelerate_history) { ?>
				        		<?php foreach ($accelerate_history as $uekey => $uevalue) {
				        			$assigned_data = $uevalue['RewardAccelerationHistory'];
				        			$accelerate_percent = $assigned_data['accelerate_percent'];
				        			$given_by = $assigned_data['given_by'];
				        			$accelerate_date = $assigned_data['accelerate_date'];

			        				// $activity_data = getByDbId('Project', $assigned_data['project_id']);
			        				// $activity_title = strip_tags($activity_data['Project']['title']);

				        			$user_chat_popover = user_chat_popover($given_by, $assigned_data['project_id']);
				        			$accelerate_created = (isset($assigned_data['accelerate_created']) && !empty($assigned_data['accelerate_created'])) ? $assigned_data['accelerate_created'] : $assigned_data['accelerate_date'];

				        			$accID = $assigned_data['reward_accelerate_id'];
				        			$activity_data = getByDbId('RewardAccelerate', $accID);
			        				$activity_title = strip_tags($activity_data['RewardAccelerate']['title']);
				        			$accelerated_history = accelerated_history($post_user_id, $assigned_data['project_id'], $accID);

				        			$accelerate_popover_flag = false;
				        			if(isset($accelerated_history) && !empty($accelerated_history)){
				        			$accelerate_popover = '';
				        				$accelerate_popover_flag = true;
				        				$accelerate_popover .= '<div class="acce-history-pop">';
				        				$accelerate_popover .= '<span class="his-row"><span>Pre-Acceleration Available OV:</span> '.$accelerated_history['remaining_amount'].'</span>';
				        				$accelerate_popover .= '<span class="his-row"><span>Post Acceleration Available OV:</span> '.($accelerated_history['remaining_amount'] + $accelerated_history['accelerated_amount']).'</span>';
				        				$accelerate_popover .= '</div>';
				        			}
				        			else{
				        				$accelerate_popover = '<div class="acce-history-pop">';
				        				$accelerate_popover .= '<span class="his-row"><span>Pre-Acceleration Available OV:</span> 0</span>';
				        				$accelerate_popover .= '<span class="his-row"><span>Post Acceleration Available OV:</span> 0</span>';
				        				$accelerate_popover .= '</div>';
				        			}
				        			?>
						            <tr>
						                <td class="col-ov-1" ><?php echo $this->Wiki->_displayDate(date('Y-m-d',strtotime($accelerate_created)), $format = 'd M Y'); ?></td>
						                <td class="col-ov-2"><?php echo (empty($activity_title)) ? 'N/A' : $activity_title; ?></td>
						                <td class="col-ov-3" align="center">
						                	<span class="ov-accelerate-percent txt-ong" style="display: table-cell;"  title="<?php echo $accelerate_popover_title; ?>" data-content='<?php echo $accelerate_popover; ?>' >
							                	<div class="c100 p100 orange-bar small">
			                                        <span></span>
			                                        <div class="small"></div>
			                                        <div class="slice">
			                                            <div class="bar"></div>
			                                            <div class="fill"></div>
			                                        </div>
			                                        <div class="nub-status txt-ong"><?php echo $accelerate_percent; ?></div>
			                                    </div>
		                                    </span>
					                	</td>
						                <td class="col-ov-4" align="center"><span class="ov-user user-modal" data-user="<?php echo $given_by; ?>" data-content="<div><p><?php echo htmlentities($user_chat_popover['user_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($user_chat_popover['job_title'],ENT_QUOTES); ?></p><?php echo $user_chat_popover['html']; ?></div>"><img src="<?php echo $user_chat_popover['user_image']; ?>" ></span></td>
						                <td class="col-ov-5"><?php echo $this->Wiki->_displayDate(date('Y-m-d',strtotime($accelerate_date)), $format = 'd M'); ?><br /><?php echo $this->Wiki->_displayDate(date('Y-m-d',strtotime($accelerate_date)), $format = 'Y'); ?></td>
						                <td class="col-ov-6"><?php echo $assigned_data['reason']; ?></td>
						            </tr>
						        	<?php } ?>
					        	<?php } else { ?>
						        	<tr>
						        		<td class="full-width-td" colspan="6"><div class="info-msg">No Accelerations</div></td>
						        	</tr>
						        <?php } ?>
						        	<tr class="not-found" style="display: none;">
						        		<td class="full-width-td" colspan="6"><div class="info-msg">No Accelerations</div></td>
						        	</tr>
				        </tbody>
				    </table>
				</div>

				<div class="ov-table-list">
				    <table class="table table-bordered list-table-allocate table-fixed">
				        <thead>
				            <tr>
				                <th class="col-ov-1">
				                    <div class="input-group ov-type-filter-wrap <?php if(!$user_earned_points) { ?>not-editable<?php } ?>">
				                    	<?php
					                        echo $this->Form->input('sel_ov_type_filter', array(
					                            'options' => $reward_types,
					                            'empty' => 'All Activities',
					                            'class' => 'form-control sel-ov-type-filter',
					                            'label' => false,
					                            'div' => false
					                        ));
				                        ?>
				                        <span class="input-group-addon bg-red clear-type-filter" style="border-color: #dd4b39;"><i class="fa fa-times"></i></span>
				                    </div>
				                </th>
				                <th class="col-ov-2">Title</th>
				                <th class="col-ov-3" align="center" style="text-align: center;">OV Reward</th>
				                <th class="col-ov-4" align="center" style="text-align: center;">Given By</th>
				                <th class="col-ov-5" style="position: relative;">
				                	<span>Date</span>

				                	<input type="text" class="inp-date-filter" name="inp_date_filter">
				                	<span class="pull-right selectedDate tipText" title="Clear" style="display: none;"><i class="fa fa-times"></i></span>
				                	<span class="pull-right date-filter <?php if(!$user_earned_points) { ?>not-editable<?php } ?>"><i class="fa fa-calendar"></i></span>

				                </th>
				                <th class="col-ov-6">Reason</th>
				            </tr>
				        </thead>
				        <tbody>
				        	<?php
				        	$earned_points = project_reward_assignments($project_id, $post_user_id, null, 'date');
				        	if($earned_points) { ?>
				        		<?php foreach ($earned_points as $uekey => $uevalue) {
				        			$assigned_data = $uevalue['RewardAssignment'];
				        			$selected_type = $assigned_data['type'];
				        			$given_by = $assigned_data['given_by'];
				        			$given_to = $assigned_data['user_id'];
				        			$activity_title = '';
				        			$activity_title_class = '';
				        			if($selected_type == 'workspace') {
				        				$selected_type = 'Workspace';
				        				if(dbExists('Workspace', $assigned_data['type_relation_id'])) {
					        				$activity_data = getByDbId('Workspace', $assigned_data['type_relation_id']);
					        				$activity_title = strip_tags($activity_data['Workspace']['title']);
					        			}
					        			else{
					        				$activity_title = 'Deleted Activity';
					        				$activity_title_class = 'deleted';
					        			}
				        			}
				        			else if($selected_type == 'task') {
				        				$selected_type = 'Task';
				        				if(dbExists('Element', $assigned_data['type_relation_id'])) {
					        				$activity_data = getByDbId('Element', $assigned_data['type_relation_id']);
					        				$activity_title = strip_tags($activity_data['Element']['title']);
				        				}
					        			else{
					        				$activity_title = 'Deleted Activity';
					        				$activity_title_class = 'deleted';
					        			}
				        			}
				        			else if($selected_type == 'risk') {
				        				$selected_type = 'Risk';
				        				if(dbExists('RmDetail', $assigned_data['type_relation_id'])) {
					        				$activity_data = getByDbId('RmDetail', $assigned_data['type_relation_id']);
					        				$activity_title = strip_tags($activity_data['RmDetail']['title']);
				        				}
					        			else{
					        				$activity_title = 'Deleted Activity';
					        				$activity_title_class = 'deleted';
					        			}
				        			}
				        			else if($selected_type == 'todo') {
				        				$selected_type = 'To-do';
				        				if(dbExists('DoList', $assigned_data['type_relation_id'])) {
					        				$activity_data = getByDbId('DoList', $assigned_data['type_relation_id']);
					        				$activity_title = strip_tags($activity_data['DoList']['title']);
				        				}
					        			else{
					        				$activity_title = 'Deleted Activity';
					        				$activity_title_class = 'deleted';
					        			}
				        			}
				        			else if($selected_type == 'subtodo') {
				        				$selected_type = 'Sub To-do';
				        				if(dbExists('DoList', $assigned_data['type_relation_id'])) {
					        				$activity_data = getByDbId('DoList', $assigned_data['type_relation_id']);
					        				$activity_title = strip_tags($activity_data['DoList']['title']);
				        				}
					        			else{
					        				$activity_title = 'Deleted Activity';
					        				$activity_title_class = 'deleted';
					        			}
				        			}
				        			else if($selected_type == 'other') {
				        				$selected_type = 'Other';
				        				$activity_title = 'N/A';
				        			}
				        			else if($selected_type == 'project') {
				        				$selected_type = 'Project';
				        				$activity_data = getByDbId('Project', $assigned_data['project_id']);
				        				$activity_title = strip_tags($activity_data['Project']['title']);
				        			}

				        			$user_chat_popover = user_chat_popover($given_by, $project_id);

				        			?>
						            <tr data-type="<?php echo $assigned_data['type']; ?>" data-date="<?php echo date('Y-m-d', strtotime($assigned_data['created'])); ?>">
						                <td class="col-ov-1"><?php echo $selected_type; ?></td>
						                <td class="col-ov-2 <?php echo $activity_title_class; ?>"><?php echo $activity_title; ?></td>
						                <td class="col-ov-3" align="center">
						                	<span class="ov-reward-nub txt-grn tbl-redeemed"><?php echo number_format($assigned_data['allocated_rewards'],0,',',','); ?></span>
						                </td>
						                <td class="col-ov-4" align="center">
						                	<span class="ov-user user-modal"  data-user="<?php echo $given_by; ?>" data-content="<div><p><?php echo htmlentities($user_chat_popover['user_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($user_chat_popover['job_title'],ENT_QUOTES); ?></p><?php echo $user_chat_popover['html']; ?></div>"><img src="<?php echo $user_chat_popover['user_image']; ?>" ></span>
						                </td>
						                <td class="col-ov-5">
						                	<?php echo $this->Wiki->_displayDate(date('Y-m-d',strtotime($assigned_data['created'])), $format = 'd M'); ?><br />
						                	<?php echo $this->Wiki->_displayDate(date('Y-m-d',strtotime($assigned_data['created'])), $format = 'Y'); ?>
					                	</td>
						                <td class="col-ov-6"><?php echo htmlentities($assigned_data['reason'], ENT_QUOTES, "UTF-8"); ?></td>
						            </tr>
						        	<?php } ?>
					        	<?php } else { ?>
						        	<tr>
						        		<td class="full-width-td" colspan="6"><div class="info-msg">No Rewards</div></td>
						        	</tr>
						        <?php } ?>
						        	<tr class="not-found" style="display: none;">
						        		<td class="full-width-td" colspan="6"><div class="info-msg">No Rewards</div></td>
						        	</tr>
				        </tbody>
				    </table>
				</div>

            </div>
        </div>
    </div>
    <?php } // END if(dbExists('Project'
		//} // END if($currentProjectPermitType) {
	} // END foreach ($allProjects as
} // END if(isset($allProjects) &&
    else{ ?>
    	<div class="info-msg">No Projects</div>
    <?php } ?>
<script type="text/javascript">
    $(function(){

    	$('.user_popover, .user-modal').click(function(event){
    		event.preventDefault();
    		var userId = $(this).data('user');
    		$('#popup_modal').modal({
	            remote: $.url + 'shares/show_profile/' + userId
	        }).show();
    	})

        $('.accelerator-popover').popover({
            placement : 'top',
            trigger : 'hover',
            html : true,
            container: 'body',
            // delay: {show: 50, hide: 400}
        });

        $('.ov-accelerate-percent').popover({
            placement : 'right',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });

        /* $('.ov-user, .user_popover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        }); */

        $('.user-project-reward-total').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });

        $('.accel_popover').popover({
            placement : 'right',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });

        $('.owl-carousel').each(function(){
			var $this = $(this);
	        $('.item', $this).sort(function (a, b) {
		      	var contentA =parseInt( $(a).attr('data-total'));
		      	var contentB =parseInt( $(b).attr('data-total'));
		      	return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
		   	}).prependTo($this);
        })

	    $('.sel-ov-type-filter').change(function(event) {
	        event.preventDefault();
	        var selected_type = $(this).val(),
	        	$parent_table = $(this).parents('.list-table-allocate:first');

        	$parent_table.find('tbody').find('tr').hide();

        	if(selected_type != ''){
        		$parent_table.find('tbody').find('tr[data-type="'+selected_type+'"]').show();
        	}
        	else{
        		$parent_table.find('tbody').find('tr').show();
        	}
        	if($parent_table.find('tbody').find('tr:not(.not-found):visible').length <= 0) {
        		$parent_table.find('tbody').find('tr.not-found').show();
        	}
        	else{
        		$parent_table.find('tbody').find('tr.not-found').hide();
        	}
	    })

	    $('.clear-type-filter').click(function(event) {
	        event.preventDefault();
	        var $parent_table = $(this).parents('.list-table-allocate:first');

	        $('.sel-ov-type-filter option[value=""]').prop('selected', true);
    		$parent_table.find('tbody').find('tr').show();
    		$parent_table.find('tbody').find('tr.not-found').hide();
	    })

        var dpick_options = {
            changeMonth: true,
            changeYear: true,
            yearRange: '2014:' + (new Date()).getFullYear(),
            dateFormat: 'yy-mm-dd',
            onClose: function(selectedDate, inst) {},
            onSelect: function(selected, inst) {
                var $input = inst.input;

                var $parent_table = $input.parents('.list-table-allocate:first');
                var $parent_th = $input.parents('th:first');
	    		$parent_table.find('tbody').find('tr').hide();
	    		$parent_table.find('tbody').find('tr[data-date="'+selected+'"]').show();
	    		$parent_th.find('.selectedDate').show('slide', {direction: 'left'}, 600);

	    		if($parent_table.find('tbody').find('tr:not(.not-found):visible').length <= 0) {
	        		$parent_table.find('tbody').find('tr.not-found').show();
	        	}
	        	else{
	        		$parent_table.find('tbody').find('tr.not-found').hide();
	        	}

            }
        };
        $('.inp-date-filter').each(function(index, el) {
        	$(this).datepicker(dpick_options);
        });

        // Open datepicker on icons
        $('.date-filter').on('click', function(event) {
            event.preventDefault();
            var $input = $(this).parents('th:first').find('.inp-date-filter');

            $input.val('').datepicker('destroy');
            $input.datepicker(dpick_options).datepicker('show');
            $input.datepicker('show');
        });

        // Open datepicker on icons
        $('.selectedDate').on('click', function(event) {
            event.preventDefault();
            var $input = $(this).parents('th:first').find('.inp-date-filter');

            $input.val('').datepicker('destroy');

            var $parent_table = $input.parents('.list-table-allocate:first');
            $parent_table.find('tbody').find('tr').show();

            $(this).hide('slide', {direction: 'right'}, 600);

            if($parent_table.find('tbody').find('tr:not(.not-found):visible').length <= 0) {
        		$parent_table.find('tbody').find('tr.not-found').show();
        	}
        	else{
        		$parent_table.find('tbody').find('tr.not-found').hide();
        	}
        });

        $.owl = $('.owl-carousel').owlCarousel({
            loop:false,
            margin:10,
            nav:true,
            dots:false,
            autoWidth: true,
            navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>']
        })
        //################################### COLLAPSE/EXPAND #############################################################
        /* add/remove expended class from each panel on collapse/expend */
        $("#rewards_accordion .panel-collapse").on("hide.bs.collapse", function() {
            $(this).parents('.panel').removeClass('expended');
        });
        $("#rewards_accordion .panel-collapse").on("show.bs.collapse", function() {
            $(this).parents('.panel').addClass('expended');
        });

        /* if all panel collapsed than update expand/collapse all button's icon and tooltip */
        $("#rewards_accordion .panel-collapse").on("hidden.bs.collapse", function() {
            var accordionId = '#rewards_accordion',
                numPanelOpen = $(accordionId + ' .collapse.in').length;

            if (numPanelOpen <= 0 && $(".toggle-accordion").hasClass('active')) {
                $(".toggle-accordion").removeClass('active');
                $(".toggle-accordion").attr('data-original-title', 'Expand All')
                    .tooltip('fixTitle');
            }

            $parent_panel = $(this).parents('.panel');
            $('.collapse-trigger', $parent_panel).attr('data-original-title', 'Expand')
                    .tooltip('fixTitle');
        });

        $('.toggle-accordion').tooltip({
            container: 'body',
            placement: 'top',
            trigger: 'hover'
        })

        /* Toggle collapse/expend all panels */
        $(".toggle-accordion").on("click", function(event) {
            event.preventDefault();
            var accordionId = '#rewards_accordion',
                numPanelOpen = $(accordionId + ' .collapse.in').length;

            if (!$(this).hasClass('active')) {
                $.openAllPanels(accordionId);
                $(this).attr('data-original-title', 'Collapse All')
                    .tooltip('fixTitle')
                    .tooltip('show');
                $(this).addClass("active");
            } else {
                $.closeAllPanels(accordionId);
                $(this).attr('data-original-title', 'Expand All')
                    .tooltip('fixTitle')
                    .tooltip('show');
                $(this).removeClass("active");
            }
        })

        $.openAllPanels = function(accordionId) {
            $('.panel-collapse')
                .removeData('bs.collapse')
                .collapse({ parent: false, toggle: false })
                .collapse('show')
                .removeData('bs.collapse')
                .collapse({ parent: accordionId, toggle: false });

            $('[data-toggle="collapse"]', $(accordionId)).attr('aria-expanded', true);

            $('.collapse-trigger').attr('data-original-title', 'Collapse').tooltip('fixTitle');
        }

        $.closeAllPanels = function(accordionId) {
            $('.panel-collapse', $(accordionId))
                .collapse('hide');
            $('[data-toggle="collapse"]', $(accordionId)).attr('aria-expanded', false);
            $('.collapse-trigger').attr('data-original-title', 'Expand').tooltip('fixTitle');
        }


        $('.collapse-trigger').tooltip({
            container: 'body',
            placement: 'top',
            trigger: 'hover'
        })

        $('.collapse-trigger').click(function(event) {
        	event.preventDefault();
        	$parent_panel = $(this).parents('.panel');
        	if (!$parent_panel.hasClass('expended')) {
                $(this).attr('data-original-title', 'Collapse')
                    .tooltip('fixTitle')
                    .tooltip('show');
            } else {
                $(this).attr('data-original-title', 'Expand')
                    .tooltip('fixTitle')
                    .tooltip('show');
            }

        })

    })
</script>
<style type="text/css">
	.deleted {
	    color: #dd4a39;
	}
	.acce-history-pop .his-row {
	    display: block;
	    font-size: 12px;
        margin-bottom: 5px;
	}
	.acce-history-pop .his-row span {
		font-weight: 600;
	}
	.accel_popover {
		cursor: default;
	}
	.no-popover {
		pointer-events: none;
	}
	.acc_history {
	    cursor: default;
	    margin: -9px -14px;
        max-height: 216px;
    	overflow-y: auto;
	}
	.acc_history .list-group {
	    margin-bottom: 0px;
	}
	.acc_history .list-group-item {
	    padding: 10px;
	    border: none;
	    border-bottom: 1px solid #ccc;
	    margin-bottom: 0;
	}
	.acc_history .list-group-item:last-child {
	    border: none;
	}
	.acc_history .item-text {
	    display: block;
	    font-size: 12px;
	}
	.project-summary span {
		cursor: default;
	    text-align: center;
	    min-width: 52px;
	    display: inline-block;
	    border-right: 1px solid #ccc;
	}
	.project-summary span:last-child {
	    border-right: none;
	}
	.sel-ov-type-filter {
		font-size: 13px;
	}
	.user_popover, .user-modal {
		cursor: pointer;
	}
	.user-project-reward-total {
		cursor: pointer;
	}
	.reward_popover .popover_amount_wrap {
	    display: block;
	    margin: 2px 0 0 0;
	}
	.reward_popover .popover_label {
		font-weight: 600;
		font-size: 12px;
	}
	.reward_popover .popover_amount {
		font-weight: normal;
		font-size: 12px;
	}
	.ov-table-list .tbl-redeemed {
	    font-size: 14px;
    	font-weight: bold;
	}
	span.selectedDate {
        margin: 0 0 0 5px;
    	font-size: 18px;
    	color: #c00;
    	cursor: pointer;
	}
    .inp-date-filter {
        width: 0;
        height: 0;
        opacity: 0;
        visibility: hidden;
    }
	.clear-type-filter {
		cursor: pointer;
	}
	.date-filter {
		cursor: pointer;
	}
	.border-sec {
	    border: 1px solid #ccc;
	}

	.project-info-padd {
	    padding-top: 15px;
	    padding-bottom: 15px;
	}

	.distributed-right {
	    float: right;
	}

	.project-thumb-slider {
		padding-left: 35px;
		padding-top: 30px;
		padding-right: 30px;
	}

	.owl-carousel.owl-loaded {
	    max-height: 80px;
	}

	.ov-project-status {
	    border: 1px solid #ccc;
	    display: inline-block;
	    width: 100%;
	    margin-top: 15px;
	}
	.full-width-td{
		width: 100%;
	}
	.ov-project-status .border-right {
	    border-right: 1px solid #ccc;

	}

	.ov-project-status .padd15 {
	    padding-top: 15px;
	    padding-bottom: 13px;
	}

	.ov-project-status-in {
	    position: relative;
	    min-height: 72px;
	    vertical-align: top;
	    padding-top: 14px;
	}

	.ov-logo-status {
	    float: left;
	}

	.ov-achieved-cont {
	    padding-right: 75px;
	    overflow: hidden;
		font-size: 11px;
	}
	.ov-achieved-cont p{
		margin-bottom: 0px;
	}
	.allocation-issue {
	    background: #f94b4b;
	    color: #fff;
	    padding: 0 5px;
	}
	.ov-status-info {
	    position: absolute;
	    right: 0px;
	    top: 0px;
	}

	.ov-achieved-cont h5 {
	    margin-bottom: 1px;
	    margin-top: 0px;
	    padding-top: 5px;
	}
	.ov-achieved-cont .accelerator-padd{
		padding-top: 5px;
	}
	.accelerator-info {
	    background: #ffc000;
	    padding: 10px 5px;
	    color: #000;
	    cursor: default;
	    display: inline-block;
	}


/*	.ov-table-list {
	    clear: both;
	    display: inline-block;
	    width: 100%;
	    margin-top: 15px;
	    max-height: 250px;
        overflow-x: auto;
	    font-size: 13px;
	    overflow-y: scroll;
	}*/
	.ov-table-list {
	    clear: both;
	    display: inline-block;
	    width: 100%;
	    margin-top: 15px;
		font-size: 13px;
	}
	.ov-table-list .table-fixed{
		table-layout: fixed;
	}
.ov-table-list .table-fixed thead, .ov-table-list .table-fixed tbody, .ov-table-list .table-fixed tr, .ov-table-list .table-fixed td, .ov-table-list .table-fixed th {
	display: block;
}
	.ov-table-list .table-fixed tr {
		width: 100%;
		display: flex;
	}
	.ov-table-list .table-fixed thead {
		background: #f5f5f5;
		padding-right: 17px;
		border-bottom: 1px solid #dcdcdc;
	}
	.ov-table-list .table-fixed thead tr th {
    float: left;
	display: flex;
	align-items: center;
	justify-content: left;
	}
	.inner-wrapper .ov-table-list .table-fixed thead tr th {
		border-bottom: none !important;
	border-left: none;
	border-top: none !important;
	}
	.inner-wrapper .ov-table-list .table-fixed thead tr th:last-child {
	border-right: none;
	}
	.ov-table-list .table-fixed {
		border: 1px solid #ddd;
		border-bottom: none;
	}
	.ov-table-list .table-fixed tbody td {
	float: left;
	display: flex;
	align-items: center;
	justify-content: left;
	border-top: none !important;
		border-left: none !important;
}
	.ov-table-list .table-fixed tbody td.col-ov-3, .ov-table-list .table-fixed tbody td.col-ov-4, .ov-table-list .table-fixed thead tr th.col-ov-3, .ov-table-list .table-fixed thead tr th.col-ov-4{
		justify-content: center;
	}
	.ov-table-list .table-fixed thead tr th.col-ov-5 {
		justify-content: space-between;
	}
	.ov-table-list .table-fixed tbody {
	max-height: 201px;
	overflow-y: scroll;
	width: 100%;
}

	.ov-table-list .table {
		margin-bottom: 0;

	}

	.inner-wrapper .ov-table-list .table thead tr {
	    background-color: transparent;
	    border-top: none;
	}

	.inner-wrapper .ov-table-list .table thead tr th {
	    background-color: #f5f5f5;
		    font-weight: normal;
    font-size: 13px;
	}
	/*.ov-table-list .table-fixed tbody tr td:first-child {
	border-left: 1px solid #dcdcdc !important;
	}*/
	.ov-table-list .col-ov-1 {
	    width: 17%;
	}

	.ov-table-list .col-ov-2 {
	    width: 23%;
	}

	.ov-table-list .col-ov-3 {
	    width: 9%;
	}

	.ov-table-list .col-ov-4 {
	    width: 10%;
	}

	.ov-table-list .col-ov-5 {
	    width: 9%;
	}

	.ov-table-list .col-ov-6 {
	    width: 32%;
	}

	.ov-table-list .table-bordered>thead>tr>th,
	.ov-table-list .table-bordered>tbody>tr>th,
	.ov-table-list .table-bordered>tfoot>tr>th,
	.ov-table-list .table-bordered>thead>tr>td,
	.ov-table-list .table-bordered>tbody>tr>td,
	.ov-table-list .table-bordered>tfoot>tr>td {
	    border: 1px solid #dcdcdc;
	    vertical-align: middle;
	}

	.ov-table-list .table-bordered>thead>tr>th {
	    border-top: 1px solid #dcdcdc !important;
	    border-bottom: none;
	}

	.ov-table-list .ov-user {
	    display: inline-block;
	}

	.ov-table-list .ov-user img {
	    width: 40px;
	    border: 2px solid #ccc;
		border-radius: 50%;
	}

	.ov-table-list .ov-type-filter-wrap {
	    width: 100%;
	    display: block;
	}

	.ov-table-list .ov-type-filter-wrap select {
	    width: 100%;
	    padding: 2px 5px;
	    height: 28px;
	    font-weight: normal;
	}

	.ov-table-list .ov-type-filter-wrap .input-group-addon {
	    border-color: #dd4b39;
	    position: absolute;
	    right: 0px;
	    z-index: 9;
	    width: auto;
	    padding:5px 6px;
		height: 28px;

	}

	.border-right3 {
	    border-right: 1px solid #ccc;
	}

	.border-right2 {
	    border-right: 1px solid #ccc;
	}

	.border-right1 {
	    border-right: 1px solid #ccc;
	}

	.colume-1 {
		width: 16.66666667%;
		float: left;
		padding: 0px 15px;
	}
	.colume-2 {
		width: 16.66666667%;
		float: left;
		padding: 0px 15px;
	}
	.colume-3 {
		width: 66.66666667%;
		float: left;
		overflow: hidden;
	}
	.owl-item {
		width: 52px !important;
	}
	.owl-carousel{
		min-width: 100%;
	}
	@media (max-width:1600px) {
		.ov-table-list .col-ov-4 {
				width: 14%;
			}
			.ov-table-list .col-ov-6 {
			width: 28%;
		}
	}
	@media (max-width:1460px) {
	    .c100.mid {
	        font-size: 60px;
	    }

	    .ov-achieved-cont {
	        padding-right: 65px;
	    }
	}

	@media (max-width:1400px) {
	    .c100.mid {
	        font-size: 55px;
	    }

	    .ov-achieved-cont {
	        padding-right: 60px;
	    }

		.colume-1, .colume-2 {
			width: 20%;
		}
		.colume-3 {
			width: 60%;
		}


	}

	@media (max-width:1365px) {
	    .ov-project-col {
	        width: 50%;
	    }

	    .border-right3 {
	        border-right: 1px solid #ccc;
	    }

	    .border-right2 {
	        border-right: none;
	        border-bottom: 1px solid #ccc;
	    }

	    .border-right1 {
	        border-right: 1px solid #ccc;
	        border-bottom: 1px solid #ccc;
	    }

	.ov-table-list .col-ov-4 {
		width: 15%;
	}
	.ov-table-list .col-ov-6 {
		width: 27%;
	}

	}

	@media (max-width:1199px) {
		.colume-1, .colume-2 {
			width: 27%;
		}
		.colume-3 {
			width: 46%;
		}
		.ov-table-list {
			overflow: auto;
		}
		  .ov-table-list .table {
	        min-width: 870px;
	    }
	.ov-table-list .table-fixed thead {
		padding-right: 0;
	}
	}

	@media (max-width:991px) {
	    .ov-project-col {
	        width: 100%;
	    }

	    .border-right1,
	    .border-right2,
	    .border-right3 {
	        border-bottom: 1px solid #ccc;
	        border-right: none;
	    }

	    .ov-project-status-in {
	        min-height: 60px;
	    }


		.colume-1, .colume-2 {
			width: 50%;
		}
		.colume-3 {
			width: 100%;
		}
		.project-thumb-slider {
			padding-top: 20px;
			border-top: 1px solid #ccc;
			margin-bottom: 15px;
		}
		.box .colume-2.border-right {
			border-right: none !important;
		}

	}

	@media (max-width:479px) {
	    .c100.mid {
	        font-size: 50px;
	    }

	    .ov-achieved-cont {
	        padding-right: 55px;
	        font-size: 13px;
	    }
		.colume-1, .colume-2 {
			width: 100%;

		}
		.box .colume-1.border-right {
			border-right: none !important;
			border-bottom: 1px solid #ccc !important;
		}
	}
</style>
<?php }else{ ?>
	<div class="info-msg">Opt In to Participate</div>
<?php } ?>
