<?php
if(isset($task_type_list) && !empty($task_type_list)){
	$list_count = count($task_type_list);
	foreach ($task_type_list as $key => $list) {
?>
<div class="task-type-list-data-row" data-id="<?php echo $list['project_types']['id']; ?>" data-title="<?php echo htmlentities($list['project_types']['title'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="task-type-list-col task-type-list-col-1"><?php echo htmlentities($list['project_types']['title'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="task-type-list-col type-list-col-3 task-type-list-actions">
		<a href="#" class="edit-task-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<?php if($list_count > 1){ ?>
		<a class="tipText delete-task-type" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.list-counter', $('#tab_task_type')).text(" (" + $('.task-type-list-data-row', $('#tab_task_type')).length + ")");
	})
</script>