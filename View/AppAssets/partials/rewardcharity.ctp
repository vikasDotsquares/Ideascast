<option value="">Select Charity</option>
<?php
if( isset( $projectcharity ) && !empty( $projectcharity ) ) {
	if( !empty($projectcharity['RewardCharity']['id']) && !empty($projectcharity['RewardCharity']['title']) ){
?>	<option value="<?php echo $projectcharity['RewardCharity']['id'];?>"><?php echo $projectcharity['RewardCharity']['title'];?></option>
<?php } 
} ?>	