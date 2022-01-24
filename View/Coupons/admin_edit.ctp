<?php //echo $this->Html->script(array('bootstrap-toggle/bootstrap-toggle','common')); ?>
<!-- EDIT Industry User -->

	<div class="panel panel-primary">
	  <div class="panel-heading">
         <h3 class="panel-title">
            Edit Coupon
            <a class="btn btn-warning btn-xs pull-right" href="<?php echo SITEURL ?>sitepanel/coupons">Back</a></h3>
      </div><?php echo $this->Session->flash(); ?>
		<div class="panel-body form-horizontal">
			<div class="panel-heading">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add Coupon</h4> -->
			</div>
			<?php echo $this->Form->create('Coupon', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
				<?php echo $this->Form->input('Coupon.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					<?php echo $this->Form->input('Coupon.is_institution', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
					
					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Name:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter name"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
												<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Min Quantity:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.on_amount', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
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
							  <label for="UserUser" class="col-lg-3 control-label">Start:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.start_time', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control from_date')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
						<div class="col-md-6">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">End:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.end_time', array('type' => 'text','label' => false, 'div' => false, 'class' => 'form-control to_date')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
							
						</div>
					</div>
					
                  <div class="row">
					<div class="col-md-6">
					
					<div class="form-group has-feedback">
					<label for="UserUser" class="col-lg-3 control-label">Discount Type:</label>
						<div class="col-md-8">
						
						   <?php 
							$checkedF = '';
							$type = '';	
							if(isset($this->data['Coupon']['percentage']) && !empty($this->data['Coupon']['percentage']) && $this->data['Coupon']['percentage'] > 0){
								$checkedF = 'true';
								$type = 'per';	
							} 
							?>
							<?php 
							$checkedP = '';
							$type = '';
							if(isset($this->data['Coupon']['flat']) && !empty($this->data['Coupon']['flat']) && $this->data['Coupon']['flat'] > 0){
								$checkedP = 'true'; 
								$type = 'flat';	
							}
							
							?><?php echo $this->Form->input('Coupon.type', array('type' => 'hidden', 'label' => false,'value'=>$type , 'div' => false, 'class' => 'form-control')); ?>
						
							<?php echo $this->Form->input("disT", array(
													'type' => 'radio',
													'options' => array(' Flat '),
													'class' => 'testClass minimal',
													'div' => false,
													'id' => 'flat' ,
													'label' => false,
													'checked'=>$checkedP,
													'hiddenField' => false, // added for non-first elements
							)); ?>
							&nbsp;
							
							<?php
													
							echo $this->Form->input("disT", array(
													'type' => 'radio',
													'options' => array(' Percent '),
													'class' => 'testClass minimal',
													'div' => false,
													'id' => 'per' ,
													'checked'=>$checkedF,
													'label' => false,								 
													'hiddenField' => false, // added for non-first elements
							));						
													
													
						   ?></div>
						</div>						
						</div>			
			        </div>
					
					
					<div class="row">
					
					    <div class="col-md-6" id="per">	
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Percent:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.percentage', array('type' => 'text','label' => false, 'div' => false,'required' => false, 'class' => 'form-control')); ?>
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter percentage"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					
						<div class="col-md-6" id="flat">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Discount:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.flat', array('type' => 'text', 'label' => false, 'div' => false,'required' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div>
					
						
					</div>
						
					</div>
					<div class="row">

					
						<!-- <div class="col-md-6">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Usable:</label>
							  <div class="col-lg-8">
								<?php echo $this->Form->input('Coupon.useable', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
								
							  </div>
							  <div class="col-lg-1">
								<a tabindex="0" data-placement="top" class="btn btn-default toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter value"><i class="fa fa-info-circle fa-3 martop"></i></a>
							  </div>					  
							</div>
						</div> -->
					
					
					
					
					<?php if(isset($this->data['Coupon']['is_institution']) && !empty($this->data['Coupon']['is_institution'])){ ?>
					<div class="col-md-6">	
						<?php 
							$checked = '';
							if(isset($this->data['Coupon']['is_institution']) && !empty($this->data['Coupon']['is_institution'])){
								$checked = 'checked'; 
							}
						?>
						<div class="form-group">
						  <label for="UserUser" class="col-lg-3 control-label">Is institution:</label>
						 <div class="col-lg-9">
								<input disabled  data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[Coupon][is_institution]" <?php echo $checked; ?> type="checkbox">
							
						  </div>
						</div>
					</div>
					<?php } ?>
					 
					 
					<?php if(isset($this->data['Coupon']['is_institution']) && !empty($this->data['Coupon']['is_institution'])){ ?>
					<div class="col-md-12 col-xs-12 form-group" id="institutes">	
					<div class="col-md-2 col-xs-2" ></div>
					<div class="col-md-10 col-xs-10">
					
					<?php 					
					if(isset($this->data['Coupon']['user_id']) && !empty($this->data['Coupon']['user_id'])) {
					$dat = explode(",",$this->data['Coupon']['user_id']);
					foreach($dat as $in){ ?>
					<div class="form-group">
					<?php 
					$user = $this->Common->userDetail($in);
					
					//pr($user);
					?>&nbsp;<input  data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="<?php echo "data[inst][".$in."]" ?>" checked="true" type="checkbox">
					<?php echo $user['UserDetail']['first_name']." ".$user['UserDetail']['last_name'] ; ?>
					</div>
					<?php } 
					
					?>
					<?php 
					$insd = array_diff($ins,$dat);
					}else{
					$insd = $ins;
					}
					foreach($insd as $im){ 					
							$inster = $this->Common->userDetail($im);
							//pr($im);		
					 ?>
					<div class="form-group">
					&nbsp;<input  data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="<?php echo "data[inst][".$im."]" ?>" type="checkbox"> <?php echo $inster['UserDetail']['first_name']." ".$inster['UserDetail']['last_name']  ?>
					
					</div>
					<?php }  ?>
					</div>
					</div>
				   <?php }  ?>
				   </div>
				   
				   <div class="row">
				   	<div class="col-md-6">
							<?php 
								$checked = '';
								if(isset($this->data['Coupon']['status']) && !empty($this->data['Coupon']['status'])){
									$checked = 'checked'; 
								}
							?>
							<div class="form-group">
							  <label for="UserUser" class="col-lg-3 control-label">Status:</label>
							  <div class="col-lg-9">
									<input id="UserStatusADD" data-toggle="toggle" data-width="80" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" class="on-off-btn" name="data[Coupon][status]" <?php echo $checked; ?> type="checkbox">
								
							  </div>
							</div>
					</div>
				   </div>
				 
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Save</button>
					
					<a class="btn btn-danger" href="<?php echo SITEURL ?>sitepanel/coupons">Cancel</a>
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



$('#per').hide();
$('#per input').attr('disabled','disabled');

$('#flat').hide();
$('#flat input').attr('disabled','disabled');





$('input[type="checkbox"].minimals, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });


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


$(window).load(function(){


		if($('#per0').is(':checked')){
			$('#per').show();	
			$('#per input').show();			
			$('#per input').removeAttr('disabled','disabled');
			$('#CouponType').val('per');
			//$('#flat input').val('');
		}
		if($('#flat0').is(':checked')){
					$('#flat').show();	
					$('#flat input').show();			
					$('#flat input').removeAttr('disabled','disabled');
					//$('#per input').val('');
					$('#CouponType').val('flat');
		}
		
		$('#per0').next().click(function() {
		    $('#per').show();	
			$('#per input').show();
			$('#per input').removeAttr('disabled','disabled');
			
			$('#flat').hide();	
			$('#flat input').hide();
			$('#flat input').attr('disabled','disabled');
			//$('#flat input').val(0);
			$('#CouponType').val('per');
			 
		}); 
		
	    $('#flat0').next().click(function() { //alert(0);
		    $('#flat').show();	
			$('#flat input').show();
			$('#flat input').removeAttr('disabled','disabled');
			
			$('#per').hide();	
			$('#per input').hide();
			$('#per input').attr('disabled','disabled');
			//$('#per input').val('');
			$('#CouponType').val('flat');
		});


	 // $('.iCheck-helper').click(function() { alert($('.icheckbox_flat-blue').attr('aria-checked'));

	//	if($('.icheckbox_flat-blue').attr('aria-checked')=='true'){
		//$(this).prev().attr('checked','true');		
	//	}else{
		//$(this).prev().removeAttr('checked');	
	//	}
	//	}); 
})
</script>