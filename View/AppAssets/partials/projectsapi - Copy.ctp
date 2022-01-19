<?php
if( isset($projectdata) && !empty($projectdata) ){
?>
<div class="panel-group panel-user-api" id="accordion">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-anchor">
						<span class="program-title" data-pid="13"><i class="fa fa-briefcase"></i> <strong><?php echo strip_tags($projectdata['Project']['title']);?></strong></span>
						<div class="right-options">
							<span class="projectid-api"><input class="form-control" type="text" value="Project Id = <?php echo $projectdata['Project']['id'];?>"></span>
							<span class="show-hide-program btn btn-xs btn-white tipText" title="" data-toggle="collapse" data-parent="#accordion" data-accordionid="collapse1" href="#collapse1" aria-expanded="true" data-original-title="Expand">
								<i class="fa"></i>
							</span>
						</div>
					 </a>
				</h4>
			</div>
			<div id="collapse1" class="panel-collapse collapse in">
				<div class="panel-body">
					<div class="api-tyle-select-one">
					<label class="custom-dropdown" style="width:100%;">
						<select class="form-control aqua" name="api_type" id="api_type">
							<option value="">Select Type</option> 
							<option value="project">Project API</option>
							<option value="workspace">Workspace API</option>
							<option value="element">Element API</option>
							<option value="risk">Risk API</option>
							<option value="cost">Cost API</option>
							<option value="user">User API</option>
							<option value="todo">ToDo API</option>
						</select>
					</label>
				</div>
				<!-- Section for Workspace API -->
				<div class="workspaceapi" id="workspace_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">					 
							<select class="form-control aqua" name="api_workspace" id="api_workspace">
								<option value="">Select Workspace</option>
								<?php 
								$projectwsp = $this->ViewModel->getProjectWorkspaces($projectdata['Project']['id'], 0);
								if( isset($projectwsp) && !empty($projectwsp) ){
									foreach($projectwsp as $key => $myProjectwsp){
										$wsp =$myProjectwsp['Workspace'];
								?>
								<option value="<?php echo $wsp['id'];?>"><?php echo $wsp['title']; ?></option>
								<?php }
								}
								?>
							</select>
						</label>
					</div>
				</div>
				<!-- Section for Element API -->
				<div class="elementapi" id="element_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">					 
							<select class="form-control aqua" name="api_workspace" id="api_workspace">
								<option value="">Select Workspace</option>
								<?php 
								$projectwsp = $this->ViewModel->getProjectWorkspaces($projectdata['Project']['id'], 0);
								if( isset($projectwsp) && !empty($projectwsp) ){
									foreach($projectwsp as $key => $myProjectwsp){
										$wsp =$myProjectwsp['Workspace'];
								?>
								<option value="<?php echo $wsp['id'];?>"><?php echo $wsp['title']; ?></option>
								<?php }
								}
								?>
							</select>
						</label>
					</div>
				</div>
				
				<div class="riskapi" id="risk_api" >
					Risk api
				</div>
				<div class="costapi" id="cost_api" >
					cost api
				</div>
				<div class="userapi" id="user_api" >
					User api
				</div>
				<div class="todoapi" id="todo_api" >
					Todo api
				</div>
				
				<div class="projectapi" id="project_api" >
					
				</div>
				
				
				<?php /* <div class="api-tyle-select-two">
					<label class="custom-dropdown" style="width:100%;">					 
						<select class="form-control aqua" name="api_workspace" id="api_workspace">
							<option value="">Select Workspace</option>
							<?php 
							$projectwsp = $this->ViewModel->getProjectWorkspaces($projectdata['Project']['id'], 0);
							if( isset($projectwsp) && !empty($projectwsp) ){
								foreach($projectwsp as $key => $myProjectwsp){
									$wsp =$myProjectwsp['Workspace'];
							?>
							<option value="<?php echo $wsp['id'];?>"><?php echo $wsp['title']; ?></option>
							<?php }
							}
							?>
						</select>
					</label>
				</div> */ ?>
				
				<!-- <div class="api-tyle-select-two">
					<label class="custom-dropdown" style="width:100%;">
						<select class="form-control aqua" name="user_projects" id="owner_user_projects">
							<option value="">Select Area in Workspace</option>							 
						</select>
					</label>
				</div>
				<div class="api-tyle-select-two">
					<label class="custom-dropdown" style="width:100%;">
						<select class="form-control aqua" name="user_projects" id="owner_user_projects">
							<option value="">Select Element</option>
						</select>
					</label>
				</div> -->
				<div class="api-select-deta">
					<table class="table table-bordered">
					  <thead>
						<tr>						
						  <th>Name</th>
						  <th>ID</th>
						</tr>
					  </thead>
					  <tbody>
						<tr>						  
						  <td id="projectname">Project Name: <?php echo strip_tags($projectdata['Project']['title']);?></td>
						  <td id="projectid">Project ID: <?php echo $projectdata['Project']['id'];?></td>
						</tr>
						<tr>						 
						  <td id="wspname">Workspace Name:</td>
						  <td id="wspid">Workspace ID:</td>
						</tr>
					  </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>              
</div>
<?php } ?>
<script>
$(function() {
	
	$("body").on("change", "#api_workspace", function(){
			 
		var wspid = $(this).val();	
		var wspname = $("#api_workspace option:selected").text();	
		$("#wspname").html("Workspace Name: "+wspname);
		$("#wspid").html("Workspace ID: "+wspid);
			
	})
	
	$("body").on("change", "#api_type", function(){
			 
		var apitype = $(this).val();	
		console.log(apitype);
		if( apitype == 'workspace' ){
			
			$("#workspace_api").show();
			$("#element_api").hide();
			$("#project_api").hide();
			$("#risk_api").hide();
			$("#cost_api").hide();
			$("#user_api").hide();
			$("#todo_api").hide();
			
		} else if( apitype == 'element' ){
			
			$("#workspace_api").hide();
			$("#element_api").show();
			$("#project_api").hide();
			$("#risk_api").hide();
			$("#cost_api").hide();
			$("#user_api").hide();
			$("#todo_api").hide();
			
		} else {
			$("#project_api").show();
			$("#workspace_api").hide();
			$("#element_api").hide();
			$("#risk_api").hide();
			$("#cost_api").hide();
			$("#user_api").hide();
			$("#todo_api").hide();
		}	
		
		
		/* var wspname = $("#api_workspace option:selected").text();	
		$("#wspname").html("Workspace Name: "+wspname);
		$("#wspid").html("Workspace ID: "+wspid); */
			
	})
	
});
</script>
