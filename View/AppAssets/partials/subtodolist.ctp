<option value="">Select Sub Todo</option>
<?php  
if( isset($finalarray) && !empty($finalarray) ){	
foreach($finalarray as $listele){
	if( !empty($listele['DoList']['id']) ){
?>
	<option value="<?php echo $listele['DoList']['id'];?>"><?php echo $listele['DoList']['title']; ?></option>
<?php 	}
	}
}?>