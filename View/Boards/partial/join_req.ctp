<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><span class="text-ellipsis">Request To Join Project</span></h3>
</div>
<div class="modal-body">
	<div class="join-req-label"><label>Project:</label> <?php echo htmlentities($data['Project']['title'], ENT_QUOTES, "UTF-8"); ?> </div>
	<div class="join-req-area"><label>Message:</label>
	<textarea placeholder="" autofocus name="message" id="message">I would like to take part in your project.</textarea>
	</div>
</div>

<div class="modal-footer clearfix">
	<button type="button" class="btn btn-success right send-request" >Request</button>
	<button type="button" id="discard" class="btn btn-danger right" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript">
	$(function(){
		var pid = '<?php echo $pid; ?>';
		$('.send-request').off('click').on('click', function(event) {
			event.preventDefault();
			$(this).prop('disabled', true);
			var msg = $.trim($("#message").val()) || "I would like to take part in your project.";
			var data = {project_id: pid, message: msg};
			$.ajax({
				url: $js_config.base_url + 'boards/join_req',
				type: 'POST',
				dataType: 'JSON',
				data: data,
				success: function(response){
					$('.send-request').prop('disabled', false);
					if(response.success){
						$.socket.emit('socket:notification', response.content, function(userdata){ });
						$.req_sent = true;
						$("#modal_request").modal('hide');
					}
				}
			})
		})
	})
</script>