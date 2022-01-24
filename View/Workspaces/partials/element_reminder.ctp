<?php
echo $this->Html->css('projects/datetime/datetime-addon');
echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));
echo $this->Html->script('projects/plugins/datetime/datetime-addon', array('inline' => true));
echo $this->Html->script('projects/plugins/datetime/datetime-addon-slider', array('inline' => true));
//echo $this->Html->css('projects/datetime/jquery-ui.theme');
?>
<style type="text/css">
 	.element-title {
 		display: block;
 	}
 	.element-start-date {
 		float: left; width: 48%;
 	}
 	.element-end-date {
 		float: right; width: 48%;
 	}
 	.date-inner {
 		padding: 6px 5px; background-color: #e4e4e4; border: 1px solid #ccc;
 	}
 	.reminder-date {
	    background-color: #e4e4e4;
    	border: 1px solid #ccc;
 	}

	.ui-datepicker .ui-datepicker-buttonpane button{
	 background: #5f9323 none repeat scroll 0 0;
     color: #fff;
	 font-weight:normal;
	}
	.ui-timepicker-div dl dd {
    margin: 0 10px 10px 25%;
	}
	.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input{
		margin-left: -7px;
		width: 104%;
	}
	.ui-timepicker-div {

		margin: 10px 0 0;
	}
	.del_confirmation {
		display: none;
	}
	.ui_tpicker_time_label,.ui_tpicker_hour_label{ font-size:14px;

	}
	.ui_tpicker_time_label { line-height : 1.8; }
	.ui_tpicker_hour_label { line-height : 1; }
	.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input{ font-size : 13px;}
	.element-title-text {
		display: block;
	}
 </style>

<?php
$el = $element['Element'];
	echo $this->Form->create('Element', array('url' => array('controller' => 'dashboards', 'action' => 'element_reminder', $el['id']), 'class' => 'form-bordered', 'id' => 'modelFormAddElementReminder' ));
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Set Task Reminder</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">

		<?php
		echo $this->Form->input('Reminder.id', [ 'type' => 'hidden' ] );
		echo $this->Form->input('Reminder.element_id', [ 'type' => 'hidden', 'value' => $el['id'] ] );
		echo $this->Form->input('Reminder.project_id', [ 'type' => 'hidden', 'value' => element_project($el['id']) ] );
		echo $this->Form->input('Reminder.user_id', [ 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id') ] );
		?>
		<?php $element_status = element_status($el['id']); ?>
		<div class="form-group">
			<label class=" " for="title">Title:</label>
			<div class="element-title-text" >
				<div class="cell_<?php echo $element_status; ?>" style="padding: 6px 5px;">
					<?php echo strip_tags($el['title']); ?>
				</div>
			</div>
		</div>

		<div class="form-group clearfix">
			<div class="element-start-date" style="" >
				<label class=" " for="title">Task Start Date:</label>
				<div class="" style="display: block;">
					<div class="date-inner" style="">
						<?php echo (isset($el['start_date']) && !empty($el['start_date'])) ? $this->TaskCenter->_displayDate_new($el['start_date'],'d M Y') : ''; ?>
					</div>
				</div>
			</div>
			<div class="element-end-date" style=" " >
				<label class=" " for="title">Task End Date:</label>
				<div class="" style="display: block;">
					<div class="date-inner" style="">
						<?php echo (isset($el['end_date']) && !empty($el['end_date'])) ? $this->TaskCenter->_displayDate_new($el['end_date'],'d M Y') : ''; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class=" " for="title">Reminder:</label>
			<div class="title-group">
				<?php
				$remind_date = (isset($this->data['Reminder']['reminder_date']) && !empty($this->data['Reminder']['reminder_date'])) ? date('d M Y g:i A',strtotime($this->data['Reminder']['reminder_date'])) : '';
				echo $this->Form->input('Reminder.reminder_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input', 'id' => 'reminder_date', 'style' => 'background-color: #f6f6f6; border: 1px solid #ccc;', 'value' => $remind_date]); ?>
				<div class="title-group-addon calendar-open">
					<i class="fa fa-calendar"></i>
				</div>
			</div>
			<span class="error-message text-danger"></span>
		</div>

		<div class="form-group">
			<label class=" " for="title">Accompanying Comments (optional):</label>
			<?php echo $this->Form->input('Reminder.comments',['type'=>'textarea','label'=>false,'rows'=>"5",'style'=>"resize: vertical;",'class'=>"form-control", 'placeholder' => 'max chars allowed 250']); ?>
				<span class="chars_left" style="font-size: 11px; color: #c00;"></span>

		</div>

	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
	<?php if(isset($this->data) && !empty($this->data)) { ?>
		<span class="del_confirmation">
	        <a href="" class="btn btn-xs btn-success del_success tipText" style="min-width: unset;" data-projectid="<?php echo element_project($el['id']);?>" data-id="<?php echo ($this->data['Reminder']['id']); ?>" title="Confirm"><i class="fa fa-check"></i></a>
	        <a href="" class="btn btn-xs btn-danger del_cancel tipText" style="min-width: unset;" title="Cancel"><i class="fa fa-times"></i></a>
	    </span>
		 <button type="button" data-projectid="<?php echo element_project($el['id']);?>" data-id="<?php echo ($this->data['Reminder']['id']); ?>" class="btn btn-danger trash_task_reminder"> Delete</button>
	<?php } 
			if( $this->data['Reminder']['id'] && ( (isset($el['end_date']) && !empty($el['end_date'])) &&  (date('Y-m-d',strtotime($el['end_date'])) < date('Y-m-d') ) ) ){
		?>
			<button type="button" data-projectid="<?php echo element_project($el['id']);?>"  class="btn btn-success disabled">Save</button>
		<?php } else { ?>
			<button type="button" data-projectid="<?php echo element_project($el['id']);?>"  class="btn btn-success submit_reminder">Save</button>
		<?php } ?>	 
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>

	<script type="text/javascript" >
	$(function() {

		$("#ReminderComments").on('keyup', function(){
			var characters = 250
			if($(this).val().length > characters){
				$(this).val($(this).val().substr(0, characters));
			}
			$(this).parent().find('.chars_left:first').text('Chars: '+characters +", "+$(this).val().length + ' characters entered.')
		})

		$('#modal_reminder').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});

	//	var start = '<?php // echo date("d-m-Y", strtotime($el['start_date'])); ?>';
		var start = '<?php echo date("d M Y"); ?>';
        var end = '<?php echo date("d M Y", strtotime($el['end_date'])); ?>';
        //var current_time = <?php // echo date('H')+1; ?>;
		var reminder_date = $('#reminder_date').datetimepicker({
			minDate: start,
            maxDate: end,
			dateFormat: 'dd M yy',
			timeFormat: 'h:mm TT',
			format : 'g:i A',
			ShowButtonPanel: false,
			showTimePicker: false,
			showSecond:false,
			showMinute:false,
			showMillisec:false,
			showMicrosec:false,
			showTimezone:false,
			ampm: true,
			hour: 6,
			//hourMin: <?php //echo date('H')-5; ?>,
			hourMax: 23,
			 closeText : "Set",
			beforeShow: function(selected, inst) {
				var divPane = inst.dpDiv;

				setTimeout(function(){
		            $('.ui-datepicker').css('z-index', 99999999999999);
		        }, 0);

			}
		});

		$('#reminder_date').datetimepicker({
			 onSelectDate:function(ct,$i){ alert(0);
			  alert(ct.dateFormat('d/m/Y'))
			}
		})

		$('body').delegate('.calendar-open',  'click', function(e){
			$('#reminder_date').datetimepicker('show')
		})
		$('body').delegate('#reminder_date',  'change', function(e){
			if($(this).val().length > 0) {
				$(this).parents('.form-group:first').find('span.error-message.text-danger').text("");
			}
		})

		$('body').delegate('.trash_task_reminder',  'click', function(e){
			e.preventDefault()
			var  $this = $(this);
			$(this).fadeOut('slow', function(){
				$this.parent().find('.del_confirmation').fadeIn('slow');
			})
		})
		$('body').delegate('.del_cancel',  'click', function(e){
			e.preventDefault();
			var  $this = $(this);
			$('.del_confirmation').fadeOut('slow', function(){
				$('.trash_task_reminder').fadeIn('slow');
			})
		})

		$('body').delegate('.del_success',  'click', function(e){
			e.preventDefault();
			var data = $(this).data(),
				id = data.id;

			var project_id = $(this).data('projectid');
			var el_id = $('#ReminderElementId').val();

			if(id) {
				$.ajax({
					url: $js_config.base_url + 'dashboards/trash_task_reminder',
					type:'POST',
					data: $.param({'id': id}),
					dataType: 'json',
					success: function( response, status, jxhr ) {

						if( response.success ) {
							$.update_reminder = el_id;
							$("#modal_reminder").modal('hide');
						}

					}
				});
			}
		})


		$('.submit_reminder').on( "click", function(e){
			e.preventDefault();

			var project_id = $(this).data('projectid');
			var $this = $(this),
				$form = $('form#modelFormAddElementReminder'),
				date_entered = $('#reminder_date').val(),
				runAjax = true;

			var el_id = $('#ReminderElementId').val();

			if(date_entered.length <= 0) {
				$('#reminder_date').parents('.form-group:first').find('span.error-message.text-danger').text("Please select a date.");
				return;
			}


			if( runAjax ) {
				runAjax = false;
				$.ajax({
					url: $form.attr('action'),
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					beforeSend: function( response, status, jxhr ) {
						// Add a spinner in button html just after ajax starts
						$this.html('<i class="fa fa-spinner fa-pulse"></i>')
					},
					success: function( response, status, jxhr ) {

						$this.html('Save');
						// REMOVE ALL ERROR SPAN
						$('#reminder_date').parents('.form-group:first').find('span.error-message.text-danger').text("");

						if( response.success ) {
							if(response.content){
								// send web notification
								$.socket.emit('socket:notification', response.content.socket, function(userdata){
									$.create_notification(userdata);
								});
							}
							$.update_reminder = el_id;
							$("#modal_reminder").modal('hide');

						}

					}
				});
				// end ajax

			}
		})

	})
	</script>
