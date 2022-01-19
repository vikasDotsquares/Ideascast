
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Organization</h3>
</div>
<div class="modal-body popup-select-icon clearfix">
	<?php if( isset($used_users) && !empty($used_users) ){ ?>

		<div class="message-text">
			<span class="del-msg-loc1">This Organization is used by <?php echo ($used_users > 1) ? $used_users.' People' : $used_users.' Person'; ?>.</span>
			<span class="del-msg-loc2">Are you sure you want to delete this Organization?</span>
		</div>

	<?php }else{ ?>
		<div class="message-text">Are you sure you want to delete this Organization?</div>
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
					data: { id: id, type: 'org' },
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
							$.countRows('org', $('#tab_org.ssd-tabs'));
							// $.countRows('loc', $('#tab_loc.ssd-tabs'));
							$.get_locations();

							$('#modal_delete').modal('hide');
						}
					}
				})
			}

		});

	})
</script>