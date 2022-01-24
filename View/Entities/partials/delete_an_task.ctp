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
				update_page = '<?php echo $update_page; ?>',
				$el_panel = $('body').find('div[data-element=' + id + ']');

			$.check_password().done(function(checked){
				if($btn.length > 0 && checked) {
					$.ajax({
						url: $js_config.base_url + 'entities/remove_task/' + id,
						global: false,
						data: $.param({
							'data[Element][id]': id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {

								$("#currenttaskid_"+id).remove();

								if(update_page == 1){
									var redirect = $js_config.base_url + 'projects/tasks/' + $js_config.currentProjectId + '/' + $js_config.currentWorkspaceId;
	                            	location.href = redirect;
								}
								else{
									// REMOVE LIST PANEL OF DELETED PROJECT.
									$.show_wsp_options();
									$.reload_wsp_progress();
									$el_panel.slideUp(1000, function() {
		                                $el_panel.remove();
		                                $js_config.elements_details = response.content;
		                            })
									$.reload_workspace();

									if($btn.is('.del-task')){
										$btn.parents('.ps-data-row:first').slideUp(() => {
											$(this).remove();
											$.countRows('task_list', $('#tab_task_list'));
											$.getWspTaskActivities();
										})
									}
									else{
										$.getWspTaskActivities();
									}
		                        }
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