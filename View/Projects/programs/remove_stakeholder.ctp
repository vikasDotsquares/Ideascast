<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

?>
<div class="modal-header">
	<button type="button" class="close close-prog prog-mt7" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Remove Me</h3>
</div><div class="modal-body popup-select-icon">
	<div class="message-text">Are you sure you want to remove yourself as a Stakeholder for this Program?</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-success remove_me">Remove</button>
	<button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<style>
</style>

<script>
$(function(){
	var program_id = '<?php echo $program_id; ?>'

	$('.remove_me').off('click').on('click', function(event) {
		event.preventDefault();
		var data = {
			program_id: program_id
		};

		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'remove_stakeholder',
			type: 'POST',
			dataType: 'json',
			data: data,
			context: this,
			success: function(response){

				if(response.success){
					if(response.content){
						// SEND WEB NOTIFICATION
						$.socket.emit('socket:notification', response.content.socket, function(userdata){});
					}
					$('.prog-summary-data .pg-data-row[data-prog="'+program_id+'"]').slideUp(300, ()=>{
						$(this).remove();
					})
					$(this).prop('disabled', false);
					$.program_deleted = true;
					$('#modal_delete').modal('hide');
				}
			}
		})
	})

	/*$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
		container: 'body',
		placement: "top"
	});*/
})

</script>