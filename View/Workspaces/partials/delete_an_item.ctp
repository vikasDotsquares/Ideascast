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
    <h3 class="modal-title" id="createModelLabel">Delete Workspace</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Workspace?</div>
	<?php if(password_protected($project_id)){ ?>
	<div class="form-horizontal">
    	<div class="col-sm-8 input-wrap">
            <input type="password" class="form-control user-password" placeholder="Enter your password" id="" autocomplete="new-password" name="fake" >
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
				project_id = '<?php echo $project_id; ?>',
				workspace_id = '<?php echo $workspace_id; ?>',
				pw_id = '<?php echo $pw_id; ?>',
				$tr = $btn.parents('.workspace-tasks-sec-top:first');

			$.check_password().done(function(checked){
				if($btn.length > 0 && checked) {
					$.ajax({
						url: $js_config.base_url + 'projects/trashWorkspace/' + project_id + '/' + workspace_id,
						global: false,
						data: $.param({
							'action': 'delete',
							pwid: pw_id,
							wsid: workspace_id,
							prjid: project_id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
								if(response.content){
									// SEND WEB NOTIFICATION
									$.socket.emit('socket:notification', response.content.socket, function(userdata){});
								}
								// REMOVE LIST OF DELETED WORKSPACE.

								if($btn.hasClass('workspace-button')){

									location.href= $js_config.base_url + 'projects/index/' + project_id ;

								}else{



							    $.reload_project_progress();
								$tr.slideUp(1000, function() {
										$(this).remove();
										// IF ALL ROWS WERE REMOVED, REFRESH THE PAGE TO SHOW CREATE WORKSPACE MESSAGE BOX
										if ($(".workspace-tasks-sec-wrap").find('.small-box').length <= 0) {
											location.reload();
									    }
									});
								// ALSO REMOVE WORKSPACE ENTRY FROM LEFT-BAR
								/*var $list = $("#sideMenu.normal-lists.sideMenu").find("ul.sidebar-menu.project_workspaces"),
									$list_item = $list.find('#' + workspace_id);
								if ($list_item.length) {
									setTimeout(function () {
										$list_item.effect("shake", {
											times: 3
									   	}, 600, function () {
											$list_item.remove()
									   	});
								  	}, 200);
							 	}*/

								}


								$('#modal_delete').modal('hide');


							}
						}
					})
				}
			})
		});
	})
</script>