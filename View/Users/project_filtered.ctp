<div class="box border-top "> 
<?php $flag = 1; ?>
    <div class="box-body clearfix list-acknowledge" id="mind_maps">
        <div id="list_grid_containers" class="list_grid_containers_tree">
            <ul class="grid clearfix filetree" id="browser" style="display:none">
                <?php
                $row_counter = 0;
                $project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
                ?>
                <?php
				//pr($projects);
				
                foreach ($projects as $key => $val) {
                    $item = $val['Project'];

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
	
} else if( $item['start_date'] < date('Y-m-d') && $item['end_date'] >= date('Y-m-d') ) {
	
	$pclst = "bg-progressing";
	$ptooltip = 'In Progress';
	$phovercls = 'prj_progressing';
	
} else  if( $item['start_date'] > date('Y-m-d')  ){
	
	$pclst = "bg-not_started";
	$ptooltip = 'Not Started';
	$phovercls = 'prj_not_started';
	
} else if( $item['end_date'] < date('Y-m-d') ){
	$pclst = "bg-overdue";
	$ptooltip = 'Overdue';
	$phovercls = 'prj_overdue';
}

$prjevidencelink = "";
if( isset($item['sign_off']) && $item['sign_off'] == 1 && $signoffproject == 1 ){				
	
	$prjevidencelink = '<a href="#" data-toggle="modal" data-target="#prj_signoff_comment_show" data-remote="'.SITEURL.'projects/show_signoff/'.$item['id'].'" class="signoff_evidence" ><span class="folders"><i title="Click to see Comment and Evidence" class="ps-flag prj-signoff wsp_flagbg '. $pclst .'"></i></a>'; 
	
} else {
	$prjevidencelink = '<i title="'.$ptooltip.'" class="ps-flag tipText wsp_flagbg '. $pclst .'"></i>';
}					
					
                    ?>
                    <?php
                     
					//$comments = $this->ViewModel->get_projectWorkspace($item['id']);
                    $report = SITEURL . "projects/index/" . $item['id'];
                    ?>
                    <li class="closed">
                        <span class="folders "><i class="<?php echo $proejct_icon;?>"></i><?php echo $prjevidencelink;?><a href="<?php echo $report; ?>"><span class="tipText" title="Open Project"><?php echo htmlentities($item['title']); ?></span></span></a>
                    <?php }

                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];
                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->ViewModel->get_projectWorkspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];

                        /* ?>
                        <ul class="list link">
                            <?php */
							$links_array = null;
							$notes_array = null;
							$document_array = null;
							$mm_array = null;
							$decision_array = null;							
							$feedback_array = null;
							$vote_array = null;							
					
                               if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        
                                 
                                          
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->areas($com['Workspace']['id']);
                                           /*  ?>
                                            <!-- link start -->

                                            <li class='closed drgP' >
                                                <span class='folders element tipText' title='Links'>
                                                    <i class='fa fa-folder-open'></i>Links
                                                </span>
                                                <ul class='drg'>
                                                    <?php */

                                                    foreach ($allArea as $area) {
                                                        
														$tasks = $this->ViewModel->getAreaTaskAsset($com['Workspace']['id'], $area['Area']['id'], $statusArr);
														
                                                        foreach ($tasks as $elems) {
															
															$elem = $elems['Element'];
															 
                                                                 $elemAssets['assets_count'] = $elems[0]; 
													 
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];
													
                    if(isset($links) && !empty($links)){													
														
                                                                if (( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links']) ) ) {
                                                                    //$alldoc = $this->requestAction('/users/links/' . $elem['id']);
																	
																	
																	$alldoc = $this->User->links($elem['id']);
                                                                    // echo "<input type='hidden' name='ele_test' class='elm_hid' value='" . $elem['id'] . "'>";
                                                                    foreach ($alldoc as $doc) {
                                                                        //pr($doc);
                                                                        $chkLinks = explode("//", $doc['ElementLink']['references']);
                                                                        
																		if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){ 
                                                                            // $classLL = "elm_docP";
                                                                            $classLL = "";
                                                                        } else {
                                                                            $classLL = " ";
                                                                        }

				 						//pr($doc['ElementLink']);
										//pr("filter");
																		if(isset($doc['ElementLink']['link_type']) && $doc['ElementLink']['link_type'] == 2){


                                                                            $links_array[] = "<li class='" . $classLL . "'><i class='ps-flag aslink mr2'></i> <span class='files column'>" . "<a title='Open Embeded Video' data-target='#modal_medium' data-toggle='modal' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link'   href=" . SITEURL.'entities/play_media/'.$doc['ElementLink']['id'] . ">"  . htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8") . "11111</a></span></li>";


																		}else{

                                                                        if (isset($chkLinks['1']) && !empty($chkLinks['1'])) {
                                                                            $links_array[] = "<li class='" . $classLL . "'><i class='ps-flag aslink mr2'></i> <span class='files column'>" . "<a title='Open Link' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link' target='_blank'  href=" . $doc['ElementLink']['references'] . ">" . htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8") . " 222222</a></span></li>";
                                                                        } else {
                                                                            $links_array[] = "<li  class='" . $classLL . "'><i class='ps-flag aslink mr2'></i> <span class='files column'>" . "<a title='Open Link' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link' target='_blank'  href=" . "http://" . $doc['ElementLink']['references'] . ">" . htmlentities($doc['ElementLink']['title'], ENT_QUOTES, "UTF-8") . " 33333</a></span></li>";
                                                                        }

																		}
                                                                        // echo " </span></li>";

																	}
																}
																
					}		
																
																
																
					/* notes loop start*/											
					/* notes loop end*/		
					
					
					/* docs loop start */											
					/* docs loop end */	
					
					/* mm loop start*/											
					/* mm loop end*/						
										

					/* decision loop start*/											
					/* decision loop end*/					
																
					/* feedback loop start*/											
					/* feedback loop end*/										 
														
					/* Votes loop start*/											
					/* Votes loop end*/															
											


                    if(isset($notes) && !empty($notes)){
						
												 
														$allNOT = $this->User->notes($elem['id']);

														if ((isset($allNOT) && !empty($allNOT) && !isset($typesArr) ) || ( isset($notes) && !empty($notes))) {

                                                            // echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='" . $elem['id'] . "'>";
                                                            foreach ($allNOT as $not) {
                                                                $notl = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$not['ElementNote']['id']. '/resources#notes';
                                                                $classmm = " ";

																$notes_array[] = "<li  class='" . $classmm . "'><i class='ps-flag asnotes mr2'></i> <span class='files column'>" . '<a href="'.$notl.'" class="portlet-headers tipText" data-id="'.$not['ElementNote']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Note" >'.$not['ElementNote']['title'].'</a></span></li>';


                                                                 
														   }
															 
                                                        }
														
                                                     
                    }
                    //-- Documents ----

                    if(isset($documents) && !empty($documents)){
                    
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
																
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];



                                                        if (( isset($elemAssets['assets_count']['docs']) && !empty($elemAssets['assets_count']['docs']) && !isset($typesArr) || ( isset($documents) && !empty($documents)))) {

                                                     
													$alldoc = $this->User->documents($elem['id']);
                                                    foreach ($alldoc as $doc) {
                                                        $cla = "";
                                                        $icon = "";
                                                        $ext = explode(".", $doc['ElementDocument']['file_name']);
                                                        if (isset($ext['1']) && !empty($ext['1'])) {
                                                            if ($ext['1'] == 'txt') {
                                                                $cla = "fa-file-text-o";
                                                            } else if ($ext['1'] == 'jpg' || $ext['1'] == 'jpeg' || $ext['1'] == 'png') {
                                                                $cla = "fa-folder-o";
                                                            } else if ($ext['1'] == 'pdf') {
                                                                $cla = "fa-file-pdf-o";
                                                            } else if ($ext['1'] == 'xls' || $ext['1'] == 'xlsx') {
                                                                $cla = "fa-file-excel-o";
                                                            } else if ($ext['1'] == 'doc' || $ext['1'] == 'docx') {
                                                                $cla = "fa-file-word-o";
                                                            } else if ($ext['1'] == 'ppt' || $ext['1'] == 'pptx') {
                                                                $cla = "fa-file-powerpoint-o";
                                                            } else {
                                                                $cla = "fa-file-o";
                                                            }
															
															$cla ="ps-flag asdocument mr2";
															
                                                            if ($ext['1'] == 'txt' || $ext['1'] == 'jpg' || $ext['1'] == 'jpeg' || $ext['1'] == 'png') {
                                                                $icon = "fa-fw fa-eye text-teal";
                                                            } else {
                                                                $icon = "fa-fw fa-download text-yellow";
                                                            }
                                                        }
                                                        if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){ 
                                                            $class = "docdrag";
                                                        } else {
                                                            $class = " ";
                                                        }
                                                        $docUrl = SITEURL . 'entities/update_element/' . $elem['id'] . '#documents';
                                                        $deleteURL = SITEURL . "/users/deleteDoc/" . $doc['ElementDocument']['id'];

                                                        $document_array[] = "<li class='" . $class . " ' ><i class=' " . $cla . "'></i> <a href=" . $docUrl . " class='tipText' title='Go To Documents'><span class='files searchD'> &nbsp;&nbsp;" . $doc['ElementDocument']['title'] . "</span></a></li></span></li>";
                                                        }
                                                       
                                                        }

 
                    }
                    //-- MMs --


                    if(isset($mms) && !empty($mms)){
						 
                  
                                                                 
                                                                
																 $elemAssets['assets_count'] = $elems[0]; 
													 
																
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];



                                        //$alldoc = $this->requestAction('/users/mms/' . $elem['id']);
										$alldoc = $this->User->mms($elem['id']);

                                        if (( isset($alldoc) && !empty($alldoc) && !isset($typesArr) || ( isset($mms) && !empty($mms)))) {

                                            // echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='" . $elem['id'] . "'>";
                                            foreach ($alldoc as $doc) {
                                                $mlink = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$doc['ElementMindmap']['id']. '/resources#mind_maps';
                                                if( (!empty($elems['user_permissions']['permit_move']) && $elems['user_permissions']['permit_move']==1 )){ 
                                                    $classmm = "elm_mmP";
                                                } else {
                                                    $classmm = " ";
                                                }
                                                $mm_array[] = "<li  class='" . $classmm . "'><i class='ps-flag asmindmap mr2'></i> <span class='files column'>".'<a href="'.$mlink.'" class="portlet-headers ddnsa view_mindmapd tipText" data-id="'.$doc['ElementMindmap']['id'].'" data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Mind Map" >'.$doc['ElementMindmap']['title'] . ' </a></span></li>';
 
                                    }

                                }


                                                  
                   
                    }
					
					
                    //-- decisions --

                     if(isset($decisions) && !empty($decisions)){
						 
                    
                                                                 $elemAssets['assets_count'] = $elems[0]; 
													 
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                                       
													   $allDDCC = $this->User->decision($elem['id']);
                                if (( isset($allDDCC) && !empty($allDDCC) && !isset($typesArr) || ( isset($decisions) && !empty($decisions)))) {

                                    // echo "<input type='hidden' name='ele_test' class='elm_dcs_hid' value='" . $elem['id'] . "'>";
                                    foreach ($allDDCC as $dsc) {
                                        //$chkLinks = explode("//",$doc['ElementMindmap']['references']);
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
										
										
                                        $dsc1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$dsc['ElementDecision']['id']. '/resources#decisions';
                                        $classmm = " ";
                                       $decision_array[] = "<li  class='" . $classmm . "'><i class='ps-flag asdecisions mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>". '<a href="'.$dsc1.'" class="portlet-headers tipText"  data-id="'.$dsc['ElementDecision']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Decision" >'. $dsc['ElementDecision']['title'] . "  </a></span></li>";
									   ?>

                                        <?php
                                        
                                    }

                                }




                                         
                    }
					?>
                    <!-- Feedback -->
                    <?php


                     if(isset($feedbacks) && !empty($feedbacks)){
                     
                                                 
                                                                 $elemAssets['assets_count'] = $elems[0]; 
													 
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                        //$allFeed = $this->requestAction('/users/feedbacks/' . $elem['id']);
										$allFeed = $this->User->feedbacks($elem['id']);

                                if (( isset($allFeed) && !empty($allFeed) && !isset($typesArr) || ( isset($feedbacks) && !empty($feedbacks)))) {

                                    // echo "<input type='hidden' name='ele_test' class='elm_ff_hid' value='" . $elem['id'] . "'>";
                                    foreach ($allFeed as $feed) {
                                        //$chkLinks = explode("//",$doc['ElementMindmap']['references']);
										
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
										
                                        $feed1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$feed['Feedback']['id']. '/resources#feedbacks';
                                        $classmm = " ";

                                        $feedback_array[] = "<li  class='" . $classmm . "'><i class='ps-flag asfeedback mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>" . '<a href="'.$feed1.'" class="portlet-headers tipText"  data-id="'.$feed['Feedback']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Feedback" >'.$feed['Feedback']['title'] . " </a></span></li>"; ?>

                                        <?php
                                        
                                    }

                                }


 
                     }
					 ?>

                    <!-- Votes -->
                    <?php


                    if(isset($votes) && !empty($votes)){
					 
                     
                                                                 $elemAssets['assets_count'] = $elems[0]; 
													 
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                                       
													   $allVote = $this->User->votes($elem['id']);
                                if (( isset($allVote) && !empty($allVote) && !isset($typesArr) || ( isset($votes) && !empty($votes)))) {

                                    // echo "<input type='hidden' name='ele_test' class='elm_vt_hid' value='" . $elem['id'] . "'>";
                                    foreach ($allVote as $vote) {
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
										
                                        $vote1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$vote['Vote']['id']. '/resources#votes';
                                        $classmm = " ";

                                        $vote_array[] = "<li  class='" . $classmm . "'><i class='ps-flag asvotes mr2'></i><i title='".$ptooltip."' class='ps-mr0 ps-flag tipText wsp_flagbg ". $pclst ."'></i><span class='files column'>".'<a href="'.$vote1.'" class="portlet-headers tipText"  data-id="'.$vote['Vote']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Vote" >'.$vote['Vote']['title'] . " </a></span></li>";


                                        
										}

									}
											 
										 
                    }

											
										/* end loops assets  */				
														
														
														
														
													}

 
                                    
                                }
                            }

                        }
                   
                    }
                    }

                    // -- notes --

					
				if( !empty($links_array)) {
						$flag = 2;	
					echo "<ul class='list links'><li class='closed' > <span class='folders  tipText' title='Links'> <i class='".$task_ele_icon."'></i>Links </span> <ul class=''>";
					foreach($links_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($notes_array)) {
					$flag = 2;	
					echo "<ul class='list note'><li class='closed' > <span class='folders element tipText' title='Notes'> <i class='".$task_ele_icon."'></i>Notes </span> <ul class=''>";
					foreach($notes_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($document_array)) {
					$flag = 2;	
					echo "<ul class='list document'><li class='closed' > <span class='folders element tipText' title='Documents'> <i class='".$task_ele_icon."'></i>Documents </span> <ul class=''>";
					foreach($document_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($mm_array)) {
					$flag = 2;	
					echo "<ul class='list mind_maps'><li class='closed' > <span class='folders element tipText' title='Mind Maps'> <i class='".$task_ele_icon."'></i>Mind Maps </span> <ul class=''>";
					foreach($mm_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($decision_array)) {
					$flag = 2;	
					echo "<ul class='list decisions'><li class='closed' > <span class='folders element tipText' title='Decisions'> <i class='".$task_ele_icon."'></i>Decisions </span> <ul class=''>";
					foreach($decision_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($feedback_array)) {
					$flag = 2;	
					echo "<ul class='list feedbacks'><li class='closed' > <span class='folders element tipText' title='Feedbacks'> <i class='".$task_ele_icon."'></i>Feedbacks </span> <ul class=''>";
					foreach($feedback_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($vote_array)) {
					$flag = 2;	
					echo "<ul class='list votes'><li class='closed' > <span class='folders element tipText' title='Votes'> <i class='".$task_ele_icon."'></i>Votes </span> <ul class=''>";
					foreach($vote_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}
				?>

            </li>

            </ul>
        </div>
    </div>
	
	
	<?php 
	 
	if(($flag==1)){?>
						
						<div class="col-sm-12 partial_data box-borders " style="padding: 0 10px 10px 10px;">
							<div class="col-sm-12 box-borders select-project-sec">
								
										<div class="no-data">No Assets</div>
									
							</div>
						</div>
						
						<?php } ?>
	
</div>
 