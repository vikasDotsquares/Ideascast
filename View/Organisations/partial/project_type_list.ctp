<?php
if(isset($lists) && !empty($lists)){
	$list_count = count($lists);
	foreach ($lists as $key => $list) {
		$prj_count = (!empty($list['pcount']['prj_count'])) ? $list['pcount']['prj_count'] : 0;
?>
<div class="type-list-data-row" data-id="<?php echo $list['align']['id']; ?>" data-title="<?php echo htmlentities($list['align']['title'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="type-list-col type-list-col-1"><?php echo htmlentities($list['align']['title'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="type-list-col type-list-col-2"><?php echo $prj_count; ?></div>
	<div class="type-list-col type-list-col-3 type-list-actions">
		<a href="#" class="edit-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<a href="#" class="down-icon tipText" title="Move Down"><i class="downblack"></i></a>
		<a href="#" class="up-icon tipText" title="Move Up"><i class="upblack"></i></a>
		<?php if($prj_count > 0){ ?>
		<a class="tipText reassign-icon" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'project_type_reassign', $list['align']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php }else if($list_count > 1){ ?>
		<a class="tipText delete-icon" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.types_wrap').find('.type-list-data-row:first').find('.up-icon').addClass('not-shown');
		$('.types_wrap').find('.type-list-data-row:last').find('.down-icon').addClass('not-shown');
		$('.total-data', $('#tab_project_type')).text(" (" + $('.type-list-data-row', $('#tab_project_type')).length + ")");
	})
</script>