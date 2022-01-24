<?php echo $this->Html->css('projects/risk_center'); ?>
<?php echo $this->Html->script('projects/risk_center'); ?>

<style>
	section.content {
		padding-top: 0;
	}
	.no-scroll {
		overflow: hidden;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<section class="main-heading-wrap">
			<div class="main-heading-sec">
				<h1><?php echo $page_heading; ?></h1>
				<div class="subtitles"><?php echo $page_subheading; ?></div>
			</div>
			<!-- <div class="header-right-side-icon">
				<div class=""><a class="" href="#">icons</a></div>
			</div> -->
		</section>
		<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder mt6">
						<div class="box-header filters" style="">
							<div class="risk-select-top">
								<label class="custom-dropdown" style="width: 100%;">
									<?php
                                		if(isset($my_risks) && !empty($my_risks)) $project_id = 'my';

                                		 if(isset($customProject) && !empty($customProject)) $project_id = $customProject;

                                        $projects1 = ["my" => "My Risks", "all" => "All Projects"];
                                        $allProjects = (isset($projects) && !empty($projects)) ? $projects1 + $projects : $projects1;

                                        echo $this->Form->select('RmDetail.project_id', $allProjects, array('escape' => false, 'empty' => 'Select View', 'class' => 'form-control aqua', 'id' => 'risk_projects', 'default' => $project_id));

                                    ?>
								</label>

								</div>
							</div>
							<div class="box-body clearfix risk-data-wrap">
								<div class="risks-multi-select-sec">
									<div class="risks-multi-select-left <?php if($no_params){ ?>no-selection<?php } ?>">
										<div class="risks-multi-select1">
											<label class="custom-dropdown" style="width: 100%;">
												<select class="form-control aqua" id="dd_risk_types">
													<option value="">All Risk Types</option>
												<?php //
													if(isset($risk_types) && !empty($risk_types)) {
														$risk_types = Set::extract($risk_types, '{n}.rpt.title');
														?>
														<?php
														foreach ($risk_types as $key => $value) {
														?>
														<option value="<?php echo htmlentities($value, ENT_QUOTES, "UTF-8"); ?>"><?php echo htmlentities($value, ENT_QUOTES, "UTF-8"); ?></option>
														<?php
														}
													}
												?>
												</select>
											</label>

										</div>
										<div class="risks-multi-select2">
											<label class="custom-dropdown" style="width: 100%;">
												<label class="custom-dropdown" style="width: 100%;">
													<select class="form-control aqua" id="dd_statuses">
														<option value="">All Statuses</option>
														<option value="Open">Open</option>
														<option value="Review">In Progress</option>
														<option value="Overdue">Overdue</option>
														<option value="Completed">Completed</option>
													</select>
												</label>
											</label>

										</div>
										<div class="risks-multi-select2">
											<label class="custom-dropdown" style="width: 100%;">
												<select class="form-control aqua" id="dd_impacts">
													<option value="">All Impacts</option>
                                                    <option value="Not Set">Not Set</option>
                                                    <option value="1">Negligible</option>
                                                    <option value="2">Minor</option>
                                                    <option value="3">Moderate</option>
                                                    <option value="4">Major</option>
                                                    <option value="5">Critical</option>
												</select>
											</label>

										</div>
										<div class="risks-multi-select2">
											<label class="custom-dropdown" style="width: 100%;">
												<select class="form-control aqua" id="dd_percentages">
													<option value="">All Probabilities</option>
                                                    <option value="Not Set">Not Set</option>
                                                    <option value="1">Rare</option>
                                                    <option value="2">Unlikely</option>
                                                    <option value="3">Possible</option>
                                                    <option value="4">Likely</option>
                                                    <option value="5">Almost Certain</option>
												</select>
											</label>

										</div>
										<div class="risks-multi-select2">
											<?php $param_exposure = strtolower($param_exposure); ?>
											<label class="custom-dropdown" style="width: 100%;">
												<select class="form-control aqua" id="dd_exposers">
													<option value="">All Exposures</option>
                                                    <option value="severe" <?php if($param_exposure == 'severe'){ echo 'selected="selected"'; } ?>>Severe</option>
                                                    <option value="high" <?php if($param_exposure == 'high'){ echo 'selected="selected"'; } ?>>High</option>
                                                    <option value="medium" <?php if($param_exposure == 'medium'){ echo 'selected="selected"'; } ?>>Medium</option>
                                                    <option value="low" <?php if($param_exposure == 'low'){ echo 'selected="selected"'; } ?>>Low</option>
												</select>
											</label>
										</div>
									</div>
									<?php
									$url = '';
									if($project_id == 'my'){
										$url = '/project:my';
									}
									else if($project_id == 'all'){
										$url = '/project:all';
									}else{
										$url = '/'.$project_id;
									}

									?>
									<div class="risks-multi-right-icon">
										<a href="" class="reset-btn-rc tipText" title="" id="reset_filters" data-original-title="Reset"><i class="resetblack"></i></a>
										<a class="tipText" href="<?php echo SITEURL.'/risks/risk_map'.$url; ?>" title="Risk Map" id="btn_risk_map"><i class="heatmapblack"></i></a>
										<a class="tipText" id="btn_manage_risk" title="Add Risk" href="<?php echo Router::Url(['controller' => 'risks', 'action' => 'manage_risk', 'admin' => false], true); ?>"> <i class="workspace-icon"></i></a>
									</div>
								</div>
								<div class="risks-summary-wrap">
									<input type="hidden" name="paging_offset" id="paging_offset" value="0">
                                    <input type="hidden" name="paging_total" id="paging_total" value="">
									<div class="rs-col-header">
										<div class="rs-col rs-col-1">
											<span class="ps-h-one">Title <span class="total-data">(0)</span>
											<span class="sort_order tipText" data-coloumn="title" data-order="desc" data-type="risks" title="" data-original-title="Sort By Risk">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="sort_order tipText" data-coloumn="ptitle" data-order="desc" data-type="risks" title="" data-original-title="Sort By Project">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</span>
									</div>
									<div class="rs-col rs-col-2">
										Team <span class="sort_order tipText" data-coloumn="creator_name" data-order="desc" data-type="risks" title="" data-original-title="Sort By Creator">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="sort_order tipText" data-coloumn="ruser_count" data-order="desc" data-type="risks" title="" data-original-title="Sort By Total Assignee">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="rs-col rs-col-3">
										Type <span class="sort_order tipText" data-coloumn="risk_type" data-order="desc" data-type="risks" title="" data-original-title="Sort By Type">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="sort_order tipText" data-coloumn="rtask_count" data-order="desc" data-type="risks" title="" data-original-title="Sort By Total Task">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="rs-col rs-col-4">
										Status <span class="sort_order tipText" data-coloumn="rd_status" data-order="desc" data-type="risks" title="" data-original-title="Sort By Status">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>

									</div>
									<div class="rs-col rs-col-5">
										Impact <span class="sort_order tipText" data-coloumn="rd_impact" data-order="desc" data-type="risks" title="" data-original-title="Sort By Impact">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>

									</div>
									<div class="rs-col rs-col-6">
										Probability <span class="sort_order tipText" data-coloumn="rd_percent" data-order="desc" data-type="risks" title="" data-original-title="Sort By Probability">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>

									</div>

									<div class="rs-col rs-col-7">
										When <span class="sort_order tipText active" data-coloumn="rdate" data-order="asc" data-type="risks" title="" data-original-title="Sort By Possible Occurrence By">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="sort_order tipText" data-coloumn="rd_exposure" data-order="desc" data-type="risks" title="" data-original-title="Sort By Exposure">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="rs-col rs-col-7">
										Actions
									</div>
								</div>

								<div class="risks-summary-data" data-flag="true">
									<?php if(!$no_params){
										echo $this->element('../Risks/section/listing');
									}else{ ?>
									<div class="no-row-wrapper">Select View</div>
									<?php } ?>
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
<script type="text/javascript">
	$(function(){
		$('html').scrollTop(0);

	})
</script>
<!-- Modal Boxes -->
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

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