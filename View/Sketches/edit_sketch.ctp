<?php
include 'partials/header_js_css.ctp';
?>
<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">

        <?php
        include 'partials/header_inner.ctp';


//        $content  = json_decode($sketchdata['ProjectSketch']['content']);
//        $canvasData  = json_decode($sketchdata['ProjectSketch']['canvas_data']);
//        $imagesData  = json_decode($sketchdata['ProjectSketch']['images_data']);
//        $configContainer = json_decode($sketchdata['ProjectSketch']['config_container']);

        $content = $sketchdata['ProjectSketch']['content'];
        $canvasData = $sketchdata['ProjectSketch']['canvas_data'];
        $imagesData = $sketchdata['ProjectSketch']['images_data'];
        $configContainer = $sketchdata['ProjectSketch']['config_container'];
        ?>



        <!-- MAIN CONTENT -->
        <div class="box-content wiki">

            <div class="row ">
                <div class="col-xs-12">

                    <div class="bg-white">    
                        <div class="fliter margin-top" style="padding :15px 0 0 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd">
                            <div class="panel-body" style="padding:0 15px 0 30px;">
                                <div class="form-group">
                                    <div class="pull-left">
                                        <label>Sketch Name: </label>
                                        <label style="" class="custom-dropdown">
                                            <div class="input select">
                                                <select data-project-id="<?php echo $project_id; ?>" id="project" style="width:400px;" class="form-control title_limit title aqua" name="data[ProjectSketch][sketch_id]">
                                                    <option value="">Select Sketcher</option>
                                                        <?php
                                                        if (isset($sketchlist) && !empty($sketchlist)) {
                                                            foreach ($sketchlist as $k => $sketch) {
                                                                if ($sketchdata['ProjectSketch']['id'] == $k) {
                                                                    echo '<option selected="selected" value="' . $k . '">' . $sketch . '</option>';
                                                                } else {

                                                                    echo '<option value="' . $k . '">' . $sketch . '</option>';
                                                                }
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
                                        <!--   <a href="<?php echo SITEURL; ?>sketches/index/project_id:<?php echo $project_id; ?>" class="btn btn-warning">Back</a>-->
                                    </div>

                                </div>

                            </div>

                        </div>

<?php echo $this->Session->flash(); ?>
                        <?php
                        // pr($project_id);
                        ?>

                        <div class="box noborder padding  " id="box_body">
                            
                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <div class="col-sm-12">
                                    <div class="idea-canvas-outerd list-group-itemd">
                                        
                                        <div class="form-group clearfix">

                                            <div class="col-sm-12"> 
                                                <label>Description:</label>   
                                                <input type="text" name="sketch_description" id="sketch_description" value="<?php echo $sketchdata['ProjectSketch']['sketch_description']; ?>" class="form-control input-small" placeholder="Description" />
                                                <input type="hidden" name="sketch_title" id="sketch_title" value="<?php echo $sketchdata['ProjectSketch']['sketch_title']; ?>" class="form-control input-small" placeholder="Description" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                            <textarea id="sketch_editor" class="redactor" cols="30" rows="10" ></textarea>
                                            </div>
                                        </div> 
                                        <div style="display:none;">
                                            <input type="hidden" name="sketch_id" id="project_id" value="<?php echo $sketchdata['ProjectSketch']['project_id']; ?>" /><input type="hidden" name="sketch_id" id="sketch_id" value="<?php echo $sketchdata['ProjectSketch']['id']; ?>" />
                                            <h4>Config</h4>    
                                            <textarea id="redactor-config-container" readonly="readonly" style="width:100%; height:120px;"></textarea>
                                            <h4>Canvas Data</h4>
                                            <textarea id="drawer-canvas-data-container" readonly="readonly" style="width:100%; height:120px;"></textarea>
                                            <h4>Canvas Images</h4>
                                            <textarea id="drawer-canvas-images-container" readonly="readonly" style="width:100%; height:120px;"></textarea>

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
</div>	

<script>
    $(document).ready(function () {
        var projectIdContainer = '#project_id';
        var sketchIdContainer = '#sketch_id';
        var sketch_editor = '#sketch_editor';
        var sketchTitleContainer = '#sketch_title';
        var sketchDescriptionContainer = '#sketch_description';
        var configDataContainer = '#redactor-config-container';
        var canvasDataContainer = '#drawer-canvas-data-container';
        var imagesDataContainer = '#drawer-canvas-images-container';

        function save_sketch_data() {

            var redactor = $(sketch_editor).data('redactor'), box = $('body');
            redactor = $(sketch_editor).data('redactor');
            if (redactor) {
                box = redactor.core.getBox();
            }
            redactor = $(sketch_editor).data('redactor');
            var dfd = $.Deferred();
            var canvases = redactor.drawer.get();
            if (canvases.length > 0) {
                for (var c = 0; c < canvases.length; c++) {
                    canvases[c].stopEditing();
                }
            }

            $(sketch_editor).redactor('focus.setEnd');
            $(sketch_editor).redactor('code.startSync');

            var sketch_id = $(sketchIdContainer).val();
            var project_id = $(projectIdContainer).val();

            var content = $(sketch_editor).redactor('code.get');

            if ($(sketchTitleContainer).is("input") || $(sketchTitleContainer).is("hidden")) {
                var sketch_title = $(sketchTitleContainer).val();
            } else {
                var sketch_title = $(sketchTitleContainer).text();
            }
            if ($(sketchDescriptionContainer).is("input") || $(sketchDescriptionContainer).is("text")) {
                var sketch_description = $(sketchDescriptionContainer).val();
            } else {
                var sketch_description = $(sketchDescriptionContainer).text();
            }

            if ($(configDataContainer).is("input") || $(configDataContainer).is("textarea")) {
                var config = $(configDataContainer).val();
            } else {
                var config = $('#redactor-config-container').text();
            }
            if ($(canvasDataContainer).is("input") || $(canvasDataContainer).is("textarea")) {
                var canvas_data = $(canvasDataContainer).val();
            } else {
                var canvas_data = $(canvasDataContainer).text();
            }

            if ($(imagesDataContainer).is("input") || $(imagesDataContainer).is("textarea")) {
                var images_data = $(imagesDataContainer).val();
            } else {
                var images_data = $(imagesDataContainer).text();
            }

            /*var $content = $('<div />',{html:content});
             $content.find('.editable-canvas-image').removeClass("edit-mode");
             content =  $content.html();*/

            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
                "sketch_title": sketch_title,
                "sketch_description": sketch_description,
                "config": config,
                "content": content,
                "canvas_data": canvas_data,
                "images_data": images_data
            };
            box.addClass('loading');
            $.post(
                    $js_config.base_url + 'skts/update_sketch',
                    params,
                    function (responce) {
                        box.removeClass('loading');
                    },
                    "json"
                    );



        }
        function load_sketch_data() {
            var redactor = $(sketch_editor).data('redactor'), box = $('body');
            redactor = $(sketch_editor).data('redactor');
            $(sketch_editor).redactor('code.startSync');
            var project_id = $(projectIdContainer).val();
            var sketch_id = $(sketchIdContainer).val();
            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
            };
            $.post(
                    $js_config.base_url + 'skts/load_sketch/' + sketch_id,
                    params,
                    function (response) {
                        if (response['success'] && response['data'] && response['data'] !== '') {

                            $(sketch_editor).redactor('code.set', response['data']['content']);

                            if ($(sketchTitleContainer).is("input") || $(sketchTitleContainer).is("hidden")) {
                                $(sketchTitleContainer).val(response['data']['sketch_title']);
                            } else {
                                $(sketchTitleContainer).text(response['data']['sketch_title']);
                            }
                            if ($(sketchDescriptionContainer).is("input") || $(sketchDescriptionContainer).is("text")) {
                                $(sketchDescriptionContainer).val(response['data']['sketch_description']);
                            } else {
                                $(sketchDescriptionContainer).text(response['data']['sketch_description']);
                            }

                            if ($(configDataContainer).is("input") || $(configDataContainer).is("textarea")) {
                                $(configDataContainer).val(response['data']['config']);
                            } else {
                                $(configDataContainer).text(response['data']['config']);
                            }
                            if ($(canvasDataContainer).is("input") || $(canvasDataContainer).is("textarea")) {
                                $(canvasDataContainer).val(response['data']['canvas_data']);
                            } else {
                                $(canvasDataContainer).text(response['data']['canvas_data']);
                            }

                            if ($(imagesDataContainer).is("input") || $(imagesDataContainer).is("textarea")) {
                                $(imagesDataContainer).val(response['data']['images_data']);
                            } else {
                                $(imagesDataContainer).text(response['data']['images_data']);
                            }

                            $(sketch_editor).redactor('focus.setEnd');
                        }
                    },
                    "json"
                    );
        }
        var redactorConfig = {
            focus: true,
            /*autosave : 	$js_config.base_url + 'skts/autosave_sketch',
             autosaveName : 'content',
             autosaveInterval : 30, // seconds
             autosaveOnChange : true,
             autosaveFields : {				
             "sketch_id":'<?php echo $sketchdata['ProjectSketch']['id']; ?>',
             "sketch_title":$("#sketch_title").val(),
             "configContainer":$('#redactor-config-container').text(),
             "canvasData":$('#drawer-canvas-data-container').text(),
             "imagesData":$('#drawer-canvas-images-container').text()
             },*/
            toolbarFixedTopOffset: 80,
            buttonSource: true,
            //imageUpload: $js_config.base_url +'img/',
            //imageManagerJson: $js_config.base_url +'img/',
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
                //defaultImageUrl: '<?php echo SITEURL; ?>/img/blank.png',
                debug: false,
                texts: CustomLocalization,
                contentConfig: {
                    // if true, drawing result will be converted to
                    // base64 png string and set as a source of drawer's image
                    // saveAfterInactiveSec: 5,
                    saveInHtml: false,
                    imagesContainer: imagesDataContainer,
                    canvasDataContainer: canvasDataContainer,
                    saveCanvasData: function (canvas_id, serializedCanvas) {

                    }
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
                defaultActivePlugin: {name: 'Pencil', mode: 'lastUsed'},
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
                    Image: {
                        maxImageSizeKb: 1024, // 1 MB
                        scaleDownLargeImage: true,
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
            },
            callbacks: {
                init: function ()
                {
                    console.log("Init");
                },
                focus: function (e)
                {
                    console.log('focus', this.code.get());
                },
                blur: function (e)
                {
                    console.log('blur', this.code.get());
                }
            }
        };

        $('#sketch_editor').redactor(redactorConfig);
        var redactor = $(sketch_editor).data('redactor'), box = $('body');
        redactor = $(sketch_editor).data('redactor');
        console.log('redactor:', redactor);

        $("#save_sketch_data").on('click', function (event) {
            save_sketch_data();
        });
        $("#load_sketch_data").on('click', function (event) {
            load_sketch_data();
        });
        load_sketch_data();
        $('body').on('hidden.bs.modal', function (event) {
            $(this).find('modal-content').html("")
            $(this).removeData('bs.modal')
        })

    });
</script>
<style>

    .redactor-box.loading::before {
        background-color:#fff;
        background-image:url('/ideascomposer/images/ajax-loader.gif');
        background-position:center center;
        background-repeat:no-repeat;
        content: "";
        height: 100%;
        left: 0;
        opacity: 0.5;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 2000;
    }
    .idea-canvas-inner-add{
        min-height: 400px;
    }
    .radio-left {
        float: left;
    }
    .box-canvace .col-sm-3{margin-bottom: 15px;}
    #ProjectId{ width: 400px; max-width: 100%; background: #E6E6E6; }

</style>