<?php

if (isset($elementData) && !empty($elementData)) {
    $record = $row = $project['Project'];

//pr($elementData['Element']['id']);
    $totalEle = $totalWs = 0;
    $totalAssets = null;
    $projectData = $this->ViewModel->getProjectDetail($record['id']);
    if (!empty($projectData)) {
        $wsList = Set::extract($projectData, '/ProjectWorkspace/workspace_id');
		$wsList_tot = ( isset($wsList) && !empty($wsList) ) ? count($wsList) : 0;
        $totalWs = $wsList_tot;
        if (!empty($wsList)) {
            foreach ($wsList as $wsid) {
                $wsData = $this->ViewModel->countAreaElements(NULL,NULL,$elementData['Element']['id']);
               // pr($wsData);
                //$totalEle += $wsData['active_element_count'];
                if (isset($wsData['assets_count']) && !empty($wsData['assets_count'])) {
                    $totalAssets = $wsData['assets_count'];
//                    foreach ($wsData as $k => $subArray) {
//                        if (is_array($subArray)) {
//                            //foreach ($subArray as $m => $value) {
//                                if (!isset($totalAssets[$m]))
//                                    $totalAssets[$m] = $value;
//                                else
//                                    $totalAssets[$m] += $value;
//                            //}
//                        }
//                    }
                }
            }
        }
    }
    ?>
<script>
$(function(){
	 $('.tipText').tooltip();
	 $(".el_body_toggle").attr('data-original-title','Show');
})
</script>	
    <div class="modal-header panel "> 
        <h2 id="modalTitle" class="panel-title" style="font-size:24px;" >Task 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </h2> 

    </div>
	
	
	
	
	<div style=" display: block;margin:30px;" class="el panel no-box-shadow <?php echo $elementData['Element']['color_code']; ?> allowed col-sm-NaN nopadding ui-draggable ui-draggable-handle" >

	 

	<div style="padding: 0 !important;" class="panel-heading clearfix">

		<h2 data-original-title="Double Click to Collapse/Expand" class="panel-title el-title" style="z-index: 0; position: relative;">
			<span class="el-title-text truncate" style=" display: inline-block;"><div style="margin: 17px; padding: 0px; font-size: 14px; border: 0px none;"><?php echo strip_tags(str_replace("&nbsp;", " ", $elementData['Element']['title'])); ?> </div></span>
			<a href="#" class="el_body_toggle text-white pull-right padding tipText" data-toggle="tooltip" title="Show" ><span class="fa fa-list"></span></a>
		</h2>
		<a style="z-index: 999; position: relative; display: none;" href="#" class="my-menu toggle_top_menus text-white pull-right">
			<span class="fa fa-list"></span>
		</a>

 
	</div>
<?php $self_status['self_status'] = element_status( $elementData['Element']['id']);
         ?>
	<div class="panel-footer clearfix padding-top el_<?php 
	echo $self_status['self_status']; ?>">
 <div class="prjct-rprt-icons gantt-task-icon-align"> 
                        <ul class="list-unstyled text-center">
                            <li class="odue">
                                <span class="label bg-mix">
                                    <?php
                                    $element_statuses = _element_statuses( $elementData['Element']['id'] );
                                    echo $element_statuses['status_short_term'];
                                    //pr($element_statuses['status_tiptext']);
                                    if($element_statuses['status_short_term'] == 'NON'){
                                        $t = 'No';
                                    }else{
                                        $t = $element_statuses['status_tiptext'];
                                    }
                                    ?>
                                </span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#tasks">
                                    <span data-original-title="<?php echo $t;?> Task Status" class="btn btn-xs bg-mix bg-navy tipText" title="" href="#"><i class="asset-all-icon overduewhite"></i></span></a>
                            </li>                            
                            <li class="ico_links">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['links']) && !empty($totalAssets['links'])) ? $totalAssets['links'] : 0; ?></span> 
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#links">
                                <span data-original-title="Links" class="btn btn-xs bg-mix tipText bg-maroon" title="" href="#"><i class="asset-all-icon linkwhite"></i></span></a>
                            </li>
                            <li class="inote">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['notes']) && !empty($totalAssets['notes'])) ? $totalAssets['notes'] : 0; ?></span> 
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#notes">
                                <span data-original-title="Notes" class="btn btn-xs bg-mix tipText bg-purple" title="" href="#"><i class="asset-all-icon notewhite"></i></span>
                                </a>
                            </li>
                            <li class="idoc">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['docs']) && !empty($totalAssets['docs'])) ? $totalAssets['docs'] : 0; ?></span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#documents">
                                <span class="btn bg-blue btn-xs bg-mix tipText" title="" href="#" data-original-title="Documents"><i class="asset-all-icon documentwhite"></i></span>
                                </a>
                            </li>
                            
                            
                            

                            <li class="green">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['mindmaps']) && !empty($totalAssets['mindmaps'])) ? $totalAssets['mindmaps'] : 0; ?></span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#mind_maps">
                                    <span data-original-title="Mind Maps" class="btn btn-xs bg-mix bg-green tipText" title="" href="#"><i class="asset-all-icon mindmapwhite"></i></span></a>
                            </li>
                            <li class="orang">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['decisions']) && !empty($totalAssets['decisions'])) ? $totalAssets['decisions'] : 0; ?></span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#decisions">
                                    <span data-original-title="Decisions" class="btn btn-xs bg-mix bg-orange  tipText decisions" title="" href="#"><i class="asset-all-icon decisionwhite"></i></span></a>
                            </li>
                            <li class="l-blue">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['feedbacks']) && !empty($totalAssets['feedbacks'])) ? $totalAssets['feedbacks'] : 0; ?></span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#feedbacks">
                                <span data-original-title="Feedback" class="btn btn-xs bg-mix bg-aqua tipText" title="" href="#"><i class="asset-all-icon feedbackwhite"></i></span>
                                </a>
                            </li>

                            <li class="l-orang">
                                <span class="label bg-mix"><?php echo (isset($totalAssets['votes']) && !empty($totalAssets['votes'])) ? $totalAssets['votes'] : 0; ?></span>
                                <a href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>#votes">
                                    <span class="btn btn-xs bg-mix tipText bg-orange-active" title="" href="#" data-original-title="Votes"><i class="asset-all-icon votewhite"></i></span></a>
                            </li>
                        </ul>  
                    </div>
                

	</div>

	<div id="el_body_div" class="panel-bodys" style="display:none">

	<div style="" class="sub-heading clearfix">

	<span>Task Description</span>

	</div>

	<div class="body-content" style="max-height: 58px; min-height: 58px; text-overflow: inherit; overflow: auto;  " > <?php echo trim($elementData['Element']['description']);?></div>

	<div style="" class="sub-heading clearfix">

	<span>Task Outcome</span>

	</div>

	<div class="body-content" style="max-height: 58px; min-height: 58px; text-overflow: inherit; overflow: auto; ">  <?php echo trim($elementData['Element']['comments']);?></div>

	 </div>
 
</div>  
 
    <div class="modal-footer"> 
        <a class="btn btn-success btn-sm" href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>">Open</a> 
        <button class="btn btn-danger btn-sm" data-dismiss="modal">Close</button> 	
    </div>	 

    <?php
}
?>
<script type="text/javascript" >
    $(function () {
		
        $('a.el_body_toggle').on('click', function (event) {
            event.preventDefault()
			var run = true
			if( run ) { 
				if( $('#el_body_div').is(':visible') ) {
					$('#el_body_div').slideUp(400);
					$(".el_body_toggle").attr('data-original-title','Show');
					
				}
				else {
					$(".el_body_toggle").attr('data-original-title','Hide');
					$('#el_body_div').slideDown(400)	
				}
				run = false;
			}
			return;
        })
    })
</script>
 
<?php
if (isset($workspaceData) && !empty($workspaceData)) { //pr($data);
    ?>
    <div class="modal-header"> 
        <h4 id="modalTitle" class="modal-title" >
            <?php echo strip_tags(str_replace("&nbsp;", " ", $workspaceData['Workspace']['title'])); ?>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                <span aria-hidden="true">&times;</span></button>
        </h4> 	
    </div>
    <div class="modal-body element-form clearfix "  >

        <div class="clearfix margin padding border" style="background-color: #EEEEEE !important">
            <div class="row"> 
                <div class="col-sm-3 text-bold">Key Target Result  </div>
                <div class="col-sm-9 text-left"> <?php echo $workspaceData['Workspace']['description'] ?></div>
            </div>
        </div>

    </div>

    <div class="modal-footer"> 
        <a class="btn btn-success" href="<?php echo SITEURL; ?>projects/manage_elements/<?php echo $project['Project']['id']; ?>/<?php echo $workspaceData['Workspace']['id']; ?>">Open</a> 
        <button class="btn btn-danger" data-dismiss="modal">Close</button> 	
    </div>	 

    <?php
}
?>
<style>.modal-content{ padding: 0 0 0 0}
.sub-heading,.body-content{ padding: 5px;}
.prjct-rprt-icons ul.list-unstyled > li span.bg-mix{font-weight: normal;}
.prjct-rprt-icons ul.list-unstyled > li span.bg-mix {
  border-color: none;
  border-image: none;
  border-style: none !important;
  border-width: none;
  color: #1f1f1f;
  padding: 2.5px 0;
}

 .body-content {
    font-size: 13px;
    margin: 10px 5px; 
}
.sub-heading span{ font-size : 13px;}
.modal-dialog{width:450px;}
</style> 