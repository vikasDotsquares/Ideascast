<style type="text/css">
	.avail_section {
		float: left !important;
	}
	.fa.fa-calendar-check-o{
		font-weight: 900;
		font-family: "Font Awesome 5 Free" !important;
	}

</style>
<?php

if( isset($avail_data) && !empty($avail_data) ){
	?>
	<h5 class="abs_text"><strong>Absences:</strong></h5>
	<?php
	foreach($avail_data as $data){
			$all_unavailable = null;
			$stEx = explode(' ', $data['availabilities']['avail_start_date']);
			$enEx = explode(' ', $data['availabilities']['avail_end_date']);
			$start_date = date('Y-m-d', strtotime($stEx[0]));
			$end_date = date('Y-m-d', strtotime($enEx[0]));
			$end = new DateTime($end_date);
			$end = $end->modify('+1 day');
			$daterange = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), $end);
			foreach ($daterange as $date) {
				$all_unavailable[] = $date->format("Y-n-j");
			}
	?>

	<div class="col-xs-12 border avail_section" id="upcominglist<?php echo $data['availabilities']['id'];?>" style="padding: 3px; margin-top:10px; line-height: 29px; ">
		<?php echo $this->Form->input('Availability.id', [ 'type' => 'hidden', 'value' => $data['availabilities']['id'], 'class' => 'avail_data_id' ]); ?>
			<div class="row  not-to-show">
				<div class="col-xs-12 all-day-wrap">
					<div class="col-xs-3 col-sm-2">All Day:</div>
					<div class="col-xs-3 col-sm-3" style="padding-top: 6px;">
						<input type="checkbox" autocomplete="off" name="update_full_day"  class="update-full-day">
						<span class="fa <?php if($data['availabilities']['full_day']){ ?>fa-check text-green<?php }else{ ?>fa-times text-red<?php } ?> right-tick"></span>
					</div>
					<div class="col-xs-5 col-sm-7">
						<div class="btn-group pull-right">
						<?php if(!$past) { ?>
							<span class="avail_update btn btn-xs  tipText" title="Update"><i class="edit-icon"></i></span>
							<span class="avail_discard btn btn-xs tipText" title="Cancel"><i class="inactivered"></i></span>
							<span class="avail_remove btn btn-xs  tipText" title="Delete"><i class="deleteblack"></i></span>
						<?php }else{ ?>
							<span class="avail_remove btn btn-xs  tipText" title="Delete"><i class="deleteblack"></i></span>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
					<div class="btn-group pull-right working-btns">
					<?php if(!$past) { ?>
						<span class="avail_update btn btn-xs tipText" title="Update" data-dates="<?php echo implode(',', $all_unavailable); ?>"><i class="edit-icon"></i></span>
						<span class="avail_discard btn btn-xs tipText" title="Cancel"><i class="inactivered"></i></span>
						<span class="avail_remove btn btn-xs tipText" title="Delete"><i class="deleteblack"></i></span>
					<?php } ?>
					</div>
				</div>
			</div>

			<div class="calendar-row">
				<div class="col-xs-3 col-sm-2">From:<?php
					$stEx = explode(' ', $data['availabilities']['avail_start_date']);
					$enEx = explode(' ', $data['availabilities']['avail_end_date']);
					$inStVal = ($stEx[1] == '00:00:00') ? date('Y-m-d',strtotime($stEx[0])).' 12:00 AM' : date('Y-m-d h:i A',strtotime($data['availabilities']['avail_start_date']));
						echo $this->Form->input('Availability.avail_start_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input start-calendar calendar-input', 'id' => '', 'style' => 'opacity: 0; width:0px; height:0px;', 'value'=> $inStVal]);
					?>
				</div>
				<div class="col-xs-2 col-sm-1">

					<i class="fa fa-calendar calendar-edit" data-original-title="Start Date/Time" aria-hidden="true" <?php /*if($data['availabilities']['full_day']){ ?>style="display: none;"<?php }*/ ?>></i>
				</div>
				<div class="col-xs-7 col-sm-9 show-date-time">
					<?php echo ($stEx[1] == '00:00:00') ? date('d, M Y',strtotime($stEx[0])) : date('d, M Y h:i A',strtotime($data['availabilities']['avail_start_date']));?>
				</div>
			</div>
			<div class="calendar-row">
				<div class="col-xs-3 col-sm-2">To:<?php
				$inEnVal = ($enEx[1] == '00:00:00') ? date('Y-m-d',strtotime($enEx[0])).' 12:00 AM' : date('Y-m-d h:i A',strtotime($data['availabilities']['avail_end_date']));
						echo $this->Form->input('Availability.avail_end_date', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'title-group-input end-calendar calendar-input', 'id' => '', 'style' => 'opacity: 0; width:0px; height:0px;', 'value'=> $inEnVal ]);
					?>
				</div>
				<div class="col-xs-2 col-sm-1">
					<i class="fa fa-calendar calendar-edit" data-original-title="End Date/Time" aria-hidden="true" <?php /*if($data['availabilities']['full_day']){ ?>style="display: none;"<?php }*/ ?>></i>
				</div>
				<div class="col-xs-7 col-sm-9 show-date-time"><?php echo ($enEx[1] == '00:00:00' ) ? date('d, M Y',strtotime($enEx[0])) : date('d, M Y h:i A',strtotime($data['availabilities']['avail_end_date']));?></div>
			</div>

			<div class="">
				<div class="col-xs-12 col-sm-2">Reason:</div>
				<div class="col-xs-12 col-sm-10"><span class="reason_txt"><?php echo !empty($data['availabilities']['avail_reason']) ? htmlentities($data['availabilities']['avail_reason'],ENT_QUOTES, "UTF-8") : 'N/A';?></span>
				<?php
					echo $this->Form->input('Availability.avail_reason', [ 'type' => 'text', 'label' => false, 'div' => false,  'required' => false, 'class' => 'title-group-input showAvailReason form-control create-avail_reason', 'value'=> $data['availabilities']['avail_reason'] ] );
				?>
				<span class="error-message error text-danger"></span>
				</div>
			</div>
			<span class="error-message error text-danger date-error"></span>
	</div>
<?php }
} else{
	?>
	<h4 class="no-avail-msg">NO ABSENCES</h4>
	<?php
}
?>
<script type="text/javascript">
	$(function(){

		$(".avail_remove").click(function(event) {
			event.preventDefault();
			$.availability_save = true;
			var $parentWrapper = $(this).parents('.parent-wrapper:first'),
				$parentDivId = $(this).parents('.avail_section:first'),
				avail_data_id = $parentDivId.find('.avail_data_id').val(),
				data = {id: avail_data_id};

			$.ajax({
				url: $js_config.base_url + 'settings/remove_availability',
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(response) {
					$.availability_triggered = true;
					if(response.success) {
						$.available_updated = true;
						$('#avail_clear').trigger('click')
						$('#activeavail span,#upcomingavail span').html('<i class="fa fa-refresh fa-spin"></i>')
						// remove parent section if query executed successfully
						$parentDivId.slideUp(300, function(){
							$(this).remove();
							// if main wrpper doesn't have any data than show no record message
							if($parentWrapper.find('.avail_section').length <= 0) {
								$parentWrapper.html('<h4 class="no-avail-msg">No ABSENCES</h4>');
							}
							// update tab counters
							$('#activeavail span').html('').text(''+$('.current_wrapper .avail_section').length+'');
							$('#upcomingavail span').html('').text(''+$('.future_wrapper .avail_section').length+'');
						})
					}
				}
			})
		});

	    var dynamic_calendar = $('.full-calendar-input').datepicker({
	        // minDate: '<?php //echo date("Y-m-d"); ?>',
	        dateFormat: 'yy-mm-dd',
	        onClose: function(selected, inst){
	            var $input = inst.input,
	                $parent = $input.parents('.avail_section.edit:first'),
	                $otherInput = ($input.hasClass('full-start-calendar')) ? $parent.find('.full-end-calendar') : $parent.find('.full-start-calendar');
	            if(selected) {
	                if($input.hasClass('full-start-calendar')) {
	                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
	                        if(new Date(selected) > new Date($otherInput.val())) {
	                            $otherInput.datepicker('setDate', selected);
	                            $otherInput.parents('.avail_section.edit:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'))
	                        }
	                    }
	                    $otherInput.datepicker("option", "minDate", $input.val());
	                }
	                else{
	                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
	                        if(new Date(selected) < new Date($otherInput.val())) {
	                            $otherInput.datepicker('setDate', selected);
	                            $otherInput.parents('.avail_section.edit:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'))
	                        }
	                    }
	                }
	            }
	        },
	        onSelect: function(selected, inst) {
	        	var $input = inst.input
	            var $parent = $input.parents('.calendar-row:first');
	            if(selected) {
	                var correctFormat = moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY');
	                $parent.find(".show-date-time").text(correctFormat);
	             }
	        }
	    });

	    $.disableDates = [];

	    $.unavailable_update = function(date) {
	        dmy = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
	        if ($.inArray(dmy, $.disableDates) == -1) {
	            return [true, ""];
	        } else {
	            return [false, "", "Unavailable Set"];
	        }
	    }

	    $.get_timepicker = function(dates) {
	    	var currentDates = dates;
	    	$.disableDates = $.unavailableDates;

	    	if(currentDates.indexOf(',') != -1){
            	currentDates = currentDates.split(',');
	    	}
	    	else{
	    		currentDates = [dates];
	    	}

            $.each(currentDates, function(i, ele) {
                $.disableDates = jQuery.grep($.disableDates, function(value) {
                  return value != ele;
                });
            });

	    	return {
			        // minDate: '<?php //echo date("Y-m-d"); ?>',
			        dateFormat: 'yy-mm-dd',
			        timeFormat: 'h:mm TT',
			        format : 'g:i A',
			        ShowButtonPanel: true,
			        showTimePicker: true,
			        showSecond:false,
			        showMinute:true,
			        showMillisec:false,
			        showMicrosec:false,
			        showTimezone:false,
			        stepMinute: 02,
			        ampm: true,
			        hourMax: 23,
			        closeText : "Set",
			        beforeShowDay: $.unavailable_update,
			        beforeShow: function(input, inst) {
			            var divPane = inst.dpDiv;
			            //cleanDatepicker();
			            setTimeout(function(){
			                $('.ui-datepicker').css('z-index', 99999999999999);
			                divPane.css({'width': '17em'})
			            }, 0);

			            var $input = inst.input,
			                $parent = $input.parents('.avail_section.edit:first'),
			                $otherInput = ($input.hasClass('start-calendar')) ? $parent.find('.end-calendar') : $parent.find('.start-calendar');

			            if($input.hasClass('start-calendar')) {
			                var sDate = $otherInput.val().split(' ');
			                // $input.datetimepicker("option", "maxDate", sDate[0]);
			            }
			            else{
			                var sDate = $otherInput.val().split(' ');
			                $input.datetimepicker("option", "minDate", sDate[0]);
			            }
			        },
			        onClose: function(selected, inst){
			            var $input = inst.input,
			                $parent = $input.parents('.avail_section.edit:first'),
			                $otherInput = ($input.hasClass('start-calendar')) ? $parent.find('.end-calendar') : $parent.find('.start-calendar');

			            if(selected) {
			                var sDate = selected.split(' ');
			                if($input.hasClass('start-calendar')) {
			                	var next = $.findNextDisabledDateWithinMonth(selected, $.disableDates);
			                	if(next) {
			                		$otherInput.datetimepicker("option", "maxDate", next);
			                	}
			                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
			                        if(new Date(selected) > new Date($otherInput.val())) {
			                            $otherInput.datetimepicker('setDate', selected);
			                            $otherInput.parents('.calendar-row:first').find('.show-date-time').text($.format_date(selected))
			                            console.log('111',$.format_date(selected))
			                        }
			                    }
			                    $otherInput.datetimepicker("option", "minDate", sDate[0]);
			                }
			                else{
			                    var prev = $.findPrevDisabledDateWithinMonth(selected, $.disableDates);
			                	if(prev) {
			                		$otherInput.datetimepicker("option", "minDate", prev);
			                	}
			                    if($otherInput.val() != '' && $otherInput.val() != undefined) {
			                        if(new Date(selected) < new Date($otherInput.val())) {
			                            $otherInput.datetimepicker('setDate', selected);
			                            $otherInput.parents('.calendar-row:first').find('.show-date-time').text(moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMMM YYYY hh:mma'))
			                            console.log('222',moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMMM YYYY hh:mma'))
			                        }
			                    }
			                    $otherInput.datetimepicker("option", "maxDate", sDate[0]);
			                }
			            }
			            if($input ){
							var $parent = $input.parents('.create-row:first');
							if(selected) {
								var correctFormat = moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMMM YYYY hh:mma');
								$parent.find(".show-date-time").text($.format_date(selected));
								console.log('333',$.format_date(selected))
							}
						}
			        },
			        onSelect: function(selected, inst){
			            $(".noEndDate").show();
			            var $parent = $(this).parents('.calendar-row:first');
			            if(selected) {
			                var correctFormat = moment(selected, 'YYYY-MM-DD HH:mm a').format('DD, MMMM YYYY hh:mma');
			                $parent.find(".show-date-time").text($.format_date(selected));
			                console.log('444',$.format_date(selected))
			             }
			        }
			    }
	    }


	    $('body').delegate('.edit .calendar-edit', 'click', function(e) {
	        var $parent = $(this).parents('.calendar-row:first');

	        var $working_btns = $(this).parents('.avail_section:first').find('.working-btns').find('.avail_update');
	        var working_btns_data = $working_btns.data('dates');
	        var t = $.get_timepicker(working_btns_data);

	        $parent.find('.calendar-input').datetimepicker(t).datepicker('show');
	        // $parent.find('.calendar-input').datetimepicker(dynamic_timecalendar).datepicker('show');
	    });

	    $('body').delegate('.edit .full-calendar-edit', 'click', function(e) {
	        var $parent = $(this).parents('.calendar-row:first');
	        $parent.find('.full-calendar-input').datepicker(dynamic_calendar).datepicker('show');
	    });

	    $('body').delegate('.avail_section.edit .full-calendar-edit, .avail_section.edit .calendar-edit', 'mouseenter', function(event) {
            $(this).tooltip({ container: 'body', placement: 'auto' })
            $(this).tooltip('show')
        })
        $('body').delegate('.avail_section.edit .full-calendar-edit, .avail_section.edit .calendar-edit', 'mouseleave', function(event) {
            $(this).tooltip('hide')
        })
	})
</script>