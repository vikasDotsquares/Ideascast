
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Story</h3>
</div>
<div class="modal-body popup-select-icon clearfix">
	<div class="message-text">Are you sure you want to delete this Story?</div>
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
					data: { id: id, type: 'story' },
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
							$.countRows('story', $('#tab_story.ssd-tabs'));
							$('.search-box[data-type="story"]').val('');
							$.get_stories();

							$('#modal_delete').modal('hide');
						}
					}
				})
			}

		});

	})
</script>