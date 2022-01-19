<!-- EDIT Industry Classification -->
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Update Admin Profile</h4>
			</div>
			<?php echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
					<?php echo $this->Form->input('User.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => 'form-control')); ?>
					<?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">First Name:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					  </div>
					</div>
					
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Last Name:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					  </div>
					</div>
					
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Address:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('UserDetail.address', array('type' => 'textarea', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					  </div>
					</div>
					
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Email:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					  </div>
					</div>
					
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Password:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('User.password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'form-control','autocomplete'=>'off')); ?>
					  </div>
					</div>
					
					<div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Confirm Password:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'required' => false, 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					  </div>
					</div>
					
					  <div class="form-group">
					  <label for="UserClassification" class="col-lg-3 control-label">Profile Image:</label>
					  <div class="col-lg-9">
						<?php echo $this->Form->input('UserDetail.profile_pic', array('type' => 'file', 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'padding:0')); ?>
						
						
					  </div>
					</div>
					
				</div>
				<div class="modal-footer clearfix" style="background:none !important;border-top-color: #f4f4f4 !important;">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i> --> Cancel</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
// Submit Edit Form 
$("#RecordFormedit").submit(function(e){
	//var postData = $(this).serializeArray();
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#myprofile').html(response);
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


$(document).ready(function(){
	// All Common Functions Will listed here
	
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
</script>
<style>
.modal-footer {
    border-top: 1px solid #e5e5e5;
	background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important
    padding: 15px;
    text-align: right;
}
</style>