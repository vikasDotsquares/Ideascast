<?php if( isset($userlists) && !empty($userlists) ){ ?>
<select name="data[User][user_id]" class="form-control" id="domain_userid"  >
<option value="">All Domain Users</option>
<?php
	foreach($userlists as $userdetails ){
	?>

	<option value="<?php echo $userdetails['id'];?>"><?php echo ucfirst($userdetails['first_name']).' '.ucfirst($userdetails['last_name']);?></option>

<?php } ?>
</select>
<label id="domainuser_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
<?php } ?>