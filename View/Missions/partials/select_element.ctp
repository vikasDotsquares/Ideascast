<style>
.error-message{
	display:none;
}  
</style> 

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 3px 0 0;"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">
		<?php if( $hash == 'mind_maps' ){
				echo "Add Mind Map";
			} else {
				echo 'Add '.ucfirst(substr($hash,0,-1));
			}	
			?>
		</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-group " >
			<input type="hidden" value="<?php echo $hash; ?>" name="hash" id="hash">
			<?php if( isset($data) && !empty($data) ) { ?>
					<label>Select Task:</label>	
					<select id="selected_element" name="selected_element" class="aqua" style="width: 100%; border: 1px solid #00C0EF;" size="5">
						
						<?php foreach( $data as $key => $element ) {
							$el = $element['Element'];
						?>
						<option value="<?php echo $el['id'] ?>" ><?php echo htmlentities($el['title']); ?></option>
						<?php } ?>
					</select>
					<div class="error-message">Please select Task</div>
			<?php }
			else{ ?>
			<div class="no-data">No task found in this workspace.</div>
			<?php } ?>
		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<?php if( isset($data) && !empty($data) ) { ?>
		 <button type="button" id="confirm_element" class="btn btn-success">Confirm</button>
		<?php }?>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>

<script type="text/javascript" >
$(function(){
	$('#modal_box').on('hidden.bs.modal', function () {
		if( $(this).data('bs.modal') ) {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')
		}
	});


})
</script>