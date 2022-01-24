<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Save As </h4>
</div>
<div class="modal-body">
	<div class="form-group mb0">
		<div class="row">
			<label for="UserUser" class="col-sm-3 control-label">Name: <sup>*</sup></label>
			<div class="col-sm-9 pl-0">
				<input class="form-control" name="view_name" id="view_name" maxlength="30" placeholder="max 30 chars" autocomplete="off" type="text" value="">
				<label class="error text-red"></label>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" id="" class="btn btn-primary save-view-data" disabled="">Save</button>
	<button type="button" id="" class="btn outline-btn-s" data-dismiss="modal">Cancel</button>
</div>
<script type="text/javascript">
	$(()=>{
		$('#view_name').off('keyup').on('keyup blur', function(event) {
			var val = $(this).val();
			$('.save-view-data').prop('disabled', false);
			if(val == '' || val === undefined){
				$('.save-view-data').prop('disabled', true);
			}
		})
		$('.save-view-data').off('click').on('click', function(event) {
			event.preventDefault();
			var data = {
				view_name: $('#view_name').val()
			}
			if($('#view_name').val() == '' || $('#view_name').val() === undefined) return;
			$.compare_post().done(function(post_data) {
				if(post_data['post']) {
					delete post_data['post'];
					var selection = {'selection': post_data};
					data = $.extend({}, data, selection);
				}
				$.ajax({
		            url: $js_config.base_url + 'competencies/saveas_compare_view',
		            type: 'POST',
		            dataType: 'json',
		            data: data,
		            success: function(response){
						if(response.success){
							$.comp_view_saved = true;
							$('#modal_save_compare_view').modal('hide');
						}
		            }
		        })
	        })
		});
	})
</script>