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
    <h3 class="modal-title" id="createModelLabel">Delete Project</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Project?</div>
	<?php if(password_protected($id)){ ?>
		<div class="form-horizontal">
	    	<div class="col-sm-8 input-wrap">
	            <input type="password" class="form-control user-password" placeholder="Enter your password" id="pwdin" autocomplete="new-password">
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
				id = '<?php echo $id; ?>';
			$.check_password().done(function(checked){
				if($btn.length > 0 && checked) {
					$.ajax({
						url: $js_config.base_url + 'users/project_delete',
						global: false,
						data: $.param({
							'action': 'delete' , 'id': id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							// UPDATE REWARD GRAPH IN HEADER.
							$.show_reward_graphs();
							if(response.success) {
								if($btn.is('.workspace-button.delete-an-item')){
									location.href = $js_config.base_url + 'projects/lists';
								}
								// REMOVE LIST PANEL OF DELETED PROJECT.
								$btn.parents('.panel:first').parent().slideUp(400, function(){
									$btn.parents('li:first').remove();
									// IF THERE ARE EMPTY PROJECTS LIST THEN RELOAD THE PAGE.
									if($('#list_grid_container ul li').length <= 0){
										location.reload();
									}
									else if ($.isFunction($.reload_chat)) {
										$.reload_chat();
									}
								})
								$( "li.current_task[data-taskproject='"+id+"']" ).remove();
							}
							if( response.currentproject ){
								var $superparents = $('#list_grid_container');
								$superparents.find('li.li-listing .fav-current-project').not('.active').attr('data-original-title','Pin Project');
								var elem = $(".sidebar").find("#sidebar_menu li.prevcrntprjt").parent().find('.currentproject');
								setTimeout(function(){
									if($("#currentpid"+id).length > 0){
										$superparents.find('li.li-listing .fav-current-project').not('.active').removeClass('disable');
										$superparents.find('li.li-listing .fav-current-project').not('.active').removeAttr('title');
										$superparents.find('li.li-listing .fav-current-project').not('.active').css('cursor','pointer');;
										//$.sortList(elem);
										$("#currentpid"+id).remove();
									}

								},100)
							}

							if( $btn.is("#confirm_deletes") ){
								location.reload();
							}

							$('#modal_delete').modal('hide');
						}
					})
				}
			})
		});
	})
</script>