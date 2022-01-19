<?php
if (isset($sketch_save_as_data) && !empty($sketch_save_as_data)) {
    foreach ($sketch_save_as_data as $key => $sketch) {
	
        ?>

        <div class="panel panel-default" id="panel-id-<?php echo $sketch['ProjectSketch']['id']; ?>">
            <div class="panel-heading bg-green" role="tab" id="headingOne-<?php echo $sketch['ProjectSketch']['id']; ?>">
                <h4 data-original-title="<?php echo $sketch['ProjectSketch']['sketch_title']; ?>" class="panel-title  tipText">
                    <a role="button" style="font:11px;" data-toggle="collapse" data-parent="#accordion" href="#collapseOne-<?php echo $sketch['ProjectSketch']['id']; ?>" aria-expanded="true" aria-controls="collapseOne-<?php echo $sketch['ProjectSketch']['id']; ?>">
                        <i style="color:#fff;" class="more-less glyphicon glyphicon-plus"></i>
                        <?php echo ( strlen($sketch['ProjectSketch']['sketch_title']) > 20 ) ? substr(html_entity_decode($sketch['ProjectSketch']['sketch_title']), 0, 20) . '...' : html_entity_decode($sketch['ProjectSketch']['sketch_title']); ?>
                    </a>
                </h4>
            </div>
            <div id="collapseOne-<?php echo $sketch['ProjectSketch']['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne-<?php echo $sketch['ProjectSketch']['id']; ?>">
                <div class="panel-body" style=" overflow:auto;max-height:250px;">
                    <?php
                    $saveassketch = $this->Sketch->getSaveasSketch($sketch['ProjectSketch']['id']);
                    ?>
                    <?php
                    //pr($sketch_save_as_data);
                    if (isset($saveassketch) && !empty($saveassketch)) {
                        foreach ($saveassketch as $data) {
                            ?>
                            <div class="edited-sketch" id="edited-sketch-saveas-<?php echo $data['ProjectSketch']['id']; ?>">
                                <div class="edited-sketch-left pull-left">
                                    
                                    <?php
                                    $des = 'Provide sketch description.';
                                    if (isset($data['ProjectSketch']['sketch_description']) && !empty($data['ProjectSketch']['sketch_description'])) {
                                        $des = $data['ProjectSketch']['sketch_description'];
                                    }
                                    ?>
                                    <span style="margin-right:5px;margin-top: -3px;" class="pull-right clickable panel-collapsed  pophover" data-placement="top"  data-toggle="popover" data-trigger="hover"  data-content="<?php echo $des ?>" data-project="2">
                                        <i class="fa fa-info fa-3 martop save_as"></i> 
                                    </span>
                                </div>
                                <div class="edited-sketch-right">
                                    <a class="tipText" href="<?php echo SITEURL; ?>skts/edit_sketch/project_id:<?php echo $data['ProjectSketch']['project_id'] ?>/sketch_id:<?php echo $data['ProjectSketch']['id']; ?>" data-original-title="<?php echo !empty($data['ProjectSketch']['sketch_title']) ? $data['ProjectSketch']['sketch_title'] : 'Provide sketch title.'; ?>">
                                        <?php
                                        if (isset($data['ProjectSketch']['sketch_title']) && !empty($data['ProjectSketch']['sketch_title'])) {
                                            //echo $data['ProjectSketch']['sketch_title'];
                                            echo ( strlen($data['ProjectSketch']['sketch_title']) > 20 ) ? substr(html_entity_decode($data['ProjectSketch']['sketch_title']), 0, 20) . '...' : html_entity_decode($data['ProjectSketch']['sketch_title']);
                                        } else {
                                            echo 'Provide sketch title.';
                                        }
                                        ?>
                                    </a>
                                        <?php
                                        if (isset($data['ProjectSketchParticipant']) && !empty($data['ProjectSketchParticipant'])) {
                                            $ids = Hash::extract($data['ProjectSketchParticipant'], '{n}.created_user_id');
                                        } else {
                                            $ids = array();
                                        }

                                        $ids = array_filter($ids);
                                        //pr($ids);
                                        if (isset($ids) && !empty($ids) && in_array($this->Session->read("Auth.User.id"), $ids)) {
                                            ?>
                                    <div class="pull-right" style="display:inline;">
                                        
                                        <button data-id="<?php echo $data['ProjectSketch']['id']; ?>" data-original-title="Delete" class="deletesketch_saveas btn tipText btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                        <button data-href="<?php echo SITEURL;?>skts/makemainsketch_html/project_id:<?php echo $project_id;?>/sketch_id:<?php echo $data['ProjectSketch']['id']; ?>" data-id="<?php echo $data['ProjectSketch']['id']; ?>" data-original-title="Make Sketch Card" class="makesketchsketch_saveas btn tipText btn-xs btn-default"><i class="fa fa-laptop"></i></button>
                                       </div> 
                                        <?php
                                    }
                                    ?>                                                            
                                </div>
                            </div>
                <?php
            }
        } else {
            ?>
                            <div class="edited-sketch text-center">
                                No Sketch Versions
                            </div>
            <?php
        }
        ?>

                </div>
            </div>
        </div>


        <?php
    }
} else {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading bg-white" role="tab" id="headingOne">
            <h4 class="panel-title text-center">No Sketch Versions</h4>
        </div>
    </div>
    <?php
}
?>
<script type="text/javascript" >
    $(document).ready(function () {
        $(".makesketchsketch_saveas").on('click', function (e) {
            var $that = $(this);
            BootstrapDialog.show({
                type:BootstrapDialog.TYPE_DANGER,
                closable: true,
                closeByBackdrop: false,
                closeByKeyboard: false,
                title:'Confirmation',
                message: 'Make this sketch version a Sketch Card?',
                buttons: [{
                    label: 'Update',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function(dialogRef){
                    var href_ = $that.data("href");
                        $.post(
                            href_,
                            null,
                            function (responce) {
                                if(responce){
                                    setTimeout(function(){
                                        $(".mainsketchdiv_ajax").html(responce); 
                                        
                                    }, 6000);
                                }
                            },
                            //"json"
                            "html"
                        );
                        
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        dialogRef.getModalBody().html('Converting this into main sketch…');
                        setTimeout(function(){
                            $("#edited-sketch-saveas-"+$that.data("id")).fadeOut("slow");
                            $("#allcountsketch").text(parseInt($("#allcountsketch").text())+1);
                            dialogRef.close();
                            /* window.location.reload(); */
                        }, 5000);
                    }
                }, {
                    label: 'Close',
                    cssClass: 'btn-danger',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
            });
        });
    });    
</script>

