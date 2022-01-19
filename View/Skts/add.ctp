<?php
include 'partials/header_js_css.ctp';
?>
<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">

        <!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $data['page_heading']; ?>
                    <p class="text-muted date-time">
                        <span><?php echo $data['page_subheading']; ?></span>
                    </p>
                </h1>
            </section>
        </div>

        <!-- END HEADING AND MENUS -->



        <!-- MAIN CONTENT -->
        <div class="box-content wiki">

            <div class="row ">
                <div class="col-xs-12">
                    <?php echo $this->Form->create('ProjectSketch',array("url"=>array("controller"=>"skts","action"=>"add","project_id"=>$project_id),array("class"=>"sketch_form")));
                    ?>
                    <div class="bg-white">
                        <div class="fliter margin-top" style="">
                            <div class="panel-body" style="padding:0 15px 0 30px;">
                                <div class="form-group">
                                    <div class="pull-left">

                                    </div>
                                    <div class="pull-right">
                                        <button type="submit" id="save_sketch_data_pro" class="btn btn-success btn-sm">Save</button>
                                        <a href="<?php echo SITEURL;?>skts/index/project_id:<?php echo $project_id;?>" id="load_sketch_data_pro" class="btn btn-danger btn-sm">Cancel</a>
                                    </div>

                                </div>

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

                        <div class="box noborder padding  " id="box_body">

                            <div class="clearfix"></div>
                            <div class="row box-canvace">
                                <div class="col-sm-12">
                                    <div class="idea-canvas-outerd list-group-itemd">
                                        <div class="form-group clearfix">
                                            <div class="col-sm-12">
                                                <label>For Project: </label>
                                            </div>

                                            <div class="col-sm-12">

                                                <label style="" class="custom-dropdown">
                                                    <div class="input select">
                                                        <select onchange="change_project();" required id="select_project" class="aqua" name="data[ProjectSketch][project_id]" style=" width: 400px; max-width: 100%; background: #E6E6E6">
                                                            <option value="">Select a Project</option>
                                                            <?php if (isset($projects) && !empty($projects)) { ?>
                                                                <?php foreach ($projects as $key => $value) {
																	$value = trim($value);
																	if(isset($value) && !empty($value)){
																	?>
                                                                    <option value="<?php echo $key; ?>" <?php echo isset($project_id) && $project_id == $key ? 'selected' : ''; ?>>
                                                                        <?php echo ( strlen(strip_tags($value)) > 80 ) ? substr(html_entity_decode(strip_tags($value)), 0, 80) . '...' : html_entity_decode(strip_tags($value)); ?></option>
                                                                <?php } } ?>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="error-message text-danger" ></span>
                                                    </div>
                                                </label>
                                            </div>

                                        </div>
                                        <div class="form-group clearfix">

                                            <div class="col-sm-12">
                                                <label>Title:</label>


                                                <?php echo $this->Form->input('ProjectSketch.sketch_title',array("required"=>true, 'id'=>"sketch_title", "value"=>"", "class"=>"form-control input-small", "placeholder"=>"Max chars allowed 50","label"=>false,"div"=>false, 'autocomplete' => 'off'));?>
                                                <span class="error-message text-danger" ></span>
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">
                                            <div class="col-sm-12">
                                                <label>Description:</label>
                                                <input type="text" name="data[ProjectSketch][sketch_description]" id="sketch_description" value="" class="form-control input-small" placeholder="Max chars allowed 250" autocomplete="off" /><span class="error-message text-danger" ></span>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">

                                                <label class="create-new-sketch-m">Available To Project Members:</label>


                                                <div class="funkyradio pull-right">
                                                    <div class="funkyradio-default _edit_participant">
                                                        <input id="checkbox_everyone" value="1" class="checkbox-custom" name="data[ProjectSketch][participant_all]" type="checkbox">
                                                        <label for="checkbox_everyone" class="checkbox-custom-label" style="margin-bottom: 6px;"> Everyone</label>

                                                        <div class="clear_members">
                                                            <span>
                                                                <i class="fa fa-times" style="font-size: 17px;"></i>
                                                            </span>
                                                            <label for="">Clear member</label>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <div class="col-sm-12 _edit_participant">
                                                <?php
                                                include 'partials/users.ctp';
                                                ?>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php echo $this->Form->end();?>
                </div>
            </div>

        </div>

    </div>
</div>
<script type="text/javascript" >
    function change_project() {
        var id = $("#select_project").val();
        if (id) {
            window.location = "<?php echo SITEURL; ?>skts/add/project_id:"+id;
        } else {
            window.location = "<?php echo SITEURL; ?>skts";
        }
    }

	$(".clear_members").click(function (e) {
            $(".checkbox-custom").removeAttr("checked");
            $(".currentuser").prop('checked', true);




        })

        var checkedcnt = $(".user-checkbox:checked").length;
        var allcnt = $(".user-checkbox").length;
        if(allcnt === checkedcnt){
            $(".checkbox-custom").trigger("click");
        }
        $(".checkbox-custom").change(function (e) {
            var $that = $(this);
            var checkedcnt = $(".user-checkbox:checked").length;
            var allcnt = $(".user-checkbox").length;
            var cnt = $(".user-checkbox").length;

            if (checkedcnt === cnt) {
                $("#checkbox_everyone").prop('checked', true);
                $(".currentuser").prop('checked', true);

            }
            if (!$(this).prop('checked')) {
                $("#checkbox_everyone").prop('checked', false);
               // $(".currentuser").prop('checked', true);
            }

            var status = $that.prop("checked");



        })
        setTimeout(function(){
            $("#checkbox_everyone").trigger("click");
        },300);
        $("#checkbox_everyone").change(function (e) {
            var checked = $(this).attr("checked");

            if (!$(this).prop('checked')) {
                $(".user-checkbox").prop('checked', false);
                $(".currentuser").prop('checked', true);
            } else {
                $(".user-checkbox").prop('checked', true);
                $(".currentuser").prop('checked', true);

            }
        })


</script>

<style>
.custom-dropdown {
    display: inline-block;
    margin: 0 auto;
    position: relative;
    width: 400px;
}
</style>
