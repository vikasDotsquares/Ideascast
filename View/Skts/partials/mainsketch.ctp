
<?php
if (isset($sketchdata) && !empty($sketchdata)) {
    foreach ($sketchdata as $key => $sketch) {
        ?>
        <div class="panel panel-default project-block skts_status" id="sketch-div-<?php echo $sketch['ProjectSketch']['id']; ?>" style="width: 100%;opacity: 0;" >
            <div class="panel-heading bg-green" style="padding:12px 2px 12px 10px">
                <h5 class="panel-title tipText nowrap-title" data-original-title="<?php echo $sketch['ProjectSketch']['sketch_title']; ?>">

                    <?php echo ( strlen($sketch['ProjectSketch']['sketch_title']) > 20 ) ? substr(html_entity_decode($sketch['ProjectSketch']['sketch_title']), 0, 20) . '...' : html_entity_decode($sketch['ProjectSketch']['sketch_title']); ?>
                </h5>
                <div class="pull-right card-buttons">
                    <span class=" clickables panel-collapsed  pophover btn btn-xs btn-defaults" data-placement="top"  data-toggle="popover" data-trigger="hover"  data-content="<?php echo $sketch['ProjectSketch']['sketch_description']; ?>" data-project="2">
                        <i class="fa fa-info fa-3 martop"></i> 
                    </span>
                    <a class=" clickable_folder tipText btn btn-xs btn-default" href="<?php echo SITEURL; ?>skts/edit_sketch/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch['ProjectSketch']['id']; ?>" data-original-title="Open Sketch"><i class="fa fa-folder-open"></i>
                    </a>
                </div>
            </div>
            <div class="panel-body" id=""> 
                <div style=" " class="wrap_project_image">
                    <?php
                    $dir = ROOT . "/json/skts/" . $project_id . "/" . $sketch['ProjectSketch']['id'] . "";
                    $filename = "data.json";
                    $filepath = $dir . '/' . $filename;
                    $data = array();
                    if (file_exists($filepath)) {
                        $json = file_get_contents($filepath);
                        $data = json_decode($json, true);
                    }
                    ?>

                    <?php
                    $sketchimg = (isset($data['content']) && !empty($data['content'])) ? $data['content'] : '';
                    ?>
                    <div class="canvas" style="" id="hidden-data-<?php echo $sketch['ProjectSketch']['id']; ?>" style="width:500px; height: 500px;display:block;"><?php echo $sketchimg; ?></div>
                    <div class="can-img canvas_wrapper " id="show-data-<?php echo $sketch['ProjectSketch']['id']; ?>">

                    </div>
                    
                </div>
                <div class="project-detail">
                    <span class="detail-text"><strong>Created On : </strong> 
                        <?php echo _displayDate($sketch['ProjectSketch']['created']); ?></span>
                    <span class="detail-text"><strong>Last Updated : </strong> 
                        <?php echo _displayDate($sketch['ProjectSketch']['modified']);
                        ?></span>
                    <span class="detail-text"><strong>Updated By: </strong> <?php echo $this->Common->userFullname($sketch['ProjectSketch']['updated_by']); ?></span>
                    <span class="detail-text">
                        <strong>Locked By: </strong>
                        <?php
                        if (isset($sketch) && $sketch['ProjectSketch']['locked'] == 1) {
                            echo $this->Common->userFullname($sketch['ProjectSketch']['locked_user_id']);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </span> 
                    <span class="detail-text">
                        <strong>Saved As: </strong>
                        <a class="text-bold filter-saveas text-black" href="javascript:void(0);" data-href="<?php echo SITEURL; ?>skts/filtersaveas/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch['ProjectSketch']['id']; ?>">
                            <?php
                            echo $this->Sketch->saveasCount($sketch['ProjectSketch']['id']);
                            ?>
                        </a>
						<input type="hidden"  id="filter-saveas" value='<?php  echo $this->Sketch->saveasCount($sketch['ProjectSketch']['id']); ?>'>
                    </span> 
                    <?php
                    if (isset($sketch['ProjectSketchParticipant']) && !empty($sketch['ProjectSketchParticipant'])) {
                        $ids = Hash::extract($sketch['ProjectSketchParticipant'], '{n}.created_user_id');
                    } else {
                        $ids = array();
                    }

                    $ids = array_filter($ids);
                    //pr($ids);
                    /*if (isset($ids) && !empty($ids) && in_array($this->Session->read("Auth.User.id"), $ids)) {
                        ?>
                        <button data-id="<?php echo $sketch['ProjectSketch']['id']; ?>" data-original-title="Delete" class="deletesketch btn tipText btn-xs btn-danger pull-right"><i class="fa fa-trash"></i></button>
                        <?php
                    } else {
                        ?>
                        <button disabled="disabled" data-original-title="Delete" class="btn tipText btn-xs btn-danger pull-right"><i class="fa fa-trash"></i></button>
                        <?php
                    }*/
                    ?>
<div class="sketch-but">
                <?php
                if (isset($ids) && !empty($ids) && in_array($this->Session->read("Auth.User.id"), $ids)) {
                        ?>
                        <button data-id="<?php echo $sketch['ProjectSketch']['id']; ?>" data-original-title="Delete" class="deletesketch btn tipText btn-xs btn-danger pull-right"><i class="fa fa-trash"></i></button>
                        <?php
                    } else {
                        ?>
                        <button disabled="disabled" data-original-title="Delete" class="btn tipText btn-xs btn-danger pull-right"><i class="fa fa-trash"></i></button>
                        <?php
                    }
                    ?>
                    </div>
                </div>
                
            </div>
        </div>

        <?php
        //pr($sketch);
    }
}
?> 
<?php if (isset($sketchdata) && empty($sketchdata)) { ?>

    <div class="panel panel-default list-group-item" style="margin: 0px 0px 0px 15px;">
        <div class="idea-canvas-outer  text-center">
            No Sketch Card Found
        </div>
    </div>
    <?php
}
?>
<?php if (isset($sketchdata) && !empty($sketchdata)) { ?>

    <div class="col-sm-12">
        <div class="idea-canvas-outer  text-center">
            <ul class="pagination pull-left">
                <?php
                // echo $this->Paginator->counter(array(
                //     'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total')
                // ));
                ?>  
            </ul>
            <ul class="pagination pull-right">

                <?php
                $this->Paginator->options(array('url' => $this->passedArgs));
                //echo $this->Paginator->prev('< ' . __('previous'), array('disabledTag' => 'a','tag' => 'li'), null, array('class' => 'prev disabled'));
                echo $this->Paginator->numbers(array('currentTag' => 'span', 'tag' => 'li', 'separator' => '', 'currentClass' => 'active'));
                //echo $this->Paginator->next(__('next') . ' >', array('disabledTag' => 'a','tag' => 'li'), null, array('class' => 'next disabled'));
                ?>
            </ul>   
        </div>
    </div>
    <?php
}
?>

<script type="text/javascript" >
    $(document).ready(function(){
        
        $(".deletesketch").on('click', function (e) {
            var $that = $(this);
            var $mid = $(this).data('id');
			
			var mood = $($that).parents('.mainsketchdiv_ajax #sketch-div-'+$mid).find('#filter-saveas').val();
			//console.log(mood);
			if(mood > 0){
			var msg = 'Are you sure you want to delete?<br>';
                msg += ' There are Saved versions of this Sketch, which will also be deleted.<br>';
                msg += 'Close this if you want to keep your versions. ';
			}else{
			 var msg = 'Are you sure you want to delete?<br>';
                
			}
            //alert("22222222222222222222222222"); false;
            BootstrapDialog.show({
                type:BootstrapDialog.TYPE_DANGER,
                closable: true,
                closeByBackdrop: false,
                closeByKeyboard: false,
                title:'Confirmation',
                message: msg,
                buttons: [{
                    label: 'Delete',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function(dialogRef){
                    var href_ = $js_config.base_url + 'skts/delete_sketch/sketch_id:'+$that.data("id");
                        $.post(
                            href_,
                            null,
                            function (responce) {
                                if(responce.success){
									location.reload();
                                }
                            },
                            "json"
                        );
                        
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        dialogRef.getModalBody().html('Deleting this sketch…');
                        setTimeout(function(){
                            $("#sketch-div-"+$that.data("id")).fadeOut("slow");
                            $("#allcountsketch").text(parseInt($("#allcountsketch").text())-1);
							$("#panel-id-"+$that.data("id")).fadeOut("slow");
							
                            dialogRef.close();
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
        $(".filter-saveas").on('click', function (e) {
            var $that = $(this);
            var href_ = $that.data("href");
            $('#ajax_overlay').show();
            $.post(
                href_,
                null,
                function (responce) {
                    if(responce){
                       $(".saveasdiv_ajax").html(responce);
					   setTimeout(function(){
							$(".saveasdiv_ajax > .panel > .panel-heading a").trigger('click');
					   },500)
                       $('#ajax_overlay').hide();
                    }
                },
                "html"
            );
        });
        

        
        $('#ajax_overlay').show();
        $(".canvas_wrapper").on('mouseover', function (e) {
            $(this).addClass('can-img2').removeClass('can-img');
        })
        $(".canvas_wrapper").on('mouseout', function (e) {
            $(this).addClass('can-img').removeClass('can-img2');

        })
        
        
        $(".canvas").each(function () {


            var $that = $(this); // global variable

            //$('.canvas .editable-canvas-image').css('height','120px');
            
            var h = String($that.html()).trim();
            if(h.length > 0) {
            if ($that.hasClass('canvas')) {
                //$that.parents('.wiki-block').find('.description').addClass('in');
                // $that.css('height','120');

                var width = $(this).parents('.can-img:first').clientWidth;
               
                 html2canvas($that.css('width', width), {
                    onrendered: function (canvas) {

                        theCanvas = canvas;
                        //document.body.appendChild(canvas);
                        // $that.parents('.panel:first').removeAttr('style');
						
								
							var dataUrl = canvas.toDataURL('image/png');
							$that.parents('.wrap_project_image:first').css('background-image', 'url(' + dataUrl + ')');
							$that.parents('.wrap_project_image:first').css('background-size', 'contain');
						
                    }

                });
                setTimeout(function () {
                    $that.hide();
                    $that.parents('.wrap_project_image').addClass('nomal');
					
                }, 1000); 
             
			}
            
            }
			setTimeout(function(){
				$that.parents('.panel:first').removeAttr('style');
			}, 1500)
        })
       
        setTimeout(function(){
            $('#ajax_overlay').hide();
        },2500);


 });
</script>
