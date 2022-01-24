<?php if(isset($program_id) && !empty($program_id)) { ?>
	<select class="aqua" name="data[Project][id]">
		<option value="">Select Project</option>
		<?php if( isset($projects) && !empty($projects) ) { ?>
			<?php foreach($projects as $key => $value ) { ?>
				<option value="<?php echo $key; ?>"><?php echo html_entity_decode($value); ?></option>
			<?php } ?>
		<?php } ?>
	</select>
<?php }else{ ?>
	<select class="aqua" name="data[Project][id]">
		<option value="none">Select Project</option>
		<option value="">All Projects</option>
		<?php if( isset($projects) && !empty($projects) ) { ?>
			<?php foreach($projects as $key => $value ) { ?>
				<option value="<?php echo $key; ?>"><?php echo html_entity_decode($value); ?></option>
			<?php } ?>
		<?php } ?>
	</select>
<?php } ?>