 
<style>
.date_box {
	display: block;
	border: 1px solid #ccc;
	padding: 20px 5px 10px 5px;
	display: none;
}
.start-date-errors, .end-date-errors, .start-end-date-errors {
	display: block;
}
#accordions  .form-control[readonly] {
	cursor: default;
}
</style>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Today's Task Reminders</h3>
	</div>

<?php 
$current_user_id = $this->Session->read('Auth.User.id');
if( isset($reminder_elements) && !empty($reminder_elements) )  {  ?>
 
<!-- POPUP MODEL BOX CONTENT HEADER -->

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">
		
		<div class="panel-group panel-listing" id="accordions" style="margin-bottom: 0px;" >
			<?php foreach( $reminder_elements as $key => $val )  {
				if( !reminder_is_deleted($val['id'], $current_user_id) ){
				$element_detail = getByDbId('Element', $val['element_id'], ['id', 'title', 'start_date', 'end_date', 'date_constraints']);
				$el = $element_detail['Element'];
			?>
			<?php $element_status = element_status($el['id']); ?>
				<div class="panel panel-default panel-els no-shadow clearfix">
					<div class="panel-heading" style="padding: 0">
						<div class="form-group"> 
							<div class="title-group">
								<div class="title-group-input cell_<?php echo $element_status; ?>"  >
									<?php echo strip_tags($el['title']); ?>
								</div>
								<div class="title-group-addon"  data-parent="#accordions"  data-toggle="collapse" href="#collapseDates_<?php echo $el['id'] ?>" aria-expanded="false" aria-controls="collapseDates_<?php echo $el['id'] ?>">
									<i class="fa"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body panel-collapse collapse" id="collapseDates_<?php echo $el['id'] ?>" data-section="<?php echo $el['id'] ?>">
					
						<!-- <div class="form-group"> 
							<div class="title-group">
								<div class="title-group-input cell_<?php echo $element_status; ?>"  >
									<?php echo strip_tags($el['title']); ?>
								</div>
								<div class="title-group-addon"  data-parent="#accordions"  data-toggle="collapse" href="#collapseDates_<?php echo $el['id'] ?>" aria-expanded="false" aria-controls="collapseDates_<?php echo $el['id'] ?>">
									<i class="fa"></i>
								</div>
							</div>
						</div> -->
					
						<div class="date_constraints_wrappers" > 
							
							<div class="form-group clearfix">
								<div class="col-sm-6 nopadding-left">
									
									<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Task Start Date:</label>
									 
										<?php 
										$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d-m-Y", strtotime($el['start_date'])) : '';
										echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]); ?> 
								</div>
								<div class="col-sm-6 nopadding-right">
									<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">Task End Date:</label>
									 
										<?php 
										$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d-m-Y", strtotime($el['end_date'])) : '';
										echo $this->Form->input('Element.end_date', [ 'name'=>'data['.$key.'][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value' => $endate]); ?> 
								</div>
							</div>
							 <?php
								$status_class_name = ''; 
								$str = '';
								$remaining_left = remaining_left($val['reminder_date']);
								 
									if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='today'){
										$str .= "Today ";
										$status_class_name = 'today';
										 
									} 
									$str .= $remaining_left['text'];
									 
									if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='OVD'){
										$status_class_name = 'overdue';
									}else if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='to go'){
										$status_class_name = 'pending';
									}
								?>
							<div class="form-group clearfix">
								<label class="" for="reminder_date" style="padding-top: 6px; padding-left: 0px;">Reminder Ends:</label> 
								<?php 

								echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][reminder_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'reminder_date'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $str ]); ?> 
								 
							</div>

							<div class="form-group clearfix">  
								<label class="" for="comments" style="padding-top: 6px; padding-left: 0px;">Comment:</label> 
								<?php echo $this->Form->input('Reminder.comments',['type'=>'textarea','label'=>false,'rows'=>"5",'style'=>"resize: none;",'class'=>"form-control", 'readonly' => 'readonly', 'value' => $val['comments']]); ?>
							</div>

						</div>
					</div>
				</div>
		<?php } ?>
		<?php } ?>
			</div>
		
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		
		 <!--<button type="button" class="btn btn-success submit_gotit">Got It</button>-->
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>
	 
	<script type="text/javascript" >
		$(function() {
			
			$('.ind-gotit').on('click', function(event){
				event.preventDefault();
				var $this = $(this);
				var reminder_id = $(this).data('reminder');
				$.ajax({
					url: $js_config.base_url + 'dashboards/delete_reminder',
					type: 'post',
					data: $.param({id: reminder_id}),
					dataType: 'JSON',
					success: function(response) {
						if(response.success) {
							$this.parents('.panel-els:first').slideUp('slow', function(){
								$(this).remove();
								if(window.location.href.indexOf("task_reminder") > -1) {
									$.indGotIt = 1;
								}
								else{
									$.indGotIt = 2;
								}
								if($('.panel-els', $('.panel-listing')).length <= 0) {
									$('#reminder_modal').modal('hide');
								}
							})
						}
					}
				})
			})

			$('.submit_gotit').on('click', function(event){
				event.preventDefault();
				$.ajax({
					url: $js_config.base_url + 'dashboards/delete_all_reminders',
					type: 'post',
					dataType: 'JSON',
					success: function(response) {
						if(response.success) {
							if(window.location.href.indexOf("task_reminder") > -1) {
						       $.gotItClicked = 2;
						    }
						    else {
						    	$.gotItClicked = 3;
						    }
							$('#reminder_modal').modal('hide');
						}
					}
				})
			})
		})
	</script> 
<?php } ?> 