<?php echo $this->Html->css(array(
	'projects/org_listings.css',
)); ?>
<?php echo $this->Html->script('projects/app_listing'); ?>

<style type="text/css">
	section.content{
		padding-top: 0;
	}

	.not-shown {
	    display: none !important;
	}
	.error {
	    margin-bottom: 0;
	    font-size: 11px;
	    font-weight: 400;
	    vertical-align: top;
	    display: block;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<section class="main-heading-wrap pb6">
			<div class="main-heading-sec">
				<h1><?php echo $page_heading; ?></h1>
				<div class="subtitles">
					<?php echo $page_subheading; ?>
				</div>
			</div>
			<div class="header-right-side-icon">
			</div>
		</section>
		<div class="box-content postion projects-summary-details">
			<div class="competencies-tab">
				<div class="row">
					<div class="col-md-12">
						<ul class="nav nav-tabs" id="list_tabs" >
							<li class="active">
								<a data-toggle="tab" class="tab_program_type" data-target="#tab_program_type" href="#tab_program_type" aria-expanded="true">program type</a>
							</li>

							<li class="">
								<a data-toggle="tab" data-type="searchs" class="tab_project_type  " data-target="#tab_project_type" href="#tab_project_type">project type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_task_type" data-target="#tab_task_type" href="#tab_task_type" >Task Type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_cost_type" data-target="#tab_cost_type" href="#tab_cost_type" >Cost Type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_risk_type" data-target="#tab_risk_type" href="#tab_risk_type" >Risk Type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_currency" data-target="#tab_currency" href="#tab_currency" >Currency</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_org_type" data-target="#tab_org_type" href="#tab_org_type" >org type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_loc_type" data-target="#tab_loc_type" href="#tab_loc_type" >location type</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_story_type" data-target="#tab_story_type" href="#tab_story_type" >story type</a>
							</li>

						</ul>
					</div>
				</div>
			</div>

			<div class="box noborder">
				<div class="box-body clearfix nopadding wsp-task-scroll" style="" id="box_body">
					<div class="tab-content">

						<div class="tab-pane fade active in " id="tab_program_type">
							<div class="tab-wrap program-type-wrap">
							  	<div class="projt-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="prog-type-id">
											<input type="text" name="program_type_title" class="form-control prog-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red prog-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success prog-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger prog-reset">Reset</a>
										</div>
									</div>
									<div class="types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-data">(0)</span>
										</div>
										<div class="types-col types-col-2">Programs
										</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="program_types_wrap">
									 	<?php echo $this->element('../Organisations/partial/program_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade " id="tab_project_type">
							<div class="tab-wrap project-type-wrap">
							  	<div class="projt-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="prj-type-id">
											<input type="text" name="type_title" class="form-control prj-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red prj-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success prj-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger prj-reset">Reset</a>
										</div>
									</div>
									<div class="types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-data">(0)</span>
										</div>
										<div class="types-col types-col-2">Projects
										</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="types_wrap">
									 	<?php echo $this->element('../Organisations/partial/project_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_task_type">
							<div class="task-type-wrap">
							  	<div class="task-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="task-type-id">
											<input type="text" name="type_title" class="form-control task-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red task-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success task-type-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger task-type-reset">Reset</a>
										</div>
									</div>
									<div class="task-types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-task-types list-counter">(0)</span>
										</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="task-type-list">
									 	<?php echo $this->element('../Organisations/partial/task_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade " id="tab_cost_type">
							<div class="tab-wrap cost-type-wrap">
							  	<div class="projt-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="cost-type-id">
											<input type="text" name="type_title" class="form-control cost-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red cost-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success cost-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger cost-reset">Reset</a>
										</div>
									</div>
									<div class="types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-data">(0)</span>
										</div>
										<div class="types-col types-col-2">Costs
										</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="cost_types_wrap">
									 	<?php echo $this->element('../Organisations/partial/cost_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_risk_type">
							<div class="task-type-wrap">
							  	<div class="task-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="task-type-id">
											<input type="text" name="type_title" class="form-control task-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red task-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success task-type-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger task-type-reset">Reset</a>
										</div>
									</div>
									<div class="task-types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-task-types list-counter">(0)</span>
										</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="task-type-list">
									 	<?php echo $this->element('../Organisations/partial/risk_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_currency">
							<div class="currency-tab-wrap">
								<div class="currency-tab-sec">
									<div class="currency-search-wrap">
										<div class="currency-col currency-col-1">
											<label>Currency Name:</label>
											<input type="hidden" name="curr_id" class="curr-id">
											<input type="text" name="curr_title" id="curr_title" class="form-control" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red curr-error"></label>
										</div>
										<div class="currency-col currency-col-2">
											<label>Code:</label>
											<input type="text" name="curr_code" id="curr_code" class="form-control" autocomplete="off" placeholder="3 chars" maxlength="3">
										</div>
										<div class="currency-col currency-col-4">
											<label>Active:</label>
											<input type="checkbox" name="active" class="active-id" id="curr_status">
										</div>
										<div class="currency-col currency-col-5">
											<a href="" class="btn btn-md btn-success curr-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger curr-reset">Reset</a>
										</div>
									</div>
									<div class="currency-list-wrap">
										<div class="currency-col-header">
											<div class="cur-col cur-col-1 ">Currencies <span class="curr-count"> (0)</span></div>
											<div class="cur-col cur-col-2">Code</div>
											<div class="cur-col cur-col-3">Active</div>
											<div class="cur-col cur-col-5">Projects</div>
											<div class="cur-col cur-col-5">Actions</div>
										</div>
										<div class="currency-col-list">
											<?php echo $this->element('../Organisations/partial/currency_list'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_org_type">
							<div class="task-type-wrap">
							  	<div class="task-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="task-type-id">
											<input type="text" name="type_title" class="form-control task-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red task-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success task-type-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger task-type-reset">Reset</a>
										</div>
									</div>
									<div class="task-types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-task-types list-counter">(0)</span>
										</div>
										<div class="types-col types-col-2">Organizations</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="task-type-list">
									 	<?php echo $this->element('../Organisations/partial/org_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_loc_type">
							<div class="task-type-wrap">
							  	<div class="task-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="task-type-id">
											<input type="text" name="type_title" class="form-control task-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red task-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success task-type-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger task-type-reset">Reset</a>
										</div>
									</div>
									<div class="task-types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-task-types list-counter">(0)</span>
										</div>
										<div class="types-col types-col-2">Locations</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="task-type-list">
									 	<?php echo $this->element('../Organisations/partial/loc_type_list'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_story_type">
							<div class="task-type-wrap">
							  	<div class="task-type-sec">
									<label>List Item:</label>
									<div class="types-search-wrap">
										<div class="search-col search-col-1">
											<input type="hidden" name="type_id" class="task-type-id">
											<input type="text" name="type_title" class="form-control task-type-title" autocomplete="off" placeholder="50 chars" maxlength="50">
											<label class="error text-red task-type-error"></label>
										</div>
										<div class="search-col search-col-2">
											<a href="" class="btn btn-md btn-success task-type-add-update">Add</a>
											<a href="" class="btn btn-md btn-danger task-type-reset">Reset</a>
										</div>
									</div>
									<div class="task-types-col-header">
										<div class="types-col types-col-1 ">Items
											<span class="total-task-types list-counter">(0)</span>
										</div>
										<div class="types-col types-col-2">Stories</div>
										<div class="types-col types-col-3">Actions</div>
									</div>
									<div class="task-type-list">
									 	<?php echo $this->element('../Organisations/partial/story_type_list'); ?>
									</div>
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

<div class="modal modal-success fade" id="model_reassign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog reassign-popup">
        <div class="modal-content"></div>
    </div>
</div>

<script type="text/javascript">
	$('html').addClass('no-scroll');

</script>
