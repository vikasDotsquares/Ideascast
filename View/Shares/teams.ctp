<?php 
echo $this->Html->css('projects/teams');
echo $this->Html->css('projects/dropdown');
echo $this->Html->script('projects/teams');


if(isset($project_id) && !empty($project_id)) { ?>
<script type="text/javascript">
	$(function () {
		setTimeout(function(){
			$('#project_users_list .list-group-item[data-pid="<?php echo $project_id;?>"]').trigger('click');
		},500);
	})
</script>
<?php } ?> 
<!-- OUTER WRAPPER	-->
<div class="row project_team">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_subheading; ?></span>
					</p>
				</h1>

		</section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
						
						<!-- CONTENT HEADING -->
                        <div class="box-header filters">
							
							
							<div class="col-sm-12 col-md-12 col-lg-12 nopadding-left">								
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-5">
										<div class="input-group idea-custom-align">
											<label class=" input-group-addon" for="ProjectCategoryId">Aligned To:</label>
											<label style="width: 100%; vertical-align:middle;" class="custom-dropdown">
												<select id="ProjectAlignedId" class="form-control aqua" name="data[Project][aligned_id]">
													<option value="">Select Alignment</option>
													<?php if(isset($aligneds) && !empty($aligneds)) { ?>
													<?php foreach($aligneds as $k => $v) { ?>
													<option value="<?php echo $k; ?>" <?php if(isset($align_id) && $align_id == $k) { ?> selected="selected" <?php } ?>><?php echo $v; ?></option>
													<?php } ?>
													<?php } ?>
												</select>
											</label>
											<div class="input-group-addon">
												<a class="btn btn-success btn-sm" id="filter_list"> Apply Filter </a>
												<a class="btn btn-danger btn-sm" id="filter_reset"> Reset </a>
											</div>
										</div>
									</div>
									<div style="margin-top: 2px;" class="col-sm-12 col-md-12 col-lg-5 btn-options">
                                        <a class="btn btn-success create_project btn-sm tipText" title="Organize Projects" href="<?php echo SITEURL.'categories/manage_categories' ?>"><i class="glyphicon glyphicon-signal fa-rotate-ac-90"></i> Category Organizer </a> 
                                        <a class="btn btn-warning btn-sm create_project tipText" title="Create Project" href="<?php echo SITEURL.'projects/manage_project' ?>"><i class="fa fa-plus"></i> Create Project </a> 
                                         <a class="btn btn-warning btn-sm create_project tipText create_todolink" title="Create To-do" href="<?php echo SITEURL.'todos/manage' ?>"><i class="fa fa-plus"></i> Create To-do </a>  
										
									</div>
									</div>
								<?php  //echo $this->Form->end(); ?>
							</div>
							
							
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							
                        </div>
						<!-- END CONTENT HEADING -->
						
                        <div class="box-body border-top" style="min-height: 500px;">
							
							<div class="col-sm-12 exp_col">
								<div class="col-sm-3 collapible" id="team_project_users">
									<div class="team-box">	
										<div class="team-box-heading">	
											<div class="filter-label">PROJECTS</div>
											<div class="filter-input-wrap"><input type="text" id="project_filter" class="filter-input" > 
												<i title="" class="fa fa-times text-red remove-filter tipText" data-original-title="Clear Filters"></i>
											</div>
										</div>
										<div class="team-box-body listing">	
											<ul class="list-group" id="project_users_list">
											<?php if( isset($projects) && !empty($projects) ) { 
												$aClass = '';
											?>
												<?php foreach($projects as $key => $value ) { 
												
													/* if( (isset($project_id) && !empty($project_id) ) && $key == $project_id ){
														$aClass = 'active';	
													} else {
														$aClass = '';	
													} */
												?>
													<li class="list-group-item <?php echo $aClass; ?>" data-pid="<?php echo $key; ?>"><a class="project_list" href="<?php echo SITEURL;?>projects/index/<?php echo $key; ?>"><?php echo ( strlen($value) > 25 ) ? substr(html_entity_decode($value), 0, 25).'...' : html_entity_decode($value); ?></a><span data-id="<?php echo $key; ?>" data-placement="left" title="" class="label label-default label-pull pull-right team_view_data tipText team_open_project" data-original-title="Shows Information"><i class="fa fa-chevron-right"></i></span>
													</li>
												<?php } ?>
											<?php } ?>
											</ul>
										</div>
									</div>
								</div>
								<div class="col-sm-3 collapible" id="owner_creator">
									<div class="team-box">	
										<div class="team-box-heading">	
											<div class="filter-label">TEAM</div>
											<div class="filter-input-wrap"><input type="text" id="pTeam_filter" class="filter-input" > 
												<i title="" class="fa fa-times text-red remove-filter tipText" data-original-title="Clear Filters"></i>
											</div>
										</div>
										<div class="team-box-body listing" id="owner_user_list">	
											<ul class="list-group" id="project_users_list" data-pid="<?php echo $project_id; ?>">
												
											</ul>
										</div>
									</div>
								</div>
								<div class="col-sm-3 collapible" id="elements">
									<div class="team-box">	
										<div class="team-box-heading">	
											<div class="filter-label">TASKS</div>
											<div class="filter-input-wrap"><input type="text" id="pTask_filter" class="filter-input" > 
												<i title="" class="fa fa-times text-red remove-filter tipText" data-original-title="Clear Filters"></i>
											</div>
										</div>
										<div class="team-box-body listing" id="project_users_element">	
											<ul class="list-group" id="project_users_list" data-pid="<?php echo $project_id; ?>">
												
											</ul>
										</div>
									</div>
								</div>
								<div class="col-sm-3 collapible" >
									<div class="team-box">	
										<div class="team-box-heading">	
											<div class="filter-label-last">CO-WORKERS ON TASK</div>
										</div>
										<div class="team-box-body listing" id="element_users">	
											<ul class="list-group" id="project_users_list" data-pid="<?php echo $project_id; ?>">
												
											</ul>
										</div>
									</div>
								</div>
							</div>
							
						</div>
                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->		
	</div>
</div>
<!-- END OUTER WRAPPER -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="popup_model_box" class="modal modal-success fade" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content"><div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img style="margin: auto;" src="<?php echo SITEURL; ?>images/ajax-loader-1.gif"></div></div>
	</div>
</div>
<script type="text/javascript" >
$(function(){
	
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }) 
	
})
</script>
		