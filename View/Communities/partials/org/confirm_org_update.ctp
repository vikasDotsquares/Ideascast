
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Edit Organization</h3>
</div>
<div class="modal-body">
	<div class="message-text">
		<span class="del-msg-loc1">Changing the Locations selection will remove Locations from <?php echo ($total_users > 1) ? $total_users.' People' : $total_users.' Person'; ?>.</span>
		<span class="del-msg-loc2">Are you sure you want save these changes?</span>
	</div>
</div>

<div class="modal-footer clearfix">
	<button type="button" class="btn btn-success confirm_submit">Save</button>
	<button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript" >
$(function(){
	$('.confirm_submit').off('click').on('click', function(event) {
		event.preventDefault();
		$('.submit_data').prop('disabled', true);
		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'save_org_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			context: this,
			success: function(response){

				if(response.success){
					$(this).prop('disabled', false);
					$.popup_updates.action = true;
					$.popup_updates.section = 'edit';
					$.popup_updates.reaction = 'org';
					$.get_locations();
					$.countRows('loc', $('#tab_loc.ssd-tabs'));
					$('#modal_create').modal('hide');
					$('#modal_delete').modal('hide');
				}
			}
		})
	})
})
</script>