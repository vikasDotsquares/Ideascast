<option value="">Select a Program</option>
<?php
if( isset($programlist) && !empty($programlist) ){
	foreach($programlist as $key => $lists){
?>
	<option value="<?php echo $key;?>"><?php echo strip_tags($lists);?> (<?php echo $this->Common->program_project_count($key);?>)</option>
<?php }
} ?>