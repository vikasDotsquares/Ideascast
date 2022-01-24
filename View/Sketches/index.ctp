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
                                <a href="<?php echo SITEURL;?>sketches/add_sketch/project_id:<?php echo $project_id;?>" class="btn btn-warning"><i class="fa fa-fw fa-plus"></i> Add canvas</a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <?php 
                                if(isset($sketchdata) && !empty($sketchdata)){
                                    foreach($sketchdata as $key => $sketch){
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="idea-canvas-outer list-group-item">
                                                <h5><?php echo $sketch['ProjectSketch']['sketch_title'];?></h5>
                                                <div class="idea-canvas-inner list-group-item">
                                                    <?php //echo $sketch['ProjectSketch']['content'];?>
                                                    <?php echo $this->Html->image("process_drawing_bg_1.jpg",array("style"=>""));?>
                                                </div>
                                                <div class="idea-canvas-btn-group">
                                                    <a data-id="<?php echo $sketch['ProjectSketch']['id'];?>" title="Edit" data-href="<?php echo SITEURL;?>sketches/edit_sketch/project_id:<?php echo $project_id;?>/sketch_id:<?php echo $sketch['ProjectSketch']['id'];?>" class="tipText btn btn-default edit_mode_check"><i class="fa fa-pencil"></i></a>
                                                    <a  title="View" href="<?php echo SITEURL;?>sketches/view_sketch/project_id:<?php echo $project_id;?>/sketch_id:<?php echo $sketch['ProjectSketch']['id'];?>" class="btn btn-default tipText "><i class="fa fa-folder-open"></i></a>
                                                    <a href="#" data-href="<?php echo SITEURL;?>sketches/delete_sketch/project_id:<?php echo $project_id;?>/sketch_id:<?php echo $sketch['ProjectSketch']['id'];?>" class="btn btn-default confirm_delete tipText " title="Delete"><i class="fa fa-trash"></i></a>
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
                
        $("body").delegate(".edit_mode_check", 'click', function (event) {
        event.preventDefault()
        var $t = $(this)
        var params = {};
        
            $.ajax({
                url: $js_config.base_url + "sketches/is_edit_sketch/project_id:<?php echo $project_id; ?>/sketch_id:" + $t.data("id"),
                type: "POST",
                data: $.param(params),
                dataType: "JSON",
                global: true,
                success: function (response) {
                    if(response.is_edit_mode  ==  0){
                        BootstrapDialog.show({
                            title: 'Confirmation',
                            message: 'Are you sure you want to edit this sketch?',
                            type: BootstrapDialog.TYPE_DANGER,
                            draggable: true,
                            buttons: [
                                {
                                    label: ' Yes',
                                    cssClass: 'btn-success',
                                    autospin: true,
                                    action: function (dialogRef) {
                                        window.location = $t.data("href");
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
                    }else{
                        BootstrapDialog.show({
                            title: 'Information',
                            message: response.msg,
                            type: BootstrapDialog.TYPE_DANGER,
                            draggable: true,
                            buttons: [
                                {
                                    label: ' OK',
                                    cssClass: 'btn-danger',
                                    action: function (dialogRef) {
                                        dialogRef.close();
                                    }
                                }
                            ]
                        });
                    }
                }
            })
        })
        
        
        
        
        
        
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

 
 .idea-canvas-inner  iframe { display:none;}
 
.idea-canvas-inner img:not(:first-child)  {
    display: none !important;
}
.idea-canvas-inner p:not(:first-child)  {
    display: none !important;
}

 
 
 
</style>