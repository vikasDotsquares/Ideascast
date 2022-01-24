<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>

			<?php echo $this->Form->create('Skill', array('class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php if( isset($this->request->data['Skill']['id']) && !empty($this->request->data['Skill']['id']) ){?>Edit<?php } else {?>Add<?php }?> Skill</h4>
			</div>
			<?php echo $this->Session->flash(); ?>
				<div class="modal-body clearfix">
				<?php
					echo $this->Form->input('Skill.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control'));

					if( isset($this->request->data['Skill']['id']) && !empty($this->request->data['Skill']['id']) ){
						echo $this->Form->input('actionType', array('type' => 'hidden','value' => 'editAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					} else {
						echo $this->Form->input('actionType', array('type' => 'hidden', 'value' => 'addAction', 'label' => false, 'div' => false, 'class' => 'form-control'));
					}
				?>

					    <div class="col-md-12">
							<div class="form-group">
							  <label for="UserUser" class=" control-label">Skill:</label>
								<?php echo $this->Form->input('Skill.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							</div>
						</div>

				</div>
				<div class="modal-footer ">
					<button type="submit" id="skill_submit"  class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
				</div>
			</form>

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

});
</script>