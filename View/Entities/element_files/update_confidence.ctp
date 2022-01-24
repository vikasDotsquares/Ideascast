<?php

	$level_data = $this->Permission->confidence_level($element_id);
	$confidence_level = 'Not Set';
	$level_value = 0;
	$level_class = 'dark-gray';
	$level_arrow = 'notsetgrey';
	if(isset($level_data) && !empty($level_data)){
		$level_data = $level_data[0];
		$confidence_level = $level_data[0]['confidence_level'];
		$level_class = $level_data[0]['confidence_class'];
		$level_arrow = $level_data[0]['confidence_arrow'];
		$level_value = $level_data['el']['level'];
	}
	
	if($level_value > 0){
		$level_value_current = $level_value.'%';
	}else{
		$level_value_current = '';
	}
 ?>

<div class="progress-col-heading">
		<span class="prog-h"><a data-target="#element_level" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'confidence', $element_id, 'admin' => FALSE ), TRUE ); ?>" href="#">CONFIDENCE <i class="arrow-down"></i></a></span>
	</div>
<div class="progress-col-cont">
	<ul class="workcount confcounters">
		
		<li class="<?php echo $level_class; ?> cost-tooltip" title="Confidence Level<br />
For This Task" data-target="#element_level" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'confidence', $element_id, 'admin' => FALSE ), TRUE ); ?>" ><?php echo $level_value_current; ?></li>
		<span><i class="level-ts <?php echo $level_arrow; ?>"></i></span>
    </ul>
	<div class="proginfotext"><?php echo $confidence_level; ?></div>
</div>

<script>
$(function(){
			$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
})
</script>