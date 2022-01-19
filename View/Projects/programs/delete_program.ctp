<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

?>
<div class="modal-header">
	<button type="button" class="close close-prog prog-mt7" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Delete Program</h3>
</div><div class="modal-body popup-select-icon">
	<div class="message-text">Are you sure you want to delete this Program?</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-success delete_prog">Delete</button>
	<button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<style>
</style>

<script>
$(function(){
	var program_id = '<?php echo $program_id; ?>'

	$('.delete_prog').off('click').on('click', function(event) {
		event.preventDefault();
		var data = {
			program_id: program_id
		};

		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'delete_programs',
			type: 'POST',
			dataType: 'json',
			data: data,
			context: this,
			success: function(response){

				if(response.success){
					$('.prog-summary-data .pg-data-row[data-prog="'+program_id+'"]').slideUp(300, ()=>{
						$(this).remove();
					})
					$(this).prop('disabled', false);
					$.program_deleted = true;
					$('#modal_delete').modal('hide');
				}
			}
		})
	})

	/*$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
		container: 'body',
		placement: "top"
	});*/
})

</script>