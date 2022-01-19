<?php
include 'partials/header_js_css.ctp';
echo $this->Html->script('canvas', array('inline' => true));
// echo $this->Html->script('canvas2image', array('inline' => true));
?> 
<!-- OUTER WRAPPER	-->
<div class="row"> 
    <!-- INNER WRAPPER	-->
    <div class="col-xs-12"> 
        <?php
        include 'partials/header_inner.ctp';
        ?> 
        <!-- MAIN CONTENT -->
        <div class="box-content sketch-wrapper" style="">

            <div class="row ">
                <div class="col-xs-12">
                    <div style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move;" class="box-header task-list-header">
                        <div class=" radio-left obj-filter obj-filter-first ">
                            <div class=" "> 
                                <label class="hidden-sm" style="min-width: 90px;margin-top : 5px;">Projects: </label>
                                <label style=" " class="custom-dropdown"> 
                                    <select id="select_project" class="aqua" onfocus='this.size=8;' onblur='this.size=1;' 
onchange='this.size=1; this.blur();' name="project_id" style="  ">
                                        <option value="">Select a Project</option>
                                        <?php if (isset($projects) && !empty($projects)) { ?>
                                            <?php foreach ($projects as $key => $value) { 
											$value = trim($value); 
											if(isset($value) && !empty($value)){
											?>
                                                <option value="<?php echo $key; ?>" <?php echo isset($project_id) && $project_id == $key ? 'selected' : ''; ?>>
                                                    <?php echo ( strlen(strip_tags($value)) > 80 ) ? substr(html_entity_decode(strip_tags($value)), 0, 80) . '...' : html_entity_decode(strip_tags($value)); ?> (<?php echo $this->Sketch->sktCounts($key); ?>)</option>
                                                <?php } } ?>
                                        <?php } ?>
                                    </select>
                                </label>
                                <span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="raio-left-select radio-left pull-right  obj-filter obj-filter-sec" style="text-align: right">
                            <div class=" "> 
                                <a href="javascript:void(0)" class=" btn btn-sm btn-warning create_sketch_but"><i class="fa fa-plus"></i> Create Sketch</a> 
                                <button  class=" btn btn-sm btn-success" onclick="select_project();" >Apply Filter</button>
                                <a href="<?php echo SITEURL; ?>skts/index" class=" btn btn-sm btn-danger">Reset</a> 
                            </div>
                        </div> 
                    </div>
                    <div class="bg-white">    
                        <?php echo $this->Session->flash(); ?>

                        <div class="box-body clearfix list-shares" style="min-height: 550px;">

                            <div class="panel panel-default" id="project_cards">
                                <div class="panel-heading" style="position: relative;">
                                    <h3 class="panel-title">SKETCH CARDS (<b id="allcountsketch"><?php echo ( isset($sketchdata) && !empty($sketchdata) ) ? count($sketchdata) : 0; ?></b>)</h3>
                                </div>
                            </div>
                            <div class="panel-body scroll-vertical toggle-scrolling"> 
                                <div class="inner-horizontal">
                                    <div class="row"> 
                                        <div class="col-sm-5 col-md-5 col-lg-3 pull-right full-col-width">
                                            <div class="panel panel-default  edit_sketch_panel">
                                                <div class="panel-heading bg-green" style="padding:12px 10px">
                                                    <h5 class="panel-title nowrap-title">
                                                        Sketches
                                                    </h5>
                                                </div>
                                                <div class="panel-body " style="background : rgb(239, 239, 239) none repeat scroll 0 0;">
                                                    <div class="panel-group saveasdiv_ajax" id="accordion" role="tablist" aria-multiselectable="true">
                                                    <?php 
                                                    echo $this->element('../Skts/partials/saveas', $sketch_save_as_data );
                                                    ?> 
                                                    </div> 
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-sm-7 col-md-7 col-lg-9 nopadding mainsketchdiv_ajax">
                                            <?php 
												echo $this->element('../Skts/partials/mainsketch', $sketchdata );
                                            ?> 
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

<style>
    /*******************************
* Does not work properly if "in" is added after "collapse".
* Get free snippets on bootpen.com
*******************************/
    .panel-group .panel {
        border-radius: 0;
        box-shadow: none;
        border-color: #EEEEEE;
    }

	.custom-dropdown {
		position: absolute !important;
		display: inline-block;
		margin: 0 auto;
		z-index: +1;		 
	}
	
	.obj-filter-first div{
		position : relative;
	} 
	
	.popover { display : inline-table !important;}
	.tooltip.CUSTOM-CLASSs .tooltip-inner { display : inline !important;}
	
    .more-less {
        float: left;
        color: #212121;
        margin: 0px 5px 0px 5px;
    }
    .panel-group .panel-title,.panel-group .edited-sketch-right{
         font-size: 11px;
    }
    .panel-group .panel-title a:focus{
        color:#fff;
    }
    .panel-group .panel-title a:hover{
        color:#fff;
    }
    
    .panel-group .edited-sketch-right a:focus {
        color:#000;
    }
    .panel-group .edited-sketch-right a:hover {
        color:#000;
    }
    
/* ----- v CAN BE DELETED v ----- */
</style>
<!-- END MODAL BOX -->
<script type="text/javascript" >
    
    $(function () {
        
        function toggleIcon(e) {
            $(e.target)
                .prev('.panel-heading')
                .find(".more-less")
                .toggleClass('glyphicon-plus glyphicon-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);
        
        
        
        
        $(".deletesketch_saveas").on('click', function (e) {			 
            var $that = $(this);
            BootstrapDialog.show({
                type:BootstrapDialog.TYPE_DANGER,
                closable: true,
                closeByBackdrop: false,
                closeByKeyboard: false,
                title:'Confirmation',
                message: 'Are you sure you want to delete?',
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
                                }
                            },
                            "json"
                        );
                        
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        dialogRef.getModalBody().html('Deleting this sketch…');
                        setTimeout(function(){
                            $("#edited-sketch-saveas-"+$that.data("id")).fadeOut("slow");
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
        
        
    })

 
    function select_project() {
        var id = $("#select_project").val();
         console.log(id);
        if (id > 0) {
             window.location = "<?php echo SITEURL; ?>skts/index/project_id:" + id;
        } else {
		  if($('.instantHide').text().length == 0  ){
	 
			  $('.custom-dropdown').after('<span style="float:left; margin : 5px 5px 0 0;" class="text-red padding-left instantHide">Please Select</span>');	
			  setTimeout(function(){
				 $('.instantHide').remove();
			  },3000)
			  }
          
        }
    }
	
	$('#select_project').change(function(){
		$id = $(this).val();
		$('.create_sketch_but').attr('href','<?php echo SITEURL; ?>skts/add/project_id:'+$id);
	})
	
	$('.create_sketch_but').click(function(event){ 
	   //event.preventDefault();
	   var id = $("#select_project").val();
        // console.log(id);
        if (id > 0) {
             window.location = "<?php echo SITEURL; ?>skts/add/project_id:" + id;
        } else {
		   if($('.instantHide').text().length == 0  ){
			  $('.custom-dropdown').after('<span style="float:left; margin : 5px 5px 0 0;" class="text-red padding-left instantHide">Please Select</span>');		 
			  setTimeout(function(){
				 $('.instantHide').remove();
			  },3000)
			  }
        
        }
		                         
	})


</script>

<style>
    .nomal{ overflow-y:visible !important; height: 100% !important; }
</style>