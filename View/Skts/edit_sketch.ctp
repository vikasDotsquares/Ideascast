<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<?php
include 'partials/header_js_css.ctp';

$p_permission = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

$user_project = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));


/*  group Work Permission and group permission and level check */

$grp_id = $this->Group->GroupIDbyUserID($project_id,$this->Session->read('Auth.User.id'));

if(isset($grp_id) && !empty($grp_id)){

$group_permission = $this->Group->group_permission_details($project_id,$grp_id);
if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
	$project_level = $group_permission['ProjectPermission']['project_level'];
}

}

/*  Full level all elements */




?>
<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">


        <!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php echo strip_tags($project_detail['title']); ?>
                    <p class="text-muted date-time" style="padding: 6px 0">
                        <span><?php echo 'Sketch Created: ' . _displayDate( $this->request->data['ProjectSketch']['created']).' &nbsp; Updated: ' . _displayDate( $this->request->data['ProjectSketch']['modified']); ?></span>
                    </p>
                </h1>
            </section>
        </div>

        <!-- END HEADING AND MENUS -->
        <span id="project_header_image" class="">
            <?php
            $style = '';
            echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id, 'style' => $style));

			//pr($this->request->data);
			?>
        </span>


        <!-- MAIN CONTENT -->
        <div class="box-content wiki">

            <div class="row ">
                <div class="col-xs-12">

                    <div class="box noborder " style=" ">
                        <div class="box-header filters editsketch-select">
                            <div class="col-sm-12 col-md-12 col-lg-12 nopadding-left">

                                <div class="">
                                    <div class="col-sm-12 col-md-3 col-lg-5 padding-ipad1">
                                        <div class="form-group row" style="margin: 0;">
                                            <label for="example-text-input" class=" col-form-label pull-left hidden-md hidden-sm" style="margin-top: 7px;">Project Sketches: </label>
                                            <div class="col-xs-8 col-sm-12 col-md-12 col-lg-8 padding-ipad">
                                                <label style="width: 100%; vertical-align:middle;" class="custom-dropdown">
                                                    <select onchange="select_sketch();" id="ProjectSketchId" class="form-control aqua" name="sketch_id">
                                                        <option value="">Select Sketch</option>
                                                        <?php
                                                        //pr($sketchdata,1);
                                                        if (isset($sketchdatalist) && !empty($sketchdatalist)) {
                                                            ?>
                                                            <?php foreach ($sketchdatalist as $k => $v) {
                                                               if(isset($v['ProjectSketch']['sketch_title']) && !empty($v['ProjectSketch']['sketch_title'])){
                                                            ?>
                                                                <option value="<?php echo $v['ProjectSketch']['id']; ?>"
                                                                <?php if ($sketch_id == $v['ProjectSketch']['id']) { ?>
                                                                            selected="selected"
                                                                        <?php } ?>
                                                                        >
                                                                            <?php echo $v['ProjectSketch']['sketch_title']; ?>
                                                                </option>
                                                            <?php }
                                                            }
                                                            ?>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="pull-right">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <a  data-href="<?php echo SITEURL; ?>skts/sketch_pdf/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>" <?php echo (empty($sketch_id) || $sketch_id == null) ? 'disabled="disabled"' : '' ?> data-original-title="PDF"  class="btn tipText btn-default btn-sm sketch-pdf-download">
                                                <i class="fas fa-file-pdf fa-fw"></i>
                                            </a>
                                        </div>
                                        <div class="btn-group">
                                            <button  type="button" data-toggle="modal" data-target="#sketch_description_pop" <?php echo (empty($sketch_id) || $sketch_id == null) ? 'disabled="disabled"' : '' ?> data-original-title="Sketch Description" class="btn tipText btn-sm btn-success">
                                                <i class="fa fa-fw"></i>
                                            </button>
                                            <?php
											 if(isset($sketchdata['ProjectSketchParticipant']) && !empty($sketchdata['ProjectSketchParticipant'])){
												$ids = Hash::extract($sketchdata['ProjectSketchParticipant'], '{n}.created_user_id');
											}else{
												$ids = array();
											}

											$ids = array_filter($ids);
											//pr($ids);

											$createrSketcher = 0;

											if(isset($ids) && !empty($ids) && in_array($this->Session->read("Auth.User.id"),$ids)){
												$createrSketcher = 1;
											}


                                         if (isset($sketchdata) &&  ($sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] == $this->Session->read("Auth.User.id")) || $sketchdata['ProjectSketch']['is_edit_mode'] == 0) {

										if(((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1))  || ($createrSketcher == 1) ) {


                                                ?>
                                              <a <?php echo (empty($sketch_id) || $sketch_id == null) ? 'disabled="disabled"' : '' ?> data-original-title="Update Properties" href="<?php echo SITEURL; ?>skts/edit/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>" class="tipText btn btn-sm btn-success">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php
											 }

											}

											?>

                                        </div>
                                        <div class="btn-group">
                                            <a href="<?php echo SITEURL; ?>skts/add/project_id:<?php echo $project_id; ?>" class=" btn btn-sm btn-warning">
                                                <i class="fa fa-plus"></i>
                                                Create Sketch
                                            </a>
                                        </div>
                                        <?php
                                        if (isset($sketchdata) && ($sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] == $this->Session->read("Auth.User.id")) || $sketchdata['ProjectSketch']['is_edit_mode'] == 0) {
                                            ?>
                                            <div class="btn-group">
                                                <button type="button"  disabled="disabled" id="save_sketch_data" class="disabledbtn btn btn btn-success btn-sm savedatacan">Save</button>
                                            </div>
                                             <div class="btn-group">
                                                <button type="button"  disabled="disabled" data-original-title="Release Edit Control" id="save_cancel_sketch_data" class="tipText btn btn btn-success btn-sm disabledbtn savedatacan">Release Editing</button>
                                            </div>

                                            <div class="btn-group">
                                                <button  disabled="disabled" class="btn disabledbtn btn btn-primary btn-sm" id="save_as_sketch_data" >Save As</button>
                                            </div>
<!--                                        <div class="btn-group">
                                                <button type="button" id="save_sketch_data" class="btn btn-primary btn-sm savedatacan">Save</button>
                                            </div>
                                            <div class="btn-group">
                                                <button disabled="disabled" class="btn disabledbtn  btn-primary btn-sm" id="save_as_sketch_data" >Save As</button>
                                            </div>-->
                                        <?php } ?>
                                        <div class="btn-group">
                                            <button disabled="disabled" class="btn btn-danger btn-sm disabledbtn edit_sketch_cancel" data-href="<?php echo SITEURL; ?>skts/cancel/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>">Close</button>
                                        </div>

                                    </div>
                                </div>

<?php //echo $this->Form->end();  ?>
                            </div>
                        </div>

<?php echo $this->Session->flash(); ?>
                        <?php
                        // pr($project_id);
                        if (isset($sketchdata['ProjectSketch']['sketch_description']))
                            $dsc = $sketchdata['ProjectSketch']['sketch_description'];
                        else
                            $dsc = '';
                        ?>

                        <div class="box noborder padding  " id="box_body" style="min-height: 700px;">

                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <div class="col-sm-12">
                                    <div class="idea-canvas-outerd list-group-itemd">

                                        <div style="display:none;" class="form-group clearfix">

                                            <div class="col-sm-12">
                                                <label>Sketch Title:</label>
                                                <input type="hidden" name="is_edit_mode" value="1" />
                                                <input type="hidden" name="edit_user_id" value="<?php echo $this->Session->read("Auth.User.id"); ?>" />

                                                <input type="text" name="sketch_title" id="sketch_title" value="<?php echo $sketchdata['ProjectSketch']['sketch_title']; ?>" class="form-control input-small" placeholder="Max chars allowed 50" /><span class="error-message text-danger" ></span>
                                            </div>
                                        </div>
                                        <div style="display:none;"  class="form-group clearfix">

                                            <div class="col-sm-12">
                                                <label>Description:</label>
                                                <input type="text" name="sketch_description" id="sketch_description" value="<?php echo $dsc; ?>" class="form-control input-small" placeholder="Max chars allowed 250" /><span class="error-message text-danger" ></span>

                                            </div>
                                        </div>
                                        <div class="form-group clearfix" style="margin-bottom:15px;">
                                            <div class="col-sm-12">
                                                <label>Participants: </label>

                                                <div id="sketch-user-load" class="users  sketch-thumb">

<?php include 'partials/participant.ctp';?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <textarea id="sketch_editor" class="redactor" cols="30" rows="10" ></textarea>
                                            </div>
                                        </div>

										<?php

										if(isset($sketchdata['ProjectSketch']['parent_id']) && $sketchdata['ProjectSketch']['parent_id'] > 0){
											//$skt_save_as_id = $sketchdata['ProjectSketch']['parent_id'];
										}else{
											//$skt_save_as_id = $sketchdata['ProjectSketch']['id'];
										}
										?>
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

<!-- Modal -->
<div id="sketch_description_pop" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Sketch Description</h4>
            </div>
            <div class="modal-body">
                <p><?php echo $dsc; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<div id="imagedata" ></div>
<div id="load_sketch_data_reload" ></div>
<div id="load_sketch_data_reload_self" ></div>
<script type="text/javascript" >
    var $toolbarval = true;
</script>

<!-- END MODAL BOX -->
<script type="text/javascript" >
    function select_sketch() {
        var id = $("#ProjectSketchId").val();
        if (id) {
            window.location = "<?php echo SITEURL; ?>skts/edit_sketch/project_id:<?php echo $project_id ?>/sketch_id:" + id;
        } else {
            window.location = "<?php echo SITEURL; ?>skts/edit_sketch/project_id:<?php echo $project_id ?>";
        }
    }


</script>
<script type="text/javascript" >
    $(document).ready(function () {
        setTimeout(function(){
            $(".disabledbtn").removeAttr("disabled");
        },5000);



         $(".sketch-pdf-download").on('click', function (e) {
            var $that = $(this);
            BootstrapDialog.show({
                type:BootstrapDialog.TYPE_SUCCESS,
                closable: true,
                closeByBackdrop: false,
                closeByKeyboard: false,
                title:'Confirmation',
                message: 'Are you sure you want to Download?',
                buttons: [{
                    label: 'Download',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        dialogRef.getModalBody().html('Downloading this sketch…');
                        setTimeout(function(){
                            dialogRef.close();
                            window.location = $that.data("href");
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






       // $(".q-thumb .user-checkbox").change(function(){
       $( "body" ).delegate( ".q-thumb .user-checkbox", "change", function() {
            var $this = $(this);
            var cur_user = "<?php echo $this->Session->read("Auth.User.id");?>";
            var status = $this.prop("checked");


            if(cur_user == $this.val()){
                var href_ = $js_config.base_url + 'skts/send_interest/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>/status:'+status+'/user_id:'+cur_user;
                $.post(
                    href_,
                    null,
                    function (responce) {
                        if(responce.success){
                           // $this.prop("checked");
                        }
                    },
                    "json"
                );
            }
            if(cur_user != $this.val() && status == true){
                $this.removeAttr("checked");
                $this.prop("checked",false)
                console.log(status,'checked');
            }
            else if(cur_user != $this.val() && status == false){
                $this.removeAttr("checked");
                $this.prop("checked",true)
                console.log(status,'not checked');
            }

        });
        $(".edit_sketch_cancel").click(function(){
            var sketch_editor = '#sketch_editor';
            var redactor = $(sketch_editor).data('redactor'), box = $('body');
            redactor = $(sketch_editor).data('redactor');
            if (redactor) {
                box = redactor.core.getBox();
            }
            //box.addClass('loading');
            $('#ajax_overlay').show();
            var href_ = $(this).data("href");
            $.post(
                href_,
                null,
                function (responce) {
                    box.removeClass('loading');
                    if(responce.success){
                        window.location = "<?php echo SITEURL; ?>skts/index/project_id:<?php echo $project_id; ?>";
                    }
                },
                "json"
            );
        });



        var title = 50;
        $("#sketch_title").keyup(function () {
            var $ts = $(this)
            if ($(this).val().length > title) {
                $(this).val($(this).val().substr(0, title));
            }
            var remaining = title - $(this).val().length;
            $(this).next().html("Char 50 , <strong>" + $(this).val().length + "</strong> characters entered.");
            if (remaining <= 10)
            {
                $(this).next().css("color", "red");
            }
            else
            {
                $(this).next().css("color", "red");
            }
        });
        var characters = 250;
        $("#sketch_description").keyup(function () {
            var $ts = $(this)
            if ($(this).val().length > characters) {
                $(this).val($(this).val().substr(0, characters));
            }
            var remaining = characters - $(this).val().length;
            $(this).next().html("Char 250 , <strong>" + $(this).val().length + "</strong> characters entered.");
            if (remaining <= 10)
            {
                $(this).next().css("color", "red");
            }
            else
            {
                $(this).next().css("color", "red");
            }
        });


        $( "body" ).delegate( ".self_edit", "change", function() {
            $(this).prop("checked",true);
        });
        $( "body" ).delegate( ".other_user", "change", function() {
            $(this).prop("checked",false);
        });
    });
</script>
<?php
if($sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->Session->read("Auth.User.id")){
?>
<script type="text/javascript" >
    $(document).ready(function () {
        $toolbarval = false;
        //console.log($toolbarval,'----------------');
        setInterval(function() {
            var project_id = $('#project_id').val();
            var sketch_id = $('#sketch_id').val();
            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
            };
            $("#load_sketch_data_reload").trigger("click");
            //$("#sketch-user-load").load($js_config.base_url + "skts/participant/"+ sketch_id);
            $(".tooltip").fadeOut();
            $('.popover').remove();
        }, 5000);
    });
</script>
<?php
}
?>
<?php
if($sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] == $this->Session->read("Auth.User.id")){
?>
<script type="text/javascript" >
    $(document).ready(function () {
        $toolbarval = true;
        //console.log($toolbarval,'===============');
        setInterval(function() {
            //$("#load_sketch_data_reload_self").trigger("click");
            var project_id = $('#project_id').val();
            var sketch_id = $('#sketch_id').val();
            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
            };
            $("#sketch-user-load").load($js_config.base_url + "skts/participant/"+ sketch_id);
            $(".tooltip").fadeOut();
			$('.popover').remove();
        }, 5000);
    });
</script>
<?php
}
?>

<script type="text/javascript" >

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
            //box.addClass('loading');
            $("#ajax_overlay .loading_text").text("Saving...");
            $('#ajax_overlay').show();
            $.post(
                    $js_config.base_url + 'skts/update_sketch',
                    params,
                    function (responce) {
                        box.removeClass('loading');
                        if(responce.success){
                           // window.location.reload();
                        }
                        $('#ajax_overlay').hide();
                        $("#ajax_overlay .loading_text").text("Loading...");
                    },
                    "json"
                    );



        }

        function save_cancel_sketch_data() {

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
                "cancel": 1,
                "sketch_title": sketch_title,
                "sketch_description": sketch_description,
                "config": config,
                "content": content,
                "canvas_data": canvas_data,
                "images_data": images_data
            };
            //box.addClass('loading');
            $("#ajax_overlay .loading_text").text("Saving...");
            $('#ajax_overlay').show();
            $.post(
                    $js_config.base_url + 'skts/update_sketch',
                    params,
                    function (responce) {
                        $('#ajax_overlay').hide();
                        $("#ajax_overlay .loading_text").text("Loading...");
                        if(responce.success){
                            window.location = "<?php echo SITEURL; ?>skts/index/project_id:<?php echo $project_id ?>";
                        }
                    },
                    "json"
                    );



        }

        function save_as_sketch_data() {

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
                "images_data": images_data,
            };
            //box.addClass('loading');
            $("#ajax_overlay .loading_text").text("Saving...");
            $('#ajax_overlay').show();
            $.post(
                    $js_config.base_url + 'skts/update_save_as_sketch',
                    params,
                    function (responce) {
                        //box.removeClass('loading');
                        $('#ajax_overlay').hide();
                        $("#ajax_overlay .loading_text").text("Loading...");
                        if(responce.success == true){
                            console.log(responce);
                            window.location = $js_config.base_url+"skts/saveas/project_id:" + project_id+"/sketch_id:"+responce.sketch_id;
                        }
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
            if (redactor) {
                box = redactor.core.getBox();
            }
           // box.addClass('loading');
            $('#ajax_overlay').show();
            $.post(
                    $js_config.base_url + 'skts/load_sketch/' + sketch_id,
                    params,
                    function (response) {
                        //console.log('response',response);
                        var editmode = response['is_edit_mode'];
                        var edituser = response['edit_user_id'];
                        var currentuser = '<?php echo $this->Session->read("Auth.User.id");?>';
                        var canvasLength = response.data.content;


                        //console.log("length",canvasLength.length)

                        //console.log(editmode,edituser,currentuser);
                        if(edituser == currentuser){
                            console.log('editing here');
                            //window.location.reload();
                        }

                        if(canvasLength.length > 0){
                          $('.redactor-toolbar .re-icon').addClass('disabled-li');
                          $('.redactor-toolbar-tooltip').text('Already Inserted');
                          $('.redactor-toolbar').find('li:first').addClass('disabled-li-text');
                        }

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
                            if($toolbarval == false){
                                $(".redactor-box").html(response['data']['content']);
                                $(".redactor-box").css('pointer-events','none');
                            }
                            $(sketch_editor).redactor('focus.setEnd');
                            box.removeClass('loading');
//                            setTimeout(function(){
//                                var $divCan = $(".redactor-box").find("ul:first-child");
//                                console.log($divCan);
//                                $divCan.css("visibility","hidden !important;");
//                            },500);
                        }
                    },
                    "json"
                    );
        }


        function load_sketch_data_reload() {
            var redactor = $(sketch_editor).data('redactor'), box = $('body');
            redactor = $(sketch_editor).data('redactor');
            $(sketch_editor).redactor('code.startSync');
            var project_id = $(projectIdContainer).val();
            var sketch_id = $(sketchIdContainer).val();
            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
            };
            if (redactor) {
                box = redactor.core.getBox();
            }
            //box.addClass('loading');
            //$('#ajax_overlay').show();
            $.post(
                    $js_config.base_url + 'skts/load_sketch/' + sketch_id,
                    params,
                    function (response) {
                        var editmode = response['is_edit_mode'];
                        var edituser = response['edit_user_id'];
                        var currentuser = '<?php echo $this->Session->read("Auth.User.id");?>';

                        //console.log(editmode,edituser,currentuser);
                        if(edituser == currentuser){
                            // console.log('editing here');
                            window.location.reload(true);
                        }

                        if (response['success'] && response['data'] && response['data'] !== '') {
                            //$("#sketch-user-load").load($js_config.base_url + 'skts/"partials/participant_one"+ sketch_id, function(){
                            //    console.log("Done Loading");
                            //});
                            //$("#sketch-user-load").fadeOut('fast').load($js_config.base_url + "skts/participant/"+ sketch_id).fadeIn('slow');
                            //$("#sketch-user-load").load($js_config.base_url + "skts/participant/"+ sketch_id);

                            $("#sketch-user-load").html(response['interest']);
                            $(".tooltip").fadeOut();


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
                            if($toolbarval == false){
                                $(".redactor-box").html(response['data']['content']);
                            }
                            $(sketch_editor).redactor('focus.setEnd');
                            //box.removeClass('loading');
                            $('#ajax_overlay').hide();
                        }
                    },
                    "json"
                    );
        }

        /*

        function load_sketch_data_reload_self() {
            var project_id = $(projectIdContainer).val();
            var sketch_id = $(sketchIdContainer).val();
            var params = {
                "project_id": project_id,
                "sketch_id": sketch_id,
            };
            //$("#sketch-user-load").load($js_config.base_url + "skts/participant/"+ sketch_id);



            $.post(
                $js_config.base_url + 'skts/load_sketch/' + sketch_id,
                params,
                function (response) {
                    if (response['success'] && response['data'] && response['data'] !== '') {
                        console.log("here self loading ...");
                        //$("#sketch-user-load").html(response['interest']);
                        $("#sketch-user-load").load($js_config.base_url + "skts/participant/"+ sketch_id);

                    }
                },
                "json"
            );

        }
        */

        var redactorConfig = {
           // toolbar: $toolbarval ,
            buttonSource: true,
            imageUpload: false,
            imageManagerJson: false,
            imageEditable: false,
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
                          'horizontalrules'
                    ],
            plugins: [
                'callbacksFix',
                /*'imagemanager',*/
                'drawer',
                //'video',
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
                basePath: $js_config.base_url + 'js/projects/plugins/sketch/',

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
                    //'Pencil',
                    //'Eraser',
                    'Text',
                    'Line',
                    'ArrowOneSide',
                    'ArrowTwoSide',
					'Triangle',
                    'Rectangle',
                    'Circle',
                    'Image',
                    // 'Polygon',
                    // Drawing options
                    //'ColorpickerHtml5',
                    'Colorpicker',
                    'CanvasColor',
                    'ShapeColor',
                    'ShapeBorder',
                    'BrushSize',
                    'Resize',
                    'Fullscreen',
					'MovableFloatingMode',
					 'Icons'
                ],

                defaultActivePlugin: {/*name: 'Pencil',*/ mode: 'lastUsed'},
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
                        maxImageSizeKb: 2048, //2MB (1024)1 MB
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
                    },
					Icons: {

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
        //console.log('redactor:', redactor);

        $("#save_sketch_data").on('click', function (event) {
            save_sketch_data();
        });
        $("#save_cancel_sketch_data").on('click', function (event) {
            save_cancel_sketch_data();
        });
        $("#save_as_sketch_data").on('click', function (event) {
            save_as_sketch_data();
        });
        $("#load_sketch_data").on('click', function (event) {
            load_sketch_data();
        });
        $("#load_sketch_data_reload").on('click', function (event) {
            load_sketch_data_reload();
        });
        $("#load_sketch_data_reload_self").on('click', function (event) {
            load_sketch_data_reload_self();
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
        background-image:url('<?php echo SITEURL; ?>images/ajax-loader.gif');
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
    .users{
        border: 1px solid #d2d6de;
        min-height: 100px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 5px;
    }
    .box-header.filters {
        background-color: #efefef;
        border-color: #dddddd;
        border-image: none;
        border-style: solid solid none;
        border-width: 1px;
    }

</style>
