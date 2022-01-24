
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Location</h3>
</div>
<div class="modal-body popup-select-icon clearfix delete-loc-body">
	<?php if (isset($total_data) ) { ?>
	<?php if((isset($used_org) && !empty($used_org)) && (isset($used_user) && !empty($used_user))){ ?>

		<div class="message-text"><span class="del-msg-loc1">This Location is used by <?php echo ($used_org > 1) ? $used_org.' Organizations' : (($used_org == 1) ? $used_org.' Organization' : ''); ?> <?php echo ($used_user > 1) ? ' and '.$used_user.' People' : (($used_user == 1) ? ' and '.$used_user.' Person' : ''); ?>.</span> <span class="del-msg-loc2">Are you sure you want to delete this Location?</span></div>

	<?php }else if((isset($used_org) && !empty($used_org)) && (!isset($used_user) || empty($used_user))){ ?>

		<div class="message-text"><span class="del-msg-loc1">This Location is used by <?php echo ($used_org > 1) ? $used_org.' Organizations' : (($used_org == 1) ? $used_org.' Organization' : ''); ?>.</span><span class="del-msg-loc2">Are you sure you want to delete this Location?</span></div>

	<?php }else if((!isset($used_org)|| empty($used_org)) && (isset($used_user) && !empty($used_user))){ ?>

		<div class="message-text"><span class="del-msg-loc1">This Location is used by <?php echo ($used_user > 1) ? $used_user.' People' : (($used_user == 1) ? $used_user.' Person' : ''); ?>.</span> <span class="del-msg-loc2">Are you sure you want to delete this Location?</span></div>

	<?php }else{ ?>
		<div class="message-text">Are you sure you want to delete this Location?</div>
	<?php } ?>
	<?php }else{ ?>
		<div class="message-text">Are you sure you want to delete this Location?</div>
	<?php } ?>

</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<button type="button" class="btn btn-success btn-delete"> Delete</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancel</button>
</div>

<script type="text/javascript">
	$(function(){


		$('.btn-delete').click(function(event) {
			event.preventDefault();

			var $btn = $.current_delete,
				$panel = $btn.parents('.ssd-data-row:first');

			var	id = '<?php echo $id; ?>';
			if( id > 0 ) {

				$.ajax({
					url: $.module_url + 'trash',
					data: { id: id, type: 'loc' },
					type: 'post',
					dataType: 'json',
					success: function (response) {
						if(response.success) {
							$($panel).animate({
								opacity: 0,
								height: 0
							}, 100, function () {
								$(this).remove()
							})
							$.countRows('loc', $('#tab_loc.ssd-tabs'));

							$('#modal_delete').modal('hide');
						}
					}
				})
			}

		});

	})
</script>