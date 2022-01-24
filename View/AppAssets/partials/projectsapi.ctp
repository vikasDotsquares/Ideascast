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
							<span class="projectid-api"><label class="form-control" type="text">Project Id = <?php echo $projectdata['Project']['id'];?></label>
							<span id="accordianshowhide" class="show-hide-program btn btn-xs btn-white" title="Collapse" data-toggle="collapse" data-parent="#accordion" data-accordionid="collapse1" href="#collapse1" aria-expanded="true">
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
							<option value="project">Project API (Export)</option>
							<option value="workspace">Workspace API (Export)</option>
							<option value="element">Task API (Export)</option>
							<option value="risk">Risk API (Export)</option>
							<option value="cost">Cost API (Export)</option>
							<option value="user">User API (Export)</option>
							<option value="todo">To-do API (Export)</option>
							<option value="ov_reward_count">Rewards (Export)</option>
							<option value="create_risk">Create Risk API (Import)</option>
							<option value="create_element">Create Task API (Import)</option>
							<option value="update_element">Update Task API (Import)</option>
							
							<option value="create_todo">Create To-do API (Import)</option>
							<option value="update_todo">Update To-do API (Import)</option>
							
							<!-- <option value="ov_reward_count">OV Reward Available count (Export)</option>
							<option value="ov_reward_redeemed">OV Reward Redeemed (Export)</option>
							<option value="ov_charity">OV Charity (Export)</option> -->
							
							
						</select>
					</label>
				</div>
				<!-- Section for Workspace API -->
				<div class="workspaceapi" id="workspace_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="api_workspace" id="api_workspace">
								<option value="">Select Workspace</option>
							</select>
						</label>
					</div>
				</div>
				<!-- Section for Element API -->
				<div class="elementapi" id="element_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="ele_api_workspace" id="ele_api_workspace">
								<option value="">Select Workspace</option>								
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="element_api_elelist" >
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="api_elements" id="api_elements" multiple=""></select>
						</label>
					</div>
				</div>

				<!-- Section for Create Element API -->
				<div class="create_elementapi" id="create_element_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="create_ele_api_workspace" id="create_ele_api_workspace">
								<option value="">Select Workspace</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="create_element_api_arealist">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="wsp_area_ele" id="wsp_area_ele">
								<option value="">Select Area</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="create_element_api_elelist">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="create_api_elements" id="create_api_elements">
								<option value="">Select Task</option>
							</select>
							<span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span>
							<a data-content="Required for importing, Links, Notes and Documents" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title="" style="position: absolute; right: -44px; top: 0;"><i class="fa fa-info fa-3 martop"></i></a>
						</label>
					</div>
				</div>
				
				<!-- Section for Update Element API -->
				<div class="update_elementapi" id="update_element_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="update_ele_api_workspace" id="update_ele_api_workspace">
								<option value="">Select Workspace</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="update_element_api_arealist">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="wsp_area_update_ele" id="wsp_area_update_ele">
								<option value="">Select Area</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="update_element_api_elelist">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="update_api_elements" id="update_api_elements">
								<option value="">Select Task</option>
							</select>
							<!-- <span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span> 
							<a data-content="Required for importing, Links, Notes and Documents" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title="" style="position: absolute; right: -44px; top: 0;"><i class="fa fa-info fa-3 martop"></i></a>-->
						</label>
					</div>
					<div class="api-tyle-select-two" id="update_element_api_type" style="display:none;">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="update_ele_type" id="update_ele_type">
								<option value="">Select Type</option>
								<option value="notes">Notes</option>
								<option value="links">Links</option>
								<option value="docs">Documents</option>
							</select>
						</label>
					</div>
					
					<div class="api-tyle-select-two" id="update_element_api_type_list" style="display:none;">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="update_ele_type_list" id="update_ele_type_list">
								<option value="">Select Type</option>
							</select>
						</label>
					</div>
					
				</div>
				

				<div class="riskapi" id="risk_api" >
					
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="projectriskapiusers" id="project_risk_api_users">
								<option value="">Select User</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="projectriskapilist" id="project_risk_api_list" multiple=""></select>
						</label>
					</div>
				</div>
				<div class="createriskapi" id="create_risk_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">
							<select class="form-control aqua" name="createriskuserapi" id="create_risk_api_users">
								<option value="">Select User</option>
							</select>
						</label>
					</div>
					<div class="api-tyle-select-two" id="create_risk_api_type">
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua disabled" name="projectrisktype" id="project_risk_type" disabled>
								<option value="">Select Type</option>								
							</select>
						</label>
					</div>
					
					<div class="api-tyle-select-two" id="create_risk_api_elelist">
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="projectriskelement" id="project_risk_element" disabled>
								<option value="">Select Task</option>								
							</select>
							<span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span>
							<a data-content="Optional, if you want to relate risk to a Task." title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title="" style="position: absolute; right: -44px; top: 0;"><i class="fa fa-info fa-3 martop"></i></a>
						</label>
					</div>
					
				</div>
				<div class="costapi" id="cost_api" >
					cost api
				</div>
				<div class="userapi" id="user_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">						 
							<select class="form-control aqua" name="userapi" id="api_users">
								<option value="">Select User</option>								
							</select>
						</label>
					</div>
				</div>
				<div class="todoapi" id="todo_api" >
					<div class="api-tyle-select-two" id="project_todo_api_users_list">
						<label class="custom-dropdown" style="width:100%;">				
							<select class="form-control aqua" name="projecttodouserapi" id="project_todo_api_users">
								<option value="">Select User</option>
							</select>							
						</label>
					</div>
					<div class="api-tyle-select-two" id="project_todo_api_list_show" >
						<label class="custom-dropdown aqua" style="width:100%;">						
							<select class="form-control aqua" name="projecttodoapilist" id="list_project_todo_api" multiple=""></select>							
						</label>
					</div>
				</div>
				
				<div class="createtodoapi" id="create_todo_api" >
					<div class="api-tyle-select-two" id="todo_api_users_list">
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="todouserapi" id="todo_api_users">
								<option value="">Select User</option>
							</select>
						</label>
					</div>					
					<div class="api-tyle-select-two" id="project_todo_api_list" >
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="projecttodoapi" id="project_todo_api">
								<option value="">Select To-do</option>								
							</select>
							<a data-content="Required for importing, Sub To-do" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title="" style="position: absolute; right: -44px; top: 0;"><i class="fa fa-info fa-3 martop"></i></a>
						</label>
					</div>					
				</div>
				
				<div class="updatetodoapi" id="update_todo_api" >
					<div class="api-tyle-select-two" id="todo_update_api_users_list">
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="todoupdateuserapi" id="todo_update_api_users">
								<option value="">Select User</option>
							</select>
						</label>
					</div>					
					<div class="api-tyle-select-two" id="project_todo_update_api_list" >
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="projectupdatetodoapi" id="project_todo_update_api">
								<option value="">Select To-do</option>								
							</select>							
						</label>
					</div>
					<div class="api-tyle-select-two" id="project_subtodo_update_api_list" >
						<label class="custom-dropdown" style="width:100%;">						
							<select class="form-control aqua" name="projectupdatesubtodoapi" id="project_subtodo_update_api">
								<option value="">Select Sub To-do</option>								
							</select>							
						</label>
					</div>		
				</div>
				
				<div class="projectapi" id="project_api"></div>
				
				<div class="ovrewardcountapi" id="ov_reward_count_api" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">						 
							<select class="form-control aqua" name="ovrewarduserapi" id="api_ovrewardusercnt" multiple=""></select>
						</label>
					</div>
					<div class="api-tyle-select-two" style="padding-top: 7px;"><span><input type="checkbox" name="allusers" ></span> <span >From All Projects</span></div>
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">						 
							<select class="form-control aqua" name="ovcharityuser" id="api_ov_charity">
								<option value="">Select Charity</option>								
							</select>
							<a data-content="This is optional for Charity Export only" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title="" style="position: absolute; right: -44px; top: 0;"><i class="fa fa-info fa-3 martop"></i></a>
						</label>
					</div>
					
				</div>
				
				<!-- <div class="ovrewardredeemedapi" id="ov_reward_redeemed_api"  style="display:none;" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">						 
							<select class="form-control aqua" name="ovredeemeduserapi" id="api_ovredeemeduser" multiple="" ></select>
						</label>
					</div>
				</div>
				
				<div class="ovcharityapi" id="ov_charity_api" style="display:none;" >
					<div class="api-tyle-select-two">
						<label class="custom-dropdown" style="width:100%;">						 
							<select class="form-control aqua" name="ovcharityuser" id="api_ov_charity_old">
								<option value="">Select Charity</option>								
							</select>
						</label>
					</div>
				</div>-->
				
				

				<div class="api-select-deta table-responsive" style="clear:both;">
					<table class="table table-bordered projectdetail">
					  <thead>
						<tr>
						  <th width="70%">Name</th>
						  <th width="30%">ID</th>
						</tr>
					  </thead>
					  <tbody>
						<tr class="projectDetailrow">
						  <td id="projectname">Project Name: <?php echo strip_tags($projectdata['Project']['title']);?></td>
						  <td id="projectid">project_id : <?php echo $projectdata['Project']['id'];?></td>
						</tr>						
					  </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<script type="text/javascript">
	$(function(){ 
			$(".projectDetailrow").show();
			$('#accordianshowhide').tooltip({
		        container: 'body',
		        placement: 'top',
		        trigger: 'hover'
		    })
			
			$(".toltipover").popover({
				container: 'body',
		        placement: 'top',
		        trigger: 'hover'
			})

	    	$('#list_project_todo_api').multiselect({
	            enableUserIcon: false,
	            enableFiltering: true,
				includeSelectAllOption: true,
				numberDisplayed: 2,
				nonSelectedText: 'Select To-do',
	            filterPlaceholder: 'Search To-do',
	            enableCaseInsensitiveFiltering: true,
	            buttonWidth: '100%',
	            onDropdownHidden: function(){
            		var brands = $('#list_project_todo_api option:selected');
			        var selected = [];
			        if(brands.length > 0) {
				        dynamicHtml = '<tr class="todoapitr"><td>To-do Name: <ol>';
				        $(brands).each(function(index, brand){
				            dynamicHtml += '<li>'+$(this).text()+'</li>';
				        });
				        dynamicHtml += '</ol></td><td>parent_id: ';
				        $(brands).each(function(index, brand){
				            selected.push([$(this).val()]);
				        });

				        dynamicHtml += selected.join(',');

				        dynamicHtml += '</td></tr>';
				
				        if($(".projectdetail tbody .todoapitr").length > 0) {
							$(".projectdetail tbody .todoapitr").remove();
						}
						
						$(".projectdetail tbody").append(dynamicHtml); 
				        //console.log("selected", $(".projectdetail tbody"));
				    }
				    else{
				    	$(".projectdetail tbody .todoapitr").remove();
				    }
	            }
	        })
			
			$('#project_risk_api_list').multiselect({
	            enableUserIcon: false,
	            enableFiltering: true,
				includeSelectAllOption: true,
				nonSelectedText: 'Select Risk',
				numberDisplayed: 2,
	            filterPlaceholder: 'Search Risk',
	            enableCaseInsensitiveFiltering: true,
	            buttonWidth: '100%',
	            onDropdownHidden: function(){
            		var brands = $('#project_risk_api_list option:selected');
			        var selected = [];
			        if(brands.length > 0) {
				        dynamicHtml = '<tr class="projectriskapitr"><td>Risk Name: <ol>';
				        $(brands).each(function(index, brand){
				            dynamicHtml += '<li>'+$(this).text()+'</li>';
				        });
				        dynamicHtml += '</ol></td><td>Risk ID: ';
				        $(brands).each(function(index, brand){
				            selected.push([$(this).val()]);
				        });

				        dynamicHtml += selected.join(',');

				        dynamicHtml += '</td></tr>';
				
				        if($(".projectdetail tbody .projectriskapitr").length > 0) {
							$(".projectdetail tbody .projectriskapitr").remove();
						}
						
						$(".projectdetail tbody").append(dynamicHtml); 
				        //console.log("selected", $(".projectdetail tbody"));
				    }
				    else{
				    	$(".projectdetail tbody .projectriskapitr").remove();
				    }
	            }
	        })
			
			
			$('#api_ovrewardusercnt').multiselect({
	            enableUserIcon: false,
	            enableFiltering: true,
				includeSelectAllOption: true,
				nonSelectedText: 'Select User',
				numberDisplayed: 2,
	            filterPlaceholder: 'Search User',
	            enableCaseInsensitiveFiltering: false,
	            buttonWidth: '100%',
	            onDropdownHidden: function(){
            		var brands = $('#api_ovrewardusercnt option:selected');
			        var selected = [];
			        if(brands.length > 0) {
						
				        dynamicHtml = '<tr class="projectrewarduserapitr"><td>User Name: <ol>';
				        $(brands).each(function(index, brand){
				            dynamicHtml += '<li>'+$(this).text()+'</li>';
				        });
				        dynamicHtml += '</ol></td><td>user_id : ';
				        $(brands).each(function(index, brand){
				            selected.push([$(this).val()]);
				        });

				        dynamicHtml += selected.join(',');

				        dynamicHtml += '</td></tr>';
				
				        if($(".projectdetail tbody .projectrewarduserapitr").length > 0) {
							$(".projectdetail tbody .projectrewarduserapitr").remove();
						}
						
						$(".projectdetail tbody").append(dynamicHtml);				        
				    }
				    else{
				    	$(".projectdetail tbody .projectrewarduserapitr").remove();
				    }
	            }
	        })
			
			$('#api_ovredeemeduser').multiselect({
	            enableUserIcon: false,
	            enableFiltering: true,
				includeSelectAllOption: true,
				nonSelectedText: 'Select User',
				numberDisplayed: 2,
	            filterPlaceholder: 'Search User',
	            enableCaseInsensitiveFiltering: false,
	            buttonWidth: '100%',
	            onDropdownHidden: function(){
            		var brands = $('#api_ovredeemeduser option:selected');
			        var selected = [];
			        if(brands.length > 0) {
						
				        dynamicHtml = '<tr class="projectrewarduserapitr"><td>User Name: <ol>';
				        $(brands).each(function(index, brand){
				            dynamicHtml += '<li>'+$(this).text()+'</li>';
				        });
				        dynamicHtml += '</ol></td><td>User ID: ';
				        $(brands).each(function(index, brand){
				            selected.push([$(this).val()]);
				        });

				        dynamicHtml += selected.join(',');

				        dynamicHtml += '</td></tr>';
				
				        if($(".projectdetail tbody .projectrewarduserapitr").length > 0) {
							$(".projectdetail tbody .projectrewarduserapitr").remove();
						}
						
						$(".projectdetail tbody").append(dynamicHtml);				        
				    }
				    else{
				    	$(".projectdetail tbody .projectrewarduserapitr").remove();
				    }
	            }
	        })
			
			
			$('#api_elements').multiselect({
	            enableUserIcon: false,
	            enableFiltering: true,
				includeSelectAllOption: true,
				nonSelectedText: 'Select Task',
				numberDisplayed: 2,
	            filterPlaceholder: 'Search Task',
	            enableCaseInsensitiveFiltering: false,
	            buttonWidth: '100%',
	            onDropdownHidden: function(){
					console.log(this)
            		var brands = $('#api_elements option:selected');
			        var selected = [];
			        if(brands.length > 0) {
						
				        dynamicHtml = '<tr class="elementstr"><td>Task Name: <ol>';
				        $(brands).each(function(index, brand){
				            dynamicHtml += '<li>'+$(this).text()+'</li>';
				        });
				        dynamicHtml += '</ol></td><td>element_id : ';
				        $(brands).each(function(index, brand){
				            selected.push([$(this).val()]);
				        });

				        dynamicHtml += selected.join(',');

				        dynamicHtml += '</td></tr>';
				
				        if($(".projectdetail tbody .elementstr").length > 0) {
							$(".projectdetail tbody .elementstr").remove();
						}
						
						$(".projectdetail tbody").append(dynamicHtml);				        
				    }
				    else{
				    	$(".projectdetail tbody .elementstr").remove();
				    }
	            }
	        })
			
		
	})
</script>