<?php
$current_list = $this->Scratch->story_type_list($id);
$lists = $this->Scratch->story_type_list($id, 1, ['where' => 'status = 1']); //
// pr($lists);
?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Reassign</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body clearfix">
		<input type="hidden" name="delete_id" id="delete_id" value="<?php echo $current_list[0]['ot']['id']; ?>">
		<div class="form-group row">
			<div class="col-sm-12"><label>Reassign Stories with Type:</label></div>
			<div class="col-sm-12"><?php echo htmlentities($current_list[0]['ot']['title'], ENT_QUOTES, "UTF-8"); ?> </div>
		</div>
		<div class="form-group mb5">
			<label>To Type:</label>
			<select class="form-control" name="update_id" id="update_id">
				<option value="">Select Type</option>
			<?php
			if(isset($lists) && !empty($lists)){
				foreach ($lists as $key => $list) {?>
				<option value="<?php echo $list['ot']['id']; ?>"><?php echo $list['ot']['title']; ?></option>
				<?php } ?>
				<?php } ?>
			</select>
		</div>
	</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-assign ftbtndisable">Reassign</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>


<script type="text/javascript">
	$(() => {

		$("#update_id").off('change').on('change', function(event) {
			var update_id = $(this).val();
			$('.submit-assign').addClass('ftbtndisable');
			if(update_id != '' && update_id !== undefined){
				$('.submit-assign').removeClass('ftbtndisable');
			}
		})

		$(".submit-assign").off('click').on('click', function(event) {
			event.preventDefault();
			var $this = $(this),
				delete_id = $('#delete_id').val(),
				update_id = $('#update_id').val();

			var data = {};

			if(update_id != '' && update_id !== undefined){
				$.ajax({
		    		url: $js_config.base_url + 'organisations/story_reassign',
		    		type: 'POST',
		    		dataType: 'json',
		    		data: {delete_id: delete_id, update_id: update_id},
		    		success: (response) => {
		    			if(response.success){
		    				$('#model_reassign').modal('hide');
		    				$.story_type_list(data);
		    			}
		    		}
		    	})
			}
		});
	})
</script>