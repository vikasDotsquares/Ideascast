<?php
if(isset($program_ists) && !empty($program_ists)){
	// pr($program_ists);
	$list_count = count($program_ists);
	foreach ($program_ists as $key => $list) {
		$prog_count = (!empty($list[0]['prog_count'])) ? $list[0]['prog_count'] : 0;
?>
<div class="type-list-data-row" data-id="<?php echo $list['types']['id']; ?>" data-title="<?php echo htmlentities($list['types']['type'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="type-list-col type-list-col-1"><?php echo htmlentities($list['types']['type'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="type-list-col type-list-col-2"><?php echo $prog_count; ?></div>
	<div class="type-list-col type-list-col-3 type-list-actions">
		<a href="#" class="edit-program-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<?php if($prog_count > 0){ ?>
		<a class="tipText reassign-icon" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'program_type_reassign', $list['types']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php }else if($list_count > 1){ ?>
		<a class="tipText delete-prog-type" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.types_wrap').find('.type-list-data-row:first').find('.up-icon').addClass('not-shown');
		$('.types_wrap').find('.type-list-data-row:last').find('.down-icon').addClass('not-shown');
		$('.total-data', $('#tab_program_type')).text(" (" + $('.type-list-data-row', $('#tab_program_type')).length + ")");
	})
</script>