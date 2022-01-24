<?php
if (isset($projectData) && !empty($projectData)) {
    ?>
    <div class="modal-header"> 
        <h2 id="modalTitle" class="modal-title" ><?php echo strip_tags($projectData['Project']['title']); ?><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></h2> 	
    </div>
    <div class="modal-body element-form clearfix "  >

        <div class="clearfix margin padding border" style="background-color: #EEEEEE !important">

            <div class="row"> 
                <div class="col-sm-3  col-md-3 text-bold">Objective </div>
                <div class="col-sm-9  col-md-9  no-padding text-left"> <?php echo $projectData['Project']['objective'] ?></div>
            </div>

            <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-3 text-bold">Description </div>
                <div class="col-sm-9  col-md-9 no-padding text-left"> <?php echo $projectData['Project']['description'] ?></div>
            </div>
        </div>

    </div>

    <div class="modal-footer"> 
        <a class="btn btn-success" href="<?php echo SITEURL;?>projects/index/<?php echo $projectData['Project']['id'];?>">Open</a> 
        <button class="btn btn-danger" data-dismiss="modal">Close</button> 	
    </div>	 

    <?php
}
?>
<style>.col-md-3{ width:23%}</style>
