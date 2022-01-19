 
<?php if( isset($workspace_detail) && !empty($workspace_detail) ) {
	// pr($workspace_detail);
	 
 ?>
	<div class="modal-header" style=" ">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel"><?php echo strip_tags($workspace_detail['Workspace']['title']); ?></h3>  
	</div>
	
	<div class="modal-body">
		<span style="display: inline-block; width: 100%">
			<?php echo $workspace_detail['Workspace']['description'] ?>
		</span>
	</div>	
	<div class="modal-footer" style="padding: 5px;">
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>
<?php } ?>

<script type="text/javascript" >
$(function(){
	
    $('#modal_medium').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
		$(this).find('.modal-header').removeAttr('style')
    });
	
})
</script>
	