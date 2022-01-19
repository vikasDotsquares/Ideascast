<?php
// echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
// echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
// echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

echo $this->Html->css('projects/risks/risk.edit');
echo $this->Html->script('projects/risks/manage_risk', array('inline' => true));

?>
<style type="text/css">
    .btn-select-list span.text-value {
        display: inline-block;
        padding: 3px 0px;
        width: 100%;
    }
    .btn-select ul {
        max-height: 155px;
    }
    .btn-select.btn-default ul li:hover {
        background-color: #f0f0f0;
    }

    .btn-select.btn-default ul li {
        border-bottom: 1px solid #ccc;
    }
    .btn-select.btn-default ul li.selected {
        background-color: #f1f1f1;
    }
	#future_date{ cursor: default;}

	.row-first.risk-filter{ padding : 0;}
    .no-scroll {
        overflow: hidden;
    }
    .risk-title, #risk_description {
        resize: none;
    }
</style>
<script type="text/javascript">
$(function() {
    // PASSWORD DELETE
    $.current_delete = {};
    $('body').delegate('.delete-an-item', 'click', function(event) {
        event.preventDefault();
        $.current_delete = $(this);
    });

    $('#modal_delete').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
        $.current_delete = {};
    });
    $('html').addClass('no-scroll');
    // $('.nav.nav-tabs').removeAttr('style');

    // RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        $('.box-body.clearfix').animate({
            minHeight: (($(window).height() - $('.box-body.clearfix').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.box-body.clearfix').offset().top) ) - 17
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })

    /*$("#summary_tabs").on('show.bs.tab', function(e){
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1000);
    })*/
})
</script>
<?php
$pid = null;
if( isset($this->params['pass'][1]) && !empty($this->params['pass'][1]) ){
	$pid = $this->params['pass'][1];
}


$all_projects = $this->Scratch->user_projects($this->Session->read('Auth.User.id'), true);
if(isset($all_projects) && !empty($all_projects)){
    $all_projects = Set::combine($all_projects, '{n}.p.id', '{n}.p.title');
}

$user_projects = array_map(function($v){
    return trim(htmlentities($v, ENT_QUOTES, "UTF-8"));
}, $all_projects);

 ?>
<div class="row manage-risk">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                <?php echo $page_heading; ?>
                <p class="text-muted date-time">
                    <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                </p>
            </h1>
            </section>
        </div>
        <div class="box-content">
            <div class="white-overlay"></div>
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header  filters" style="">
                            <!-- Modal Boxes // PASSWORD DELETE-->
                            <div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                            <!-- FILTER BOX -->
                            <div class="col-sm-9 col-md-9 col-lg-7 row-first risk-filter">
                               <!-- <label for="ProjectCategoryId" class="pull-left" style="margin-top: 5px; font-weight: bold;">Assigned to project: </label>-->
                                <div class="col-sm-7 col-md-6 col-lg-6 risk-select">
                                    <label class="custom-dropdown <?php if(isset($project_id) && !empty($project_id)) { echo 'disabled'; } ?>" style="width: 100%;">
                                        <?php
                                            echo $this->Form->select('RmDetail.project_id', $user_projects, array('escape' => false, 'empty' => 'Select Project', 'class' => 'form-control aqua', 'id' => 'risk_projects', 'default' => $passed_project));
                                        ?>
                                    </label>
                                </div>
                                <span class="error-message error text-danger" style="padding: 10px 0 0 0; display: inline-block;"><?php if(!$RmDetail){ ?>Please select a Project<?php } ?></span>

                            <?php if($RmDetail){ ?>
                                <span class="ico-nudge ico-risk tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $RmDetail['RmDetail']['project_id'], 'risk' => $RmDetail['RmDetail']['id'], 'type' => 'risk', 'admin' => false)); ?>"></span>
                            <?php } ?>
                            </div>


                            <div class="action more-dropdown pull-right risk-but-top">
                                <span class="loader-icon fa fa-spinner fa-pulse save-loader" style="display: none;"></span>
                                <?php if($RmDetail){ ?>
                                    <a class="btn btn-success btn-sm update_risk"> Save </a>
                                <?php }else{ ?>
                                    <a class="btn btn-success btn-sm save_risk" > Add </a>
                                <?php } ?>
                                <?php
                                $link = array("controller" => "risks", "action" => "index", $pid);
                                if(isset($from_summary) && !empty($from_summary)){
                                    $link = array("controller" => "projects", "action" => "index", $from_summary, 'tab' => 'risk');
                                }
                                else if($RmDetail){
                                    $link = array("controller" => "risks", "action" => "index", $RmDetail['RmDetail']['project_id']);
                                } ?>
                                <a href="<?php echo Router::Url($link, true); ?>" class="btn btn-sm btn-danger cancel_risk" >Cancel</a>
                            </div>
                            <!-- END FILTER BOX -->
                        </div>
                        <div class="box-body clearfix" style="overflow-y: auto; overflow-x: hidden;" id="box_body">
                            <div class="form-overlay"></div>
                            <div class="risk-center-area">
                                <form>
                                    <?php if($RmDetail){ ?>
                                        <input type="hidden" name="risk_id" id="risk_id" value="<?php echo $RmDetail['RmDetail']['id']; ?>" />
                                    <?php } ?>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="RiskTitle">Risk Title:</label>
                                            <textarea class="form-control risk-title" name="data[RmDetail][title]" id="risk_title" placeholder="max chars allowed 50" rows="1"><?php echo ($RmDetail) ? htmlentities($RmDetail['RmDetail']['title']) : ''; ?></textarea>
                                            <span class="error-message error text-danger" ></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="RiskType">Risk Type: </label>
                                            <?php //
                                            echo $this->Form->select('RmDetail.rm_project_risk_type_id', [], array('escape' => false, 'empty' => 'Select Risk Type', 'class' => 'form-control', 'id' => 'project_risk_types', 'onfocus'=>'this.size=8;', 'onblur'=>'this.size=1;', 'onchange'=>'this.size=1; this.blur();', 'style' => 'position: absolute; z-index: 3;')); ?>

                                            <span class="loader-icon fa fa-spinner fa-pulse"></span>
                                            <span class="error-message error text-danger" style="padding-top: 40px; clear: both !important; display: block !important;"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="RiskTypes"><span class="risktype">Manage Risk Types: </span>
                                                <span class="addupdate">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active">
                                                            <a class="add-link" href="#add_types" data-toggle="tab" aria-expanded="false">Add</a></li>
                                                        <li><a  href="#update_types" data-toggle="tab" aria-expanded="false">Edit</a></li>
                                                    </ul>
                                                </span>
                                            </label>
                                            <div id="add_types" class="tab-pane fade active in">
                                                <input type="text" class="form-control risk-types-input" id="RiskTypes" placeholder="max chars allowed 50">
                                                <span class="iocn-plus">
                                                    <i class="workspace-icon ico-types ico-add-type tipText" title="Add"></i>
                                                    <i class="clearblackicon ico-types ico-clear-type tipText" title="Clear"></i>
                                                </span>
                                                <span class="error-message error text-danger" style="font-size: 11px;"></span>
                                            </div>
                                            <div id="update_types" class="tab-pane fade">
                                                <div class="rm-type-dd">
                                                    <span class="selected-type">No Risk Type found</span>
                                                </div>
                                            </div>
                                            <span class="loader-icon fa fa-spinner fa-pulse"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 assigned-to">
                                        <div class="form-group">
                                            <label for="RiskTypes">Risk Team Members:</label>
                                            <div class="project_user_filter" id="project_user_filter">
                                                <select class="form-control" id="project_users" multiple=""></select>
                                            </div>
                                            <span class="loader-icon fa fa-spinner fa-pulse"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="">
                                            <label for="RiskTypes">Related To Tasks:</label>
                                            <div class="project_element_filter" id="project_element_filter">
                                                <select class="form-control" id="project_elements" ></select>
                                            </div>
                                            <span class="loader-icon fa fa-spinner fa-pulse"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="RiskTypes">Possible Occurrence By:</label>
                                            <div class="input-group">
                                                <?php
                                                $future_date = false;
                                                if($RmDetail){
                                                    if(isset($RmDetail['RmDetail']['possible_occurrence']) && !empty(($RmDetail['RmDetail']['possible_occurrence']))) {
                                                        $future_date = date('d M Y', strtotime($RmDetail['RmDetail']['possible_occurrence']));
                                                    }
                                                }
                                                ?>
                                                <input name="future_date" id="future_date" readonly="readonly" class="form-control dates input-small future_date" value="<?php if($future_date) echo $future_date; ?>" type="text">
                                                <div class="input-group-addon calendar-trigger">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                            <span class="error-message error text-danger" style="font-size: 11px;"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="risk_leaders">Risk Team Leaders:</label>
                                            <div class="risk_leaders_wrap" id="risk_leaders_wrap">
                                                <select class="form-control" id="risk_leaders" multiple=""></select>
                                            </div>
                                            <span class="loader-icon fa fa-spinner fa-pulse"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="RiskTitle">Description:</label>
                                            <textarea class="form-control" rows="7" id="risk_description" placeholder="max chars allowed 750"><?php echo ($RmDetail) ? htmlentities($RmDetail['RmDetail']['description']) : ''; ?></textarea>
                                            <span class="error-message error text-danger" ></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
							<div class="action more-dropdown pull-left risk-but-top" style="margin-left:15px;">
                                <?php if($RmDetail){ ?>
                                    <a class="btn btn-success btn-sm update_risk"> Save </a>
                                <?php }else{ ?>
                                    <a class="btn btn-success btn-sm save_risk "> Add </a>
                                <?php } ?>

                                <?php
                                $link = array("controller" => "risks", "action" => "index", $pid);
                                if(isset($from_summary) && !empty($from_summary)){
                                    $link = array("controller" => "projects", "action" => "index", $from_summary, 'tab' => 'risk');
                                }
                                else if($RmDetail){
                                    $link = array("controller" => "risks", "action" => "index", $RmDetail['RmDetail']['project_id']);
                                } ?>
                                <a href="<?php echo Router::Url($link, true); ?>" class="btn btn-sm btn-danger cancel_risk" >Cancel</a>
                                <span class="loader-icon fa fa-spinner fa-pulse save-loader" style="display: none;"></span>
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
