<style>
.templates_list {
	max-height: 345px;
	overflow-x: hidden;
	overflow-y: auto;
}
.templates_list li {
	list-style: outside none none;
}
.templates_list li .box {
	border-bottom: 1px solid #67a028;
	border-left: 1px solid #67a028;
	border-right: 1px solid #67a028;
	max-height: 155px;
	min-height: 155px;
}
.templates_list ul {
	padding: 0;
}
.templates_list ul > li > div.box {
	transition: background-color 0.5s ease 0s;
}
.templates_list ul > li > div.box:hover {
	background: rgba(105, 165, 142, 0.2) none repeat scroll 0 0;
}
.btn_select_workspace {
	margin: 25px 0 0 !important;
}
i.exceeded {
	background: #f39c12 none repeat scroll 0 0;
	border-radius: 50%;
	color: #fff;
	font-size: 13px;
	padding: 3px 7px;
	margin-right: 5px;
}
.headline_icon {
	float: none;
	font-size: 50px;
	font-weight: 300;
	text-align: center;
}
.stitle {
	font-size: 15px;
	margin: 10px 0;
	padding: 0 15px;
}
.wstitle {
	display: block;
	font-size: 20px;
	padding: 0 15px;
}
</style>

<?php if(isset($data) && !empty($data)){ ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Add Workspace</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body modal_body" >
		<input type="hidden" name="ProjectId" id="ProjectId" value="<?php echo $data['project_id']; ?>" />
		<input type="hidden" name="WorkspaceId" id="WorkspaceId" value="<?php echo $data['workspace_id']; ?>" />
		<?php 
		if( isset($workspace_id) && !empty($workspace_id) ) {
		?>
		<div class="wstitle list-title">Workspace: 	
		<?php  
			$ws = $this->Viewmodel->getWorkspaceDetail($workspace_id);
			echo ucfirst(htmlentities($ws['Workspace']['title']));
		?>
		</div>
		<?php 
		}
		$templates = get_templates(); 
		if(isset($templates) && !empty($templates)){
		?>
		<div class="stitle">Select Workspace Type:</div>
		
		<div class="templates_list" >
			<ul class="clearfix">
			
			<?php foreach($templates as $key => $val){ 
				$item = $val['Template'];
			?>
			
				<li class="col-sm-3">
					<div class="box box-success">
					
						<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
						<div class="box-body clearfix"> 
							<a title="Select" href="#" class="btn btn-jeera btn-sm select-btn btn_select_template" id="" data-id="<?php echo $item['id']; ?>"  > <i class="fa fa-check"></i> Select </a>
								<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>	
						</div>	
						
					</div>
				 
				</li>
			<?php } ?>
			
			</ul>
		</div>
		<?php }
			else {
		?>
		<div style="text-align:center">
			<h2 class="headline_icon text-yellow"> <span class="glyphicon glyphicon-th-large"></span> </h2>
			<!-- <i class="fa fa-exclamation exceeded"></i> -->
				Zones limit has exceeded. No template available for the available Zones.
		</div>
		<?php 
			}?>
	
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer"> 
		 <button type="button" id="close_modal_template" class="btn btn-danger" >Cancel</button>
	</div>
	
<?php  } ?>

<script type="text/javascript" >
$(function(){
	
	/*
	 * @access  public
	 * @todo  	Bind click event to assign template for a workspace
	 * @return  None
	 * */
	$('body').delegate('#close_modal_template', 'click', function(event) {
		event.preventDefault();
		
		var project_id = $('.modal_body').find('#ProjectId').val(),
			workspace_id = $('.modal_body').find('#WorkspaceId').val();
		
		BootstrapDialog.show({
			title: 'Delete confirmation',
			message: 'Are you sure you want to cancel?<br />No Workspace will be created.',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [{
				label: 'Yes',
				cssClass: 'btn-success',
				autospin: false,
				action: function (dialogRef) {
					$.when(
						$.ajax({
							url: $js_config.base_url + 'missions/delete_workspace',
							type: "POST",
							data: $.param({'project_id': project_id, 'workspace_id': workspace_id}),
							dataType: "JSON",
							global: false,
							success: function (response) {
								if(response.success) {
									var $selected_list = $('body').find('.idea-workspace-carousel li.selectable.selected');
									console.log($selected_list)
									if( $selected_list.length > 0) {
										var selected_workspace_id = $selected_list.data('id');
										location.href = $js_config.base_url + 'missions/index/project:' + project_id + '/workspace:' + selected_workspace_id
									}
									else {
										location.href = $js_config.base_url + 'missions/index/project:' + project_id
									}
								}
							}
						})
					).then(function( data, textStatus, jqXHR ) {
						dialogRef.close();
					})
					
				}
				}, {
					label: 'No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
		
		// $('#modal_box2').modal('hide')
		// $('#modal_box').show(); 
	})
	
	/*
	 * @access  public
	 * @todo  	Bind click event to assign template for a workspace
	 * @return  None
	 * */
	$('body').delegate('.btn_select_template', 'click', function(event) {
		event.preventDefault();
		var $that = $(this),
			project_id = $('.modal_body').find('#ProjectId').val(),
			workspace_id = $('.modal_body').find('#WorkspaceId').val(),
			template_id = $(this).data('id');
		
		$that.html('<i class="fa fa-spinner fa-pulse"></i>')
		setTimeout(function(){
			
			if( ($.isNumeric(template_id) && template_id > 0) && ($.isNumeric(workspace_id) && workspace_id > 0) ) {
				$.post( $js_config.base_url + 'missions/setting_templates', $.param({project_id: project_id, workspace_id: workspace_id, template_id: template_id}), function( data ){
					$('#modal_box2').modal('hide')
					$.template_selected = true; 
					// location.href = $js_config.base_url + 'missions/index/project:' + project_id + '/workspace:' + workspace_id
				});
			}
		}, 1000)
	})
	
})
</script>