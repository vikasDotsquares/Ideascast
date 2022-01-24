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
            API Details
      </div>
		<div class="panel-body form-horizontal add-user-api">
				<div class="modal-body">
					<div class="row apitype">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="api_type" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Type</label>							  
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
									<select class="form-control aqua" name="api_type" id="api_type">
										<option value="">Select a API Type</option>
										
										<option value="project">Project</option>
										<option value="workspace">Workspace</option>
										<option value="element">Element</option>
										<option value="todo">ToDo</option>
										<option value="user">User</option>
										
									</select>
							  </div>							 					  
							</div>
						</div>
					</div>
					
					<div class="row apitype">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="api_type" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Project</label>							  
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
									<select class="form-control aqua" name="api_type" id="api_type">
										<option value="">Select Project</option>
										
										<option value="project">Project 1</option>
										<option value="workspace">Project 1</option>
										<option value="element">Project 1</option>
										<option value="todo">Project 1</option>
										<option value="user">Project 1</option>
										
									</select>
							  </div>							 					  
							</div>
						</div>
					</div>
					
					<div class="row apitype">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="api_type" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Workspace</label>							  
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
									<select class="form-control aqua" name="api_type" id="api_type">
										<option value="">Select Workspace</option>
										
										<option value="project">Workspace 1</option>
										<option value="workspace">Workspace 1</option>
										<option value="element">Workspace 1</option>
										<option value="todo">Workspace 1</option>
										<option value="user">Workspace 1</option>
										
									</select>
							  </div>							 					  
							</div>
						</div>
					</div>
					
					<div class="row apitype">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="api_type" class="col-lg-3 col-sm-12 col-xs-12 control-label user-api-label">API Element</label>							  
							  <div class="col-lg-8 col-sm-10 col-xs-10 user-api-col">
									<select class="form-control aqua" name="api_type" id="api_type">
										<option value="">Select Element</option>
										
										<option value="project">Element 1</option>
										<option value="workspace">Element 1</option>
										<option value="element">Element 1</option>
										<option value="todo">Element 1</option>
										<option value="user">Element 1</option>
										
									</select>
							  </div>							 					  
							</div>
						</div>
					</div>
					
					 
					
				<div class="modal-footer clearfix"></div>
			 
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