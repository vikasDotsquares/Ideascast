<style>
.label-1 {
	text-align: right;
    width: 12%;
    float: left;
	padding-right: 5px;
}
.chk-status {
	top: 6px;
    float: left;
    width: 12%;
    padding: 0 10px;
    padding-left: 23px;
}
.label-2 {
	top: 6px;
    width: 9%;
    float: left;
}
.chk-web-permit {
	top: 6px;
    width: 12%;
    float: left;
}
@media (max-width: 1023px) {
	.label-1 {
		width: 13.8%;
		padding-right: 0;
	}
	.label-2 {
		width: 27%;
	}
}
@media (min-width: 1024px) and (max-width: 1365px) {
	.label-1 {
		width: 8.8%;
		padding-right: 0;
	}
	.label-2 {
		width: 19%;
	}
}
@media (min-width: 1366px) and (max-width: 1600px) {
	.label-1 {
		width: 14.3%;
		padding-right: 0;
	}
	.label-2 {
		width: 12%;
	}
}
</style>
<!-- ADD NEW Industry User -->
	<div class="panel panel-success">
	  <div class="panel-heading bg-green">
         <h3 class="panel-title">
            Add API User
            <!--<a class="btn btn-warning btn-xs pull-right" href="<?php //echo SITEURL ?>app_users">Back</a>--></h3>
      </div>
		<div class="panel-body form-horizontal add-user-api">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('AppUser', array( 'url' => array('controller'=>'app_users', 'action'=>'add'),  'type' => 'file', 'class' => 'form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'User')); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">First Name:</label>
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
								<?php echo $this->Form->input('AppUser.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control','required' => true)); ?>
								
							  </div>
							  <div class="col-lg-1 col-sm-2 col-xs-2 padding0 text-right">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user first name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">Last Name:</label>
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
								<?php echo $this->Form->input('AppUser.last_name', array('type' => 'text','required' => true,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 col-sm-2 padding0 text-right">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter user last name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Username:</label>
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
								<?php echo $this->Form->input('AppUser.api_username', array('type' => 'text','required' => true , 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1 col-sm-2 padding0 text-right">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Min 6 and Max 10 chars"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Email:</label>
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
								<?php echo $this->Form->input('AppUser.api_email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1 col-sm-2 padding0 text-right">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter email"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
					
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Key:</label>
							  <div class="col-lg-7 col-sm-9 col-xs-8 user-api-col-two">
								<?php echo $this->Form->input('AppUser.api_key', array('type' => 'text', 'required' => true, 'label' => false, 'div' => false, 'class' => 'form-control','value'=>$api_key)); ?>
							  </div>
							  <div class="col-lg-2 col-sm-3 col-xs-4 user-api-col-btn padding0 text-right">
							  	<a tabindex="0" onclick="generateApiKey();" data-placement="top" class="btn btn-default " role="button"  data-trigger="hover" title="" data-content="Click for generate new API KEY"><i class="fa fa-refresh fa-3 martop"></i></a>
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="This is your API Key"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-12">	
							<div class="form-group">
							  <label for="UserUser" class="label-1">Status:</label>
							  <div class="chk-status" style="top:6px;">
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[AppUser][status]" checked  type="checkbox">
								
							  </div>
							  
							  <label for="UserUser" class="label-2">Web Permission:</label>
							  <div class="chk-web-permit" style="top:6px;">
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[AppUser][web_execution_permission]" checked  type="checkbox">
								
							  </div>
							</div>
							 
							
						</div>
					</div> 			
					 
					
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Create</button>
					<a class="btn btn-danger" href="<?php echo SITEURL ?>app_users">Cancel</a>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
$(document).ready(function(){
	
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
function generateApiKey() {
	$.ajax({
		url : '<?php echo Router::url('/',true)?>app_users/generate_keygen',
		type: "POST",
		success:function(response){	
			$("#AppUserApiKey").val(response.apikey);
		},
		dataType:'json',
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
}
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