<option value="">Select Task</option>
<?php  
if( isset($projectelement) && !empty($projectelement) ){
foreach($projectelement as $listele){
	if( !empty($listele['id']) ){
?>
	<option value="<?php echo $listele['id'];?>"><?php echo $listele['title']; ?></option>
<?php 	}
	}
}?>