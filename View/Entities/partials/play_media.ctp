

<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 3px 0 0;"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Media Player</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body"> 
	<div  id="embed_frame" class="video-container" style=" " > 
		<?php echo ($data['ElementLink']['embed_code']); ?>
	</div>

</div>
		
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer"> 
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	
<script type="text/javascript" >
$(function(){
	$('#modal_medium').on('hidden.bs.modal', function () { 
			if( $(this).data('bs.modal') )
				$(this).removeData('bs.modal'); 
		});
})
</script>