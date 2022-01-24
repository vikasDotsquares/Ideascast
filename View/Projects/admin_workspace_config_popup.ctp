

<?php echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'configureWorkspaces'), 'class' => 'form-horizontal form-bordered', 'id' => 'ProjectPop')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Configure Workspaces</h3>
                
            </div>
            <?php echo $this->Session->flash('auth'); ?>

            <?php //echo $this->Form->input('ProjectWorkspace.project_id', array('type' => 'hidden', 'value' => $projects['Project']['id']));
             
            echo $this->Form->input('ProjectWorkspace.project_id', array('type' => 'hidden', 'value' => $projectId));
            ?>
            <div class="modal-body">
			<h5 class="project-name">Project : <?php  echo $projects_title ?></h5>
			
			<div id="sortable-list-config"  >
                  <div class="row">
					  <?php $max_sort_val = (isset($max_sort) && !empty($max_sort)) ? $max_sort : 0; ?>
					   <?php
                    $workspacesList = $this->requestAction(array('controller' => 'Workspaces', 'action' => 'configureWorkspaces'), array('project_id' => $projectId));
					// pr($workspacesList, 1);
                    $stageHelper = array();
                    $checked = false;
					if(isset($workspacesList) && !empty($workspacesList)) {
                    foreach ($workspacesList as $key => $val) {
							echo $this->Form->hidden('pw_id.'.$key.'', ['value' => $val['ProjectWorkspace']['pwid']]); 
                        $ws_status = $val['ProjectWorkspace']['leftbar_status'];
                        $checked = ($ws_status > 0) ? true : false;
                        ?>
                          
							<div class="col-sm-6">
								
                        <label for="ProjectWorkspaceWorkspaceId<?php echo $key ?>" title="<?php echo $key; ?>"> <?php echo $this->Form->input("ProjectWorkspace.workspace_id.", array('id' => 'ProjectWorkspaceWorkspaceId' . $key, 'type' => 'checkbox',  'label' => false,  'class' => 'minimal', 'div' => false, 'hiddenField' => false, 'value' => $val['Workspace']['id'], 'checked' => $checked)); ?> 
                        <?php echo $val['Workspace']['title']; ?>
						</label>
					
						</div>
                     
                    <?php }} ?>
					   </div>
                    
                    </div>
			
                <input type="checkbox" style="display:none;" value="0" name="autoSubmit" id="autoSubmitConfig" checked="checked" />
                <?php //echo $this->Form->end(); ?>
            </div>

            <div class="modal-footer">
                 <button type="submit" class="btn btn-success">Save changes</button>
				 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               
            </div>
            <?php echo $this->Form->end(); ?>
        
     

<script type="text/javascript" >
    $('#modal_medium').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
	$('#modal_medium').modal({
		backdrop: true
	})
	$('#modal_medium').on('shown', function () {
		$("div.modal-backdrop").on('click', function() {
			$('#modal_medium').modal('hide');
		})
	});
	
	
// Submit Add Form 
      jQuery("#ProjectPop").submit(function (e) {
        var postData = jQuery(this).serializeArray();
        var formURL = jQuery(this).attr("action");

        jQuery.ajax({
            url: formURL, ///ideascomposer/projects/updateWorkplaceListForUser
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {
                    jQuery('#ProjectPop').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action
        //e.unbind(); //unbind. to stop multiple form submit.
    }); 




$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });

	
</script>