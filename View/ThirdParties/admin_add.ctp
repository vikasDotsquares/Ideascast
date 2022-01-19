<!-- ADD NEW Industry User -->
	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Add Third Party User
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">				
			</div>
			<?php echo $this->Form->create('ThirdParty', array( 'url' => array('controller'=>'third_parties', 'action'=>'add'),  'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'User')); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Username:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.username', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>								
							  </div>							 					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Email:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.email', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user email(username)"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>	
					</div>
					
					<div class="row">						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Phone:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.phone', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>								
							  </div>							  					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="IndustryClassificationClassification" class="col-lg-3 control-label">Profile Image:</label>
								<div class="col-lg-8">
									<?php echo $this->Form->input('ThirdParty.profile_img', array('type' => 'file', 'accept'=>"image/x-png,image/gif,image/jpeg,image/jpg", 'label' => false, 'div' => false, 'class' => 'form-control', 'style'=>'height:auto' )); ?>
								</div>									
							</div>
						</div>
					</div>		
					
					<div class="row">						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Address:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.address', array('type' => 'textarea','rows' => 2,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>							 					  
							</div>								
						</div>
						<div class="col-md-6">							
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Status:</label>
							  <div class="col-lg-8">
									<?php $arr_list = array( '1' => 'Active','0' =>'Inactive', ); ?>
									<?php  echo $this->Form->input('ThirdParty.status', array('label'=>false, 'options' =>$arr_list,'class'=>"field form-control")); ?>
							  </div>
							</div>
						</div>
					</div>
					
					<div class="row">						
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact 1:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.contact1', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>							 					  
							</div>								
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact 2:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.contact2', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>							 					  
							</div>								
						</div>
						
					</div>
					
					<div class="row">

						<div class="col-md-6">							
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Website:</label>
							  <div class="col-lg-8">
									<?php echo $this->Form->input('ThirdParty.website', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>	
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Summary:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('ThirdParty.summary', array('type' => 'textarea','rows' => 2,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>							 					  
							</div>								
						</div>
						
					</div>
					
					
					
					<div class="modal-footer clearfix">
						<button type="submit" class="btn btn-success"> Create</button>
						<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/third_parties">Cancel</a>
					</div>
					
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
$(document).ready(function(){

	$('#ThirdPartyPhone').keypress(function (event) {
	    var keycode = event.which;
	    if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
	        event.preventDefault();
	    }
	});
	$('#ThirdPartyPhone').on('paste', function (event) {
  if (event.originalEvent.clipboardData.getData('Text').match(/[^\d]/)) {
    event.preventDefault();
  }
});

	    $("#ThirdPartyUsername").keypress(function(event) {
	    var character = String.fromCharCode(event.keyCode);
	    return isValid(character);     
	});

	function isValid(str) {
	    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
	}

	$("#ThirdPartyUsername").bind('paste', function(e) {
	    var character = e.originalEvent.clipboardData.getData('Text');
	       return isValid(character); 
	});

	
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
// Submit Add Form 
$("#Users").submit(function(e){
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");
	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#RecordAdd').html(response);
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
</script>