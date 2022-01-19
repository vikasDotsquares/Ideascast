<?php
if(isset($org_lists) && !empty($org_lists)){
	// pr($org_lists);
	$list_count = count($org_lists);
	foreach ($org_lists as $key => $list) {
		$counter = (!empty($list['counts']['counter'])) ? $list['counts']['counter'] : 0;
?>
<div class="task-type-list-data-row" data-id="<?php echo $list['ot']['id']; ?>" data-title="<?php echo htmlentities($list['ot']['title'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="task-type-list-col task-type-list-col-1"><?php echo htmlentities($list['ot']['title'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="task-type-list-col task-type-list-col-2"><?php echo $counter; ?></div>
	<div class="task-type-list-col type-list-col-3 task-type-list-actions">
		<a href="#" class="edit-task-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<?php if($counter > 0){ ?>
		<a class="tipText reassign-item" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'org_reassign', $list['ot']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php } else if($list_count > 1){ ?>
		<a class="tipText delete-task-type" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.list-counter', $('#tab_org_type')).text(" (" + $('.task-type-list-data-row', $('#tab_org_type')).length + ")");
	})
</script>