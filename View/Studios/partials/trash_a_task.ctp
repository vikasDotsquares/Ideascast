<style type="text/css">
	.not-editable {
		pointer-events: none;
		background-color: #f4f4f4;
	    color: #444;
	    border-color: #ddd;
	}
	.error-text {
		font-size: 12px;
		padding-top: 7px;
    	display: inline-block;
	}
	.pass-wrap {
		margin-top: 20px;
	}
	.check-password {
		cursor: pointer;
	}
	.modal-body.clearfix .form-horizontal {
		margin-top: 20px;
	}
	.form-horizontal .input-wrap {
		padding-left: 0;
	}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Task</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Task?</div>
	<?php if(password_protected($project_id)){ ?>
	<div class="form-horizontal">
    	<div class="col-sm-8 input-wrap">
            <input type="password" class="form-control user-password" placeholder="Enter your password" id="pwdin" autocomplete="off">
		</div>
		<div class="col-sm-4">
			<span class="error-text"></span>
		</div>
    </div>
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
				id = '<?php echo $element_id; ?>',
				$panel = $btn.parents('.panel:first'),
	            data = $panel.data(),
	            area_id = data.aid;

			$.check_password().done(function(checked){
				if($btn.length > 0 && checked) {
					$.ajax({
						url: $js_config.base_url + 'entities/remove_element/' + id,
						global: true,
						data: $.param({
							'data[Element][id]': id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
								$("#currenttaskid_"+id).remove(); 
								$('.zone_wrapper .panel-heading .selectable.selected').removeClass('selected');
	                            var $parent_panel = $('.zone_wrapper .panel[data-id=' + area_id + ']');

	                            $('.panel-heading .selectable', $parent_panel).trigger('click')
	                            $.area_studio_status('Area', area_id)
	                            if (response.content) {
	                                // send web notification
	                                $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
	                            }
							}
							$('#modal_delete').modal('hide');
						}
					})
				}
			})
		});

	})
</script>