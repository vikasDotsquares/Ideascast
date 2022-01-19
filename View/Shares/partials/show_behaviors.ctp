<?php
if( isset($user_id['user_id']) && !empty($user_id['user_id']) ){

	$user_details['User']['id'] = $user_id['user_id'];
	$user_details['UserDetail']['user_id'] = $user_id['user_id'];

	$social_activity= $this->ViewModel->social_activity($user_details['UserDetail']['user_id']);
	$elements = [];
	// pr($social_activity);
	if(isset($social_activity) && !empty($social_activity)){
		$elIds = Set::extract($social_activity,'{n}.0 ');
		foreach ($elIds as $element) {
			$elements[$element['Description']][] = $element['UserProjCount'];
		}
	}

if( isset($elements) && !empty($elements) )	{

?>
	<div class="social-section">
		<div class="social-cont-sec">
			<strong>Participation:</strong>
			<span class="social-cont-text"><?php echo $elements['Current Project(s)'][0]; ?> Current Project(s)</span>
			<span class="social-cont-text"><?php 	echo (isset($elements) && !empty($elements) && !empty($elements['Past Project(s)'][0]) ) ? $elements['Past Project(s)'][0] : 0; ?> Past Project(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Current Shared Task(s)'][0]) ) ? $elements['Current Shared Task(s)'][0] : 0; ?> Current Shared Task(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Past Shared Task(s)'][0]) ) ? $elements['Past Shared Task(s)'][0] : 0; ?> Past Shared Task(s)</span>
		</div>
		<div class="social-cont-sec">
			<strong>Leadership:</strong>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Leadership:Project(s)'][0]) ) ? $elements['Leadership:Project(s)'][0] : 0; ?> Project(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Leadership:Task(s)'][0]) ) ? $elements['Leadership:Task(s)'][0] :0;  ?> Task(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Leadership:Risk(s)'][0]) ) ? $elements['Leadership:Risk(s)'][0] : 0 ;  ?> Risk(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['user template relations'][0]) ) ? $elements['user template relations'][0] : 0;  ?> Knowledge Template(s)</span>
		</div>
		<div class="social-cont-sec">
			<strong>Engagement:</strong>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Engagement: Social Board Nudge(s)'][0]) ) ? $elements['Engagement: Social Board Nudge(s)'][0] : 0; ?> Opportunity Request(s)</span>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['user vote results'][0]) ) ? $elements['user vote results'][0] : 0; ?> Vote(s)</span>

		</div>
		<div class="social-cont-sec">
			<strong>Networking:</strong>
			<span class="social-cont-text"><?php echo (isset($elements) && !empty($elements) && !empty($elements['Social Networking: Task(s) Shared via Propagation'][0]) ) ? $elements['Social Networking: Task(s) Shared via Propagation'][0] : 0; ?> Task(s) Shared via Propagation</span>
		</div>

		<div class="social-cont-sec">
			<strong>Recognition and Achievement:</strong>
			<span class="social-cont-text">
			<?php echo number_format($this->Common->feedbackRateAverage($user_details['User']['id']), 1); ?> Feedback Rating </span>
			<span class="social-cont-text">
			<?php echo (isset($elements) && !empty($elements)) ? $elements['user feedback ratings'][0] :0; ?> Feedback Provided </span>
			<span class="social-cont-text">
			<?php echo (isset($elements) && !empty($elements) && !empty($elements['allocated reward'][0])) ? $elements['allocated reward'][0]:0; ?>  Reward(s) Earned
			</span>
		</div>
	</div>

<?php } else {
		echo "None";
	}
} ?>
<script type="text/javascript">
	$(function(){
		$(".social-section").slimScroll({height: 320, alwaysVisible: true});
	})
</script>