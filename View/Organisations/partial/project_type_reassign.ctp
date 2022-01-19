<?php
$current_list = $this->Scratch->project_type_list($id);
$lists = $this->Scratch->project_type_list($id, 1);

function asrt($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['align']['title']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['align']['title']);
    return strcasecmp($t1, $t2);
}
// usort($lists, 'asrt');
?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Reassign</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body clearfix">
		<input type="hidden" name="delete_id" id="delete_id" value="<?php echo $current_list[0]['align']['id']; ?>">
		<div class="form-group row">
			<div class="col-sm-12"><label>Reassign Projects with Type:</label></div>
			<div class="col-sm-12"><?php echo htmlentities($current_list[0]['align']['title'], ENT_QUOTES, "UTF-8"); ?></div>
		</div>
		<div class="form-group mb5">
			<label>To Type:</label>
			<select class="form-control" name="update_id" id="update_id">
				<option value="">Select Type</option>
			<?php
			if(isset($lists) && !empty($lists)){
				foreach ($lists as $key => $list) {
					$prj_count = (!empty($list['pcount']['prj_count'])) ? $list['pcount']['prj_count'] : 0; ?>
				<option value="<?php echo $list['align']['id']; ?>"><?php echo $list['align']['title'];//.' ('.$prj_count.')'; ?></option>
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


			var $ofld = $('#tab_project_type').find('.sort_order.active'),
				order = ($ofld.data('order') == 'asc') ? 'desc' : 'asc',
				field = $ofld.data('field');
			var data = { order: order, field: field };

			if(update_id != '' && update_id !== undefined){
				$.ajax({
		    		url: $js_config.base_url + 'organisations/project_type_reassign',
		    		type: 'POST',
		    		dataType: 'json',
		    		data: {delete_id: delete_id, update_id: update_id},
		    		success: (response) => {
		    			if(response.success){
		    				$('#model_reassign').modal('hide');
		    				$.project_type_list(data);
		    			}
		    		}
		    	})
			}
		});
	})
</script>