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
    <h3 class="modal-title" id="createModelLabel">Delete Skill</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Skill?</div>
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
				$panel = $btn.parents('.ssd-data-row:first'),
	            data = $panel.data();
			
			var	id = '<?php echo $skill_id; ?>';
				if( id > 0 ) {
					
					$.ajax({
						url: $js_config.base_url + 'competencies/delete_skill',
						global: true,
						data: $.param({
							skill_id: id
						}),
						type: 'post',
						dataType: 'json',
						success: function (response) {
							if(response.success) {
											
								//$panel.remove();
								
								setTimeout(function () {
									$($panel).animate({
										opacity: 0,
										height: 0
									}, 800, function () {
										$(this).remove()
									})
								}, 1000)
								
							}
							$('#modal_delete').modal('hide');
						}
					})
				}
			 
		});

	})
</script>