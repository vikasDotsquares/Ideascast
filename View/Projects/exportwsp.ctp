
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<style>
.popup_label {
	font-weight: normal;
	margin-bottom: 10px !important;
}
#tick_on_off {
	margin: 0px !important;
	font-size: 12px;
}
#tick_on_off .tick_default, 
#tick_on_off .tick_on, 
#tick_on_off .tick_off {
	color: #FFFFFF;
	padding: 0 3px;
	font-size: inherit;
}
#tick_on_off .tick_default {
	padding: 0 3px !important;
	margin: 0 !important;
	background-color: #EEEEEE;
	border-color: #D7D7D7;
	color: #222222;
} 
#tick_on_off .tick_on {
	background-color: #449d44;
	border-color: #398439;
}
#tick_on_off .tick_off {
	background-color: #c9302c;
	border-color: #ac2925; 
}
#tick_on_off .tick_on.active, .tick_off.active {
	margin: 0 !important;
	cursor: default !important;
}
#sortable-list-config {
	padding: 10px 0 0;
}
	
</style>
<?php if( isset($project_workspaces) && !empty($project_workspaces) ) {
	 // pr($user_project, 1);
	$project = $user_project['Project'];
 ?>

<?php 
if(isset($this->request->params['pass'][1]) && $this->request->params['pass'][1]=='doc'){
$action = 'savedoc';
}else if(isset($this->request->params['pass'][1]) && $this->request->params['pass'][1]=='ppt'){
$action = 'saveppt';
}
else{
$action = 'savepdf';
}

echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => $action,0,$project['title']), 'class' => 'form-horizontal form-bordered', 'id' => 'ProjectPop')); ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Select Workspaces for export</h3> 
	</div>
<?php //echo $this->Session->flash('auth'); ?>
 
	<div class="modal-body">
		<h5 class="project-name">Project : <?php  echo $project['title'] ?>
			<p class="text-muted date-time">Set which workspaces you want to export</p>
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
								//	'checked' => $checked
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
		 <button type="submit" class="btn btn-success">Save changes</button>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	   
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
      //  console.log(postData);
	  
		$('#modal_medium').modal('hide');
		
/*         jQuery.ajax({
            url: formURL, 
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {
                  
					// location.reload();
				//	window.location.href = SITEURL+'/pdfs/'+'<?php echo $project['title']; ?>'+'.pdf';		
                } else {
                   
					//window.location.href = SITEURL+'/pdfs/test.pdf';
					location.reload();
					
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        }); */
      
	  //  e.preventDefault(); //STOP default action
	  
	  
        //e.unbind(); //unbind. to stop multiple form submit.
    }); 




$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });

	
	
</script>