<?php
$assistant_data = [];
$project_data = $this->Permission->assistant_data();
// pr($project_data);

$red_list = $amber_list = $ovd_list = $reminder_list = $today_list = $assigned_list = $over_budget_list = $risk_list = [];
if(isset($project_data) && !empty($project_data)){
	foreach ($project_data as $key => $value) {
		$perm = $value['user_permissions'];
		$prj = $value['projects'];
		// $cur = $value['currencies'];
		$other = $value[0];
		$project_id = $prj['id'];
		$project_title = $prj['title'];
		$assistant_data[$project_id] = array_merge($perm, $prj, $other);

		if(isset($prj['rag_current_status']) && !empty($prj['rag_current_status']) && $prj['rag_current_status'] == 1) {
			$red_list[$project_id] = [ 'id' => $project_id, 'title' => $project_title];
		}

		if(isset($prj['rag_current_status']) && !empty($prj['rag_current_status']) && $prj['rag_current_status'] == 2) {
			$amber_list[$project_id] = [ 'id' => $project_id, 'title' => $project_title];
		}

		if(isset($other['OVD']) && !empty($other['OVD'])) {
			$ovd_list[$project_id] = ['elements_total' => $other['OVD'], 'id' => $project_id, 'project' => $project_title];
		}

		if(isset($other['rem_elements']) && !empty($other['rem_elements'])) {
			$reminder_list[$project_id] = ['elements_total' => $other['rem_elements'], 'id' => $project_id, 'project' => $project_title];
		}

		/*if(isset($other['today_tomm']) && !empty($other['today_tomm'])) {
			$today_list[$project_id] = ['elements_total' => $other['today_tomm'], 'id' => $project_id, 'project' => $project_title];
		}*/

		/*if(isset($other['assigned_elements']) && !empty($other['assigned_elements'])) {
			$assigned_list[$project_id] = ['elements_total' => $other['assigned_elements'], 'id' => $project_id, 'project' => $project_title];
		}*/

		$projectbudget = $prj['budget'];
		$estimatcost =  $other['est_cost'];
		$spendcost = $other['sp_cost'];
		if( ( isset($projectbudget) && $projectbudget > 0) && ( !isset($estimatcost) || $estimatcost == 0 ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
			$costStatus = 'Over Budget';
			$over_budget_list[$project_id] = ['project' => strip_tags($project_title)];
		}
		else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget ) ){
			$costStatus = 'On Budget, at Risk';
			$over_budget_list[$project_id] = ['project' => strip_tags($project_title)];
		}
		else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
			$costStatus = 'Over Budget';
			$over_budget_list[$project_id] = ['project' => strip_tags($project_title)];
		}
		else if( (isset($projectbudget) && $projectbudget > 0) && ( isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget ) && ( isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget ) ){
			$costStatus = 'Over Budget';
			$over_budget_list[$project_id] = ['project' => strip_tags($project_title)];
		}
		else if( (isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && ( !isset($spendcost) || $spendcost <= 0 ) ){
			$costStatus = 'On Budget, at Risk';
			$over_budget_list[$project_id] = ['project' => strip_tags($project_title)];
		}

		if( (isset($other['high_risk']) && !empty($other['high_risk'])) || (isset($other['severe_risk']) && !empty($other['severe_risk']))) {
			$high_risk = (!empty($other['high_risk'])) ? $other['high_risk'] : 0;
			$severe_risk = (!empty($other['severe_risk'])) ? $other['severe_risk'] : 0;
			$total_risk = $high_risk + $severe_risk;
			$risk_list[$project_id] = ['project' => strip_tags($project_title), 'total' => $total_risk];
		}


	}
}


	uasort($ovd_list, function($a, $b) {
	    return $a['elements_total'] <= $b['elements_total'];
	});
	uasort($reminder_list, function($a, $b) {
	    return $a['elements_total'] <= $b['elements_total'];
	});

$red_rag_count = count($red_list);
$amber_rag_count = count($amber_list);
$ovd_count = count($ovd_list);
$reminder_count = count($reminder_list);
$over_budget_count = count($over_budget_list);
$risk_count = count($risk_list);

$ovd_ele_count = array_sum(array_column($ovd_list, 'elements_total'));
$reminder_ele_count = array_sum(array_column($reminder_list, 'elements_total'));

$loggedin_user = $this->Session->read('Auth.User.id');
$userFirstname = $this->Common->userFirstname($loggedin_user);
?>
<div class="col-sm-12" style="margin-bottom: 20px; ">
			<div class="user-name">Hi <?php echo $userFirstname; ?>, here are project events you may be interested in:</div>
		</div>
		<div class="listingassistant">

			<!-- RED RAG STATUS -->
			<div class="data-wrapper rag-status-red <?php if($red_rag_count <= 0){ ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo($red_rag_count); ?></div>
				<div class="data-detail">
					<div class="project-type">
						RAG Status - RED
					</div>
					<div class="project-icons">
						Projects
						<?php
						$rag_red_html = '';
						if($red_rag_count > 0){
							$rag_red_html .= '<div class="jani_popover" style="display: none;"><ul>';
							foreach ($red_list as $key => $value) {
								$rag_red_html .= '<li class="prj_list"><a href="#" data-url="'.Router::Url(array("controller" => "projects", "action" => "objectives", $value['id']), true).'/1" class="jani-project"><span class="text-info">'. strip_tags($value['title']) .'</span></a></li>';
							}
							$rag_red_html .= '</ul></div>';
						} ?>
						<?php echo($rag_red_html); ?>
						<a href="#" class="project-list" ><i class="fa fa-mail-forward ico-show-list"></i></a>
						<a href='<?php if($red_rag_count > 0){ ?><?php echo Router::Url(array("controller" => "projects", "action" => "objectives"), true); ?>/0/1<?php }else{echo "#";} ?>' class="show-more-btn action">Show Detail</a>
					</div>
				</div>
			</div>


			<!-- AMBER RAG STATUS -->
			<div class="data-wrapper rag-status-amber <?php if($amber_rag_count <= 0){ ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo($amber_rag_count); ?></div>
				<div class="data-detail">
					<div class="project-type">
						RAG Status - AMBER
					</div>
					<div class="project-icons">
						Projects
						<?php
						$rag_amber_html = '';
						if($amber_rag_count > 0){
							$rag_amber_html .= '<div class="jani_popover" style="display: none;"><ul>';
							foreach ($amber_list as $key => $value) {
								$rag_amber_html .= '<li class="prj_list"><a href="#" data-url="'.Router::Url(array("controller" => "projects", "action" => "objectives", $value['id']), true).'/2" class="jani-project"><span class="text-info">'. strip_tags($value['title']) .'</span></a></li>';
							}
							$rag_amber_html .= '</ul></div>';
						} ?>
						<?php echo($rag_amber_html); ?>
						<a href="#" class="project-list" ><i class="fa fa-mail-forward ico-show-list"></i></a>
						<a href='<?php if($amber_rag_count > 0){ ?><?php echo Router::Url(array("controller" => "projects", "action" => "objectives"), true); ?>/0/2<?php }else{echo "#";} ?>' class="show-more-btn">Show Detail</a>
					</div>
				</div>
			</div>

			<!-- OVERDUE PROJECTS -->
			<div class="data-wrapper task-status-overdue <?php if ($ovd_count <= 0) { ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo $ovd_ele_count ; ?></div>
				<div class="data-detail">
					<div class="project-type">
						Tasks with OVERDUE status
					</div>
					<div class="project-icons">
						<?php echo $ovd_count; ?> Projects
						<?php
						if (isset($ovd_list) && !empty($ovd_list)) {
							$overdue_html = '';
								$overdue_html .= '<div class="jani_popover" style="display: none;"><ul>';
								foreach ($ovd_list as $key => $value) {
									$overdue_html .= '<li class="prj_list"><a href="#" data-url="'.TASK_CENTERS.$key.'/status:1" class="jani-project"><b class="total-elements">'. $value['elements_total'] . '</b> in <span class="text-info">' . strip_tags($value['project']) .'</span></a></li>';
								}
								$overdue_html .= '</ul></div>';
							 echo($overdue_html);
						}
						?>
						<a href="#" class="project-list" ><i class="fa fa-mail-forward ico-show-list"></i></a>
						<a href='<?php if ($ovd_count) { ?><?php echo TASK_CENTERS."status:1"; ?><?php }else{echo "#";} ?>' class="show-more-btn">Show Detail</a>
					</div>
				</div>
			</div>

			<!-- REMINDERS SET FOR TODAY -->
			<div class="data-wrapper reminder-today <?php if ($reminder_count <= 0) { ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo $reminder_ele_count; ?></div>
				<div class="data-detail">
					<div class="project-type">
						Reminders set for today
					</div>
					<div class="project-icons">
						<?php echo $reminder_count; ?> Projects
						<?php
						if (isset($reminder_list) && !empty($reminder_list)) {
							$remind_html = '';
								$remind_html .= '<div class="jani_popover" style="display: none;"><ul>';
								foreach ($reminder_list as $key => $value) {
									$remind_html .= '<li class="prj_list"><a href="#" data-url="'.Router::Url(array("controller" => "dashboards", "action" => "task_reminder", 'project' => $key, 'today'), true).'" class="jani-project"><b class="total-elements">'. $value['elements_total'] . '</b> in <span class="text-info">' . strip_tags($value['project']) .'</span></a></li>';
								}
								$remind_html .= '</ul></div>';
							 echo($remind_html);
						}
						?>
						<a href="#" class="project-list"><i class="fa fa-mail-forward ico-show-list"></i></a>
						<a href='<?php if ($reminder_count) { ?><?php echo Router::Url(array("controller" => "dashboards", "action" => "task_reminder", 'today'), true); ?><?php }else{echo "#";} ?>' class="show-more-btn">Show Detail</a>
					</div>
				</div>
			</div>

			<!-- OVER BUDGET STATUS -->
			<div class="data-wrapper over-budget <?php if( $over_budget_count <= 0 ){ ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo $over_budget_count; ?></div>
				<div class="data-detail">
					<div class="project-type">
						Budgets - Off Plan
					</div>
					<div class="project-icons">
						Projects
						<?php
						$over_budget_html = '';
						if($over_budget_count > 0){
							$over_budget_html .= '<div class="jani_popover" style="display: none;"><ul>';
							foreach ($over_budget_list as $key => $value) {
								$over_budget_html .= '<li class="prj_list"><a href="#" data-url="'.Router::Url(array("controller" => "projects", "action" => "objectives", $key), true).'/0/1" class="jani-project"><span class="text-info">'. strip_tags($value['project']) .'</span></a></li>';
							}
							$over_budget_html .= '</ul></div>';
						} ?>
						<?php echo($over_budget_html); ?>
						<a href="#" class="project-list" ><i class="fa fa-mail-forward ico-show-list"></i></a>
						<a href='<?php if($over_budget_count > 0){ ?><?php echo Router::Url(array("controller" => "projects", "action" => "objectives"), true); ?>/0/0/1<?php }else{echo "#";} ?>' class="show-more-btn">Show Detail</a>
					</div>
				</div>
			</div>

			<!-- RISK SEVERE AND HIGH -->
			<div class="data-wrapper risk-severe-high <?php if ($risk_count <= 0 ) { ?>locked<?php }else{ ?>unlocked<?php } ?>">
				<div class="data-counter"><?php echo $risk_count; ?></div>
				<div class="data-detail">
					<div class="project-type">
						Risks at SEVERE and HIGH levels
					</div>
					<div class="project-icons"> Projects
						<?php
						if (isset($risk_list) && !empty($risk_list)) {
							$risk_html = '';
							$risk_html .= '<div class="jani_popover" style="display: none;"><ul>';
							foreach ($risk_list as $key => $value) {
								$risk_html .= '<li class="prj_list"><a href="#" data-url="'.Router::Url(array("controller" => "risks", "action" => "index", $key ), true).'" class="jani-project"><span class="text-info">' . $value['project'] .'</span></a></li>';
							}
							$risk_html .= '</ul></div>';
							echo($risk_html);
						}
						?>
						<a href="#" class="project-list"><i class="fa fa-mail-forward ico-show-list"></i></a>
					</div>
				</div>
			</div>

		</div>

<script type="text/javascript">
	$(function(){

		// setTimeout(function(){
		// 	$('.listingassistant').slideDown(100)
		// }, 250)

		/*$.partial_rag_voice = $.partial_task_voice = $.partial_risk_voice = '';
		var username = '<?php //echo $userFirstname; ?>';
		$.voice = "Hi " + username +
					".\n here are project events you may be interested in. ";
		$('#hidden-text').html($.voice)
		if( !!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform)) {
			responsiveVoice.setDefaultVoice("US English Female");
		}
		else{
			responsiveVoice.setDefaultVoice("UK English Female");
		}*/

		$('body').on('click', '.jani-project', function(event){
			event.preventDefault();
			$(this).addClass('hoverstart')

			var $text_info = $(this).find('span.text-info');
			if(responsiveVoice.isPlaying()) {
			  	responsiveVoice.cancel();
			}
			if(!$('.voice-btn').find('i').hasClass('audio-mute')) {
				responsiveVoice.speak("Loading project "+$text_info.text()+"");
			}

			setTimeout($.proxy(function() {
		        if($(this).data('url')){
			        location.href = $(this).data('url');
			    }
		    }, this), 2500);
		})


		var username = '<?php echo $userFirstname; ?>';
		var total_rag_red = '<?php echo ($red_rag_count > 0)? $red_rag_count : ' No'; ?>';
		var total_rag_amber = '<?php echo ($amber_rag_count > 0)? $amber_rag_count : ' No'; ?>';
		var total_over_budget = '<?php echo ( $over_budget_count > 0)? $over_budget_count : ' No'; ?>';
		var total_overdue_els = '<?php echo (!empty($ovd_ele_count))? $ovd_ele_count : ' No'; ?>';
		var total_reminder_els = '<?php echo (!empty($reminder_ele_count))? $reminder_ele_count : ' No'; ?>';
		var total_risks = '<?php echo (!empty($risk_count))? $risk_count : ' No'; ?>';

		$.voice = "Hi " + username +
					".\n here are project events you may be interested in. " +
					total_rag_red +
					" projects at red RAG Status. " +
					total_rag_amber +
					" projects at amber RAG Status. " +
					total_overdue_els +
					" tasks overdue. " +
					total_reminder_els +
					" reminders set for today. \n " +
					total_over_budget +
					" projects with budgets are off plan. " +
					total_risks +
					" projects with risks at severe and high levels.";

		$('#hidden-text').html($.voice)
		if( !!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform)) {
			responsiveVoice.setDefaultVoice("US English Female");
		}
		else{
			responsiveVoice.setDefaultVoice("UK English Female");
		}
		setTimeout(function(){
			$('.voice-btn-wrap').show();
		}, 2000)

		$('.data-wrapper.unlocked .show-more-btn').on('click', function(event){
			$(this).addClass('shoverstart');
		})

		$('.unlocked .project-list').on('mouseleave', function(event){
			event.preventDefault();
		})

		$('.unlocked .project-list').on('click', function(event){
			console.log('this', $(this).data())
			event.preventDefault();
			var $parent = $(this).parent('.project-icons');
			var $jani_popover = $parent.find('.jani_popover');
			var $icon = $(this).find('i.ico-show-list');

			$('.jani_popover').not($jani_popover).removeClass('open');
			$('i.ico-show-list').not($icon).removeClass('fa-reply').addClass('fa-mail-forward');
			$('.project-list').not($(this)).removeClass('opened')
			// console.log($('.project-icons').not($parent))
			if(!$jani_popover.hasClass('open')){
				$jani_popover.addClass('open').show();
				$(this).addClass('opened');
				$icon.removeClass('fa-mail-forward').addClass('fa-reply');
			}
			else{
				$jani_popover.removeClass('open');
				$(this).removeClass('opened');
				$icon.removeClass('fa-reply').addClass('fa-mail-forward');
			}

			$(this).data('jani_popover', $jani_popover);
			$jani_popover.data('project_list', $(this));
		})

		$('.unlocked .project-list').each(function () {
			var $parent = $(this).parent();
			var $jani_popover = $parent.find('.jani_popover');
			$(this).data('jani_popover', $jani_popover);
			$jani_popover.data('project_list', $(this));
		})

		$('body').on('click', function (e) {
			$('.unlocked .project-list').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.jani_popover').has(e.target).length === 0) {
					var $jani_popover = $(this).data('jani_popover');
					if($jani_popover.hasClass('open')){
						$jani_popover.removeClass('open');
						$jani_popover.parent().find('.project-list').removeClass('opened');
						$jani_popover.parent().find('i.ico-show-list').removeClass('fa-reply').addClass('fa-mail-forward');
						$('.jani-project').removeClass('hoverstart')
					}
				}
			});
		});

		$('body').on('click', '.locked .project-list,.locked .show-more-btn', function(event){
			event.preventDefault();
		})

	})
</script>