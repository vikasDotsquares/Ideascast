<?php
echo $this->Html->css('jquery.treeview');
echo $this->Html->css('projects/bs.checkbox');
echo $this->Html->script('jquery.treeview', array('inline' => true));
echo $this->Html->css('projects/costs');
echo $this->Html->script('projects/costs', array('inline' => true));


if(isset($project_id) && !empty($project_id)) {
?>
<script type="text/javascript" >
    $(function(){

        $('body').on('hidden.bs.modal', '#modal_small', function(event){
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('')
        })

		$('body').on('hidden.bs.modal', '#modal_medium', function(event){
			console.log("#modal_medium");
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('')
        })
    })
</script>
<?php } ?>
<style>

    #myModalLabel {
        font-size: 24px;
    }
	.icon-file-export {
		height: 21px;
		width: 21px;
		vertical-align: top;
	}
	.editannotatetext{
		word-break: break-all;
	}
    .nudge-wrapper {
        display: none;
    }
    .nudge-wrapper.sel {
        display: inline-block;
    }
</style>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content"></div>
    </div>
</div>

<div class="row manage-costs">
    <div class="col-xs-12">
        <div class="row">
           <section class="content-header clearfix">
                <h1 class="pull-left">
                    <?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
           </section>
        </div>
        <span id="project_header_image">
            <?php
            if (isset($project_id) && !empty($project_id)) {
                echo $this->element('../Projects/partials/project_header_image', array( 'p_id' => $project_id ));
            }
			$export_show_or_not = 'display:none;';

			if (isset($project_id) && !empty($project_id)) {
				$project_workspace_details = $this->ViewModel->getProjectWorkspaces( $project_id, 1 );
				if (isset($project_workspace_details) && !empty($project_workspace_details)) {
					$export_show_or_not = 'display:inline-block;';
				}
			}

            ?>
        </span>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header border-bottom-two filters"  style="">
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
                            <div class="col-sm-12 col-md-12 col-lg-12 row-first  cost-header-sec">
                                <div class="clearfix inner-first cost-label-sec">
                                <div class="select-filds-header studios-row-h">
                                   <label for="projectId">Projects</label>
									<label class="custom-dropdown">
    								    <select class="aqua" name="project_id" id="projectId">
                                            <option value="">Select Project</option>
                                            <?php
                                            if(isset($my_projects) && !empty($my_projects)) {
                                                foreach($my_projects as $key => $val ) {
                                                    if( !empty($key)){
                                                        $sel = '';
                                                        if (isset($project_id) && !empty($project_id) && $key == $project_id) {
                                                            $sel = 'selected="selected"';
                                                        }
                                                        echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                                                    }
                                                }
                                            } ?>
                                        </select>
									</label>
                                    <i class="fa fa-spinner fa-pulse" id="project_spinner" style="display: none;"></i>

                                    <div class="nudge-wrapper">
                                        <span class="ico-nudge ico-cost tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php //echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge', 'project' => $RmDetail['RmDetail']['project_id'], 'risk' => $RmDetail['RmDetail']['id'], 'type' => 'risk', 'admin' => false)); ?>"></span>
                                    </div>
                                </div>

                                </div>

                                <div class="ratecard">

									<a data-toggle="modal" title="Generate Spreadsheet" id="exportdataswcost" data-target="#modal_medium" data-modal-width="600" class=" tipText" data-remote="<?php echo SITEURL;?>costs/export_xls/<?php echo $project_id;?>" rel="tooltip"  <?php if (!isset($project_id) || empty($project_id)) { ?> style="<?php echo $export_show_or_not;?> " <?php } else { ?> style="<?php echo $export_show_or_not;?> "<?php } ?> > <i class="reportblack"></i></a>

                                    <a href="#" class="cost-rate-btn tipText" title="Rate Cards" id="ratecard-but" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::Url( array( "controller" => "costs", "action" => "user_rates", $project_id, 'admin' => FALSE ), true ); ?>" <?php if (!isset($project_id) || empty($project_id)) { ?> style="display: none;" <?php } ?>> <i class="ratesblack"></i></a>

                                    <span id="sidetreecontrol" style="display: none;">
    									<a href="#" class="tipText searchbtn collp" title="Collapse"><i class="showlessblack"></i> </a>
    									<a href="#" class="tipText searchbtn expd" title="Expand"><i class="showmoreblack"></i> </a>
                                    </span>


									<div class="serch-input">
										<div class="input-group input-group-sm">
											<input type="text" placeholder="Search"  class="form-control search" id="search" autocomplete="off" />
											<span class="input-group-addon clear-serch not-working" >
												<i class="fa fa-times"></i>
											</span>
										</div>
									</div>



                                </div>
                            </div>
                            <!-- END FILTER BOX -->
                        </div>

                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">
                            <?php
							if(isset($my_projects) && !empty($my_projects)) {
								$txs = "SELECT PROJECT";
							}else{
								$txs = "NO PROJECTS";
							}
                            if (!isset($project_id) || empty($project_id)) { ?>
							     <div class="select_msg col-sm-12"> <?php echo $txs; ?></div>
                            <?php }
                                //echo $this->element('../Costs/partials/tree', ['project_id' => $project_id]);
                            ?>
                        </div><!-- /.box-body -->

                    </div><!-- /.box -->
                </div>
           </div>
        </div>
    </div>
</div>
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog rate-card-popup">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="model_bx_wsp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade" id="model_bx_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<script>
$(function(){
    $('#model_bx').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('')
    })

});
</script>

<?php if (isset($project_id) && !empty($project_id) ) { ?>
<script type="text/javascript">
    $(function(){
        setTimeout(function(){
            $('#projectId').trigger('change');
        }, 500)


    })
</script>
<?php } ?>