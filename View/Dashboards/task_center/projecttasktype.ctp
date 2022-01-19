<?php if( isset($tasktype_lists) && !empty($tasktype_lists) ) {?>
<?php
foreach($tasktype_lists as $id => $typelist){ ?><option value="<?php echo $typelist['project_element_types']['ele_type_id'];?>"><?php echo $typelist['project_element_types']['ele_type_title'];?></option>
<?php	} 
	}  
?> 