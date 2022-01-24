<style>
	.fa.fa-calendar-check-o{
		font-weight: 900;
		font-family: "Font Awesome 5 Free" !important;
	}
	.create-clear-btn .btn{
    padding: 7px 10px;
    font-size: 12px;
	}

</style>
<div class="col-sm-12 border avail_section create-availability" style="">
	<div class="not-to-show">
		<div class="col-xs-3 col-sm-2" style="font-weight: 600;">All Day:</div>
		<div class="col-xs-8 col-sm-10">
			<input type="checkbox" autocomplete="off" name="create_full_day" class="chk-full-day">
		</div>
	</div>
	<div class="wrap-non-full-day dates-wrapper">
		<div class=" create-row">
			<div class="col-xs-3 col-sm-2" style="font-weight: 600;">From:<?php
				echo $this->Form->input('Availability.avail_start_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input non-start-cal non-create-cal', 'id' => 'non_create_start_cal', 'style' => 'opacity: 0; width:0px; height:0px;' ]);
			?></div>
			<div class="col-xs-2 col-sm-1">
				<i class="fa fa-calendar non-cal-open tipText" title="Start Date/Time" aria-hidden="true"></i>
			</div>
			<div class="col-xs-7 col-sm-9 setStartAvailDate show-date-time">Day, Month Year: Time</div>
		</div>
		<div class="create-row">
			<div class="col-xs-3 col-sm-2" style="font-weight: 600;">To:<?php
				echo $this->Form->input('Availability.avail_end_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input non-end-cal non-create-cal', 'id' => 'non_create_end_cal', 'style' => 'opacity: 0; width:0px; height:0px;' ]);
			?></div>
			<div class="col-xs-2 col-sm-1">
				<i class="fa fa-calendar non-cal-open tipText" title="End Date/Time" aria-hidden="true"></i></div>
			<div class="col-xs-7 col-sm-9 setEndAvailDate show-date-time" >Day, Month Year: Time</div>
		</div>
	</div>

	<div class="wrap-full-day dates-wrapper" style="display: none;">
		<div class=" create-row">
			<div class="col-xs-3 col-sm-2" style="font-weight: 600;">From:<?php
				echo $this->Form->input('Availability.avail_start_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input start-cal create-cal', 'id' => 'create_start_cal', 'style' => 'opacity: 0; width:0px; height:0px;' ]);
			?></div>
			<div class="col-xs-2 col-sm-1">
				<i class="fa fa-calendar cal-open tipText" title="Start Date" aria-hidden="true"></i>
			</div>
			<div class="col-xs-7 col-sm-9 setStartAvailDate show-date-time">Day, Month Year</div>
		</div>
		<div class="create-row">
			<div class="col-xs-3 col-sm-2" style="font-weight: 600;">To:<?php
				echo $this->Form->input('Availability.avail_end_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input end-cal create-cal', 'id' => 'create_end_cal', 'style' => 'opacity: 0; width:0px; height:0px;' ]);
			?></div>
			<div class="col-xs-2 col-sm-1">
				<i class="fa fa-calendar cal-open tipText" title="End Date" aria-hidden="true"></i></div>
			<div class="col-xs-7 col-sm-9 setEndAvailDate show-date-time" >Day, Month Year</div>
		</div>
	</div>

	<div class="reason-wrap" >
		<div class="col-sm-12" style="font-weight: 600;">Reason (optional):</div>
		<div class="col-sm-12">
		<?php
			echo $this->Form->input('Availability.avail_reason', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'class' => 'form-control create-avail_reason',  'placeholder' => '50 Chars' ]);
		?>
		<span class="error-message error text-danger"></span>
		</div>
	</div>
	<div class="">
		<div class="col-sm-12 text-right create-clear-btn" style="padding: 12px 15px 7px; float: right">
			<span class="error-message error text-danger date-error text-center pull-left"></span>
			<button class="btn btn-success pull-rights" id="avail_save_page">Create</button>
			<button class="btn btn-danger pull-rights" id="avail_clear">Clear</button>
			<!-- <button class="btn btn-danger btn-xs">Cancel</button> -->
		</div>
	</div>
</div>
<?php echo $this->Html->script('projects/unavailable', array('inline' => true)); ?>
<script type="text/javascript">
	$(function(){


	$.format_date = function(selected_date) {
		var sDate = selected_date.split(' ');
		var formated_date = '';
		if(sDate[1] == '09:00' && sDate[2] == 'AM') {
			formated_date = moment(selected_date, 'YYYY-MM-DD').format('DD, MMM YYYY'); // full day
		}
		else {
			formated_date = moment(selected_date, 'YYYY-MM-DD HH:mm a').format('DD, MMM YYYY hh:mm A'); // partial day
		}
		return formated_date;
	}



	var start = '<?php echo date("Y-m-d"); ?>';
	var startDate = '<?php echo date("Y-m-d"); ?>';

		var dp_picker = $('.non-create-cal').datetimepicker({
		// minDate: start,
		// minTime: '9:00',
		hour: 9,
		minute: 0,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:mm TT',
		format : 'g:i A',
		ShowButtonPanel: true,
		showTimePicker: false,
		showSecond:false,
		showMinute:true,
		showMillisec:false,
		showMicrosec:false,
		showTimezone:false,
		stepMinute: 2,
		ampm: true,
		hourMax: 24,
		closeText : "Set",
		beforeShowDay: $.unavailable,
		beforeShow: function(input, inst) {
			var divPane = inst.dpDiv;
			//cleanDatepicker();
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
				divPane.css({'width': '17em'})
			}, 0);

		},
		onClose: function(selected, inst){
			var $input = inst.input,
				$parent = $input.parents('.dates-wrapper:first'),
				$otherInput = ($input.hasClass('non-start-cal')) ? $parent.find('.non-end-cal') : $parent.find('.non-start-cal');

			if(selected) {
                var sDate = selected.split(' ');
                if($input.hasClass('non-start-cal')) {
                	var next = $.findNextDisabledDateWithinMonth(selected, $.unavailableDates);
                	if(next) {
                		$otherInput.datetimepicker("option", "maxDate", next);
                	}
                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
                        if(new Date(selected) > new Date($otherInput.val())){
                            $otherInput.datetimepicker('setDate', selected);
                            // $otherInput.parents('.dates-wrapper:first').find('.show-date-time').text($.format_date(selected))
                            $otherInput.parents('.dates-wrapper:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMM YYYY hh:mm A'))
                        }
                    }
                	$otherInput.datetimepicker("option", "minDate", sDate[0]);
                }
                else{
                	var prev = $.findPrevDisabledDateWithinMonth(selected, $.unavailableDates);
                	if(prev) {
                		$otherInput.datetimepicker("option", "minDate", prev);
                	}
                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
                        if(new Date(selected) < new Date($otherInput.val())) {
                            $otherInput.datetimepicker('setDate', selected);
                            $otherInput.parents('.dates-wrapper:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMM YYYY hh:mm A'))
                        }
                    }
                    $otherInput.datetimepicker("option", "maxDate", sDate[0]);
                }
            }
			if($input){
				var $parent = $input.parents('.create-row:first');
				if(selected) {
					var correctFormat = moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMM YYYY hh:mm A');
					$parent.find(".show-date-time").text($.format_date(selected));
					// $parent.find(".show-date-time").text(correctFormat);
				}
			}
		},
		onSelect: function(selected, inst){
			var $input = inst.input;
			if($input ){
				var $parent = $input.parents('.create-row:first');
				if(selected) {
					var correctFormat = moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMM YYYY hh:mm A');
					$parent.find(".show-date-time").text($.format_date(selected));
					// $parent.find(".show-date-time").text(correctFormat);
				}
			}
		}
	});

	var d_pick = $('.create-cal').datepicker({
            // minDate: startDate,
            dateFormat: 'yy-mm-dd',
			onClose: function(selected, inst){
				var $input = inst.input,
					$parent = $input.parents('.dates-wrapper:first'),
					$otherInput = ($input.hasClass('start-cal')) ? $parent.find('.end-cal') : $parent.find('.start-cal');
				if(selected) {
	                if($input.hasClass('start-cal')) {
	                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
	                        if(new Date(selected) > new Date($otherInput.val())) {
	                            $otherInput.datepicker('setDate', selected);
	                            $otherInput.parents('.dates-wrapper:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD').format('DD, MMMM YYYY'))
	                        }
	                    }
	                	$otherInput.datepicker("option", "minDate", $input.val());
	                }
	                else{
	                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
	                        if(new Date(selected) < new Date($otherInput.val())) {
	                            $otherInput.datepicker('setDate', selected);
	                            $otherInput.parents('.dates-wrapper:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD').format('DD, MMMM YYYY'))
	                        }
	                    }
	                }
	            }
			},
			onSelect: function(selected, inst){
				var $parent = $(this).parents('.create-row:first');
				if(selected) {
					var correctFormat = moment(selected, 'YYYY-MM-DD').format('DD, MMMM YYYY');
					$parent.find(".show-date-time").text(correctFormat);
				 }
			}
        });

	$('body').delegate('.non-cal-open', 'click', function(e) {
		var $parent = $(this).parents('.create-row:first');
		$parent.find('.non-create-cal').datetimepicker(dp_picker).datepicker('show');
	});

	$('body').delegate('.cal-open', 'click', function(e) {
		var $parent = $(this).parents('.create-row:first');
		$parent.find('.create-cal').datepicker(d_pick).datepicker('show');
	});


	$('body').delegate('.chk-full-day', 'change', function(e) {
		if($(this).prop('checked')) {
			$('.wrap-full-day').show();
			$('.wrap-non-full-day').hide();
		}
		else{
			$('.wrap-full-day').hide();
			$('.wrap-non-full-day').show();
		}
	});



	})
</script>