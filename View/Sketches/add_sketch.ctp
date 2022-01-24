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
                        <div class="fliter margin-top" style="padding :15px 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd">
					                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <div class="pull-left">
                                                        <label>Sketch Name: </label>
                                                        <label style="" class="custom-dropdown">
                                                                <div class="input select">
                                                                    <select data-project-id="<?php echo $project_id;?>"  id="project" style="width:400px;" class="form-control title_limit title aqua" name="data[ProjectSketch][sketch_id]">
                                                                        <option value="">Select Sketcher</option>
                                                                        <?php 
                                                                        if(isset($sketchlist) && !empty($sketchlist)){
                                                                            foreach($sketchlist as $k => $sketch){
                                                                                echo '<option value="'.$k.'">'.$sketch.'</option>';
                                                                            }
                                                                            
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>		
                                                        </label>
                                                    </div>
                                                    <div class="pull-right">
                                                        <button type="button" id="save_sketch_data" class="btn btn-primary  savedatacan">Save</button>
                                                        <button type="button" id="load_sketch_data" class="btn btn-primary">Load</button>
                                                        <a href="<?php echo SITEURL;?>sketches/index/project_id:<?php echo $project_id;?>" class="btn btn-warning">Back</a>
                                                    </div>
                                                    
                                                </div>
  
                                            </div>
					
					</div>
                        

                        <div class="box noborder padding margin-top" id="box_body">
                            
                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <div class="col-sm-12">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <input name="sketch_title" id="sketch_title" value="Demo one" class="form-control dates input-small" />
                                        </div>
                                        <div class="form-group">
                                            <textarea class="redactor" cols="30" rows="10" ></textarea>
                                        </div>
                                        <div style="display: none;" id="redactor-config-container"></div>
                                        <div style="display: none;" id="drawer-canvas-data-container" contenteditable="true"></div>
                                        <div style="display: none;" id="drawer-canvas-images-container"></div>
                                    </div>

                                </div>

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
    var saveUsingHtmlContainers = function () {
        
        var content = $(".redactor-editor").html();//$('.redactor').redactor('code.get');
        var canvasData = $('#drawer-canvas-data-container').text();
        var imagesData = $('#drawer-canvas-images-container').text();
        var configContainer = $('#redactor-config-container').text();
        
        console.log('content to save:');
        console.log(content);
        console.log(canvasData);
        console.log(imagesData);
        
        
        localStorage.setItem('content', content);
        localStorage.setItem('contentCanvasData', canvasData);
        localStorage.setItem('contentImagesData', imagesData);
        localStorage.setItem('contentconfigContainer', configContainer);
        
        
        
        
        
        
        update_sketch($("#sketch_title").val(),content,canvasData,imagesData,configContainer,null);
    };

    var load = function () {
        // firstly clean all content from redactor
        $('.redactor').redactor('code.set', '');
        $('.redactor').redactor('code.startSync');

        var content = localStorage.getItem('content');
        var canvasData = localStorage.getItem('contentCanvasData');
        var imagesData = localStorage.getItem('contentImagesData');
        var configContainer = localStorage.getItem('contentconfigContainer');

        $('#redactor-config-container').text(configContainer);
        $('#drawer-canvas-data-container').text(canvasData);
        $('#drawer-canvas-images-container').text(imagesData);
        $('.redactor').redactor('code.set', content);
    };

    $(document).ready(function () {
        var redactorConfig = {
            buttonSource: true,
            //imageUpload: '/imageUpload/',
            //imageManagerJson: '/imageManagerJson/',
            buttons: [
                'bold',
                'html',
                'image'
            ],
            plugins: [
                'callbacksFix',
                'video',
                'drawer'
            ],
            drawer: {
                // this function will be called when canvas needs to determine
                // whether it is working on touch device
//                detectTouch: function(){
//                    // if TRUE is returned canvas will assume that it is
//                    // working on touch device
//                    return true;
//                },
                debug: true,
                texts: CustomLocalization,
                contentConfig: {
                    // if true, drawing result will be converted to
                    // base64 png string and set as a source of drawer's image
                    saveAfterInactiveSec: 600000,
                    saveInHtml: true,
                    imagesContainer: '#drawer-canvas-images-container',
                    canvasDataContainer: '#drawer-canvas-data-container'
                },

                //basePath: '/',

                toolbarSize: 35, // width and height of toolbar buttons
                toolbarSizeTouch: 43,

                tooltipCss: {
                    color: 'white',
                    background: 'black'
                },

                backgroundCss: 'transparent',

                activeColor: '#19A6FD',

                canvasProperties: {
                    selectionColor: 'rgba(255, 255, 255, 0.3)',

                    // first digit is space length between dashes and second is dashes length
                    selectionDashArray: [3, 8],

                    selectionBorderColor: '#5f5f5f'
                },

                objectControls: {
                    borderColor: 'rgba(102,153,255,0.75)',
                    borderOpacityWhenMoving: 0.4,
                    cornerColor: 'rgba(102,153,255,0.5)',
                    cornerSize: 12,
                    hasBorders: true
                },

                objectControlsTouch: {
                    borderColor: 'rgba(102,153,255,0.75)',
                    borderOpacityWhenMoving: 0.4,
                    cornerColor: 'rgba(102,153,255,0.5)',
                    cornerSize: 20,
                    hasBorders: true
                },

                plugins: [
                    // Drawing tools
                    'Pencil',
                    'Eraser',
                    'Text',
                    'Line',
                    'ArrowOneSide',
                    'ArrowTwoSide',
                    'Triangle',
                    'Rectangle',
                    'Circle',
                    'Image',
                    'Polygon',

                    // Drawing options
                    //'ColorpickerHtml5',
                    'Colorpicker',
                    'ShapeBorder',
                    'BrushSize',
                    'Resize',
                    'Fullscreen',
                    'MovableFloatingMode'
                ],
                
                defaultActivePlugin : {name :'Pencil', mode : 'lastUsed'},


                pluginsConfig: {
                    'ShapeBorder': {
                        color: 'rgba(0, 0, 0, 0)'
                    },
                    'Pencil': {
                        // 'pencil' is default build-in icon which is font-awesome fa-pencil icon converted to cur
                        // Any other cursor should be specified in css-url format like:
                        // "url(path/to/cur/file.cur), default"
                        // where "default" is cursor name that will be applied if url is not available.
                        // read more here: https://developer.mozilla.org/en-US/docs/Web/CSS/cursor
                        cursorUrl: 'pencil',
                        brushSize: 3
                    },
                    'Eraser': {
                        brushSize: 5
                    },
                    'Circle': {
                        centeringMode: 'normal'
                    },
                    'Rectangle': {
                        centeringMode: 'normal'
                    },
                    'Triangle': {
                        centeringMode: 'normal'
                    },
                    'Text': {
                        // keys here are font names displayes in the list, values are css properties for font-family
                        fonts: {
                            'Georgia': 'Georgia, serif',
                            'Palatino': "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
                            'Times New Roman': "'Times New Roman', Times, serif",

                            'Arial': 'Arial, Helvetica, sans-serif',
                            'Arial Black': "'Arial Black', Gadget, sans-serif",
                            'Comic Sans MS': "'Comic Sans MS', cursive, sans-serif",
                            'Impact': 'Impact, Charcoal, sans-serif',
                            'Lucida Grande': "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
                            'Tahoma': 'Tahoma, Geneva, sans-serif',
                            'Trebuchet MS': "'Trebuchet MS', Helvetica, sans-serif",
                            'Verdana': 'Verdana, Geneva, sans-serif',

                            'Courier New': "'Courier New', Courier, monospace",
                            'Lucida Console': "'Lucida Console', Monaco, monospace"
                        },
                        // default font name
                        defaultFont: 'Palatino'
                    },
                    Image : {
                        maxImageSizeKb : 1024, // 1 MB
                        scaleDownLargeImage : true,
                        acceptedMIMETypes: ['image/jpeg', 'image/png', 'image/gif']
                    },
                    'ShapeContextMenu': {
                        position: {
                            // 'rightBottom': context menu will be placed at shape's right bottom corner
                            // 'cursor': context menu will be placed in the position of click
                            touch: 'cursor',
                            mouse: 'cursor'
                        }
                    }
                }
            }
        };

        $('.redactor').redactor(redactorConfig);
    });
</script>
<script>

    function update_sketch(sketch_title,content,canvasData,imagesData,configContainer,sketch_id){
        var params = {sketch_title:sketch_title,content:content,canvasData:canvasData,imagesData:imagesData,configContainer:configContainer,sketch_id:sketch_id};
        $.ajax({
            url: $js_config.base_url + 'sketches/update_sketch/project_id:<?php echo $project_id; ?>',
            type: "POST",
            data: $.param(params),
            dataType: "JSON",
            global: false,
            success: function (response) {
                window.location = $js_config.base_url + "sketches/edit_sketch/project_id:<?php echo $project_id; ?>/sketch_id:" + response.sketch_id;
            }
        })
    }
    $(function () {
        $( "body" ).delegate( "#project", "change", function() {
            $current = $(this)
            if($current.val() == ''){
                 window.location = $js_config.base_url + "sketches/add_sketch/project_id:"+$current.data("project-id");
             }else{
                 window.location = $js_config.base_url + "sketches/edit_sketch/project_id:"+$current.data("project-id")+"/sketch_id:" + $current.val();
             }
        })
        $('body').on('hidden.bs.modal', function (event) {
            $(this).find('modal-content').html("")
            $(this).removeData('bs.modal')
        })

    });
</script>
<style>
    .idea-canvas-inner-add{
        min-height: 400px;
    }
    .radio-left {
        float: left;
    }
    .box-canvace .col-sm-3{margin-bottom: 15px;}
    #ProjectId{ width: 400px; max-width: 100%; background: #E6E6E6; }

</style>