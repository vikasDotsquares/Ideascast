<?php 
if (isset($category_id) && !empty($category_id)) {
	$cat_data = getByDbId('Category', $category_id);
	
}
 ?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Projects in <?php echo $cat_data['Category']['title']; ?></h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">


<div class="row  clearfix">
<?php
// pr($category_id, 1);
if (isset($rows) && !empty($rows)) {

     if (isset($rows['count']) && $rows['count'] > 0) {

	  foreach ($rows['projects'] as $row) {
	       $record = $row['Project'];

	       // Get all workspaces of the project and count assets

	       $totalEle = $totalWs = 0;
	       $totalAssets = null;
	       $projectData = $this->ViewModel->getProjectDetail($record['id']);
		   

	       if (!empty($projectData)) {
		   
		    $wsList = get_project_workspace($record['id'],true);
		     
		    //$wsList = Set::extract($projectData, '/ProjectWorkspace/workspace_id');
		    $totalWs = ( isset($wsList) && !empty($wsList) )? count($wsList) : 0;
			
			
		    if (!empty($wsList)) {
			 foreach ($wsList as $wsid=>$val) {
			      $wsData = $this->ViewModel->countAreaElements($wsid);
			      $totalEle += $wsData['active_element_count'];
			      if (isset($wsData['assets_count']) && !empty($wsData['assets_count'])) {

				   foreach ($wsData as $k => $subArray) {
					if (is_array($subArray)) {
					     foreach ($subArray as $m => $value) {
						  if (!isset($totalAssets[$m]))
						       $totalAssets[$m] = $value;
						  else
						       $totalAssets[$m] += $value;
					     }
					}
				   }
			      }
			 }
		    }
	       }
	       // pr($wsid);
	       // pr($totalAssets, 1);
	       ?> 
	       <div class="col-md-6">
	            <div class="panel clearfix border category_projects  box-shadow <?php echo $record['color_code'] ?>" style="">
	       	  <div class="panel-heading clearfix ">
	       	       <h4 class="panel-title pull-left">
	       		    <a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $record['id'], 'admin' => FALSE), TRUE) ?>" style="text-transform: capitalize !important;" class="cat_proj_title"><?php echo $row[0]['subTitle'] ?> </a>
	       	       </h4>
	       	  </div>
	       	  <div class="panel-body padding noborder-radius clearfix">


	       	       <div class="prjct-rprt-icons"> 
	       		    <ul class="list-unstyled text-center">
	       			 <li class="iele">
	       			      <span class="label bg-mix " title=""><?php echo (!empty($totalEle)) ? $totalEle : 0; ?></span>
	       			      <span class="icon_element_white btn btn-xs bg-dark-gray tipText" data-original-title="Elements" href="#"> </span>
	       			 </li>
	       			 <li class="ico_links">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0; ?></span> 
	       			      <span data-original-title="Links" class="btn btn-xs bg-mix tipText bg-maroon" title="" href="#"><i class="fa fa-link"></i></span>
	       			 </li>
	       			 <li class="inote">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0; ?></span> 
	       			      <span data-original-title="Notes" class="btn btn-xs bg-mix tipText bg-purple" title="" href="#"><i class="fa fa-file-text-o"></i></span>
	       			 </li>
	       			 <li class="idoc">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0; ?></span>
	       			      <span class="btn bg-blue btn-xs bg-mix tipText" title="" href="#" data-original-title="Documents"><i class="fa fa-folder-o"></i></span>
	       			 </li>
	       			 <li class="odue">
	       			      <span class="label bg-mix">
						  <?php
						  $due_status_ind = _due_status($record['id'], 'overdue_status');
						  if (!empty($due_status_ind) && is_array($due_status_ind)) {
						       echo array_sum($due_status_ind);
						  } else {
						       echo '0';
						  }
						  ?>
	       			      </span>
	       			      <span data-original-title="Overdue Statuses" class="btn btn-xs bg-mix bg-navy tipText" title="" href="#"><i class="fa fa-exclamation"></i></span>
	       			 </li>
	       			 <li class="green">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0; ?></span>
	       			      <span data-original-title="Mind Maps" class="btn btn-xs bg-mix bg-green tipText" title="" href="#"><i class="fa fa-sitemap"></i></span>
	       			 </li>
	       			 <li class="orang">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['decisions']) && !empty($totalAssets['decisions'])) ? $totalAssets['decisions'] : 0; ?></span>
	       			      <span data-original-title="Decisions" class="btn btn-xs bg-mix bg-orange  tipText decisions" title="" href="#"><i class="fa fa-expand"></i></span>
	       			 </li>
	       			 <li class="l-blue">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['feedbacks']) && !empty($totalAssets['feedbacks'])) ? $totalAssets['feedbacks'] : 0; ?></span>
	       			      <span data-original-title="Feedback" class="btn btn-xs bg-mix bg-aqua tipText" title="" href="#"><i class="fa fa-bullhorn"></i></span>
	       			 </li>

	       			 <li class="l-orang">
	       			      <span class="label bg-mix"><?php echo (isset($totalAssets['votes']) && !empty($totalAssets['votes'])) ? $totalAssets['votes'] : 0; ?></span>
	       			      <span class="btn btn-xs bg-mix tipText bg-orange-active" title="" href="#" data-original-title="Votes"><i class="fa fa-inbox"></i></span>
	       			 </li>
	       		    </ul>  
	       	       </div>


	       	  </div>
	            </div> 
	       </div> 


	  <?php
	  }
     } else {
	  ?> 
	       <div class="error-page">
	  	  <h2 class="headline text-yellow"> <span class="glyphicon glyphicon-th-large"></span> </h2>
	  	  <div class="error-content" style="padding-top: 17px;">
	  	       <h3><span class="glyphicon glyphicon-info-sign text-yellow"></span> &nbsp; The selected category does not have any Project. </h3>
	  	       <p class="text-center">
	  		    Click <a class="" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE), TRUE); ?>"> here </a>to add a new Project.
	  	       </p>
	  	  </div><!-- /.error-content -->
	       </div><!-- /.error-page --> 
     <?php
     }
}
?>
 
</div>
</div>
 
	