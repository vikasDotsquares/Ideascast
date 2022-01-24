<?php
$overdues = [];

foreach ($projects as $key => $value) {
    $elements_overdue = $this->ViewModel->elements_overdue($value);
    // pr($elements_overdue);
        $overdues[$value] = (isset($elements_overdue) && !empty($elements_overdue)) ? count($elements_overdue) : 0;
}

uasort($overdues, function($a, $b) {
    return $a < $b;
});
foreach ($overdues as $key => $value) {
    // e($key);
    $elements_overdue_total = $this->ViewModel->elements_overdue_total($key);
    $elements_overdue = $this->ViewModel->elements_overdue($key);
    $project_elements = $this->ViewModel->project_elements($key);

    $upid = project_upid($key);
    $project_detail = project_primary_id($upid, true);

    if (isset($project_detail) && !empty($project_detail)) {

        $project_image = $project_detail['image_file'];
        $rag_status = $project_detail['rag_status'];
        $project_color = $project_detail['color_code'];
        ?>

        <?php
        $RAG = $this->Common->getRAG($key);
        $parent_color = 'green_status';
        $parent_color =  ($RAG['rag_color'] == 'bg-red') ? 'red_status' : ( ( $RAG['rag_color'] == 'bg-yellow') ? 'amber_status' : 'green_status' );

        ?>

        <!-- <div class="col-sm-6 col-md-4 col-lg-3 "> -->

        <div class="panel panel-default project-block  <?php echo $parent_color; ?>" data-id="<?php echo $key; ?>">
            <div class="panel-heading <?php //echo str_replace('panel-', 'bg-', $project_color) ;  ?>">
                <h5 class="panel-title nowrap-title"><?php echo strip_tags($project_detail['title']); ?></h5>
                <a class="pull-right btn btn-default btn-xs tipText" title="Open Project" href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $key)); ?>" style="margin-left: 3px;">
                    <i class="fa fa-folder-open"></i>
                </a>
               <?php /* ?> <a class="pull-right btn btn-default btn-xs tipText" title="Status" href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'objectives', $key)); ?>">
                    <i class="fa fa-dashboard"></i>
                </a><?php */ ?>
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
                            <span style="text-align: center;font-weight:normal;" class="bar <?php echo $RAG['rag_color'];?>">RAG</span>
                        </span>

                        <?php
                        $rag_exist = rag_exist($key);
                        if( !empty($rag_exist)) { ?>
						      <span class="rag_update_rules tipText" title="Rules" ></span>
                        <?php }else { ?>
                                <span class="rag_update_manual tipText" title="Manual" ></span>
                        <?php } ?>

                        <?php if (annotation_exists($key) > 0) { ?>
                            <span class="annotation annotation-black pull-right tipText" title="Annotations" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'add_annotate', $key)); ?>"></span>
                        <?php } else { ?>
                            <span class="annotation annotation-grey pull-right tipText" title="Annotate" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'add_annotate', $key)); ?>"></span>
                        <?php } ?>
                    </div>
                        <?php /*  ?><?php  */ ?>
                    <span class="detail-text">
                        <b>Start: </b><?php echo ( isset($project_detail['start_date']) && !empty($project_detail['start_date']) ) ? _displayDate($project_detail['start_date'], 'd M, Y') : 'N/A'; ?> <b>End: </b><?php echo ( isset($project_detail['end_date']) && !empty($project_detail['end_date']) ) ? _displayDate($project_detail['end_date'], 'd M, Y') : 'N/A'; ?>
                    </span>
					<span class="detail-text"><b>Risks:</b>
						<?php
    						$highRisks = risk_by_exposer_type($project_detail['id'], 'high');
    						$severeRisks = risk_by_exposer_type($project_detail['id'], 'severe');
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
					<span class="detail-text"><b>Costs:</b>
					<?php $CheckProjectType = CheckProjectType($project_detail['id'],$this->Session->read('Auth.User.id')); ?>
					<a class="CheckProjectType" href="<?php echo SITEURL.'costs/index/'.$CheckProjectType.':'.$project_detail['id']; ?>"><?php
						echo $this->ViewModel->projectCostStatus($project_detail['id'], $project_detail['budget']);
					?></a></span>
                    <span class="detail-text">
                        <b>Tasks: </b><?php   echo (isset($project_elements) && !empty($project_elements)) ? count($project_elements) : 0; ?> |
                        <?php if (isset($elements_overdue) && !empty($elements_overdue)) { ?>
                            <a style="font-weight: 600; color: #ff4444;" href="<?php //echo Router::url(array('controller' => 'dashboards', 'action' => 'task_center', $key, 'status' => 1));
							echo TASK_CENTERS.$key.'/status:1';
							?>">
                                <b style="">Overdue: </b><?php   echo (isset($elements_overdue) && !empty($elements_overdue)) ? count($elements_overdue) : 0; ?>
                            </a>
                        <?php } else { ?>
                            <b style="">Overdue: </b>0
                        <?php } ?>
                    </span>
                    <span class="detail-text last">
                        <b>Tasks Ending (Next 5 days): </b><?php   echo (isset($elements_overdue_total) && !empty($elements_overdue_total)) ? count($elements_overdue_total) : 0; ?>
                    </span>


                </div>
            </div>
        </div>
        <!-- </div>  -->
    <?php } ?>
<?php } ?>