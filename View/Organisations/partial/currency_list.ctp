
<?php

$active_currencies = $this->Scratch->active_currencies();
if(isset($currency_list) && !empty($currency_list)){
	$list_count = count($currency_list);
	foreach ($currency_list as $key => $list) {
		$prj_count = (!empty($list['pcount']['prj_count'])) ? $list['pcount']['prj_count'] : 0;
?>
<div class="currency-col-row" data-id="<?php echo $list['cur']['id']; ?>" data-restrict="<?php if($active_currencies > 1){ ?>false<?php }else{ ?>true<?php } ?>" data-pcount="<?php echo $prj_count; ?>">
	<div class="cur-col cur-col-1 "><?php echo htmlentities($list['cur']['title'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="cur-col cur-col-2"><?php echo htmlentities($list['cur']['sign'], ENT_QUOTES, "UTF-8"); ?></div>
	<div class="cur-col cur-col-3"><?php if(!empty($list['cur']['status'])){ ?><i class="checkblack"></i><?php } ?></div>
	<div class="cur-col cur-col-5"><?php echo $prj_count; ?></div>
	<div class="cur-col cur-col-5 cur-actions">

		<a href="#" class="edit-curr tipText" title="Edit"><i class="edit-icon"></i></a>

		<?php if($prj_count > 0){
		if($active_currencies > 1){ ?>
		<a class="tipText reassign-curr" href="#" title="Reassign" data-toggle="modal" data-target="#model_reassign" data-remote="<?php echo Router::url(['controller' => 'organisations', 'action' => 'currency_reassign', $list['cur']['id'], 'admin' => FALSE], TRUE); ?>"> <i class="reassignblackicon"></i></a>
		<?php }
			}else if($list_count > 1){ ?>
		<a class="tipText del-curr" href="#" title="Delete"> <i class="clearblackicon"></i></a>
		<?php } ?>
	</div>
</div>
	<?php } ?>
<?php } ?>

<script type="text/javascript">
	$(() => {
		$('.curr-count').text(" (" + $('.currency-col-row').length + ")");
	})
</script>