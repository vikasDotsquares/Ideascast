<!-- EDIT Project  -->
	<div class="modal-dialog fullwidth">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit Project </h4>
			</div>
			<?php echo $this->Form->create('Project', array( 'url' => array('controller'=>'projects', 'action'=>'edit'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
					<?php echo $this->Form->input('Project.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => 'form-control')); ?>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectProjectName" class="col-lg-3 control-label">Title:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.project_name', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Enter name of Project "><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectCompanyId" class="col-lg-3 control-label">Company:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.company_id', array('options' => $this->Common->getCompanyList(),'label' => false, 'div' => false, 'class' => 'form-control','empty'=>'Select Company')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Select name of Company "><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectTaxYearInitial" class="col-lg-3 control-label">Tax Year:</label>
							  <div class="col-lg-3">
								<?php echo $this->Form->input('Project.tax_year_initial', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-2 center">To</div>
							  <div class="col-lg-3">
								<?php echo $this->Form->input('Project.tax_year_expire', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Enter Initial and Expire Tax Year of Project "><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectJrsx" class="col-lg-3 control-label">Jrsx:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.jrsx', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Enter Jrsx of Project "><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectProjectsCategoryId" class="col-lg-3 control-label">Project Category:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.projects_category_id', array('options' => $this->Common->getProjectCategorylist(),'label' => false, 'div' => false, 'class' => 'form-control','empty'=>'Select Project Category')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Select Project Category"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectProjectsSourceId" class="col-lg-3 control-label">Project Source:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.projects_source_id', array('options' => $this->Common->getProjectSourcelist(),'label' => false, 'div' => false, 'class' => 'form-control','empty'=>'Select Project Source')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Select Project Source"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectSubjectExpenseTypeId" class="col-lg-3 control-label">Subject Expense Type:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.subject_expense_type_id', array('options' => $this->Common->getSubjectExpenseTypelist(),'label' => false, 'div' => false, 'class' => 'form-control','empty'=>'Select Subject Expense Type')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Select Subject Expense Type"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectSubjectHeadcountIncrease" class="col-lg-3 control-label">Subject Headcount Increase:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.subject_headcount_increase', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Enter Subject Headcount Increase of Project"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectSubjectAmountInvested" class="col-lg-3 control-label">Subject Amount Invested:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.subject_amount_invested', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Enter Subject Amount Invested of Project"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="ProjectProjectsStatusId" class="col-lg-3 control-label">Project Status:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Project.projects_status_id', array('options' => $this->Common->getProjectStatusList(),'label' => false, 'div' => false, 'class' => 'form-control','empty'=>'Select Project Status')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Select Project Status"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>
							</div>
						</div>
					</div>
			
					
				</div>
				<div class="modal-footer clearfix">
					<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-check"></i> Update</button>
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
// Submit Edit Form 
$("#RecordFormedit").submit(function(e){
	var postData = $(this).serializeArray();
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#Recordedit').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
	e.preventDefault(); //STOP default action
	//e.unbind(); //unbind. to stop multiple form submit.
});


$(document).ready(function(){
	// All Common Functions Will listed here
	$('.on-off-btn').bootstrapToggle();
	// initilize popover tooltip message
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});
</script>