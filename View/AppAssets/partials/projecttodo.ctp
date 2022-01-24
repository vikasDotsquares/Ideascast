<option value="">Select To-do</option>
<?php 
if( isset($projecttodos) && !empty($projecttodos) ){
foreach($projecttodos as $key => $listtodos){
	if( !empty($key) && !empty($listtodos) ){
?>
	<option value="<?php echo $key;?>"><?php echo $listtodos;?></option>
<?php 	}
	}
}?>