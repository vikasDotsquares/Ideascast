<!-- ADD NEW Industry User -->
	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Add Settings
            <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/plans">Back</a></h3>
      </div>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('Setting', array( 'url' => array('controller'=>'settings', 'action'=>'add'),  'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'Plan')); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Facebook:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.fb', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter url"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Twitter:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.twitter', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control ')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter url"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						
						
					</div>
					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">LinkedIn:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.linkedin', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter url"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Youtube:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.youtube', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control ')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter url"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
							
						</div>
					</div>
					

					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Phone:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.phone', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Company Phone:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.cphone', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
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
							  <label for="UserUser" class="col-lg-3 control-label">Email:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.email', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter email"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>					
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Contact Address:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Setting.address', array('type' => 'textarea', 'escape' => false,'label' => false, 'div' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>	
						
					</div>					
					
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-check"></i> Create</button>
					 <a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/plans">Discard</a>
					<!--<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button> -->
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
<script type="text/javascript" >
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
// Submit Add Form 
$("#Plans").submit(function(e){
	var postData = new FormData($(this)[0]);
	var formURL = $(this).attr("action");
	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		success:function(response){	
			if($.trim(response) != 'success'){
				$('#RecordAdd').html(response);
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
</script>