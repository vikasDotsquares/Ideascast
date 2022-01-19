                    <div class="box border-top ">                        
						<?php echo $this->Session->flash(); ?>
						<div class="box-body clearfix list-acknowledge" id="mind_maps">
							<div id="list_grid_containers" class="list_grid_containers_tree">
							
						<?php   // $this->Session->read('Auth.User.id');
							//$uplan = $this->requestAction('/users/getUPlan/'.$this->Session->read('Auth.User.id'));			
						?>
								
								<!-- LIST AND GRID VIEW START	-->
								<!--<ul class="grid clearfix filetree"  id="browser" >
								<li>
								<span class="folder">Projects</span>-->
								<ul class="grid clearfix filetree" id="browser" style="display:none">
							
									<?php  
									$row_counter = 0;
									$project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
									foreach( $projects as $key => $val ) {
										
										$item = $val['Project'];
										//pr($item);
									?>
									<?php
									     
										$comments = $this->ViewModel->get_projectWorkspace($item['id'],$statusArr);
										//pr($comments);
										
										
										$report = SITEURL."projects/index/".$item['id'];

// check wsp signoff comment
$signoffproject = $this->ViewModel->signoff_comment('SignoffProject',$item['id']);											
										
$pclst = '';
$ptooltip = 'Not Set';
$phovercls = '';							 
if( isset($item['sign_off']) && $item['sign_off'] == 1 ){
																
	$pclst = "bg-completed";
	$ptooltip = 'Completed';
	$phovercls = 'prj_completed';
	
} else if( !isset($item['start_date']) && $item['start_date'] == null  ){
	
	$pclst = "bg-undefined";
	$ptooltip = 'Not Set';
	$phovercls = 'prj_undefined';
	
} else if( date($item['start_date']) <= date('Y-m-d') && date($item['end_date']) >= date('Y-m-d') ) {
	
	$pclst = "bg-progressing";
	$ptooltip = 'In Progress';
	$phovercls = 'prj_progressing';
	
} else  if( date($item['start_date']) > date('Y-m-d')  ){
	
	$pclst = "bg-not_started";
	$ptooltip = 'Not Started';
	$phovercls = 'prj_not_started';
	
} else if( date($item['end_date']) < date('Y-m-d') ){
	$pclst = "bg-overdue";
	$ptooltip = 'Overdue';
	$phovercls = 'prj_overdue';
}


$prjevidencelink = "";
if( isset($item['sign_off']) && $item['sign_off'] == 1 && $signoffproject == 1 ){				
	
	$prjevidencelink = '<a href="#" data-toggle="modal" data-target="#prj_signoff_comment_show" data-remote="'.SITEURL.'projects/show_signoff/'.$item['id'].'" class="signoff_evidence" ><span class="folders"> <i title="Click to see Comment and Evidence" class="ps-flag prj-signoff wsp_flagbg '. $pclst .'"></i></a>'; 
	//<i class="fa fa-th"></i>
 } else {
	 $prjevidencelink = '<i title="'.$ptooltip.'" class="ps-flag tipText wsp_flagbg '. $pclst .'"></i>';
 }


										/*  <i title="<?php echo $ptooltip;?>" class="ps-flag tipText wsp_flagbg <?php echo $pclst; ?>"></i> */

?>
									<li class="closed"> 
											<span class="folders "><i class="<?php echo $proejct_icon;?>"></i><?php echo $prjevidencelink;?><a href="<?php echo $report; ?>"><span class="tipText" title="Open Project"><?php echo htmlentities($item['title']); ?></span></span></a>
										
									<!--<ul>
									  
										
										<li class="closed">
									     <span class="folder">Workspace</span>-->
										   
										<ul class="list">  
										
										<?php
										  
										if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){  
										if(!empty($com['Workspace'])){
										$uuid = $this->Session->read('Auth.User.id');
										 $pid = $project_id;
										//$pid = $this->params['pass']['0'];
										 
										
										
										$wml = SITEURL."projects/manage_elements/".$item['id']."/".$com['Workspace']['id']; 
										
										// check wsp signoff comment
										$signoffworkspace = $this->ViewModel->signoff_comment('SignoffWorkspace',$com['Workspace']['id']);	
										
							//	if( $d['active_element_count'] > 0 && (( isset($d['assets_count']['docs']) && !empty($d['assets_count']['docs']) ) || ( isset($d['assets_count']['links']) && !empty($d['assets_count']['links']) )) ) {
								
								
								       if($com[0]['w_status']=='Completed'){
										   $clst = "bg-completed";
									   }
									   else if($com[0]['w_status']=='In Progress'){
										   $clst = "bg-progressing";
									   }  
									   else if($com[0]['w_status']=='Not Set'){
										   $clst = "bg-undefined";
									   } 
									   else if($com[0]['w_status']=='Not Started'){
										   $clst = "bg-not_started";
									   }
									   else if($com[0]['w_status']=='Overdue'){
										   $clst = "bg-overdue";
									   }   
									   
										$wspevidencelink = "";
										if( $com[0]['w_status']=='Completed' && $signoffworkspace == 1 ){				
											
											$wspevidencelink = '<a href="#"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="'.SITEURL.'workspaces/show_signoff/'.$com['Workspace']['id'].'" class="signoff_evidence"  ><span class="folders"><i class="'.$wsp_icon.'"></i><i title="Click to see Comment and Evidence" class="ps-flag wsp-signoff '.$clst.' wsp_flagbg " ></i></a>'; 
											
										 } else {
											 $wspevidencelink = '<span class="folders"  ><i class="'.$wsp_icon.'"></i><i title="'.$com[0]['w_status'].' " class="ps-flag tipText '.$clst.' wsp_flagbg "></i>';
										 }
										 
										 
										 echo '<li class="closed closed_sear wsp" data-id='.$com['Workspace']['id'].'>'.$wspevidencelink.'<a   href="'.$wml.'" cla="wsp_row"><span class="tipText" title="Open Workspace">'.htmlentities($com['Workspace']['title']).'</span></span></a>'; //die;
									   									   
										/* echo '<li class="closed closed_sear"><a href="'.$wml.'" cla="wsp_row"><span class="folders"  ><i class="fa fa-th"></i> <i title="'.$com[0]['w_status'].' " class="ps-flag tipText '.$clst.' wsp_flagbg "></i> <span class="tipText" title="Open Workspace">'.htmlentities($com['Workspace']['title']).'</span></span></a>'; //die; */
										
											  
										$allArea = $this->User->areass($com['Workspace']['id'],$statusArr);
										
										//$allArea = $this->User->areas($com['Workspace']['id'],$statusArr);
										 
										// pr($allArea);
										/* <ul>
										<li class='closed'>
									     <span class='folder'>Area</span> */
										
										
										echo "
										
										<ul>";
												foreach($allArea as $area){
													
													
												echo "<li class='closed area_folder'><span class='folders tipText' title='Area'><i class='".$area_icon."'></i>".htmlentities($area['Area']['title'])."</span>
												<!--<ul>
												
												<li class='closed '>
														<span class='folder elm_doc'>Element</span> -->
												<ul>";
											 	
											$tasks = $this->ViewModel->getAreaTaskAsset($com['Workspace']['id'], $area['Area']['id'], $statusArr);
	//pr($tasks);
													foreach($tasks as $elems){
														
														$elem = $elems['Element'];
													
													if($elem['studio_status'] !=1){
													
													 //pr($elems);
													 $elemAssets['assets_count'] = $elems[0]; 
													 
													// check task signoff comment
													$signoffcomment = $this->ViewModel->signoff_comment('SignoffTask',$elem['id']); 
															
													if(empty($elem['title'])){
														$etl = "N/A";
													}else{
														$etl = $elem['title'];
													}
													
												   $estatus = $elems[0]['e_status'];
												   $evidencelink = '';
												   $elst = '';
												   if($elems[0]['e_status']=='Completed' && $signoffcomment == 1 ){
													   $elst = "bg-completed";
													   $estatus = "Click to see Comment and Evidence";
													   
												   } else if($elems[0]['e_status']=='Completed' && $signoffcomment != 1 ){
													   $elst = "bg-completed";
													   $estatus =  $elems[0]['e_status'];
													   
												   }
												   else if($elems[0]['e_status']=='In Progress'){
													   $elst = "bg-progressing";
												   }  
												   else if($elems[0]['e_status']=='Not Set'){
													   $elst = "bg-undefined";
												   } 
												   else if($elems[0]['e_status']=='Not Started'){
													   $elst = "bg-not_started";
												   }
												   else if($elems[0]['e_status']=='Overdue'){
													   $elst = "bg-overdue";
												   } 
												  

												  
												
												  
												 if( $elems[0]['e_status']=='Completed' && $signoffcomment == 1 ){
													 
													$evidencelink = '<a href="#" title="Click to see Comment and Evidence"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="'.SITEURL.'entities/show_signoff/'.$elem['id'].'" class="signoff_evidence" ><i class="ps-flag task-signoff '.$elst.'" title="'.$estatus.'"  ></i></a>'; 
													
												 } else {
													 $evidencelink = '<i class="ps-flag tipText '.$elst.'" title="'.$estatus.'" style=" cursor:default;"></i>';
												 }
												 
												 
													$lml = SITEURL."entities/update_element/".$elem['id']; 
													
													
													echo "<li class='closed '><i class='ps-flag task-flag'></i>$evidencelink<a href=".$lml."><span class='folders  elm_doc tipText' style='display:inline-block; ' title='Open Task' >".htmlentities($etl)."</span></a>";
													
													/* echo "<li class='closed '>
														<a href=".$lml."><span class='folders  elm_doc '  ><i class='icon_element_add_black'></i> <i class='ps-flag tipText ".$elst."' title='".$estatus."'> </i>".htmlentities($etl)."</span></a>"; */
									
											if( ( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links'] ) ) && !isset($typesArr)  || ( isset($links) && !empty($links))  ) {												
										
													echo "<ul>
														<li class='closed drgP' >
														<span class='folders element tipText' title='Links'><i class='".$task_ele_icon."'></i>Links</span>
														<ul class='drg'>";
														//$alldoc = $this->requestAction('/users/links/'.$elem['id']);
														$alldoc = $this->User->links($elem['id']);
														
														echo "<input type='hidden' name='ele_test' class='elm_hid' value='".$elem['id']."'>";
														
															foreach($alldoc as $doc){
															
															$chkLinks = explode("//",$doc['ElementLink']['references']);
															
															 
															
															if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){ 
													 
																$classLL ="elm_docP";
															 
															}else{
																$classLL =" ";
															}
															//pr($doc['ElementLink']);	
															 	
															if(isset($doc['ElementLink']['link_type']) && $doc['ElementLink']['link_type'] == 2){
															
																		 
																echo "<li class='" . $classLL . "'><i class='ps-flag aslink mr2'></i> <span class='files column'>" . "<a title='Open Link' data-target='#modal_medium' data-toggle='modal' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link'   href=" . SITEURL.'entities/play_media/'.$doc['ElementLink']['id'] . ">"  . htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8") . "</a></span></li>"; 
                                                                        
																		
															}else{	
															
															if(isset($chkLinks['1']) && !empty($chkLinks['1'])){
															 echo "<li class='".$classLL."'><i class='ps-flag aslink mr2'></i> <span class='files column'>"."<a title='Open Link' id='".$doc['ElementLink']['id']."' class='portlet-headers tipText search_link' target='_blank'  href=".$doc['ElementLink']['references'].">". htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8")."</a>";
															}else{
															 
															 echo "<li  class='".$classLL."'><i class='ps-flag aslink mr2'></i> <span class='files column'>"."<a title='Open Link' id='".$doc['ElementLink']['id']."' class='portlet-headers tipText search_link' target='_blank'   href="."http://".$doc['ElementLink']['references'].">". htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8")."</a>";
															}
															}
									
													?>
									
															
													<?php	echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}
												
									//$allNOT = $this->requestAction('/users/notes/'.$elem['id']);
									$allNOT = $this->User->notes($elem['id']);
	
									if(  isset($allNOT) && !empty($allNOT)   && !isset($typesArr)  || ( isset($notes) && !empty($notes)) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Notes'><i class='".$task_ele_icon."'></i>Notes</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='".$elem['id']."'>";
														
															foreach($allNOT as $not){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$notl = SITEURL.'entities/update_element/'.$elem['id'].'/'.$not['ElementNote']['id'].'/resources#notes';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='ps-flag asnotes mr2'></i> <span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $notl; ?>" class="portlet-headers   tipText"  data-id="<?php echo $not['ElementNote']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Note" > 
													
													<?php echo	htmlentities($not['ElementNote']['title'], ENT_QUOTES, "UTF-8"); ?>
													
													</a>
													
									<?php echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}
            
												if( ( isset($elemAssets['assets_count']['docs']) && !empty($elemAssets['assets_count']['docs'] )   && !isset($typesArr)  || ( isset($documents) && !empty($documents)))  ) {
													  
													  echo "<ul>

														<li class='closed docdp'>
														<span class='folders element tipText' title='Documents'><i class='".$task_ele_icon."'></i>Documents</span>
														<ul class='docdrop'>";
														
														
														echo "<input type='hidden' name='ele_test' class='elm_hid' value='".$elem['id']."'>";
														
															//$alldoc = $this->requestAction('/users/documents/'.$elem['id']);
															$alldoc = $this->User->documents($elem['id']);
															
															foreach($alldoc as $doc){
															
															$cla = "";
															$icon = "";
															$ext = explode(".",$doc['ElementDocument']['file_name']);
															if(isset($ext['1']) && !empty($ext['1'])){
															  
																if($ext['1']=='txt') {
																	$cla ="fa-file-text-o";
																}else if($ext['1']=='jpg' || $ext['1']=='jpeg' || $ext['1']=='png') {
																	$cla ="fa-folder-o";
																}
															 else  if($ext['1']=='pdf') {
															 $cla ="fa-file-pdf-o";
															 }else if($ext['1']=='xls' || $ext['1']=='xlsx') {
															 $cla ="fa-file-excel-o";
															 }
															 else if($ext['1']=='doc' || $ext['1']=='docx') {
															 $cla ="fa-file-word-o";
															 }
															 else if($ext['1']=='ppt' || $ext['1']=='pptx') {
																$cla ="fa-file-powerpoint-o";
															 }else{
															 $cla ="fa-file-o";
															 }
															
															
															if($ext['1']=='txt' || $ext['1']=='jpg' || $ext['1']=='jpeg' || $ext['1']=='png') {
															
															$icon ="fa-fw fa-eye text-teal";
															}else{
															$icon ="fa-fw fa-download text-yellow";
															}
															// $cla =" fa-file-zip-o";
															}
															
															$cla ="ps-flag asdocument mr2";
														
																			
													if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){  
													 
														$class ="docdrag";
													 
													}else{
														$class =" ";
													}
															//pr($us_permission);
															 //pr($es);
															//pr($pr_permission['ProjectPermission']);									

															$mBNlink = SITEURL.'entities/update_element/'.$elem['id'].'/'.$doc['ElementDocument']['id'].'/resources#documents';
															
															 echo "<li class='".$class." '><i class=' ".$cla."'></i> <a  class='ddn tipText' title='Go To Documents'  rel='".$elem['id']."'  id='".$doc['ElementDocument']['id']."'  href=".$mBNlink."> <span class='files searchD'> &nbsp;&nbsp;".$doc['ElementDocument']['title'] ;
												
                                        			 					
										$deleteURL = SITEURL."/users/deleteDoc/".$doc['ElementDocument']['id']; 
										if( (!empty($elems['user_permissions']['permit_edit']) && $elems['user_permissions']['permit_edit']==1 )){ 
										?>
										
										<!--<a data-toggle="modal" class="RecordDeleteClass tipText" data-target="#deleteBox" rel="<?php echo $doc['ElementDocument']['id']; ?>"   title="Delete document" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o text-maroon"></i></a>
										-->
										
										<?php 
										}else{ ?>
										
										<?php  }?>
															
													<?php	echo	" </span></a></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";
												
												}
	
	
//$alldoc = $this->requestAction('/users/mms/'.$elem['id']);
$alldoc = $this->User->mms($elem['id']);
	
	if( ( isset($alldoc) && !empty($alldoc) && !isset($typesArr)  || ( isset($mms) && !empty($mms))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Mind Maps'><i class='".$task_ele_icon."'></i>Mind Maps</span>
														<ul class='drgm'>";
														//$alldoc = $this->requestAction('/users/mms/'.$elem['id']);
														
														echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='".$elem['id']."'>";
														
															foreach($alldoc as $doc){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$mlink = SITEURL.'entities/update_element/'.$elem['id'].'/'.$doc['ElementMindmap']['id'].'/resources#mind_maps';
															
															
															//pr($chkLinks); 
															
															//
															if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){
													 
																$classmm ="elm_mmP";
															 
															}else{
																$classmm =" ";
															}
															
															 echo "<li  class='".$classmm."'><i class='ps-flag asmindmap mr2'></i> <span class='files column'>";
															
													?>
													<!--<a href="#" class="portlet-headers ddnsa view_mindmap" data-remote="	<?php echo Router::Url(array('controller' => 'entities', 'action' => 'view_mindmap', $elem['id'], $doc['ElementMindmap']['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $doc['ElementMindmap']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open MindMap" > -->
													
													<a href="<?php echo $mlink; ?>" class="portlet-headers ddnsa view_mindmapd tipText"  data-id="<?php echo $doc['ElementMindmap']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Mind Map" > 
													
																			<?php echo	$doc['ElementMindmap']['title']; ?>
																			</a>
																			
																			
															
													<?php	echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}	

	
	//$allDDCC = $this->requestAction('/users/decision/'.$elem['id']);
	$allDDCC = $this->User->decision($elem['id']);

	if( ( isset($allDDCC) && !empty($allDDCC) && !isset($typesArr)  || ( isset($decisions) && !empty($decisions))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Decision'><i class='".$task_ele_icon."'></i>Decision</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_dcs_hid' value='".$elem['id']."'>";
														
															foreach($allDDCC as $dsc){
																
															$pclst = '';
															$ptooltip = 'Not Set';
															$phovercls = '';							 
															if( isset($dsc['ElementDecision']['sign_off']) && $dsc['ElementDecision']['sign_off'] == 1 ){
																
																$pclst = "bg-completed";
																$ptooltip = 'Completed';
																$phovercls = 'prj_completed';
																
															} else {
																
																$pclst = "bg-progressing";
																$ptooltip = 'In Progress';
																$phovercls = 'prj_progressing';
																
															}	
																
															
 														
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$dsc1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$dsc['ElementDecision']['id'].'/resources#decisions';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='ps-flag asdecisions mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $dsc1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $dsc['ElementDecision']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Decision" > 
													
													<?php echo	$dsc['ElementDecision']['title']; ?>
													</a>
																			
																			
															
													<?php	echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}			
	
	
												
	//$allFeed = $this->requestAction('/users/feedbacks/'.$elem['id']);
	$allFeed = $this->User->feedbacks($elem['id']);
	//pr($allFeed);
	
	if( ( isset($allFeed) && !empty($allFeed) && !isset($typesArr)  || ( isset($feedbacks) && !empty($feedbacks))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Feedback'><i class='".$task_ele_icon."'></i>Feedback</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_ff_hid' value='".$elem['id']."'>";
														
															foreach($allFeed as $feed){
																
																
																							
															$pclst = '';
															$ptooltip = 'Not Set';
															$phovercls = '';							 
															if( isset($feed['Feedback']['sign_off']) && $feed['Feedback']['sign_off'] == 1 ){
																
																$pclst = "bg-completed";
																$ptooltip = 'Completed';
																$phovercls = 'prj_completed';
																
															} else if( !isset($feed['Feedback']['start_date']) && $feed['Feedback']['start_date'] == null  ){
																
																$pclst = "bg-undefined";
																$ptooltip = 'Not Set';
																$phovercls = 'prj_undefined';
																
															} else if( date('Y-m-d',strtotime($feed['Feedback']['start_date'])) <= date('Y-m-d') && date('Y-m-d',strtotime($feed['Feedback']['end_date'])) >= date('Y-m-d') ) {
																
																$pclst = "bg-progressing";
																$ptooltip = 'In Progress';
																$phovercls = 'prj_progressing';
																
															} else  if( date('Y-m-d',strtotime($feed['Feedback']['start_date'])) > date('Y-m-d')  ){
																
																$pclst = "bg-not_started";
																$ptooltip = 'Not Started';
																$phovercls = 'prj_not_started';
																
															} else if( date('Y-m-d',strtotime($feed['Feedback']['end_date'])) < date('Y-m-d') ){
																$pclst = "bg-overdue";
																$ptooltip = 'Overdue';
																$phovercls = 'prj_overdue';
															}	
																
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$feed1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$feed['Feedback']['id'].'/resources#feedbacks';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='ps-flag asfeedback mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $feed1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $feed['Feedback']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Feedback" > 
													
													<?php echo	$feed['Feedback']['title']; ?>
													</a>
																			
																			
															
													<?php	echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}												
												
												
	//$allVote = $this->requestAction('/users/votes/'.$elem['id']);
	$allVote = $this->User->votes($elem['id']);
	//pr($allVote);
	if( ( isset($allVote) && !empty($allVote) && !isset($typesArr)  || ( isset($votes) && !empty($votes))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Votes'><i class='".$task_ele_icon."'></i>Votes</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_vt_hid' value='".$elem['id']."'>";
														
															foreach($allVote as $vote){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$pclst = '';
															$ptooltip = 'Not Set';
															$phovercls = '';							 
															if( isset($vote['Vote']['sign_off']) && $vote['Vote']['sign_off'] == 1 ){
																
																$pclst = "bg-completed";
																$ptooltip = 'Completed';
																$phovercls = 'prj_completed';
																
															} else if( !isset($vote['Vote']['start_date']) && $vote['Vote']['start_date'] == null  ){
																
																$pclst = "bg-undefined";
																$ptooltip = 'Not Set';
																$phovercls = 'prj_undefined';
																
															} else if( date('Y-m-d',strtotime($vote['Vote']['start_date'])) <= date('Y-m-d') && date('Y-m-d',strtotime($vote['Vote']['end_date'])) >= date('Y-m-d') ) {
																
																$pclst = "bg-progressing";
																$ptooltip = 'In Progress';
																$phovercls = 'prj_progressing';
																
															} else  if( date('Y-m-d',strtotime($vote['Vote']['start_date'])) > date('Y-m-d')  ){
																 
																$pclst = "bg-not_started";
																$ptooltip = 'Not Started';
																$phovercls = 'prj_not_started';
																
															} else if( date('Y-m-d',strtotime($vote['Vote']['end_date'])) < date('Y-m-d') ){
																$pclst = "bg-overdue";
																$ptooltip = 'Overdue';
																$phovercls = 'prj_overdue';
															}	
																
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
 															
															
															$vote1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$vote['Vote']['id'].'/resources#votes';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='ps-flag asvotes mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $vote1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $vote['Vote']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Vote" > 
													
													<?php echo	$vote['Vote']['title']; ?>
													</a>
																			
																			
															
													<?php	echo 	" </span></li>";
															}
															
													echo "
												</ul>
												</li>
												</ul>";

												}													
												
												
												echo "</li>";		
															
													//}
													
													//}	
												}
											
											}	
													
												echo "
												</ul>
												<!--</li>
												</ul> -->
												</li>";
												//}
								}
										
									/*
									</ul>

									</li>
									 */	
									 
										echo "
										
										</ul>
										</li>";		
											
										?>	
										
										<?php //pr($com);
										//}
										
										//}
										
										}
										} }
										
									 ?><!--</li></ul>-->
									 
									 </li>
									</ul>
									
									 								 
										</li>
										
									<?php 
									
									}
									?>
									
											 
										</ul>
									<!-- </li>
								</ul> --->
								
						   
							</div> 
							
					    </div> 
						
						
						<?php if(!isset($comments) || empty($comments)){?>
						
						<div class="col-sm-12 partial_data box-borders " style="padding: 0 10px 10px 10px;">
							<div class="col-sm-12 box-borders select-project-sec">
								
										<div class="no-data">No Tasks</div>
									
							</div>
						</div>
						
						<?php } ?>
						
						
                    </div>
					
 