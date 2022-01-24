<?php
/*	$prt_array = array( "Compliance", "Cost", "Customer", "Equipment");
	$rt_array = array( "Compliance", "Cost", "Customer", "Governance");

	$one = array_diff($prt_array,$rt_array);
	$two = array_diff($rt_array,$prt_array);

	e("delete");
	pr($one);
	echo "add";
	pr($two);*/
?>

<?php
if(isset($story_lists) && !empty($story_lists)){
	// pr($org_lists);
	$list_count = count($story_lists);
	foreach ($story_lists as $key => $list) {
		$counter = (!empty($list['counts']['counter'])) ? $list['counts']['counter'] : 0;
?>
<div class="task-type-list-data-row" data-id="<?php echo $list['ot']['id']; ?>" data-title="<?php echo htmlentities($list['ot']['title'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="task-type-list-col task-type-list-col-1"><?php echo htmlentities($list['ot']['title'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="task-type-list-col task-type-list-col-2"><?php echo $counter; ?></div>
	<div class="task-type-list-col type-list-col-3 task-type-list-actions">
		<a href="#" class="edit-task-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<?php if($counter > 0){ ?>
		<a class="tipText reassign-item" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'story_reassign', $list['ot']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php } else if($list_count > 1){ ?>
		<a class="tipText delete-task-type" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.list-counter', $('#tab_story_type')).text(" (" + $('.task-type-list-data-row', $('#tab_story_type')).length + ")");
	})
</script>