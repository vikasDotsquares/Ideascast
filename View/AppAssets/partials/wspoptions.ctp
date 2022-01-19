<option value="">Select Workspace</option>
<?php
if( isset($projectdata) && !empty($projectdata) ){ 
foreach($projectdata as $listele){
	if( !empty($listele['Workspace']['id']) ){
?>
	<option value="<?php echo $listele['Workspace']['id'];?>"><?php echo $listele['Workspace']['title']; ?></option>
<?php 	}
	}
}?>