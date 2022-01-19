<?php
if (isset($workspaceData) && !empty($workspaceData)) { //pr($data);
    ?>
    <div class="modal-header"> 
		<button type="button" class="close close_wspdetail" data-dismiss="modal" aria-label="Close"  ><span aria-hidden="true">&times;</span></button>
        <h3 id="modalTitle" class="modal-title" ><?php echo htmlentities($workspaceData['Workspace']['title']); ?></h3> 	
    </div>
    <div class="modal-body element-form clearfix "  >

        <!--<div class="clearfix margin padding border" style="background-color: #EEEEEE !important">-->
            <div class="row col-sm-12 no-padding no-margin">
				<label class="col-sm-12" for="descriptionworkspace" style="width:100%; margin: 0;padding: 0 0 5px 0">Key Result Target:</label>
				<textarea  class="form-control col-sm-12" rows="2" placeholder="" style="border-color:#00c0ef;"  aria-invalid="false" id="descriptionworkspace"><?php echo htmlentities($workspaceData['Workspace']['description']); ?></textarea>
				
                 <!-- <div class="col-sm-4  col-md-4 text-bold">Key Target Result</div>
                <div class="col-sm-8 no-padding  col-md-8 text-left"><?php //echo $workspaceData['Workspace']['description'] ?></div> -->
            </div>
        <!--</div>-->

    </div>

    <div class="modal-footer"> 
        <a class="btn btn-success" href="<?php echo SITEURL;?>projects/manage_elements/<?php echo $project['Project']['id'];?>/<?php echo $workspaceData['Workspace']['id'];?>">Open Workspace</a> 
        <button class="btn btn-danger" data-dismiss="modal">Close</button> 	
    </div>	 

    <?php
}
?>
<style> .col-md-4{width:31.333%;}</style>
