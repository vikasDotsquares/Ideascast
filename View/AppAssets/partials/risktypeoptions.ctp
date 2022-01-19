<option value="">Select Type</option>
<?php 
if( isset( $risktype ) && !empty( $risktype ) ) {
	foreach($risktype as $list_type){
		if( !empty($list_type['RmProjectRiskType']['id']) ){
?>
	<option value="<?php echo $list_type['RmProjectRiskType']['id'];?>"><?php echo $list_type['RmProjectRiskType']['title'];?></option>
<?php  } 
	}	
} ?>	