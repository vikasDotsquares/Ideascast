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
                    <?php echo $this->Form->create('ProjectSketch',array("url"=>array("controller"=>"skts","action"=>"saveas","project_id"=>$project_id,"sketch_id"=>$sketch_id),array("class"=>"sketch_form")));
                    ?>
                    <input type="hidden" name="data[ProjectSketch][id]" value="<?php echo $sketch_id;?>"/>
                    
                    <div class="bg-white">    
                        <div class="fliter margin-top" style="padding :15px 0 0 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd">
                            <div class="panel-body" style="padding:0 15px 0 30px;">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                    <div class="pull-left">
                                        <!--<div class="funkyradio-default">
                                            <?php 
                                            $locked = '';
                                            if(isset($sketchdata['ProjectSketch']['locked']) && $sketchdata['ProjectSketch']['locked'] == 1){
                                                $locked = 'checked="checked"';
                                            }
                                            ?>
                                            <input type="hidden" name="data[ProjectSketch][locked_user_id]" value="<?php echo $sketchdata['ProjectSketch']['locked_user_id'];?>" />
                                            <input value="1" <?php echo $locked;?> type="checkbox" class="custom_locked" name="data[ProjectSketch][locked]" id="checkbox1"/>
                                            <label style="margin-left: -14px;width: 200px;" class="checkbox-custom-label" for="checkbox1"> Lock - No edits</label>
                                        </div>-->
                                        
                                        
                                    </div>
                                    <div class="pull-right">
                                        <button type="submit" id="save_sketch_data_pro" class="btn btn-success btn-sm">Save</button>
                                        
                                        <a href="<?php echo SITEURL;?>skts/saveasdelete/project_id:<?php echo $project_id;?>/sketch_id:<?php echo $sketch_id?>" type="submit" id="load_sketch_data_pro" class="btn btn-danger btn-sm">Cancel</a>
                                        
                                        
                                    </div>
                                    </div>

                                </div>

                            </div>

                        </div>

<?php echo $this->Session->flash(); ?>
                        <?php
                         
                        if(isset($sketchdata['ProjectSketch']['sketch_description']))
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
                                                        <select disabled="disabled" class="aqua" name="data[ProjectSketch][project_id]" style=" width: 400px; max-width: 100%; background: #E6E6E6">
                                                            <option value="">Select a Project </option>
                                                            <?php if (isset($projects) && !empty($projects)) { ?>
                                                                <?php foreach ($projects as $key => $value) {  
																	$value = trim($value); 
																	if(isset($value) && !empty($value)){
																	?>
                                                                    <option value="<?php echo $key; ?>" <?php echo isset($project_id) && $project_id == $key ? 'selected' : ''; ?>>
                                                                        <?php echo ( strlen(strip_tags($value)) > 60 ) ? substr(html_entity_decode(strip_tags($value)), 0, 60) . '...' : html_entity_decode(strip_tags($value)); ?></option>
                                                                <?php } } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </div>		
                                                </label>
                                            </div>

                                        </div>
                                        <div class="form-group clearfix">

                                            <div class="col-sm-12"> 
                                                <label>Sketch Title:</label>   
                                                
                                                
                                                <?php echo $this->Form->input('ProjectSketch.sketch_title',array("required"=>true, 'id'=>"sketch_title", "value"=>$sketchdata['ProjectSketch']['sketch_title'], "class"=>"form-control input-small", "placeholder"=>"Max chars allowed 50","label"=>false,"div"=>false));?>
                                                <span class="error-message text-danger" ></span>
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">

                                            <div class="col-sm-12"> 
                                                <label>Description:</label>   
                                                <input  type="text" name="data[ProjectSketch][sketch_description]" id="sketch_description" value="<?php echo $dsc; ?>" class="form-control input-small" placeholder="Max chars allowed 250" /><span class="error-message text-danger" ></span>
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                
                                                <label>Available To Project Members:</label>
                                                <div class="funkyradio pull-right">
                                                    <div class="funkyradio-default _edit_participant ">
                                                        <?php 
                                                        $checked_all = '';
                                                        
                                                            if(isset($sketchdata['ProjectSketch']['participant_all']) && $sketchdata['ProjectSketch']['participant_all'] == 1){
                                                               $checked_all = 'checked'; 
                                                            }
                                                        ?>
                                                        <input value="1" id="checkbox_everyone" class="checkbox-custom" name="data[ProjectSketch][participant_all]" type="checkbox" <?php echo $checked_all;?>>
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

<style>
.custom-dropdown {
    display: inline-block;
    margin: 0 auto;
    position: relative;
    width: 400px;
}
</style>

<script type="text/javascript" >
$(document).ready(function () {
    function disableBack() {window.history.forward()}

    window.onload = disableBack();
    window.onpageshow = function (evt) {if (evt.persisted) disableBack()}
});
$(function(){
    
    
    
    
        $(".clear_members").click(function (e) {
            $(".checkbox-custom").removeAttr("checked");
            $(".currentuser").prop('checked', true);
            
            var href_ = $js_config.base_url + 'skts/remove_participant/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>/status:all';
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
            
            
        })
        
        var checkedcnt = $(".user-checkbox:checked").length;
        var allcnt = $(".user-checkbox").length;
        if(allcnt === checkedcnt){
            //$(".checkbox-custom").trigger("click");
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
            if(status == false){
                var href_ = $js_config.base_url + 'skts/remove_participant/project_id:<?php echo $project_id; ?>/sketch_id:<?php echo $sketch_id; ?>/status:single/user_id:'+$that.val();
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
            
            
        })
        
       
        
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
})		
</script>


