<?php 
include 'partials/header_js_css.ctp';
?>
<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">

        <?php 
        include 'partials/header_inner.ctp';
        ?>


        <!-- MAIN CONTENT -->
        <div class="box-content wiki">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="bg-white">    
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        // pr($project_id);
                        ?>

                        <div class="box noborder padding margin-top" id="box_body">
                            <div class="pull-right form-group">
                                <a href="<?php echo SITEURL;?>sketches/index/project_id:<?php echo $project_id;?>" class="btn btn-warning"> Back</a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <?php 
                                if(isset($sketchdata) && !empty($sketchdata)){
                                    foreach($sketchdata as $key => $sketch){
                                        ?>
                                        <div class="col-sm-12">
                                            <div class="idea-canvas-outer list-group-item">
                                                <h5><?php echo $sketch['ProjectSketch']['sketch_title'];?></h5>
                                                <div class="idea-canvas-inner list-group-item">
                                                    <?php echo $sketch['ProjectSketch']['content'];?>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <?php
                                        
                                    }
                                }else{ ?>
                                
                                <div class="col-sm-12">
                                    <div class="idea-canvas-outer list-group-item text-center">
                                        No Record Found!
                                    </div>
                                </div>
                                <?php
                                    
                                }
                                ?>
                                
                                
                                
                                
                               
                            </div>
                        </div>

                    </div>
                </div>							
            </div>   

        </div>

    </div>
</div>	


<!-- END MODAL BOX -->
<script>
    $(function () {
        $("body").delegate(".confirm_delete", 'click', function (event) {
        event.preventDefault()
        var $t = $(this)
        var params = {};
        BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Are you sure you want to delete this sketch?',
            type: BootstrapDialog.TYPE_DANGER,
            draggable: true,
            buttons: [
                {
                    label: ' Yes',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function (dialogRef) {
                        $.when(
                                $.ajax({
                                    url: $t.data("href"),
                                    type: "POST",
                                    data: $.param(params),
                                    dataType: "JSON",
                                    global: false,
                                    success: function (response) {
                                        $t.parents('.col-sm-3').fadeOut(500, function () {
                                            $t.parents('.col-sm-3').remove()
                                        })
                                    }
                                })
                        ).then(function (data, textStatus, jqXHR) {
                            dialogRef.close();
                        })

                    }
                },
                {
                    label: ' No',
                    cssClass: 'btn-danger',
                    action: function (dialogRef) {
                        dialogRef.close();
                    }
                }
            ]
        });
    });
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        $('body').on('hidden.bs.modal', function (event) {
            $(this).find('modal-content').html("")
            $(this).removeData('bs.modal')
        })

    });
</script>
<style>
    .radio-left {
        float: left;
    }
    .box-canvace .col-sm-3{margin-bottom: 15px;}
    #ProjectId{ width: 400px; max-width: 100%; background: #E6E6E6; }

 

 
 
 
</style>