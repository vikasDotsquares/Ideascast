<!--<option value="">Select Risk</option>-->
<?php 
if( isset($RmDetail) && !empty($RmDetail) ){
foreach($RmDetail as $listrisk){
	if( !empty($listrisk['RmDetail']['id']) ){
?>
	<option value="<?php echo $listrisk['RmDetail']['id'];?>"><?php echo $listrisk['RmDetail']['title']; ?></option>
<?php } 	
	}
}?>