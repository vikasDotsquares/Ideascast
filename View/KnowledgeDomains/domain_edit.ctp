<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>
<!-- EDIT Industry User -->
	<div class="modal-dialog fullwidth">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php if( isset($this->request->data['KnowledgeDomain']['id']) && !empty($this->request->data['KnowledgeDomain']['id']) ){?>Edit<?php } else {?>Add<?php }?> Domain</h4>
			</div>
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->Form->create('KnowledgeDomain', array('class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
				<?php 
					echo $this->Form->input('KnowledgeDomain.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control'));
					
					if( isset($this->request->data['KnowledgeDomain']['id']) && !empty($this->request->data['KnowledgeDomain']['id']) ){
						echo $this->Form->input('actionType', array('type' => 'hidden','value' => 'editAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					} else {	 		
						echo $this->Form->input('actionType', array('type' => 'hidden', 'value' => 'addAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					}	
				?>
					
					<div class="row">
					    <div class="col-md-12">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Domain:</label>
							  <div class="col-lg-6">						
								<?php echo $this->Form->input('KnowledgeDomain.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-3">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter KnowledgeDomain title"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>						
						</div>
					</div>				
				<div class="modal-footer clearfix">
					<button type="submit" id="skill_submit"  class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>					
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
// Submit Edit Form 
$("#RecordFormedit").submit(function(e){
	
	$("#skill_submit").addClass('disabled');
	
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
					$("#skill_submit").removeClass('disabled');
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


$(document).ready(function(){

	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
</script>