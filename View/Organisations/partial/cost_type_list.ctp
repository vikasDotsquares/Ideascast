<?php
if(isset($cost_type_list) && !empty($cost_type_list)){
	// pr($program_ists);
	$list_count = count($cost_type_list);
	foreach ($cost_type_list as $key => $list) {
		// pr($list);
		$el_count = (!empty($list[0]['el_count'])) ? $list[0]['el_count'] : 0;
?>
<div class="type-list-data-row" data-id="<?php echo $list['ctypes']['id']; ?>" data-title="<?php echo htmlentities($list['ctypes']['type'], ENT_QUOTES, "UTF-8"); ?>">
	<div class="type-list-col type-list-col-1"><?php echo htmlentities($list['ctypes']['type'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="type-list-col type-list-col-2"><?php echo $el_count; ?></div>
	<div class="type-list-col type-list-col-3 type-list-actions">
		<a href="#" class="edit-cost-type tipText" title="Edit"><i class="edit-icon"></i></a>
		<?php if($el_count > 0){ ?>
		<a class="tipText reassign-icon" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'cost_type_reassign', $list['ctypes']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php }else if($list_count > 1){ ?>
		<a class="tipText delete-cost-type" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(() => {
		$('.types_wrap', $('#tab_cost_type')).find('.type-list-data-row:first').find('.up-icon').addClass('not-shown');
		$('.types_wrap', $('#tab_cost_type')).find('.type-list-data-row:last').find('.down-icon').addClass('not-shown');
		$('.total-data', $('#tab_cost_type')).text(" (" + $('.type-list-data-row', $('#tab_cost_type')).length + ")");
		console.log($('.total-data', $('#tab_cost_type')))
	})
</script>