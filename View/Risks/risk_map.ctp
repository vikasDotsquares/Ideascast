<?php
echo $this->Html->css('projects/risks/risk.map');
echo $this->Html->script('projects/risks/risk.map', array('inline' => true));
$current_user_id = $this->Session->read('Auth.User.id');
?>
    <div class="row risk-map">
        <div class="col-xs-12">
            <div class="row">
                <section class="content-header clearfix">
                    <h1 class="pull-left">
                        <?php echo $page_heading; ?>
                        <p class="text-muted date-time" style="padding: 5px 0; margin: 0 !important;">
                            <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                        </p>
                    </h1>
                </section>
            </div>
            <div class="box-content">
                <div class="row ">
                    <div class="col-xs-12">
                        <div class="box noborder margin-top map-bg">
                            <div class="box-header  filters" style="">
                                <!-- Modal Boxes -->
                                <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content"></div>
                                    </div>
                                </div>
                                <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content"></div>
                                    </div>
                                </div>
                                <!-- /.modal -->
                                <!-- FILTER BOX -->
                                <div class="col-sm-9 col-md-9 col-lg-7 row-first risk-filter">
                                    <!-- <label for="risk_projects" class="pull-left" style="margin-top: 5px; font-weight: normal;"> Project: </label> -->
                                    <div class="col-sm-7 col-md-6 col-lg-6 risk-select nopadding">
                                        <label class="custom-dropdown" style="width: 100%;">
                                            <?php
                                            if(isset($projects) && !empty($projects)) {
												if(isset($my_risks) && !empty($my_risks)) $project_id = 'my';
                                                $projects1 = ["my" => "My Risks", "all" => "All Projects"];
                                                $allProjects = $projects1 + $projects;
                                                if(isset($param) && !empty($param)) $project_id = $param;
                                                //echo $this->Form->select('RmDetail.project_id', $allProjects, array('escape' => false, 'empty' => 'All Projects', 'class' => 'form-control aqua', 'id' => 'risk_projects', 'value' => $project_id));
												 echo $this->Form->select('RmDetail.project_id', $allProjects, array('escape' => false, 'empty' => 'Select Project', 'class' => 'form-control aqua', 'id' => 'risk_projects', 'default' => $project_id));
												
                                            }
                                            else{
                                        ?>
                                                <select class="form-control aqua" id="risk_projects">
                                                    <option value="">No Project</option>
                                                </select>
                                                <?php
                                            }
                                        ?>
                                        </label>
                                        <span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span>
                                    </div>
                                </div>
                               <?php /*?> <div class="action more-dropdown pull-right risk-but-top">
                                    <a class="tipText" title="Risk Details" href="<?php echo Router::url(['controller' => 'risks', 'action' => 'index', 'admin' => false], true) ?>"><i class="listblack"></i></a>
                                    <a class="tipText" title="Create Risk" href="<?php echo Router::Url(['controller' => 'risks', 'action' => 'manage_risk', 'admin' => false], true); ?>"> <i class="workspace-icon"></i></a>
                                </div><?php */?>
                                <!-- END FILTER BOX -->
                            </div>
                            <div class="box-body clearfix risk-map-cont"   id="box_body">
                                <div class="data-wrapper">
                                    <?php echo $this->element('../Risks/partials/risk_map_data', array('RmDetail' => $RmDetail, 'load' => 1)); ?>
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
    <script type="text/javascript">
    $(function() {
        $.update_flag = false;

        $('body').delegate('.popover .el_users', 'click', function(e) {
            // e.preventDefault()
            setTimeout($.proxy(function() {
                $(this).parents('.popover:first').data('bs.popover').$element.popover('hide');
            }, this), 300);
        })
    })
    </script>