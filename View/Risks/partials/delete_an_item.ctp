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
	.modal-body.clearfix .form-horizontal {
		margin-top: 20px;
	}
	.form-horizontal .input-wrap {
		padding-left: 0;
	}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Delete Risk</h3>
</div>
<div class="modal-body clearfix">
	<div class="message-text">Are you sure you want to delete this Risk?</div>
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
				id = '<?php echo $risk_id; ?>',
				project_id = '<?php echo $project_id; ?>',
				edit = '<?php echo $edit; ?>',
				summary = '<?php echo $summary; ?>',
				$parent_row = $btn.parents('.risks-data-row:first');

			var $parent = $('.risks-summary-wrap:first')
            var $wrapper = $('.risks-summary-data')


			if(edit){
				$.check_password().done(function(checked){
					if($btn.length > 0 && checked) {
						var params = { 'id': id };
						$.trash_risk(params).done(function(response){
							location.href = $js_config.base_url + 'risks/risk_center/' + project_id
						});
					}
				})
			}
			else{
				$.check_password().done(function(checked){
					if($btn.length > 0 && checked) {
						var params = { 'id': id };
						$.trash_risk(params).done(function(response){
							$parent_row.remove();
	                        $('.tooltip').remove();
	                        if(!summary){
		                        /*setTimeout(function() {
		                            $.risk_projects(null, project_id);
		                        }, 1)*/
	                        	$.countRows('risks', $parent);
		                    }
		                    else{
		                    	$parent = $("#tab_risk");
		                    	$.countRiskRows('risks', $parent);
		                    	$.update_risk_map();
		                    }
		                    $.update_my_risk();
            				$.risk_list(0, $wrapper, $parent);
		                    $('#modal_delete').modal('hide');
						});
					}
				})
			}
		});
	})
</script>