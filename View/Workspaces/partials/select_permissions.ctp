

<label class="permissions permit_read btn-circle btn-xs tipText " data-original-title="Read"> 
	<input name="data[ElementPermission][permit_read]" value="1" id="" type="checkbox" > 
	<i class="fa fa-eye lbl-icn"></i> 
</label> 

<label class="permissions permit_edit btn-circle btn-xs tipText " data-original-title="Update"> 
	<input name="data[ElementPermission][permit_edit]" value="1" type="checkbox" > 
	<i class="fa fa-pencil"></i> 
</label> 

<label class="permissions permit_delete btn-circle btn-xs tipText " data-original-title="Delete"> 
	<input name="data[ElementPermission][permit_delete]" value="1" type="checkbox" > 
	<i class="fa fa-trash"></i> 
</label> 
<?php if( isset($type) && $type == 'select' ) { ?>
<label class="permissions permit_copy btn-circle btn-xs tipText " data-original-title="Copy"> 
	<input name="data[ElementPermission][permit_copy]" value="1" type="checkbox" > 
	<i class="fa fa-copy"></i> 
</label> 

<label class="permissions permit_move btn-circle btn-xs tipText " data-original-title="Cut &amp; Move"> 
	<input name="data[ElementPermission][permit_move]" value="1" type="checkbox" > 
	<i class="fa fa-cut"></i> 
</label>
<?php } ?>