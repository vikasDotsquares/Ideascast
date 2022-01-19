
<?php
    $project_detail = getByDbId("Project", $project_id, ['id', 'title', 'start_date', 'end_date', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $currency_symbol = 'GBP';
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id',  'name',  'sign']);
        $currency_detail = $currency_detail['Currency'];
        $currency_symbol = $currency_detail['sign'];
        // pr($currency_detail);
    }
    /*if($currency_symbol == 'USD') {
        $currency_symbol = '<i class="fa fa-dollar"></i>';
    }
    else if($currency_symbol == 'GBP') {
        $currency_symbol = '<i class="fa fa-gbp"></i>';
    }
    else if($currency_symbol == 'EUR') {
        $currency_symbol = '<i class="fa fa-eur"></i>';
    }
    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
        $currency_symbol = '<span style="font-weight: 600">Kr</span>';
    }*/
    // pr($currency_symbol);
    $prj_start_date = (isset($project_detail['start_date']) && !empty($project_detail['start_date'])) ? date("d M, Y", strtotime($project_detail['start_date'])) : 'Not Set';
    $prj_end_date = (isset($project_detail['end_date']) && !empty($project_detail['end_date'])) ? date("d M, Y", strtotime($project_detail['end_date'])) : 'Not Set';

    $project_wsps = get_project_workspace($project_id);
    $workspaces = Set::extract($project_wsps, '/Workspace/id');
    $all_workspace_elements = workspace_elements($workspaces);
    $westimate_sum = $wspend_sum = 0;
    if(isset($all_workspace_elements) && !empty($all_workspace_elements)){
        $wels = Set::extract($all_workspace_elements, '/Element/id');
        $westimate_sum = $this->ViewModel->wsp_element_cost($wels, 1);
        $wspend_sum = $this->ViewModel->wsp_element_cost($wels, 2);
    }
    // pr($westimate_sum);
    // pr($wspend_sum);
?>
<div id="list_grid_containers" class="list_grid_containers_tree">
    <div class="tree-heading-wrap">
        <div class="heading-left-sec">
            <span class="title-heading estimates-tital">Project Work</span>
            <span class="title-heading spending-tital">Schedule</span>
        </div>
        <div class="heading-right-sec">
			<div class="col-sm-6">
            <span class="title-heading estimates-tital">Budget (<?php echo $currency_symbol; ?>)</span> </div>
          <div class="col-sm-6">  <span class="title-heading spending-tital">Actual (<?php echo $currency_symbol; ?>)</span></div>
        </div>
    </div>
    <ul id="pptree">
        <li class="project-li">
            <span class="folder">
               <span class="left-sec">
                <i class="costbriefcase"></i>
                <span class="prj title-text">
                    <a class="tipText" title="Project" href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $project_id, 'admin' => FALSE ), true ); ?>"><?php echo(htmlentities($project_detail['title'])); ?></a>
                </span>
                <span class="prj date-text">
                    <?php
					if(isset($prj_start_date) && $prj_start_date=='Not Set'){
						echo "Not Set";
					}else{
					echo($prj_start_date); ?> → <?php echo($prj_end_date);
					}
					?>
                </span>
            </span>
            <?php

                    $prjCountEstimatedComment = $this->Common->getProjectCostComment($project_detail['id'], 1);
                    $estimatedAnnotationClassPrj = 'annotation-grey';
                    if( isset($prjCountEstimatedComment) ){
                        if($prjCountEstimatedComment > 0){
                          $estimatedAnnotationClassPrj = 'annotation-black';
                        } else {
                          $estimatedAnnotationClassPrj = 'annotation-grey';
                        }

                    }
                    $prjCountSpendComment = $this->Common->getProjectCostComment($project_detail['id'], 2);
                    $spendAnnotationClassPrj = 'annotation-grey';
                    if( isset($prjCountSpendComment) ){
                        if($prjCountSpendComment > 0){
                          $spendAnnotationClassPrj = 'annotation-black';
                        } else {
                          $spendAnnotationClassPrj = 'annotation-grey';
                        }
                    }
             ?>
                <span class="right-sec estimates-spending-sec">
                    <div class="col-sm-6 wsp-col-first">
                        <div class="input-group doller-sec wsp-disabled tipText" title="Total Project Budget">
                            <!-- <span class="input-group-addon"><?php echo($currency_symbol) ?></span> -->
                            <input type="text" class="form-control total-estimated readonly" value="<?php echo (isset($westimate_sum) && !empty($westimate_sum)) ? number_format($westimate_sum, 2, '.', ',') : '0.00'; ?>">
                        </div>
                        <span data-pid="<?php echo $project_detail['id']; ?>" data-costype="1"  class="annotation <?php echo $estimatedAnnotationClassPrj; ?> tipText" title="" data-toggle="modal" data-target="#model_bx_project" data-remote="<?php echo Router::Url( array( "controller" => "costs", "action" => "add_project_annotate", 1, $project_detail['id'], 'admin' => FALSE ), true ); ?>" data-original-title="Annotations" style="margin-left: 34px"></span>
                     </div>
                    <div class="col-sm-6 wsp-col-sec">
                        <div class="input-group doller-sec wsp-disabled tipText" title="Total Project Actuals">
                            <!-- <span class="input-group-addon"><?php echo($currency_symbol) ?></span> -->
                            <input type="text" class="form-control total-spend readonly" value="<?php echo (isset($wspend_sum) && !empty($wspend_sum)) ? number_format($wspend_sum, 2, '.', ',') : '0.00'; ?>">
                        </div>
                        <span data-pid="<?php echo $project_detail['id']; ?>" data-costype="2" class="annotation <?php echo $spendAnnotationClassPrj; ?> tipText" title="" data-toggle="modal" data-target="#model_bx_project" data-remote="<?php echo Router::Url( array( "controller" => "costs", "action" => "add_project_annotate", 2, $project_detail['id'], 'admin' => FALSE ), true ); ?>" data-original-title="Annotations" style="margin-left: 34px"></span>
                    </div>
                 </span>
            </span>
            <?php
            $estimate_sum = $spend_sum = 0;

            if(isset($project_wsps) && !empty($project_wsps)){ ?>
            <ul id="project_tree" class="filetree">
                <?php
                foreach ($project_wsps as $wsp_key => $wsp_val) {
                    $wsp_data = $wsp_val['Workspace'];
                    $wsp_start_date = (isset($wsp_data['start_date']) && !empty($wsp_data['start_date'])) ? date("d M, Y", strtotime($wsp_data['start_date'])) : 'Not Set';
                    $wsp_end_date = (isset($wsp_data['end_date']) && !empty($wsp_data['end_date'])) ? date("d M, Y", strtotime($wsp_data['end_date'])) : 'Not Set';


                    $wspCountEstimatedComment = $this->Common->getWorkspaceCostComment($wsp_data['id'], 1);
                    $estimatedAnnotationClasswsp = 'annotation-grey';
                    if( isset($wspCountEstimatedComment) ){
                        if($wspCountEstimatedComment > 0){
                          $estimatedAnnotationClasswsp = 'annotation-black';
                        } else {
                          $estimatedAnnotationClasswsp = 'annotation-grey';
                        }

                    }
                    $wspCountSpendComment = $this->Common->getWorkspaceCostComment($wsp_data['id'], 2);
                    $spendAnnotationClasswsp = 'annotation-grey';
                    if( isset($wspCountSpendComment) ){
                        if($wspCountSpendComment > 0){
                          $spendAnnotationClasswsp = 'annotation-black';
                        } else {
                          $spendAnnotationClasswsp = 'annotation-grey';
                        }
                    }
                ?>
                <?php
                $workspace_elements = workspace_elements($wsp_data['id']);
                if(isset($workspace_elements) && !empty($workspace_elements)){
                    $we = Set::extract($workspace_elements, '/Element/id');
                    $estimate_sum = $this->ViewModel->wsp_element_cost($we, 1);
                    $spend_sum = $this->ViewModel->wsp_element_cost($we, 2);
                }
                //if(isset($workspace_elements) && !empty($workspace_elements)){
                    // pr($workspace_elements);
                    $dateFill = $dateBlank = [];
                    foreach ($workspace_elements as $key => $value) {
                        if(isset($value['Element']['start_date']) && !empty($value['Element']['start_date'])) {
                            $dateFill[] = $value;
                        }
                        else{
                            $dateBlank[] = $value;
                        }
                    }
                    usort($dateFill, function($a, $b) {
                        $d1 = strtotime($a['Element']['start_date']);
                        $d2 = strtotime($b['Element']['start_date']);
                        return $d1 > $d2;
                    });
                    $datesAll = [];
                    if(isset($dateFill) && !empty($dateFill)){
                        $datesAll = array_merge($datesAll, $dateFill);
                    }

                    if(isset($dateBlank) && !empty($dateBlank)){
                        $datesAll = array_merge($datesAll, $dateBlank);
                    }
                    foreach ($dateFill as $key => $value) {
                        // e(date("d M, Y", strtotime($value['Element']['start_date'])));
                    }
                    $workspace_elements = $datesAll;

                ?>
                <li class="closed wsp" >
                    <span class="folder">
                         <span class="left-sec">
                        <i class="costworkspaceblack"></i>
                        <span class="wsp title-text ">
                            <a class=" tipText" title="Workspace" href="<?php echo Router::Url( array( "controller" => "projects", "action" => "manage_elements", $project_id, $wsp_data['id'], 'admin' => FALSE ), true ); ?>"><?php echo htmlentities($wsp_data['title']); ?></a>
                        </span>
                        <span class="wsp date-text">
                            <?php
							if(isset($wsp_start_date) && $wsp_start_date=='Not Set'){
							echo "Not Set";
							}else{
							echo($wsp_start_date); ?>  → <?php echo($wsp_end_date);
							}
							?>
                        </span></span>
                        <span class="right-sec">
                            <div class="col-sm-6 wsp-col-first">
                                <div class="input-group doller-sec wsp-disabled tipText" title="Total Workspace Budget">
                                    <!-- <span class="input-group-addon only-read"><?php echo($currency_symbol) ?></span> -->
                                    <input type="text" class="form-control only-read wsp-estimated" readonly=""  value="<?php echo (isset($estimate_sum) && !empty($estimate_sum)) ? number_format($estimate_sum, 2, '.', ',') : '0.00'; ?>">
                                </div>
                                <span data-workspaceid="<?php echo $wsp_data['id']; ?>" data-costype="1" class="annotation <?php echo $estimatedAnnotationClasswsp;?> tipText" title="" data-toggle="modal" data-target="#model_bx_wsp" data-remote="<?php echo Router::Url( array( "controller" => "costs", "action" => "add_ws_annotate", 1, $wsp_data['id'], 'admin' => FALSE ), true ); ?>" data-original-title="Annotations" style="margin-left: 34px"></span>



                            </div>
                            <div class="col-sm-6 wsp-col-sec">
                                <div class="input-group doller-sec wsp-disabled wsp-spend-wraper tipText" title="Total Workspace Actuals">
                                    <!-- <span class="input-group-addon only-read"><?php echo($currency_symbol) ?></span> -->
                                    <input type="text" class="form-control only-read wsp-spend" value="<?php echo (isset($spend_sum) && !empty($spend_sum)) ? number_format($spend_sum, 2, '.', ',') : '0.00'; ?>">
                                </div>
                                <span data-workspaceid="<?php echo $wsp_data['id']; ?>" data-costype="2" class="annotation <?php echo $spendAnnotationClasswsp;?> tipText" title="" data-toggle="modal" data-target="#model_bx_wsp" data-remote="<?php echo Router::Url( array( "controller" => "costs", "action" => "add_ws_annotate", 2, $wsp_data['id'], 'admin' => FALSE ), true ); ?>" data-original-title="Annotations" style="margin-left: 34px"></span>
                            </div>
                        </span>
                    </span>

                    <?php if(isset($workspace_elements) && !empty($workspace_elements)){ ?>
                    <ul>
                        <?php
                        foreach ($workspace_elements as $elm_key => $elm_val) {
                            $elm_data = $elm_val['Element'];
                            $elm_start_date = (isset($elm_data['start_date']) && !empty($elm_data['start_date'])) ? date("d M, Y", strtotime($elm_data['start_date'])) : 'Not Set';
                            $elm_end_date = (isset($elm_data['end_date']) && !empty($elm_data['end_date'])) ? date("d M, Y", strtotime($elm_data['end_date'])) : 'Not Set';

                            $countEstimatedComment = $this->Common->getElementCostComment($elm_data['id'], 1);
                            $estimatedAnnotationClass = 'annotation-grey';
                            if( isset($countEstimatedComment) ){
                                if($countEstimatedComment > 0){
                                $estimatedAnnotationClass = 'annotation-black';
                                } else {
                                $estimatedAnnotationClass = 'annotation-grey';
                                }

                            }
                            $countSpendComment = $this->Common->getElementCostComment($elm_data['id'], 2);
                            $spendAnnotationClass = 'annotation-grey';
                            if( isset($countSpendComment) ){
                                if($countSpendComment > 0){
                                $spendAnnotationClass = 'annotation-black';
                                } else {
                                $spendAnnotationClass = 'annotation-grey';
                                }

                            }
                        ?>
                        <li class="closed" >

                            <span class="file">
                                <span class="left-sec">
                                <i class="icn_element_black"></i>
                                <span class="elm title-text " title="" >
                                    <a class=" tipText" title="Task" href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $elm_data['id'], 'admin' => FALSE ), true ); ?>"><?php echo(htmlentities($elm_data['title'])); ?></a>
                                </span>
                                <span  class="elm date-text ">
                                    <?php
									if(isset($elm_start_date) && $elm_start_date=='Not Set'){
									echo "Not Set";
									}else{
									echo($elm_start_date); ?>  → <?php echo($elm_end_date);
									}
									?>
                                </span></span>
                            </span>
                            <span class="right-sec">
                                <div class="col-sm-6 estimated_wrapper cost-inputs">
                                    <?php
                                    $element_cost = $this->ViewModel->element_cost($elm_data['id'], 1);
                                    $evalue = ($element_cost) ? $element_cost['ElementCost']['cost'] : '0.00';
                                    ?>
                                    <div class="input-group doller-sec elm-estimate-wrap">
                                        <!-- <span class="input-group-addon"><?php echo($currency_symbol) ?></span> -->
                                        <input type="text" value="<?php echo (isset($evalue) && !empty($evalue)) ? number_format($evalue, 2, '.', ',') : 0.00; ?>" class="form-control elm-estimate data-holder" readonly>
                                    </div>
                                    <span class="check-iocn elm-check-estimate" data-type="1" data-id="<?php echo($elm_data['id']) ?>"  rel="popover" data-title="Set Budget" data-popurl="<?php echo Router::Url( array( "controller" => "costs", "action" => "task_details", $elm_data['id'], 1, 'admin' => FALSE ), true ); ?>" ><i class="costeditblack"></i></span>
                                    <span data-elementid="<?php echo $elm_data['id']; ?>" data-costype="1" class="annotation <?php echo $estimatedAnnotationClass;?> tipText costannotation" title="" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo SITEURL;?>costs/add_annotate/1/<?php echo $elm_data['id']; ?>" data-original-title="Annotations"></span>
                                </div>
                                <div class="col-sm-6 spend_wrapper cost-inputs">
                                    <?php
                                    $element_cost = $this->ViewModel->element_cost($elm_data['id'], 2);
                                    $svalue = ($element_cost) ? $element_cost['ElementCost']['cost'] : '0.00';
                                    ?>
                                    <div class="input-group doller-sec">
                                        <!-- <span class="input-group-addon"><?php echo($currency_symbol) ?></span> -->
                                        <input type="text" value="<?php echo (isset($svalue) && !empty($svalue)) ? number_format($svalue, 2, '.', ',') : 0.00; ?>" class="form-control elm-spend data-holder" readonly>
                                    </div>
                                    <span class="check-iocn elm-check-spend" data-type="2" data-id="<?php echo($elm_data['id']) ?>" data-title="Set Actual" data-popurl="<?php echo Router::Url( array( "controller" => "costs", "action" => "task_details", $elm_data['id'], 2, 'admin' => FALSE ), true ); ?>"><i class="costeditblack" ></i></span>

                                    <span data-elementid="<?php echo $elm_data['id']; ?>" data-costype="2" class="annotation <?php echo $spendAnnotationClass;?> tipText costannotation" title="" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo SITEURL;?>costs/add_annotate/2/<?php echo $elm_data['id']; ?>" data-original-title="Annotations"></span>
                                </div>
                            </span>
                        </li>
                        <?php } // END elements loop ?>
                    </ul>
                    <?php } // END elements condition ?>
                </li>
                <?php //} // END workspace elements count check ?>
                <?php } // END workspace loop ?>
            </ul>
            <?php } // END workspace condition ?>
        </li>
    </ul>
</div>
<span id="sidetreecontrols" style="display: none;">
    <a href="#" class="tipText searchbtn collp" title="Collapse" style="display: none;"><i class="showlessblack"></i> </a>
    <a href="#" class="tipText searchbtn expd" title="Expand" style="display: none;"><i class="showmoreblack"></i> </a>
</span>
<script type="text/javascript">
    $(function(){
        $("#project_tree").treeview({
            collapsed: false,
            animated: "fast",
            control: "#sidetreecontrols",
            except: ':not(#project_tree > li > div.hitarea)',
            persist: "location"
        });
        $.create_popovers();
        $('.check-iocn').tooltip({
            placement: 'top',
            title: 'Edit',
            container: 'body'
        })
        /*
         * Save project budget
         */
        // $.old_budget = 0;
    })
</script>