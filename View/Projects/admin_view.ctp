<?php
//pr($this->data);
?>
<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Project: 
				<?php if(isset($this->data['Project']['project_name']) && !empty($this->data['Project']['project_name'])){
					echo $this->data['Project']['project_name'];
				} ?>
				</h4>
			</div>
				<div class="modal-body">
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Company:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['Company']['company_name']) && !empty($this->data['Company']['company_name'])){
							echo $this->data['Company']['company_name'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Tax Year:</label>
					  <div class="col-lg-7">
						<?php 
							echo $this->data['Project']['tax_year_initial'].' To '.$this->data['Project']['tax_year_expire'];
						?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Jrsx:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['Project']['jrsx']) && !empty($this->data['Project']['jrsx'])){
							echo $this->data['Project']['jrsx'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Project Category:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['ProjectsCategory']['category']) && !empty($this->data['ProjectsCategory']['category'])){
							echo $this->data['ProjectsCategory']['category'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Project Source:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['ProjectsSource']['source']) && !empty($this->data['ProjectsSource']['source'])){
							echo $this->data['ProjectsSource']['source'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Subject Expense Type:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['SubjectExpenseType']['expense']) && !empty($this->data['SubjectExpenseType']['expense'])){
							echo $this->data['SubjectExpenseType']['expense'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Subject Headcount Increase:</label>
					  <div class="col-lg-7">
						<?php 
						//if(isset($this->data['Project']['subject_headcount_increase']) && !empty($this->data['Project']['subject_headcount_increase'])){}
							echo $this->data['Project']['subject_headcount_increase'];
						 ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Subject Amount Invested:</label>
					  <div class="col-lg-7">
						<?php 
						//if(isset($this->data['Project']['subject_amount_invested']) && !empty($this->data['Project']['subject_amount_invested'])){}
							echo $this->data['Project']['subject_amount_invested'];
						 ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Calculated Value:</label>
					  <div class="col-lg-7">
						<?php 
						//if(isset($this->data['Project']['calculated_value']) && !empty($this->data['Project']['calculated_value'])){}
							echo $this->data['Project']['calculated_value'];
						 ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Created  Date:</label>
					  <div class="col-lg-7">
						<?php if(isset($this->data['Project']['created']) && !empty($this->data['Project']['created'])){
							echo $this->data['Project']['created'];
						} ?>
					  </div>
					</div>
					<div class="form-group clearfix">
					  <label for="IndustryClassificationClassification" class="col-lg-5 control-label">Project Status:</label>
					  <div class="col-lg-7">
							<?php if(isset($this->data['ProjectsStatus']['title']) && !empty($this->data['ProjectsStatus']['title'])){ 
							echo $this->data['ProjectsStatus']['title'];
						}
						?>
					  </div>
					</div>
				</div>
				
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->