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
    <h3 class="modal-title" id="createModelLabel">Delete Area</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Area?<br /><br />After deleting this Area, click Generate to assign a new template to the parent Workspace.</div>
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
				id = '<?php echo $area_id; ?>',
				$panel = $btn.parents('.panel:first'),
	            data = $panel.data(),
	            workspace_id = data.wid;

			$.check_password().done(function(checked){
				if($btn.length > 0 && checked) {
					$.ajax({
						url: $js_config.base_url + 'studios/delete_area',
						global: true,
						data: $.param({
							workspace_id: workspace_id, area_id: id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
								$('.workspace_wrapper .panel-heading .selectable.selected').removeClass('selected');
	                            var $parent_panel = $('.workspace_wrapper .panel[data-id=' + workspace_id + ']');

	                            $('.panel-heading .selectable', $parent_panel).trigger('click')
							}
							$('#modal_delete').modal('hide');
						}
					})
				}
			})
		});

	})
</script>