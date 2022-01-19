<?php
if (isset($elementData) && !empty($elementData)) {  
//pr($elementData);
$element_id = $elementData['Element']['id'];

    ?>
    <div class="modal-header"> 
        <h3 id="modalTitle" class="modal-title popup-title-wrap"><span class="popup-title-ellipsis"><?php echo htmlentities($elementData['Element']['title']) ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </h3>
    </div>
    <div class="modal-body element-form clearfix ">
		<!-- padding border style="background-color: #EEEEEE !important" -->	
        <div class="clearfix" >

			<div class="row"> 
                <div class="col-sm-12  col-md-12 text-bold text-bold">Task Team Members:</div>
            </div>
			<div class="row">
                <?php include "element_people.ctp"; ?>
            </div>
		
            <?php /* <div class="row"> 
                <div class="col-sm-3  col-md-3 text-bold text-bold">Project  </div>
                <div class="col-sm-8 no-padding col-md-9 text-bold text-left"> <?php echo $project['Project']['title'] ?></div>
            </div>*/ ?>

            <div class="row" style="padding:15px 0 0 0 "> 
                <!--<div class="col-sm-3  col-md-3 text-bold">Description  </div>
                <div class="col-sm-9 no-padding  col-md-9 text-left"> <?php //echo $project['Project']['description'] ?></div> -->	<div class="col-sm-12">		
				
				<label class="col-sm-12" for="descriptionelement" style="width:100%; margin: 0;padding: 0 0 5px 0">Task Description:</label>
				<textarea  class="form-control col-sm-12" rows="4" placeholder="" style="border-color:#00c0ef;"  aria-invalid="false" id="descriptionelement"><?php echo htmlentities($elementData['Element']['description']); ?></textarea>
				
            </div></div>
            <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-2 text-bold">Workspace:</div>
                <div class="col-sm-9  col-md-10 text-left"> <?php echo htmlentities($elementData['Area']['Workspace']['title']); ?></div>
            </div>
            <!-- <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-3 text-bold">Key Result Target </div>
                <div class="col-sm-9  no-padding col-md-9 text-left"> <?php //echo $elementData['Area']['Workspace']['description']; ?></div>
            </div>-->
            <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-2 text-bold">Area:</div>
                <div class="col-sm-9  col-md-10 text-left"> <?php echo strip_tags($elementData['Area']['title']); ?></div>
            </div>
            <?php /* <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-3 text-bold">Start  </div>
                <div class="col-sm-9 no-padding  col-md-9 text-left"> 
                    <?php 
                        if(isset($elementData['Element']['start_date']) && !empty($elementData['Element']['start_date'])){
                            //echo date("d M y",strtotime($elementData['Element']['start_date']));
							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($elementData['Element']['start_date'])),$format = 'd M y');
                        }else{
                            echo 'Not specified';
                        }
                         
                    ?>
                </div>
            </div>
            <div class="row" style="padding:15px 0 0 0 "> 
                <div class="col-sm-3  col-md-3 text-bold">End  </div>
                <div class="col-sm-9 no-padding  col-md-9 text-left">
                    <?php 
                        if(isset($elementData['Element']['end_date']) && !empty($elementData['Element']['end_date'])){
                            //echo date("d M y",strtotime($elementData['Element']['end_date']));
							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($elementData['Element']['end_date'])),$format = 'd M y');
                        }else{
                            echo 'Not specified';
                        }
                    ?>
                </div>
            </div>
			*/ ?>
        </div>

    </div>

    <div class="modal-footer"> 
        <a class="btn btn-success" href="<?php echo SITEURL;?>entities/update_element/<?php echo $elementData['Element']['id'];?>">Open Task</a> 
        <button class="btn btn-danger" data-dismiss="modal">Close</button> 	
    </div>	 

    <?php
}
?>

 