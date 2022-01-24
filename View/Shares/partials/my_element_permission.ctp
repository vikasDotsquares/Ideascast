<?php // USED TO SHOW POPUP MODAL BOX TO SHOW ALL THE PERMISSIONS THAT ARE GIVEN TO THE CURRENT USER. ?>
<?php // Used in Projects/share_projects.ctp -> popover box as Element View ?>

<?php echo $this->Html->css('projects/manage_categories') ?>
<style>
.modal-header {display: block; height: 70px;}
.sharing-icon .permissions.active {
    margin-left: 3px !important;
}

.modal-dialog {
  margin: 30px auto;
  width: 800px;
   
}
.ico_element {
    
    cursor: pointer;
    display: inline-block;
    height: 17px;
    margin: 5px 5px 0 0;
    vertical-align: middle;
    width: 17px;
}
.sharing_list .nav.nav-list.tree a i.tree_icons {
    border-radius: 15px;
    color: #fff;
    font-size: 10px;
    height: 15px;
    line-height: 1.42;
    margin-right: 10px;
    padding: 0 !important;
    text-align: center;
    width: 15px;
}
.propogat_permisions {
    background-color: rgba(0, 0, 0, 0.2);
}
.options-arrow.right {
	border-right: 8px solid rgba(0, 0, 0, 0.2);
}
.options-inner .permissions.active {
    font-size: 12px;
    height: 25px;
    margin: 0 !important;
    padding: 2px !important;
    width: 25px;
}

.modal-header .sharing-icon {
    border: 1px solid rgba(0, 0, 0, 0);
    display: inline;
    float: none;
    vertical-align: top;
    width: 380px;
}

h3 .permissions.active {
    font-size: 12px;
    height: 22px;
    margin: 0 !important;
    padding: px !important;
    width: 22px;
	color: #222;
}
.prop {
    display: inline-block;
    height: 30px;
    padding: 0;
    width: auto;
}
</style>
<?php 
	$project_title = ""; 
	$share_permission = false;
	
	$pdata = $this->ViewModel->all_permissions( $user_id, $project_id );
	if( isset($pdata) && !empty($pdata) ) {
		$ppdata = isset($pdata['pp_data']) ? $pdata['pp_data']: null;
		 
		$prPermit = isset($pdata['ProjectPermission']) ? $pdata['ProjectPermission']: null;
		$prDetail = $this->ViewModel->getProjectDetail( $project_id );
		$project_title = strip_tags($prDetail['Project']['title']);
	}
 ?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel" style="display: inline"><?php echo $project_title; ?> 
	</h3> 
</div> 
<div class="modal-body">
	<div id="sharing_list" class="sharing_list" >
		<ul class="nav nav-list tree" >
			
<?php 
$pdata = $this->ViewModel->all_permissions( $user_id, $project_id );
 
if( isset($pdata) && !empty($pdata) ) {
	$icon_class = 'tree_icons opened fa fa-minus';
	$ppdata = ( isset($pdata['pp_data']) && !empty($pdata['pp_data']) ) ? $pdata['pp_data'] : null;
	$wpdata = ( isset($pdata['wp_data']) && !empty($pdata['wp_data']) ) ? $pdata['wp_data'] : null; 
	$epdata = ( isset($pdata['ep_data']) && !empty($pdata['ep_data']) ) ? $pdata['ep_data'] : null; 
	
	if( isset($ppdata['ProjectPermission']['share_permission']) && !empty($ppdata['ProjectPermission']['share_permission']) ){
		$share_permission = true;
	}?>
	 
	<li class="has-sub-cat">
		<a  class="tipText tree-toggler nav-header tree_links"> <i class="<?php echo $icon_class; ?>"></i>
			<span class="tree_text" title="" style=""><?php echo $project_title; ?></span>
		</a>
	<div class="sharing-icon"> 
		<?php  
		if( isset($prPermit) && !empty($prPermit) ) {
				
			if($prPermit['permit_read']){
				echo '<label class="permissions permit_read btn-circle btn-xs active tipText project" title="Read">'.
				'<i class="fa fa-eye"></i>'.
				'</label>';
			}
			if($prPermit['permit_edit']){
				echo '<label class="permissions permit_edit btn-circle btn-xs active tipText project" title="Update">'.
				'<i class="fa fa-pencil"></i>'.
				'</label>';
			}
			if($prPermit['permit_delete']){
				echo '<label class="permissions permit_delete btn-circle btn-xs active tipText project" title="Delete">'.
				'<i class="fa fa-trash"></i>'.
				'</label>';
			}
			if($prPermit['permit_add']){
				echo '<label class="permissions permit_add btn-circle btn-xs active tipText project" title="Add">'.
				'<i class="fa fa-plus"></i>'.
				'</label>';
			}
			
			if( $share_permission ) {
				$prPropagate = $this->ViewModel->project_propagation( $project_id, $user_id );
				if( isset($prPropagate) && !empty($prPropagate) ) {
					$prProp = $prPropagate['ProjectPropagate'];
?> 
				<div class="propogation-wrapper">
					<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">  
					<i class="fa fa-code-fork fa-rotate-180"></i>  
					</a>  
				
				<div class="propogat_permisions" style="display: block;">
				
					<div class="options-arrow right"></div>
					<div class="options-inner">
						<?php if($prProp['permit_read']){ ?>
							<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
						<?php } ?>
						
						<?php if($prProp['permit_edit']){ ?>
							<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label> 
						<?php } ?>
						
						<?php if($prProp['permit_delete']){ ?>
							<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label> 
						<?php } ?>
						
						<?php if($prProp['permit_add']){ ?>
							<label title="" class="permissions permit_add btn-circle btn-xs tipText active" data-original-title="Add New Task"><i class="fa fa-plus"></i></label> 
						<?php } ?> 
					</div>
				
				</div>
				
				</div> 
				<?php 
				}
			}
			
			// 
		} 
	?>
	</div>
	
	
	
	
	<?php 
	if( isset($wpdata) && !empty($wpdata) ) {
		echo '<ul class="nav nav-list tree">';
		foreach($wpdata as $k => $v) {
			$wsPermit = $v['WorkspacePermission'];
			$wsid = pwid_workspace($wsPermit['project_workspace_id'], $project_id);
			$wsDetail = $this->ViewModel->getWorkspaceDetail( $wsid );
			echo  '<li class="has-sub-cat">';
				echo  '<a ' .
					' data-id="' . $wsDetail['Workspace']['id'] . '"' .
					' class="tipText tree-toggler nav-header tree_links">' .
					'<i class="'.$icon_class.'"></i> ' .
					'<span class="tree_text" title="" style="">'.$wsDetail['Workspace']['title'].'</span>' .
					'</a>' ;
				echo '<div class="sharing-icon"> ';
					if($wsPermit['permit_read']){
						echo '<label class="permissions permit_read btn-circle btn-xs active tipText" title="Read">'.
						'<i class="fa fa-eye"></i>'.
						'</label>';
					}
					if($wsPermit['permit_edit']){
						echo '<label class="permissions permit_edit btn-circle btn-xs active tipText" title="Update">'.
						'<i class="fa fa-pencil"></i>'.
						'</label>';
					}
					if($wsPermit['permit_delete']){
						echo '<label class="permissions permit_delete btn-circle btn-xs active tipText" title="Delete">'.
						'<i class="fa fa-trash"></i>'.
						'</label>';
					}
					if($wsPermit['permit_add']){
						echo '<label class="permissions permit_add btn-circle btn-xs active tipText" title="Add New Task">'.
						'<i class="fa fa-plus"></i>'.
						'</label>';
					}
					
					
					if( $share_permission ){
						$wsPropagate = $this->ViewModel->workspace_propagation( $wsid, $user_id, null, $project_id );
						if( isset($wsPropagate) && !empty($wsPropagate) ) {
							$wsProp = $wsPropagate['WorkspacePropagate'];
	?> 
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">  
								<i class="fa fa-code-fork fa-rotate-180"></i>  
							</a>  
							
							<div class="propogat_permisions" style="display: block;">
								 
								<div class="options-arrow right"></div>
									
								<div class="options-inner">
									<?php if($wsProp['permit_read']){ ?>
										<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
									<?php } ?>
									
									<?php if($wsProp['permit_edit']){ ?>
										<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label> 
									<?php } ?>
									
									<?php if($wsProp['permit_delete']){ ?>
										<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label> 
									<?php } ?>
									
									<?php if($wsProp['permit_add']){ ?>
										<label title="" class="permissions permit_add btn-circle btn-xs tipText active" data-original-title="Add New Task"><i class="fa fa-plus"></i></label> 
									<?php } ?> 
								</div>
								
							</div>
							
						</div>
	<?php 
						}
					}
					 
				echo '</div> ';
					
			$edata = arraySearch($epdata, 'workspace_id', $wsDetail['Workspace']['id']);
			
			if( !empty($edata) ) {
				echo '<ul class="nav nav-list tree">';
				foreach($edata as $k => $v) {
					$elPermit = $v;
					$elDetail = $this->ViewModel->getElementDetail( $elPermit['element_id'] );
					// pr($elDetail, 1);
					echo  '<li class="">';
					echo  '<a ' .
						' data-id="' . $elDetail['Element']['id'] . '"' .
						' class="tipText tree-toggler nav-header tree_links">' .
						'<i class="ico_element pull-left"></i> ' .
						'<span class="tree_text" title="" style="">'.$elDetail['Element']['title'].'</span>' .
						'</a>' ;
					echo '<div class="sharing-icon"> ';
					if($elPermit['permit_read']){
						echo '<label class="permissions permit_read btn-circle btn-xs active tipText" title="Read">'.
						'<i class="fa fa-eye"></i>'.
						'</label>';
					}
					if($elPermit['permit_edit']){
						echo '<label class="permissions permit_edit btn-circle btn-xs active tipText" title="Update">'.
						'<i class="fa fa-pencil"></i>'.
						'</label>';
					}
					if($elPermit['permit_delete']){
						echo '<label class="permissions permit_delete btn-circle btn-xs active tipText" title="Delete">'.
						'<i class="fa fa-trash"></i>'.
						'</label>';
					}
					if($elPermit['permit_add']){
						echo '<label class="permissions permit_add btn-circle btn-xs active tipText" title="Add">'.
						'<i class="fa fa-plus"></i>'.
						'</label>';
					}
					if($elPermit['permit_copy']){
						echo '<label class="permissions permit_copy btn-circle btn-xs active tipText" title="Copy">'.
						'<i class="fa fa-copy"></i>'.
						'</label>';
					}
					if($elPermit['permit_move']){
						echo '<label class="permissions permit_move btn-circle btn-xs active tipText" title="Cut">'.
						'<i class="fa fa-cut"></i>'.
						'</label>';
					}
					
					
					// Get all propagations 
					
					if( $share_permission ){
						$elids = ['project_id' => $project_id, 'workspace_id' => $wsid, 'element_id' => $elPermit['element_id'] ];
						$elPropagate = $this->ViewModel->element_propagation( $elids, $user_id );
						if( isset($elPropagate) && !empty($elPropagate) ) {
							$elProp = $elPropagate['ElementPropagate'];
	?> 
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">  
								<i class="fa fa-code-fork fa-rotate-180"></i>  
							</a>  
							
							<div class="propogat_permisions" style="display: block;">
								 
								<div class="options-arrow right"></div>
									
								<div class="options-inner">
								
									<?php if($elProp['permit_read']){ ?>
										<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
									<?php } ?>
									
									<?php if($elProp['permit_edit']){ ?>
										<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label> 
									<?php } ?>
									
									<?php if($elProp['permit_delete']){ ?>
										<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label> 
									<?php } ?> 
									
									<?php if($elProp['permit_copy']){ ?>
										<label title="" class="permissions permit_copy btn-circle btn-xs tipText active" data-original-title="Copy"><i class="fa fa-copy"></i></label> 
									<?php } ?> 
									
									<?php if($elProp['permit_move']){ ?>
										<label title="" class="permissions permit_move btn-circle btn-xs tipText active" data-original-title="Cut"><i class="fa fa-cut"></i></label> 
									<?php } ?> 
				
								</div>
								
							</div>
							
						</div>
					<?php 
						}
					}
					echo '</div> ';
					
					
					echo '</li>';
				}
				echo '</ul>';
			}
			echo '</li>';
		}
	}
		echo '</ul>';
	echo '</li>';
	// pr($ppdata  );
	// pr($wpdata );
	// pr($epdata );
	// $projectDetail = $this->ViewModel->getProjectDetail( $project_id );
}
// pr($pdata, 1);
?>
		</ul>
	</div>
	
</div>
 
	<div class="modal-footer"> 
	    
		<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button> 
	</div>	
	
<script type="text/javascript" >
$(function(){
	$('#permissionModal').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
	
	$('.sharing-icon').each(function(i, v) {
		// console.log($(this).children('.permissions'))
	})
})
</script>
 
<script type="text/javascript" >
$(function(){
	$('#popup_model_box_new').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL ?>images/ajax-loader-1.gif" style="margin: auto;"></div>');
    });
		// $(".modal-content").hide()
		setTimeout(function(){
			// $(".modal-content").show()
		}, 1500)
})


</script>

