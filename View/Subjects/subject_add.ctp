<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>
<!-- EDIT Industry User -->
	<div class="modal-dialog fullwidth">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Skill</h4>
			</div>
			<?php echo $this->Form->create('Skill', array('class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
					<div class="row">
					    <div class="col-md-12">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Skill:</label>
							  <div class="col-lg-6">						
								<?php echo $this->Form->input('Skill.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-3">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter Skill title"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>						
						</div>
					</div>				
				<div class="modal-footer clearfix">
					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>					
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
// Submit Edit Form 
$("#RecordFormedit").submit(function(e){
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#Recordedit').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		},
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
	e.preventDefault(); //STOP default action
	//e.unbind(); //unbind. to stop multiple form submit.
});
$('#RecordAdd').on('hidden.bs.modal', function(){
	$(this).removeData('bs.modal')
	$(this).find('.modal-content').html('')
})


$(document).ready(function(){

	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
</script>