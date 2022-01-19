
<?php
echo $this->Html->css('projects/planning');
?>
<script type="text/javascript">
    $('html').addClass('no-scroll');
</script>
<?php $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer'); ?>
<div class="row">
    <div class="col-xs-12">
        <section class="main-heading-wrap pb6">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><?php echo $page_subheading; ?></div>
            </div>
			<div class="header-right-side-icon">
				<span class="headertag ico-project-summary tipText" title="Tag" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", 'type' => 'planning', 'admin' => FALSE ), true ); ?>" data-original-title="Tag Team Members"></span>
			</div>

        </section>
        <div class="box-content postion ">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="competencies-tab people-info-wrap mt0">
                        <div class="row">
                            <div class="col-md-9">
                                <ul class="nav nav-tabs" id="people_list_tabs">
                                    <li class="active" >
                                        <a data-toggle="tab" data-type="utilization" class="t-people" data-target="#tab_utilization" href="#tab_utilization" aria-expanded="true">UTILIZATION </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <div class="utilization-link-top-right">
                                    <?php if($resourcer){ ?>
                                    <a class="tipTextcommon-btns tipText" title="Adjustments" data-toggle="modal" data-target="#modal_adjustments" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "adjustments", 'admin' => FALSE ), true ); ?>"><i class="adjustblack-icon"></i></a>
                                    <a class="tipText common-btns tipText"  title="Add Adjustment" data-toggle="modal" data-target="#modal_add_adj" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "add_adjustment", 'admin' => FALSE ), true ); ?>"><i class="add-adjustment-icon"></i></a>
                                    <?php } ?>
                                    <?php  /*
                                    if($resourcer){ ?>
                                        <a class="tipTextcommon-btns tipText" title="Adjustments" ><i class="adjustblack-icon"></i></a>
                                        <a class="tipText common-btns tipText"  title="Add Adjustment" ><i class="add-adjustment-icon"></i></a>
                                    <?php }*/ ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box noborder">

                        <div class="box-body clearfix nopadding people-tab-scroll" >
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="tab_utilization" data-type="utilization">
                                    <div class="utilization-wrap">
                                        <div class="utilization-header-wrap">
                                            <div class="utilization-header-col1">
                                                <label class="custom-dropdown" style="width: 100%;">
                                                    <select class="form-control aqua" id="sel_people_from">
                                                        <option value="">Select People From</option>
                                                        <?php if($resourcer ){ ?>
                                                            <option value="community">All Community</option>
                                                            <option value="profile">My Profile</option>
                                                            <option value="organizations">Specific Organizations</option>
                                                            <option value="locations">Specific Locations</option>
                                                            <option value="departments">Specific Departments</option>
                                                            <option value="users" <?php if(isset($param_type) && !empty($param_type) && ($param_type == 'people' || $param_type == 'user')){ ?> selected <?php } ?>>Specific People</option>
                                                            <option value="tags"<?php if(isset($param_type) && !empty($param_type) && $param_type == 'tags'){ ?> selected <?php } ?>>Specific Tags</option>
                                                            <option value="skills">Specific Skills</option>
                                                            <option value="subjects">Specific Subjects</option>
                                                            <option value="domains">Specific Domains</option>
                                                            <option value="all_projects">All My Projects</option>
                                                            <option value="created_projects">Projects I Created</option>
                                                            <option value="owner_projects">Projects I Own</option>
                                                            <option value="shared_projects">Projects Shared With Me</option>
                                                            <option value="project">Specific Projects</option>
                                                        <?php }else{ ?>
                                                            <option value="profile" <?php if(isset($param_type) && !empty($param_type)){ ?> selected <?php } ?>>My Profile</option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label class="custom-dropdown" style="width: 100%;">
                                                    <select class="form-control aqua" id="sel_work_from">
                                                        <option value="">Select Work From</option>
                                                        <?php if($resourcer ){ ?>
                                                            <option value="community"  <?php if(isset($param_type) && !empty($param_type)){ ?> selected <?php } ?>>All Community</option>
                                                            <option value="project">Specific Projects</option>
                                                        <?php }else{ ?>
                                                            <option value="project" <?php if(isset($param_type) && !empty($param_type)){ ?> selected <?php } ?>>Specific Projects</option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                            </div>
                                            <div class="utilization-header-col2 popup-select-icon">
                                               <div class="utilization-selectbox ">
                                                <?php if(isset($param_type) && !empty($param_type) && $resourcer){ ?>
                                                    <?php echo $this->Form->input('users', array('type' => 'select', 'options' => $people_list, 'label' => false, 'div' => false, 'class' => 'form-control aqua', 'id' => 'specific_item_1', 'multiple' => 'multiple', 'default' => array_keys($people_list) )); ?>
                                                <?php }else{ ?>
                                                    <select class="form-control aqua" id="specific_item_1" multiple="" disabled="">
                                                    </select>
                                                <?php } ?>
                                                </div>
                                                <div class="utilization-selectbox">
                                                <?php if(isset($param_type) && !empty($param_type) && !$resourcer){ ?>
                                                    <?php echo $this->Form->input('projects', array('type' => 'select', 'options' => $project_list, 'label' => false, 'div' => false, 'class' => 'form-control aqua', 'id' => 'specific_item_2', 'multiple' => 'multiple', 'default' => array_keys($project_list) )); ?>
                                                <?php }else{ ?>
                                                    <select class="form-control aqua" id="specific_item_2" multiple="" disabled="">
                                                    </select>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div class="utilization-header-col3">
                                                <div class="utilization-daily-date">
                                                    <span class="utilization-daily-sec">
                                                        <label class="custom-dropdown" style="width: 100%;">
                                                            <select class="form-control aqua date-type" id="date_type">
                                                                <option value="daily">Daily</option>
                                                                <option value="weekly">Weekly</option>
                                                                <option value="monthly">Monthly</option>
                                                            </select>
                                                        </label>
                                                    </span>
                                                    <span class="utilization-date-sec">
                                                        <div class="input-group">
                                                            <input name="dates" value="" id="dates" class="form-control input-small dates" type="text" autocomplete="off" >
                                                            <div class="input-group-addon open-date-picker">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>
                                                <?php if($resourcer){ ?>
                                                <div class="checkbox-util">
                                                    <input type="checkbox" id="adjustments" name="Adjustments" value="1">
                                                    <label class="adjustments-text" for="adjustments">Include Adjustments</label>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="utilization-header-col4">
                                                <input type="button" name="show_user_btn" id="utilization_showbtn" title="Show" class="btn btn-success utilization_showbtn tipText" disabled="true" value="Show">
                                                <div class="utilization-button">
                                                    <button class="btn btn-success tipText prev" title="Show Previous" disabled="true"> < </button>
                                                    <button class="btn btn-success tipText next" title="Show Next" disabled="true"> > </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="utilization-list-wrapper">
                                            <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                                            <input type="hidden" name="paging_total" id="paging_total" value="0">
                                            <input type="hidden" name="allSelectedUsers" id="allSelectedUsers" value="0">
                                            <div class="utilization-list-data" data-flag="true">
                                                <div class="no-utilization">select people and work</div>
                                            </div>
                                            <?php //echo $this->element('../Searches/planning/utilization'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Html->script("projects/planning");
?>

<style type="text/css">
    section.content {
        padding-top: 0;
    }
    .wpop p {
    margin-bottom: 2px !important;
    }
    .wpop p:first-child {
        font-weight: 600 !important;
        width: 170px !important;
    }
    .wpop p:nth-child(2) {
        font-size: 11px;
    }
    .abs {
        position: absolute;
        bottom: 0;
        left: 20%;
        right: 0;
        z-index: 3;
        overflow: auto;
        display: flex;
        height: 15px;
    }
    .abs .util-col {
        min-width: 96px;
        max-width: 96px;
    }
</style>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<script type="text/javascript">
$(() => {
    $specific_items_1 = $('#specific_item_1').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-info aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 0,
            maxHeight: '327',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            disableIfEmpty: true,
            filterPlaceholder: 'Search Specific Items',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Specific Items',
            onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });

    $specific_items_2 = $('#specific_item_2').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-info aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 0,
            maxHeight: '327',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            disableIfEmpty: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Specific Items',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Specific Items',
            onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });

        $('body').on('click', '.al-list', function(event) {
            event.preventDefault();
            var $that = $(this);
            var column = $(this).data('by');
            var direction = $(this).data('order') || 'asc';
            var $parent = $('.adj-list-header');

            if( direction == 'desc' ) {
                $(this).attr('data-order', 'asc').data('order', 'asc');
            }
            else{
                $(this).attr('data-order', 'desc').data('order', 'desc');
            }

            $parent.find('.sort_order.active').not(this).removeClass('active');

            $that.addClass('active');
            $('.tooltip').remove();

            var data = {
                column: column,
                direction: direction
            }
            $.ajax({
                url: $js_config.base_url + 'searches/adjustment_list',
                type: 'POST',
                // dataType: 'json',
                data: data,
                success: function(response){
                    $(".unit-filt-adj-cont").html(response);
                }
            });

        });

        $('body').on('click', '.delete-pe', function(event) {
            event.preventDefault();
            var $that = $(this);
            var $parent = $(this).parents('.pln-data-row:first');
            var id = $parent.data('id');
            $.ajax({
                url: $js_config.base_url + 'searches/delete_plan_effort',
                type: 'POST',
                dataType: 'json',
                data: {id: id},
                success: function(response){
                    if(response.success){
                        $parent.slideUp(200, function(){
                            $(this).remove();
                            $('.tooltip').remove();
                            if($('.unit-filt-adj-cont .pln-data-row').length <= 0){
                                $.ajax({
                                    url: $js_config.base_url + 'searches/adjustment_list',
                                    type: 'POST',
                                    data: {},
                                    success: function(response){
                                        $(".unit-filt-adj-cont").html(response);
                                    }
                                });
                            }
                        })
                    }
                }
            });

        });

      $('body').on('click', '.ws', function(event) {
         event.preventDefault();
         console.log('test')
         var $that = $(this);
         var column = $(this).data('by');
         var direction = $(this).data('order') || 'asc';
         var $parent = $('.work-header');
         var cuser = $('#cuser').val();
         var cdate = $('#cdate').val();
         var cdate_type = $('#cdate_type').val();

         if( direction == 'desc' ) {
             $(this).attr('data-order', 'asc').data('order', 'asc');
         }
         else{
             $(this).attr('data-order', 'desc').data('order', 'desc');
         }

         $parent.find('.sort_order.active').not(this).removeClass('active');

         $that.addClass('active');
         $('.tooltip').remove();

         var data = {
             column: column,
             direction: direction,
             user_id: cuser,
             date: cdate,
             date_type: cdate_type
         }
         $.ajax({
             url: $js_config.base_url + 'searches/utill_work_list',
             type: 'POST',
             data: data,
             success: function(response){
                 $(".work-list-wrap").html(response);
             }
         });
      });

      $('body').on('click', '.adjl', function(event) {
         event.preventDefault();
         var $that = $(this);
         var column = $(this).data('by');
         var direction = $(this).data('order') || 'asc';
         var $parent = $('.adj-header');
         var cuser = $('#cuser').val();
         var cdate = $('#cdate').val();
         var cdate_type = $('#cdate_type').val();

         if( direction == 'desc' ) {
             $(this).attr('data-order', 'asc').data('order', 'asc');
         }
         else{
             $(this).attr('data-order', 'desc').data('order', 'desc');
         }

         $parent.find('.sort_order.active').not(this).removeClass('active');

         $that.addClass('active');
         $('.tooltip').remove();

         var data = {
             column: column,
             direction: direction,
             user_id: cuser,
             date: cdate,
             date_type: cdate_type
         }
         $.ajax({
             url: $js_config.base_url + 'searches/utill_adj_list',
             type: 'POST',
             data: data,
             success: function(response){
                 $(".adj-wrap").html(response);
             }
         });
      });

})
</script>
<!-- Modal Boxes -->
<div class="modal modal-success fade" id="modal_add_adj" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog add-util-modal">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_adjustments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog unit-filter-adjustment">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_util" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog utilization-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<!-- /.modal -->