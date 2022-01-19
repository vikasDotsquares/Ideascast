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

    <div class="box-body clearfix list-acknowledge" id="mind_maps">
        <div id="list_grid_containers" class="list_grid_containers_tree">
            <ul class="grid clearfix filetree" id="browser" style="display:none">
                <?php
                $row_counter = 0;
                $project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
                ?>
                <?php
                foreach ($projects as $key => $val) {
                    $item = $val['Project'];
                    ?>
                    <?php
                    //$comments = $this->requestAction('/users/workspace/' . $item['id']);
					$comments = $this->User->workspace($item['id']);
                    $report = SITEURL . "projects/index/" . $item['id'];
                    ?>
                    <li class="closed">
                        <span class="folders tipText" title="Open Project">
                            <i class="fa fa-briefcase"></i><a href="<?php echo $report; ?>">
                                <?php echo htmlentities($item['title']); ?></a>
                        </span>
                    <?php }
                    if(isset($links) && !empty($links)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];
                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];

                        /* ?>
                        <ul class="list link">
                            <?php */
							$links_array = null;
                               if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                           /*  ?>
                                            <!-- link start -->

                                            <li class='closed drgP' >
                                                <span class='folders element tipText' title='Links'>
                                                    <i class='fa fa-folder-open'></i>Links
                                                </span>
                                                <ul class='drg'>
                                                    <?php */

                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);


                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];
                                                                if (( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links']) ) ) {
                                                                    //$alldoc = $this->requestAction('/users/links/' . $elem['id']);
																	$alldoc = $this->User->links($elem['id']);
                                                                    // echo "<input type='hidden' name='ele_test' class='elm_hid' value='" . $elem['id'] . "'>";
                                                                    foreach ($alldoc as $doc) {
                                                                        //pr($doc);
                                                                        $chkLinks = explode("//", $doc['ElementLink']['references']);
                                                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move'] == 1 )) {
                                                                            // $classLL = "elm_docP";
                                                                            $classLL = "";
                                                                        } else {
                                                                            $classLL = " ";
                                                                        }

				 						//pr($doc['ElementLink']);
										//pr("filter");
																		if(isset($doc['ElementLink']['link_type']) && $doc['ElementLink']['link_type'] == 2){


                                                                            $links_array[] = "<li class='" . $classLL . "'><i class='fa fa-link'></i> <span class='files column'>" . "<a title='Open Embeded Video' data-target='#modal_medium' data-toggle='modal' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link'   href=" . SITEURL.'entities/play_media/'.$doc['ElementLink']['id'] . ">"  . $doc['ElementLink']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementLink']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementLink']['updated_user_id']) . "</span> ] </a></span></li>";


																		}else{

                                                                        if (isset($chkLinks['1']) && !empty($chkLinks['1'])) {
                                                                            $links_array[] = "<li class='" . $classLL . "'><i class='fa fa-link'></i> <span class='files column'>" . "<a title='Open Link' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link' target='_blank'  href=" . $doc['ElementLink']['references'] . ">" . $doc['ElementLink']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementLink']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementLink']['updated_user_id']) . "</span> ] </a></span></li>";
                                                                        } else {
                                                                            $links_array[] = "<li  class='" . $classLL . "'><i class='fa fa-link'></i> <span class='files column'>" . "<a title='Open Link' id='" . $doc['ElementLink']['id'] . "' class='portlet-headers tipText search_link' target='_blank'  href=" . "http://" . $doc['ElementLink']['references'] . ">" . $doc['ElementLink']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementLink']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementLink']['updated_user_id']) . "</span> ] </a></span></li>";
                                                                        }

																		}
                                                                        // echo " </span></li>";

																	}
																}
															}
														}
													}



                                            /* ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }

                        }
                   /*  ?>
                    </ul>
                    <?php */
                    }
                    }

                    // -- notes --

					$notes_array = null;
                    if(isset($notes) && !empty($notes)){
						foreach ($projects as $key => $val) {
                        $item = $val['Project'];

                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];
                        // pr($comments );
                        /* ?>
                        <ul class="list link">
                            <?php */
                            if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                           /*  ?>
                                            <!-- notes start -->

                                            <li class='closed drgMM'>
                                                <span class='folders element tipText' title='Notes'><i class='fa fa-folder-open'></i>Notes</span>
                                                    <ul class='drgmffggfg'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                                        //$allNOT = $this->requestAction('/users/notes/' . $elem['id']);
														$allNOT = $this->User->notes($elem['id']);

														if ((isset($allNOT) && !empty($allNOT) && !isset($typesArr) ) || ( isset($notes) && !empty($notes))) {

                                                            // echo "<input type='hidden' name='ele_test' class='elm_mm_hid' value='" . $elem['id'] . "'>";
                                                            foreach ($allNOT as $not) {
                                                                $notl = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$not['ElementNote']['id']. '/resources#notes';
                                                                $classmm = " ";

																$notes_array[] = "<li  class='" . $classmm . "'><i class='fa fa-file-text'></i> <span class='files column'>" . '<a href="'.$notl.'" class="portlet-headers tipText" data-id="'.$not['ElementNote']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Note" >'.$not['ElementNote']['title'].'&nbsp;[ Last Update: <span class="text-red">'. _displayDate($not['ElementNote']['modified']).'</span> &nbsp; Updated By: <span class="text-red">'.$this->Common->userFullname($not['ElementNote']['updated_user_id']).'</span> ]</a></span></li>';


                                                                /* ?>
                                                                <a href="<?php echo $notl; ?>" class="portlet-headers   tipText"  data-id="<?php echo $not['ElementNote']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open Note" >
                                                                <?php echo $not['ElementNote']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($not['ElementNote']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($not['ElementNote']['updated_user_id']) . "</span> ]"; ?>
                                                                </a>

                                                           <?php  */
														   }
															// echo "</span></li>";
                                                        }
                                                    }
                                                }
                                            }
                                           /*  ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }

                    /* ?>
                    </ul>
                    <?php */
                    }
                    }
                    //-- Documents ----
					$document_array = null;
                    if(isset($documents) && !empty($documents)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];

                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];
                        // pr($comments );
                       /*  ?>
                        <ul class="list link">
                            <?php */
                           if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                           /*  ?>
                                            <li class='closed docdp'>
                                                <span class='folders element tipText' title='Documents'><i class='fa fa-folder-open'></i>Documents</span>
                                                    <ul class='docdrop'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];



                                                        if (( isset($elemAssets['assets_count']['docs']) && !empty($elemAssets['assets_count']['docs']) && !isset($typesArr) || ( isset($documents) && !empty($documents)))) {

                                                    // echo "<input type='hidden' name='ele_test' class='elm_hid' value='" . $elem['id'] . "'>";
                                                    //$alldoc = $this->requestAction('/users/documents/' . $elem['id']);
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
                                                            if ($ext['1'] == 'txt' || $ext['1'] == 'jpg' || $ext['1'] == 'jpeg' || $ext['1'] == 'png') {
                                                                $icon = "fa-fw fa-eye text-teal";
                                                            } else {
                                                                $icon = "fa-fw fa-download text-yellow";
                                                            }
                                                        }
                                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move'] == 1 )) {
                                                            $class = "docdrag";
                                                        } else {
                                                            $class = " ";
                                                        }
                                                        $docUrl = SITEURL . 'entities/update_element/' . $elem['id'] . '#documents';
                                                        $deleteURL = SITEURL . "/users/deleteDoc/" . $doc['ElementDocument']['id'];

                                                        $document_array[] = "<li class='" . $class . " ' ><i class='fa " . $cla . "'></i> <a href=" . $docUrl . " class='tipText' title='Go To Documents'><span class='files searchD'> &nbsp;&nbsp;" . $doc['ElementDocument']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementDocument']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementDocument']['updated_user_id']) . "</span> ] </span></a></li></span></li>";
                                                        }
                                                        // echo " </span></li>";
                                                        }


                                                    }
                                                }
                                            }
                                           /*  ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }
                    /* ?>
                    </ul>
                    <?php */
                    }
                    }
                    //-- MMs --

					$mm_array = null;
                    if(isset($mms) && !empty($mms)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];

                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];
                        // pr($comments );
                       /*  ?>
                        <ul class="list link">
                            <?php */
                           if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                            /* ?>
                                            <li class='closed drgMM'>
                                                <span class='folders element tipText' title='MMs'><i class='fa fa-folder-open'></i>MMs</span>
                                                <ul class='drgm'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
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
                                                if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (!empty($es['ElementPermission']['permit_move']) && $es['ElementPermission']['permit_move'] == 1 )) {
                                                    $classmm = "elm_mmP";
                                                } else {
                                                    $classmm = " ";
                                                }
                                                $mm_array[] = "<li  class='" . $classmm . "'><i class='fa fa-sitemap'></i> <span class='files column'>".'<a href="'.$mlink.'" class="portlet-headers ddnsa view_mindmapd tipText" data-id="'.$doc['ElementMindmap']['id'].'" data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Mind Map" >'.$doc['ElementMindmap']['title'] . '&nbsp;[ Last Update: <span class="text-red">' . _displayDate($doc['ElementMindmap']['modified']) . '</span> &nbsp; Updated By: <span class="text-red">' . $this->Common->userFullname($doc['ElementMindmap']['updated_user_id']) . '</span> ]</a></span></li>';


												/*
                                                ?>
                                                <a href="<?php echo $mlink; ?>" class="portlet-headers ddnsa view_mindmapd tipText"  data-id="<?php echo $doc['ElementMindmap']['id']; ?>"  data-eid="<?php echo $elem['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Open MindMap" >
                                                <?php echo $doc['ElementMindmap']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($doc['ElementMindmap']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($doc['ElementMindmap']['updated_user_id']) . "</span> ]"; ?>
                                                </a>
                                        <?php */
                                        // echo " </span></li>";
                                    }

                                }


                                                    }
                                                }
                                            }
                                           /*  ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }
                   /*  ?>
                    </ul>
                    <?php */
                    }
                    }
                    //-- decisions --
					 $decision_array = null;
                     if(isset($decisions) && !empty($decisions)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];

                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];
                        //pr($comments );
                        /* ?>
                        <ul class="list link">
                            <?php */
                           if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                           /*  ?>
                                            <li class='closed drgMM'>
                                            <span class='folders element tipText' title='Decision'><i class='fa fa-folder-open'></i>Decision</span>
                                                <ul class='drgmffggfg'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                                       //$allDDCC = $this->requestAction('/users/decision/' . $elem['id']);
													   $allDDCC = $this->User->decision($elem['id']);
                                if (( isset($allDDCC) && !empty($allDDCC) && !isset($typesArr) || ( isset($decisions) && !empty($decisions)))) {

                                    // echo "<input type='hidden' name='ele_test' class='elm_dcs_hid' value='" . $elem['id'] . "'>";
                                    foreach ($allDDCC as $dsc) {
                                        //$chkLinks = explode("//",$doc['ElementMindmap']['references']);
                                        $dsc1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$dsc['ElementDecision']['id']. '/resources#decisions';
                                        $classmm = " ";
                                       $decision_array[] = "<li  class='" . $classmm . "'><i class='far fa-arrow-alt-circle-right'></i> <span class='files column'>". '<a href="'.$dsc1.'" class="portlet-headers tipText"  data-id="'.$dsc['ElementDecision']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Decision" >'. $dsc['ElementDecision']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($dsc['ElementDecision']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($dsc['ElementDecision']['updated_user_id']) . "</span> ] </a></span></li>";
									   ?>

                                        <?php
                                        // echo " </span></li>";
                                    }

                                }




                                                    }
                                                }
                                            }
                                            /* ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }
                   /*  ?>
                    </ul>
                    <?php */
                    }
                    }?>
                    <!-- Feedback -->
                    <?php
					$feedback_array = null;

                     if(isset($feedbacks) && !empty($feedbacks)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];
                        ?>
                        <?php
                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);
                        $report = SITEURL . "projects/reports/" . $item['id'];
                        // pr($comments );
                       /*  ?>
                        <ul class="list link">
                            <?php */
                           if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                            /* ?>
                                            <li class='closed drgMM'>
                                            <span class='folders element tipText' title='Feedback'><i class='fa fa-folder-open'></i>Feedback</span>
                                            <ul class='drgmffggfg'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
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
                                        $feed1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$feed['Feedback']['id']. '/resources#feedbacks';
                                        $classmm = " ";

                                        $feedback_array[] = "<li  class='" . $classmm . "'><i class='fa fa-bullhorn'></i> <span class='files column'>" . '<a href="'.$feed1.'" class="portlet-headers tipText"  data-id="'.$feed['Feedback']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Feedback" >'.$feed['Feedback']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate($feed['Feedback']['modified']) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($feed['Feedback']['updated_user_id']) . "</span> ]</a></span></li>"; ?>

                                        <?php
                                        // echo " </span></li>";
                                    }

                                }




                                                    }
                                                }
                                            }
                                           /*  ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }
                   /*  ?>
                    </ul>
                    <?php */
                    }
                     }?>

                    <!-- Votes -->
                    <?php

					$vote_array = null;
                    if(isset($votes) && !empty($votes)){
                    foreach ($projects as $key => $val) {
                        $item = $val['Project'];
                        ?>
                        <?php
                        //$comments = $this->requestAction('/users/workspace/' . $item['id']);
						$comments = $this->User->workspace($item['id']);

                        $report = SITEURL . "projects/reports/" . $item['id'];
                        // pr($comments );
                      /*   ?>
                        <ul class="list link">
                            <?php */
                            if(!empty($comments)){ //pr($comments);
										foreach($comments as $com){
										if(!empty($com['Workspace'])){
                                        $uuid = $this->Session->read('Auth.User.id');
                                        $pid = $project_id;
                                        $us_permission = $this->Common->userproject($pid, $uuid);
                                        $pr_permission = $this->Common->project_permission_details($pid, $uuid);
                                        if (isset($gpid) && !empty($gpid)) {
                                            $pr_permission = $this->Group->group_permission_details($pid, $gpid);
                                            $ws_permission = $this->Group->group_work_permission_details($pid, $gpid);
                                        }
                                        if (isset($pr_permission) && !empty($pr_permission)) {
                                            $ws_permission = $this->Common->work_permission_details($pid, $uuid);
                                        }
                                        if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ( ((isset($ws_permission) && !empty($ws_permission))) && (in_array($com['Workspace']['id'], $ws_permission)))) {
                                            $d = $this->ViewModel->countAreaElements($com['Workspace']['id']);
                                            $wml = SITEURL . "projects/manage_elements/" . $item['id'] . "/" . $com['Workspace']['id'];
                                            //$allArea = $this->requestAction('/users/area/' . $com['Workspace']['id']);
											$allArea = $this->User->area($com['Workspace']['id']);
                                            /* ?>
                                            <li class='closed drgMM'>
                                                <span class='folders element tipText' title='Votes'><i class='fa fa-folder-open'></i>Votes</span>
                                                    <ul class='drgmffggfg'>
                                                    <?php */
                                                    foreach ($allArea as $area) {
                                                        $areaAssets = $this->ViewModel->countAreaElements(null, $area['Area']['id']);

                                                        foreach ($area['Elements'] as $elem) {

														if($elem['studio_status'] !=1){

                                                            $e_permission = $this->Common->element_permission_details($com['Workspace']['id'], $pid, $uuid);
                                                            if ((isset($gpid) && !empty($gpid))) {
                                                                $e_permission = $this->Group->group_element_permission_details($com['Workspace']['id'], $pid, $gpid);
                                                            }
                                                            if ((!empty($us_permission)) || (isset($project_level) && $project_level == 1) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || ((isset($e_permission) && !empty($e_permission)) && (in_array($elem['id'], $e_permission) == 1) )) {
                                                                $es = $this->Common->element_share_permission($elem['id'], $pid, $uuid);
                                                                if ((isset($gpid) && !empty($gpid))) {
                                                                    $es = $this->Group->group_element_share_permission($elem['id'], $pid, $gpid);
                                                                }
                                                                $elemAssets = $this->ViewModel->countAreaElements(null, null, $elem['id']);
                                                                if (empty($elem['title'])) {
                                                                    $etl = "N/A";
                                                                } else {
                                                                    $etl = $elem['title'];
                                                                }
                                                                $lml = SITEURL . "entities/update_element/" . $elem['id'];

                                                       //$allVote = $this->requestAction('/users/votes/' . $elem['id']);
													   $allVote = $this->User->votes($elem['id']);
                                if (( isset($allVote) && !empty($allVote) && !isset($typesArr) || ( isset($votes) && !empty($votes)))) {

                                    // echo "<input type='hidden' name='ele_test' class='elm_vt_hid' value='" . $elem['id'] . "'>";
                                    foreach ($allVote as $vote) {
                                        $vote1 = SITEURL . 'entities/update_element/' . $elem['id'] .'/'.$vote['Vote']['id']. '/resources#votes';
                                        $classmm = " ";

                                        $vote_array[] = "<li  class='" . $classmm . "'><i class='fa fa-inbox'></i> <span class='files column'>".'<a href="'.$vote1.'" class="portlet-headers tipText"  data-id="'.$vote['Vote']['id'].'"  data-eid="'.$elem['id'].'" data-uid="'.$user_id.'" data-sessionid="'.$session_id.'" title="Open Vote" >'.$vote['Vote']['title'] . "&nbsp;[ Last Update: <span class='text-red'>" . _displayDate(date('Y-m-d h:i:s',$vote['Vote']['modified'])) . "</span> &nbsp; Updated By: <span class='text-red'>" . $this->Common->userFullname($vote['Vote']['updated_user_id']) . "</span> ]</a></span></li>";


                                        // echo " </span></li>";
										}

									}
											}
										}
									}
								}
                                            /* ?>
                                        </ul>
                                    </li>
                                    <?php */
                                    }
                                }
                            }
                        }
                   /*  ?>
                    </ul>
                    <?php */
                    }
                    }

				if( !empty($links_array)) {
					echo "<ul class='list links'><li class='closed' > <span class='folders  tipText' title='Links'> <i class='fa fa-folder-open'></i>Links </span> <ul class=''>";
					foreach($links_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($notes_array)) {
					echo "<ul class='list note'><li class='closed' > <span class='folders element tipText' title='Notes'> <i class='fa fa-folder-open'></i>Notes </span> <ul class=''>";
					foreach($notes_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($document_array)) {
					echo "<ul class='list document'><li class='closed' > <span class='folders element tipText' title='Documents'> <i class='fa fa-folder-open'></i>Documents </span> <ul class=''>";
					foreach($document_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($mm_array)) {
					echo "<ul class='list mind_maps'><li class='closed' > <span class='folders element tipText' title='Mind Maps'> <i class='fa fa-folder-open'></i>Mind Maps </span> <ul class=''>";
					foreach($mm_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($decision_array)) {
					echo "<ul class='list decisions'><li class='closed' > <span class='folders element tipText' title='Decisions'> <i class='fa fa-folder-open'></i>Decisions </span> <ul class=''>";
					foreach($decision_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($feedback_array)) {
					echo "<ul class='list feedbacks'><li class='closed' > <span class='folders element tipText' title='Feedbacks'> <i class='fa fa-folder-open'></i>Feedbacks </span> <ul class=''>";
					foreach($feedback_array as $val ) {
						echo $val;
					}
					echo '</ul></li></ul>';
				}

				if( !empty($vote_array)) {
					echo "<ul class='list votes'><li class='closed' > <span class='folders element tipText' title='Votes'> <i class='fa fa-folder-open'></i>Votes </span> <ul class=''>";
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
</div>
