<?php
$overdues = [];


uasort($projects, function($a, $b) {
    return $a['task']['OVD'] < $b['task']['OVD'];
});
// pr($projects, 1);
/*

foreach ($projects as $key => $value) {
    $total_elements = 0;
     $taskStatusCount = $this->ViewModel->taskStatusCount($this->Session->read('Auth.User.id'), $value['id'] );
    $ovd = 0; //(isset($taskStatusCount[0][0]['OVD']) && !empty($taskStatusCount[0][0]['OVD'])) ? $taskStatusCount[0][0]['OVD'] : 0;

    $total_elements = 0; //($taskStatusCount[0][0]['CMP'] + $taskStatusCount[0][0]['NON'] + $taskStatusCount[0][0]['PRG'] + $taskStatusCount[0][0]['PND'] + $taskStatusCount[0][0]['OVD']);
    $overdues[$value['id']] = ['overdues' => $ovd, 'detail' => $value, 'total_elements' => $total_elements];
}

uasort($overdues, function($a, $b) {
    return $a['overdues'] < $b['overdues'];
});*/


foreach ($projects as $key => $value) {
    $detail = $value['project'];
    $taskData = $value['task'];

    $total_ovd = $value['task']['OVD'];
    $total_elements = $taskData['CMP'] + $taskData['NON'] + $taskData['PRG'] + $taskData['PND'] + $taskData['OVD'];
    $elements_overdue_total = $total_ovd;
    $project_elements = $total_elements;

    $project_detail = $detail;//project_primary_id($upid, true);
    // pr($detail, 1);
    if (isset($project_detail) && !empty($project_detail)) {

        $project_image = $project_detail['image_file'];
        $rag_status = $project_detail['rag_status'];
        $project_color = $project_detail['color_code'];

        // $RAG = $this->Common->getRAG($key);
        // $RAG = $this->ViewModel->getRAGStatus($key, false, $total_elements, $total_ovd);
        $RAG = $value['rag_data'];
        // $parent_color = 'green_status';
        $parent_color =  ($RAG['rag_color'] == 1) ? 'red_status' : ( ( $RAG['rag_color'] == 2) ? 'amber_status' : 'green_status' );
        $bar_color =  ($RAG['rag_color'] == 1) ? 'bg-red' : ( ( $RAG['rag_color'] == 2) ? 'bg-yellow' : 'bg-green' );

        ?>


        <div class="panel panel-default project-block  <?php echo $parent_color; ?>" data-id="<?php echo $project_detail['id']; ?>">
            <div class="panel-heading ">
                <h5 class="panel-title nowrap-title"><?php echo htmlentities($project_detail['title']); ?></h5>
                <span class="pull-right clickable panel-collapsed tipText" title="Show Summary" data-project="<?php echo $project_detail['id']; ?>"><i class="fa fa-arrows-v"></i></span>
                <a class="pull-right clickable_folder tipText" title="Open Project" href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $project_detail['id'])); ?>"><i class="fa fa-folder-open"></i></a>
            </div>
            <div class="panel-body" id="">
                <div style=" " class="wrap_project_image">
                    <?php
                    if (!empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)) {
                        $project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;

                        echo $this->Image->resize($project_detail['image_file'], 248, 120, array("class" => 'project_image'), 100);
                    } else {
                        ?>
                        <div class="image-not-available">No Project Image</div>
                    <?php } ?>
                </div>
                <div class="project-detail">

                    <div class="rag-status-outer">
                        <span class="rag-status">
                            <span style="text-align: center;font-weight:normal;" class="bar <?php echo $bar_color;?>">RAG</span>
                        </span>

                        <?php
                        $rag_exist = rag_exist($project_detail['id']);
                        if( !empty($rag_exist)) { ?>
						      <span class="rag_update_rules tipText" title="Rules" ></span>
                        <?php }else { ?>
                                <span class="rag_update_manual tipText" title="Manual" ></span>
                        <?php } ?>

                        <?php if (annotation_exists($project_detail['id']) > 0) { ?>
                            <span class="annotation annotation-black pull-right tipText" title="Annotations" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'add_annotate', $project_detail['id'])); ?>"></span>
                        <?php } else { ?>
                            <span class="annotation annotation-grey pull-right tipText" title="Annotate" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'add_annotate', $project_detail['id'])); ?>"></span>
                        <?php } ?>
                    </div>
                    <span class="detail-text">
                        <b>Start: </b><?php echo ( isset($project_detail['start_date']) && !empty($project_detail['start_date']) ) ? _displayDate($project_detail['start_date'], 'd M, Y') : 'N/A'; ?> <b>End: </b><?php echo ( isset($project_detail['end_date']) && !empty($project_detail['end_date']) ) ? _displayDate($project_detail['end_date'], 'd M, Y') : 'N/A'; ?>
                    </span>

					<span class="detail-text"><b>Risks:</b>
						<?php
						$highRisks = $taskData['high_risk'];//risk_by_exposer_type($project_detail['id'], 'high');
						$severeRisks = $taskData['severe_risk'];//risk_by_exposer_type($project_detail['id'], 'severe');
						?>
						<?php

						if( $highRisks > 0 ){
						?>
						<a class="CheckProjectType" href="<?php echo SITEURL.'risks/index/'.$project_detail['id']; ?>/exposure:high"><?php echo $highRisks." HIGH ";?></a>
						<?php } else {?>
						<span class="CheckProjectType" style="pointer-events:none;"><?php echo "0 HIGH ";?></span>
						<?php } ?>
						|
						<?php if( $severeRisks > 0 ){ ?><a class="CheckProjectType" href="<?php echo SITEURL.'risks/index/'.$project_detail['id']; ?>/exposure:severe"><?php echo " ".$severeRisks." SEVERE";?></a>
						<?php } else { ?>
						<span class="CheckProjectType" style="pointer-events:none;"><?php echo " 0 SEVERE";?></span>
						<?php } ?>

					</span>

					<span class="detail-text "><b>Costs:</b>
					<?php $CheckProjectType = $taskData['prj_role']; ?>
                    <a class="CheckProjectType" href="<?php echo SITEURL.'costs/index/'.$CheckProjectType.':'.$project_detail['id']; ?>"><?php
                        echo $this->ViewModel->prjCstSts($project_detail['id'], $project_detail['budget'], $taskData['etotal'], $taskData['stotal']);
                    ?></a>
					 </span>
                    <span class="detail-text">
                        <b>Tasks: </b><?php
						echo (isset($project_elements) && !empty($project_elements)) ? $project_elements : '0';
						 ?> |
                        <?php if ( isset($total_ovd) && !empty($total_ovd) ) { ?>
                            <a style="font-weight: 600; color: #ff4444;" href="<?php echo TASK_CENTERS.$project_detail['id'].'/status:1';?>">
                                <b style="">Overdue: </b><?php
								echo $total_ovd;?>
                            </a>
                        <?php } else { ?>
                            <b style="">Overdue: </b>0
                        <?php } ?>
                    </span>
                    <span class="detail-text last">
                        <b>Tasks Ending (Next 5 days): </b><?php echo $elements_overdue_total; ?>
                    </span>


                </div>
            </div>
        </div>
        <!-- </div>  -->
    <?php } ?>
<?php } ?>