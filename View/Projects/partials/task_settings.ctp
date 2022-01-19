<?php
// pr($workspace_detail, 1);
$project_id = $workspace_id = null;
if( isset($this->params['pass']) && !empty($this->params['pass'])) {
	$project_id = (isset($this->params['pass'][0]))
						? $this->params['pass'][0]
						: (
							( isset($menu_project_id) && !empty($menu_project_id))
								? $menu_project_id
								: null
						);
	$workspace_id = (isset($this->params['pass'][1]))
								? $this->params['pass'][1]
								: null;
}


?>
<style>
.content-wrapper{
    background-color: #f1f3f4;
}
.sep-header-fliter{
    background-color: #f1f3f4;
    border-top: 1px solid #dcdcdc;
}
</style>

<section class="content-header clearfix nopadding">

    <div class="header-progressbar">
	    <div class="progressbar-sec wsp_progress_bar">
	    	<?php echo $this->element('../Projects/partials/wsp_progress_bar', ['project_id' => $project_id, 'workspace_id' => $workspace_id]); ?>
		</div>



      <div class=" right-side-progress-bar">
			<!-- Project Options -->
				<?php


				if(isset($wsp_permissions[0]['user_permissions']) && in_array($wsp_permissions[0]['user_permissions']['role'],array('Creator','Group Owner','Owner')) ){
				  {
 ?>

				<?php

				}
				//pr($data['workspace']['ProjectWorkspace']['0']['WorkspacePermission']['0']);
				?>
			<div class="btn-group action progres-mt-btn" style="display:none">
				<?php
					if( isset($wsp_permissions[0]['user_permissions']) && $wsp_permissions[0]['user_permissions']['p_edit']==1 ){

					// SHOW PROJECT EDIT LINK IF PROJECT ID VALUES ARE EXISTS ?>
						<a href="<?php echo Router::url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspace_id)); ?>" class="btn btn-sm btn-success tipText" title="<?php echo tipText('Update Workspace Details') ?>" id="menu_open_workspace" ><i class="fa fa-fw fa-pencil"></i> </a>

						<?php } ?>

			</div>
			<div class="btn-group action progres-mt-btn">
			<?php //********************* More Button ************************
				//echo $this->element('task_more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'projects' ,'wsp_permissions'=>$wsp_permissions)); ?>
			</div>

			<div class="btn-group action progres-mt-btn"> <?php if($this->request->params['action']=='manage_elements'){ ?>
				<a id="btn_go_back" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $this->params['pass']['0'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-warning tipText pull-right btn-sm" > <i class="fa fa-fw fa-chevron-left"></i> Back </a>
				<?php }else{ ?>
				<a id="btn_go_back" data-original-title="Go Back" href="<?php echo $this->request->referer(); ?>" class="btn btn-warning tipText pull-right btn-sm"> <i class="fa fa-fw fa-chevron-left"></i> Back </a>
				<?php }  ?>
			</div>
			<?php } ?>
		</div></div>




			<?php
			$toc = 'bg-green';


			$total_elements = ( isset($taskCount[0][0]['e_count']) && !empty($taskCount[0][0]['e_count']) ) ? $taskCount[0][0]['e_count'] : 0;
			$total_completed = ( isset($taskCount[0][0]['sign_off']) && !empty($taskCount[0][0]['sign_off']) ) ? $taskCount[0][0]['sign_off'] : 0;
			//pr($total_elements); die;

			$percent = 0;
			if( $total_elements > 0 ) {
				$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
			}


			$quick_share_disable = '';
			$quick_share_disable_tip = '';
			$quick_share_disable_cursor = '';
			if(isset($data['workspace']['Workspace']['sign_off']) && !empty($data['workspace']['Workspace']['sign_off'])){
				$percent = 100;
				$toc = 'bg-red';

				$quick_share_disable = 'disable';
				$quick_share_disable_tip = 'Workspace Is Signed Off';
				$quick_share_disable_cursor = 'cursor:default !important;';
			}


			 $projects = project_detail($this->params['pass']['0']);

			if(isset($projects['rag_status']) && !empty($projects['rag_status'])){

			  if($projects['rag_status'] == 1){
				$toc = 	'bg-red';
			  }else if($projects['rag_status']==2){
				$toc = 	'bg-yellow';

			  }else if($projects['rag_status'] ==3){
				$toc = 	'bg-green';
			  }

			}


			$total_elem = 0;
			if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
			}


			   if( isset($project_id) && !empty($project_id) ){

 if(isset($wsp_permissions[0]['user_permissions']) && in_array($wsp_permissions[0]['user_permissions']['role'],array('Creator','Group Owner','Owner')) ){

			?>
 <?php } } ?>
</section>



<style type="text/css">
	.taskcounters li {
		cursor: pointer;
	}
	.default-tooltip {
	    text-transform: none;
	}
</style>

<script type="text/javascript">
	$(function(){
		$('.task_count').on('click', function(event) {
			event.preventDefault();
			var url = $(this).data('url');
			location.href = url;
		});
		$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .cost-percent,.progres-mt-btn.disable').tooltip({
			'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
			'placement': 'top',
			'container': 'body'
		})
		$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
	})
</script>