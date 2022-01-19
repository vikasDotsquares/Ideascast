<?php 
if( isset($projectpeople) && !empty($projectpeople) ){
foreach($projectpeople as $listusers){
	if( !empty($listusers) ){
?>
	<option value="<?php echo $listusers;?>"><?php echo $this->Common->userFullname($listusers); ?></option>
<?php 	}
	}
}?>