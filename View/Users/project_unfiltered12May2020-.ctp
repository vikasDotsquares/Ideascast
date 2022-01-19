                    <div class="box border-top ">
                        <div class="box-header" style=" ">
                            
							 
							
							
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							
                        </div>
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
									?>
									<?php
									    
									    //$comments = $this->requestAction('/users/workspace/'.$item['id']);
									    $comments = $this->User->workspace($item['id']);
										pr($comments);
										
										$report = SITEURL."projects/index/".$item['id']; 
										
										//pr($comments['ProjectWorkspace']);
										?>
									<li class="closed"> 
											<span class="folders tipText" title="Open Project"><i class="fa fa-briefcase"></i><a href="<?php echo $report; ?>"> <?php echo htmlentities($item['title']); ?></span></a>
										
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
										 
										$us_permission = $this->Common->userproject($pid,$uuid);
										$pr_permission =  $this->Common->project_permission_details($pid,$uuid);
										//$ws_permission =  $this->Common->work_permission_details($pid,$uuid);
										
											if(isset($gpid) && !empty($gpid)){
											    
												$pr_permission = $this->Group->group_permission_details($pid, $gpid);
												$ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
												//pr($wwsid); die;
											}

											if(isset($pr_permission) && !empty($pr_permission))
											{
												$ws_permission = $this->Common->work_permission_details($pid,$uuid);
											}
										
										// pr($ws_permission);
										 
										//pr($ws_permission);
										
										if((!empty($us_permission)) || (isset($project_level) && $project_level==1)  ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || ( ((isset($ws_permission) && !empty($ws_permission))) &&  (in_array($com['Workspace']['id'],$ws_permission)))   ){
										
										
										$d =	$this->ViewModel->countAreaElements($com['Workspace']['id']);
										
										
										
										$wml = SITEURL."projects/manage_elements/".$item['id']."/".$com['Workspace']['id']; 
										
										
										
							//	if( $d['active_element_count'] > 0 && (( isset($d['assets_count']['docs']) && !empty($d['assets_count']['docs']) ) || ( isset($d['assets_count']['links']) && !empty($d['assets_count']['links']) )) ) {
								
								
										echo '<li class="closed closed_sear"><a href="'.$wml.'"><span class="folders tipText" title="Open Workspace"><i class="fa fa-th"></i> '.htmlentities($this->Common->workspaceName($com['Workspace']['id'])).'</span></a>'; //die;
										
												//$allArea = $this->Common->area($com['workspace_id']);
										//$allArea = $this->requestAction('/users/area/'.$com['Workspace']['id']);
										$allArea = $this->User->area($com['Workspace']['id']);
										//pr($allArea); 
										
										/* <ul>
										<li class='closed'>
									     <span class='folder'>Area</span> */
										
										
										echo "
										
										<ul>";
												foreach($allArea as $area){
													$areaAssets =	$this->ViewModel->countAreaElements(null, $area['Area']['id']);
												//	if( $areaAssets['element_count'] > 0 && (( isset($areaAssets['assets_count']['docs']) && !empty($areaAssets['assets_count']['docs']) ) || ( isset($areaAssets['assets_count']['links']) && !empty($areaAssets['assets_count']['links']) ) )) {
													
												echo "<li class='closed'><span class='folders tipText' title='Area'><i class='fa fa fa-list-alt'></i>  ".htmlentities($area['Area']['title'])."</span>
												<!--<ul>
												
												<li class='closed '>
														<span class='folder elm_doc'>Element</span> -->
												<ul>";
	
													foreach($area['Elements'] as $elem){
													
													if($elem['studio_status'] !=1){
													
													 $e_permission = $this->Common->element_permission_details($com['Workspace']['id'],$pid,$uuid);
													 
													 
													
													 if((isset($gpid) && !empty($gpid))){									
													$e_permission =  $this->Group->group_element_permission_details( $com['Workspace']['id'], $pid, $gpid);										 
													}
													// pr($e_permission,1); 
													 
													if( (!empty($us_permission))  || (isset($project_level) && $project_level==1)  ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'] ,$e_permission) == 1) )){
													 
												 
													$es  = $this->Common->element_share_permission($elem['id'],$pid,$uuid);
													
													if((isset($gpid) && !empty($gpid))){		
													$es  = $this->Group->group_element_share_permission($elem['id'],$pid,$gpid);
													}
													 
													$elemAssets =	$this->ViewModel->countAreaElements(null, null, $elem['id']);
													//	if( ( isset($elemAssets['assets_count']['docs']) && !empty($elemAssets['assets_count']['docs'] ) ) || ( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links'] ) ) ) {	
															
													if(empty($elem['title'])){
													$etl = "N/A";
													}else{
													$etl = $elem['title'];
													}
													
													$lml = SITEURL."entities/update_element/".$elem['id']; 
													
													echo "<li class='closed '>
														<a href=".$lml."><span class='folders  elm_doc tipText' title='Open Task'><i class='icon_element_add_black'></i>  ".htmlentities($etl)."</span></a>";
									
											if( ( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links'] ) ) && !isset($typesArr)  || ( isset($links) && !empty($links))  ) {												
										
													echo "<ul>
														<li class='closed drgP' >
														<span class='folders element tipText' title='Links'><i class='fa fa-folder-open'></i>Links</span>
														<ul class='drg'>";
														//$alldoc = $this->requestAction('/users/links/'.$elem['id']);
														$alldoc = $this->User->links($elem['id']);
														
														echo "<input type='hidden' name='ele_test' class='elm_hid' value='".$elem['id']."'>";
														
															foreach($alldoc as $doc){
															
															$chkLinks = explode("//",$doc['ElementLink']['references']);
															
															//pr($chkLinks); 
															
															if( (!empty($us_permission)) || (isset($project_level) && $project_level==1)   ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move']==1 )){ 
													 
																$classLL ="elm_docP";
															 
															}else{
																$classLL =" ";
															}
															//pr($doc['ElementLink']);	
															 	
															if(isset($doc['ElementLink']['link_type']) && $doc['ElementLink']['link_type'] == 2){
															
																		 
																echo "<li class='" . $classLL . "'><i class='fa fa-link'></i> <span class='files column'>" . "<a title='Open Link' data-target='#modal_medium' data-toggle='modal' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link'   href=" . SITEURL.'entities/play_media/'.$doc['ElementLink']['id'] . ">"  . $doc['ElementLink']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementLink']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementLink']['updated_user_id']) . "</span> ] </a></span></li>"; 
                                                                        
																		
															}else{	
															
															if(isset($chkLinks['1']) && !empty($chkLinks['1'])){
															 echo "<li class='".$classLL."'><i class='fa fa-link'></i> <span class='files column'>"."<a title='Open Link' id='".$doc['ElementLink']['id']."' class='portlet-headers tipText search_link' target='_blank'  href=".$doc['ElementLink']['references'].">". $doc['ElementLink']['title']."  [ Last Update: <span class='text-red'>"._displayDate($doc['ElementLink']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($doc['ElementLink']['updated_user_id'])."</span> ] </a>";
															}else{
															 
															 echo "<li  class='".$classLL."'><i class='fa fa-link'></i> <span class='files column'>"."<a title='Open Link' id='".$doc['ElementLink']['id']."' class='portlet-headers tipText search_link' target='_blank'   href="."http://".$doc['ElementLink']['references'].">". $doc['ElementLink']['title']."  [ Last Update: <span class='text-red'>"._displayDate($doc['ElementLink']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($doc['ElementLink']['updated_user_id'])."</span> ] </a>";
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
														<span class='folders element tipText' title='Notes'><i class='fa fa-folder-open'></i>Notes</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='".$elem['id']."'>";
														
															foreach($allNOT as $not){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$notl = SITEURL.'entities/update_element/'.$elem['id'].'/'.$not['ElementNote']['id'].'/resources#notes';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='fa fa-file-text'></i> <span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $notl; ?>" class="portlet-headers   tipText"  data-id="<?php echo $not['ElementNote']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Note" > 
													
													<?php echo	$not['ElementNote']['title']."  [ Last Update: <span class='text-red'>"._displayDate($not['ElementNote']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($not['ElementNote']['updated_user_id'])."</span> ]"; ?>
													
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
														<span class='folders element tipText' title='Documents'><i class='fa fa-folder-open'></i>Documents</span>
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
															
													if( (!empty($us_permission)) || (isset($project_level) && $project_level==1)   ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move']==1 )){ 
													 
														$class ="docdrag";
													 
													}else{
														$class =" ";
													}
															//pr($us_permission);
															 //pr($es);
															//pr($pr_permission['ProjectPermission']);									

															$mBNlink = SITEURL.'entities/update_element/'.$elem['id'].'/'.$doc['ElementDocument']['id'].'/resources#documents';
															
															 echo "<li class='".$class." '><i class='fa ".$cla."'></i> <a  class='ddn tipText' title='Go To Documents'  rel='".$elem['id']."'  id='".$doc['ElementDocument']['id']."'  href=".$mBNlink."> <span class='files searchD'> &nbsp;&nbsp;".$doc['ElementDocument']['title'] ;
												
                                        			 					
										$deleteURL = SITEURL."/users/deleteDoc/".$doc['ElementDocument']['id']; 
										if( (!empty($us_permission)) || (isset($project_level) && $project_level==1)   ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || (!empty($es['ElementPermission']['permit_edit']) && $es['ElementPermission']['permit_edit']==1 )){
										?>
										
										<!--<a data-toggle="modal" class="RecordDeleteClass tipText" data-target="#deleteBox" rel="<?php echo $doc['ElementDocument']['id']; ?>"   title="Delete document" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o text-maroon"></i></a>
										-->
										
										<?php 
										}else{ ?>
										
										<?php  }?>
															
													<?php	echo	" [ Last Update: <span class='text-red'>"._displayDate($doc['ElementDocument']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($doc['ElementDocument']['updated_user_id'])."</span> ] </span></a></li>";
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
														<span class='folders element tipText' title='Mind Maps'><i class='fa fa-folder-open'></i>Mind Maps</span>
														<ul class='drgm'>";
														//$alldoc = $this->requestAction('/users/mms/'.$elem['id']);
														
														echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='".$elem['id']."'>";
														
															foreach($alldoc as $doc){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$mlink = SITEURL.'entities/update_element/'.$elem['id'].'/'.$doc['ElementMindmap']['id'].'/resources#mind_maps';
															
															
															//pr($chkLinks); 
															
															//
															if( (!empty($us_permission)) || (isset($project_level) && $project_level==1)   ||  (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] ==1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move']==1 )){ 
													 
																$classmm ="elm_mmP";
															 
															}else{
																$classmm =" ";
															}
															
															 echo "<li  class='".$classmm."'><i class='fa fa-sitemap'></i> <span class='files column'>";
															
													?>
													<!--<a href="#" class="portlet-headers ddnsa view_mindmap" data-remote="	<?php echo Router::Url(array('controller' => 'entities', 'action' => 'view_mindmap', $elem['id'], $doc['ElementMindmap']['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $doc['ElementMindmap']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open MindMap" > -->
													
													<a href="<?php echo $mlink; ?>" class="portlet-headers ddnsa view_mindmapd tipText"  data-id="<?php echo $doc['ElementMindmap']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Mind Map" > 
													
																			<?php echo	$doc['ElementMindmap']['title']."  [ Last Update: <span class='text-red'>"._displayDate($doc['ElementMindmap']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($doc['ElementMindmap']['updated_user_id'])."</span> ]"; ?>
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
														<span class='folders element tipText' title='Decision'><i class='fa fa-folder-open'></i>Decision</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_dcs_hid' value='".$elem['id']."'>";
														
															foreach($allDDCC as $dsc){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$dsc1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$dsc['ElementDecision']['id'].'/resources#decisions';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='far fa-arrow-alt-circle-right'></i> <span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $dsc1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $dsc['ElementDecision']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Decision" > 
													
													<?php echo	$dsc['ElementDecision']['title']."  [ Last Update: <span class='text-red'>"._displayDate($dsc['ElementDecision']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($dsc['ElementDecision']['updated_user_id'])."</span> ]"; ?>
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
	
	if( ( isset($allFeed) && !empty($allFeed) && !isset($typesArr)  || ( isset($feedbacks) && !empty($feedbacks))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Feedback'><i class='fa fa-folder-open'></i>Feedback</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_ff_hid' value='".$elem['id']."'>";
														
															foreach($allFeed as $feed){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$feed1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$feed['Feedback']['id'].'/resources#feedbacks';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='fa fa-bullhorn'></i> <span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $feed1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $feed['Feedback']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Feedback" > 
													
													<?php echo	$feed['Feedback']['title']."  [ Last Update: <span class='text-red'>"._displayDate($feed['Feedback']['modified'])."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($feed['Feedback']['updated_user_id'])."</span> ]"; ?>
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
	
	if( ( isset($allVote) && !empty($allVote) && !isset($typesArr)  || ( isset($votes) && !empty($votes))) ) {												
										
													echo "<ul>
												

														<li class='closed drgMM'>
														<span class='folders element tipText' title='Votes'><i class='fa fa-folder-open'></i>Votes</span>
														<ul class='drgmffggfg'>";
												 
														
														echo "<input type='hidden' name='ele_test' class='elm_vt_hid' value='".$elem['id']."'>";
														
															foreach($allVote as $vote){
															
															//$chkLinks = explode("//",$doc['ElementMindmap']['references']);
															
															$vote1 = SITEURL.'entities/update_element/'.$elem['id'].'/'.$vote['Vote']['id'].'/resources#votes';
															
															
															
																$classmm =" ";
															
															
															 echo "<li  class='".$classmm."'><i class='fa fa-inbox'></i> <span class='files column'>";
															
													?>
													 
													
													<a href="<?php echo $vote1; ?>" class="portlet-headers   tipText"  data-id="<?php echo $vote['Vote']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Vote" > 
													
													<?php echo	$vote['Vote']['title']."  [ Last Update: <span class='text-red'>"._displayDate(date('y-m-d h:i:s',$vote['Vote']['modified']))."</span> &nbsp; Updated By: <span class='text-red'>".$this->Common->userFullname($vote['Vote']['updated_user_id'])."</span> ]"; ?>
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
													
													}	
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
										
										}
										
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
                    </div>
					
					