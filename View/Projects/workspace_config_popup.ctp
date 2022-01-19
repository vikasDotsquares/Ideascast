
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>

<?php if( isset($project_workspaces) && !empty($project_workspaces) ) {
	// $project = $this->params['pass'][0];
	$project = $user_project['Project'];
 ?>

<?php echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'configureWorkspaces'), 'class' => 'form-horizontal form-bordered', 'id' => 'ProjectPop')); ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Configure Workspaces</h3> 
	</div> 
	<div class="modal-body">
		<h5 class="project-name">Project : <?php  echo $project['title'] ?>
			<p class="text-muted date-time">Set which workspaces are seen in the summary</p>
		</h5>

		<div id="sortable-list-config"  >
			<div class="row">
				
					
			<?php
			
			echo $this->Form->input('Project.id', array('type' => 'hidden', 'value' => $project['id'] ));
			
				foreach ($project_workspaces as $key => $val) {
					echo $this->Form->hidden('ProjectWorkspace.'.$key.'.id', ['value' => $val['ProjectWorkspace']['id']]); 
						
					$checked = ($val['ProjectWorkspace']['leftbar_status'] > 0) ? true : false;
					
					echo '<div class="col-sm-6">';
					echo '<label for="ProjectWorkspaceWorkspaceId' . $key . '" class="popup_label tipText"> ';
					echo $this->Form->input("ProjectWorkspace.".$key.".workspace_id", 
								array(
									'id' => 'ProjectWorkspaceWorkspaceId' . $key, 
									'type' => 'checkbox',
									'label' => false,
									'class' => 'checkbox_on_off',
									'div' => false,
									'hiddenField' => false,
									'value' => $val['Workspace']['id'],
									'checked' => $checked
								));	
					// echo $val['Workspace']['title'];		
					echo '&nbsp;&nbsp; ' . $val['Workspace']['title'] . '</label>';
					echo '</div>';
				}
			?>
			</div>
			
		</div>

		<input type="checkbox" style="display:none;" value="0" name="autoSubmit" id="autoSubmitConfig" checked="checked" />
		
	</div>

	<div class="modal-footer">
		 <button type="submit" class="btn btn-success"> Save </button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	   
	</div>
<?php echo $this->Form->end();
}
 ?>
        
     

<script type="text/javascript" > 
	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
	})
	
	$('label.tipText').tooltip({ container: 'body', placement: 'auto'});
	
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