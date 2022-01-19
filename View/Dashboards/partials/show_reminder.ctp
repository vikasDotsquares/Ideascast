
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
	.no-reminder {
	    padding: 10px;
	    background: #efefef none repeat scroll 0 0;
	    text-align: center;
	    font-weight: 600;
	}
	/***************** tabs *************************/

	.reminder-tabs .nav.nav-tabs.rem-tabs {
	    background-color: transparent;
	    margin-bottom: 5px;
	    border-bottom: 1px solid #67a028;
	}

	.reminder-tabs .nav-tabs {
	    border-bottom: medium none;
	}

	.reminder-tabs .nav.nav-tabs li {
	    margin-bottom: 0;
	}

	.reminder-tabs .nav.nav-tabs.rem-tabs li  {
	    border-right: 2px solid #67a028;
	}

	.reminder-tabs .nav.nav-tabs.rem-tabs li:last-child {
	    border-right: 2px solid transparent;
	}

	.reminder-tabs .nav.nav-tabs.rem-tabs > li.active {
	    background-color: transparent;
	}

	.reminder-tabs .nav-tabs.rem-tabs > li.active > a {
	    color: #cccccc;
	}

	.reminder-tabs .nav-tabs.rem-tabs > li.active > a,
	.reminder-tabs .nav-tabs.rem-tabs > li.active > a:focus,
	.reminder-tabs .nav-tabs.rem-tabs > li.active > a:hover {
	    background-color: transparent !important;
	    border: 1px solid transparent;
	    cursor: default;
	}

	.reminder-tabs .nav-tabs.rem-tabs > li > a {
	    border-radius: 0;
	    font-weight: 600;
	    padding: 2px 10px;
	}

	.reminder-tabs .nav-tabs.rem-tabs > li > a {
	    border: 1px solid #000000;
	    border-radius: 0;
	    color: #7ddf7d;
	    font-weight: 600;
	    padding: 2px 10px;
	}

	.reminder-tabs .nav-tabs.rem-tabs > li > a:hover {
	    text-decoration: none;
	    background-color: transparent !important;
	}

	.reminder-tabs .tab-pane {
	    /*border: 1px solid #ccc;*/
	    padding: 5px 10px;
	}

	.reminder-tabs .nav-tabs > li > a,
	.reminder-tabs .nav-tabs > li > a:focus,
	.reminder-tabs .nav-tabs > li > a:hover {
	    border: 1px solid transparent !important;
	}

	.color_default .reminder-tabs .nav-tabs.rem-tabs > li > a {
	    color: ##00733e !important;
	}

	.color_default .reminder-tabs .nav-tabs.rem-tabs > li > a:hover {
	    color: #67a028 !important;
	}

	.color_default .reminder-tabs .nav-tabs.rem-tabs > li.active > a {
	    color: #bdbdae !important;
	}

	.color_default .reminder-tabs .nav.nav-tabs.rem-tabs li:first-child {
	    border-right: 2px solid #4cae4c;
	}
	/***************** tabs *************************/
	.title-group .title-group-input {
	    padding: 0px !important;
	}
	.title-group .title-group-input a.open-task {
	    display: block;
	    padding: 6px;
	    color: #fff;
	}

	.title-group .title-group-input.cell_progress a.open-task {
	    display: block;
	    padding: 6px;
	    color: #000;
	}


</style>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Task Reminders</h3>
	</div>

<?php
$current_user_id = $this->Session->read('Auth.User.id');
if( isset($reminder_elements) && !empty($reminder_elements) ) { ?>

<?php
	$active = 1;
	if(isset($today_reminder_elements) && !empty($today_reminder_elements)){
		$active = 1;
	}
	else if( (!isset($today_reminder_elements) || empty($today_reminder_elements)) && (isset($upcoming_reminder_elements) && !empty($upcoming_reminder_elements)) ){
		$active = 2;
	}
	else if( (!isset($today_reminder_elements) || empty($today_reminder_elements)) && (!isset($upcoming_reminder_elements) || empty($upcoming_reminder_elements)) && (isset($overdue_reminder_elements) && !empty($overdue_reminder_elements)) ){
		$active = 3;
	}
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list reminder-tabs">

	<ul class="nav nav-tabs rem-tabs">
        <li <?php if($active == 1){ ?>class="active" <?php } ?>>
            <a id="tab_todays" href="#today_reminder" <?php if($active == 1){ ?>class="active" <?php } ?> data-toggle="tab">Today (<?php echo (isset($today_reminder_elements) && !empty($today_reminder_elements)) ? count($today_reminder_elements) : '0' ?>)</a>
        </li>
        <li <?php if($active == 2){ ?>class="active" <?php } ?>>
            <a id="tab_upcoming" href="#upcoming_reminder" <?php if($active == 2){ ?>class="active" <?php } ?> data-toggle="tab">Upcoming (<?php echo (isset($upcoming_reminder_elements) && !empty($upcoming_reminder_elements)) ? count($upcoming_reminder_elements) : '0' ?>)</a>
        </li>
        <li <?php if($active == 3){ ?>class="active" <?php } ?>>
            <a id="tab_overdue" href="#overdue_reminder" <?php if($active == 3){ ?>class="active" <?php } ?> data-toggle="tab">Overdue (<?php echo (isset($overdue_reminder_elements) && !empty($overdue_reminder_elements)) ? count($overdue_reminder_elements) : '0' ?>)</a>
        </li>
    </ul>

    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade <?php if($active == 1){ ?>active in<?php } ?>" id="today_reminder">
			<?php if(isset($today_reminder_elements) && !empty($today_reminder_elements)) { ?>
			<div class="panel-group panel-listing" id="today_accordions" style="margin-bottom: 0px;" >
				<?php foreach( $today_reminder_elements as $key => $val )  {
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
										<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $el['id'], 'admin' => FALSE ), TRUE ); ?>" class="open-task tipText" title="Open Task"><?php echo strip_tags($el['title']); ?></a>
									</div>
									<div class="title-group-addon"  data-parent="#today_accordions"  data-toggle="collapse" data-target="#collapse_data_<?php echo $el['id']; ?>" aria-expanded="false" aria-controls="collapse_data_<?php echo $el['id']; ?>">
										<i class="fas"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="panel-body panel-collapse collapse" id="collapse_data_<?php echo $el['id'] ?>" data-section="<?php echo $el['id'] ?>" >
							<div class="date_constraints_wrappers">

								<div class="form-group clearfix">
									<div class="col-sm-6 nopadding-left">

										<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Task Start Date:</label>
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
											echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]); ?>
									</div>
									<div class="col-sm-6 nopadding-right">
										<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">Task End Date:</label>
											<?php
											$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';
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

								<!-- <div class="form-group clearfix">
								  <a href="#" class="btn btn-success btn-sm pull-right ind-gotit" data-reminder="<?php // echo($val['id']); ?>">Got It</a>
							    </div> -->

							</div>
						</div>
					</div>
			<?php } ?>
			<?php } ?>
			</div>
			<?php }
			else{
			 ?>
			<div class="no-reminder">No reminders for today.</div>
			<?php
			} ?>
		</div>
		<div class="tab-pane fade <?php if($active == 2){ ?>active in<?php } ?>" id="upcoming_reminder">
			<?php if(isset($upcoming_reminder_elements) && !empty($upcoming_reminder_elements)) { ?>
			<div class="panel-group panel-listing" id="upcoming_accordions" style="margin-bottom: 0px;" >
				<?php foreach( $upcoming_reminder_elements as $key => $val )  {
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
										<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $el['id'], 'admin' => FALSE ), TRUE ); ?>" class="open-task tipText" title="Open Task"><?php echo strip_tags($el['title']); ?></a>
									</div>
									<div class="title-group-addon"  data-parent="#upcoming_accordions"  data-toggle="collapse" data-target="#collapse_data_<?php echo $el['id'] ?>" aria-expanded="false" aria-controls="collapse_data_<?php echo $el['id'] ?>">
										<i class="fas"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="panel-body panel-collapse collapse" id="collapse_data_<?php echo $el['id'] ?>" data-section="<?php echo $el['id'] ?>" >
							<div class="date_constraints_wrappers">

								<div class="form-group clearfix">
									<div class="col-sm-6 nopadding-left">

										<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Task Start Date:</label>
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
											echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]); ?>
									</div>
									<div class="col-sm-6 nopadding-right">
										<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">Task End Date:</label>
											<?php
											$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';
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

								<!-- <div class="form-group clearfix">
								  <a href="#" class="btn btn-success btn-sm pull-right ind-gotit" data-reminder="<?php // echo($val['id']); ?>">Got It</a>
							    </div> -->

							</div>
						</div>
					</div>
			<?php } ?>
			<?php } ?>
			</div>
			<?php }
			else{
			?>
				<div class="no-reminder">No upcoming reminders.</div>
			<?php
			} ?>
		</div>
		<div class="tab-pane fade <?php if($active == 3){ ?>active in<?php } ?>" id="overdue_reminder">
			<?php if(isset($overdue_reminder_elements) && !empty($overdue_reminder_elements)) { ?>
			<div class="panel-group panel-listing" id="overdue_accordions" style="margin-bottom: 0px;" >
				<?php foreach( $overdue_reminder_elements as $key => $val )  {
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
										<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $el['id'], 'admin' => FALSE ), TRUE ); ?>" class="open-task tipText" title="Open Task"><?php echo strip_tags($el['title']); ?></a>
									</div>
									<div class="title-group-addon"  data-parent="#overdue_accordions"  data-toggle="collapse" data-target="#collapse_data_<?php echo $el['id'] ?>" aria-expanded="false" aria-controls="collapse_data_<?php echo $el['id'] ?>">
										<i class="fas"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="panel-body panel-collapse collapse" id="collapse_data_<?php echo $el['id'] ?>" data-section="<?php echo $el['id'] ?>" >
							<div class="date_constraints_wrappers">

								<div class="form-group clearfix">
									<div class="col-sm-6 nopadding-left">

										<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Task Start Date:</label>
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
											echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]); ?>
									</div>
									<div class="col-sm-6 nopadding-right">
										<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">Task End Date:</label>
											<?php
											$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';
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

								<!-- <div class="form-group clearfix">
								  <a href="#" class="btn btn-success btn-sm pull-right ind-gotit" data-reminder="<?php // echo($val['id']); ?>">Got It</a>
							    </div> -->

							</div>
						</div>
					</div>
			<?php } ?>
			<?php } ?>
			</div>
			<?php }
			else{
				?>
			<div class="no-reminder">No overdue reminders.</div>
			<?php
			} ?>
		</div>
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