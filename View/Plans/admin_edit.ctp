<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>
<!-- EDIT Industry User -->
	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Edit Plan
            <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/plans">Back</a></h3>
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('Plan', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
				<?php echo $this->Form->input('Plan.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
				
					
					<div class="row">
						<div class="col-md-6">

							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Title:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Plan.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter title"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Description:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Plan.description', array('type' => 'textarea', 'escape' => false,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					</div>
					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Monthly:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Plan.plantype_monthly', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control ')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Yearly:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Plan.plantype_yearly', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control ')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
							
						</div>
					</div>
					
					<!--<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Once:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Plan.plantype_once', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
					</div>-->
					
					
					<div class="row">
						<div class="col-md-6">
							<?php 
								$checked = '';
								if(isset($this->data['Plan']['status']) && !empty($this->data['Plan']['status'])){
									$checked = 'checked'; 
								}
							?>
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Status:</label>
							  <div class="col-lg-9">
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[Plan][status]" <?php echo $checked; ?> type="checkbox">
								
							  </div>
							</div>
						</div>
					</div>
					
					
				</div>
				
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>

					 <a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/plans">Cancel</a>
					<!--<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
// Submit Edit Form 
$("#RecordFormedits").submit(function(e){
	var postData = new FormData($(this)[0]);
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
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){
			// Error Found
		}
	});
	e.preventDefault(); //STOP default action
	//e.unbind(); //unbind. to stop multiple form submit.
});


$(document).ready(function(){
var startDate = new Date();
var FromEndDate = new Date();
var ToEndDate = new Date();

ToEndDate.setDate(ToEndDate.getDate()+365);

	$('.from_date').datepicker({
	weekStart: 1,
	endDate: FromEndDate, 
	autoclose: true
	})
	.on('changeDate', function(selected){
	startDate = new Date(selected.date.valueOf());
	startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
	$('.to_date').datepicker('setStartDate', startDate);
	}); 
	
	$('.to_date').datepicker({
        
        weekStart: 1,
        endDate: ToEndDate,
        autoclose: true
    })
    .on('changeDate', function(selected){
        FromEndDate = new Date(selected.date.valueOf());
        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
        $('.from_date').datepicker('setEndDate', FromEndDate);
    });

  
	$('[data-toggle="popover"]').popover({container: 'body',html: true,placement: "left"});
});

</script>