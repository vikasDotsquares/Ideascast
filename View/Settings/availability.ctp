<?php

echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');

$logged_user = $this->Session->read('Auth.User.id');
$currentavail =  $this->ViewModel->currentAvaiability($logged_user);
$upcoming =  $this->ViewModel->upcomingAvaiability($logged_user);
$pastAvail =  $this->ViewModel->pastAvaiability($logged_user);

?>

<style>
	.fa.fa-calendar-check-o{
		font-weight: 900;
		font-family: "Font Awesome 5 Free" !important;
	}
</style>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel" style="display: inline"> Absences </h3>
	</div>

	<div class="modal-body allpopuptabs">
	        <ul class="nav nav-tabs">
	            <li class="active">
	                <a id="createavail" class="active" href="#create_avail" data-toggle="tab" aria-expanded="true">Create</a>
	            </li>
	            <li>
	                <a id="activeavail" class="active" href="#active_avail" data-toggle="tab" aria-expanded="true">Current (<span><?php echo ( isset($currentavail) && !empty($currentavail) ) ? count($currentavail) : 0;?></span>)</a>
	            </li>
	            <li>
	                <a id="upcomingavail" href="#upcoming_avail" data-toggle="tab" aria-expanded="false">Upcoming (<span><?php echo ( isset($upcoming) && !empty($upcoming) ) ? count($upcoming) : 0;?></span>)</a>
	            </li>
	            <li>
	                <a id="pastavail" href="#past_avail" data-toggle="tab" aria-expanded="false">Past (<span><?php echo ( isset($pastAvail) && !empty($pastAvail) ) ? count($pastAvail) : 0;?></span>)</a>
	            </li>
	        </ul>

	    <div id="elementTabContent" class="tab-content">
	        <div class="tab-pane fade active in" id="create_avail">
	        	<h5 style="margin: 10px 10px 20px 0;"><strong>Absent:</strong></h5>
                <div class="row">
                    <div class="col-xs-12 create_avail_form">
						<?php echo $this->element('../Settings/partials/create_avail_form'); ?>
					</div>
					<!-- <div class="col-xs-12 padding current_wrapper parent-wrapper"  data-parent="current_availability">
						<?php
						//$currentavail =  $this->ViewModel->currentAvaiability($logged_user);
						//echo $this->element('../Settings/partials/available_data', ['avail_data' => $currentavail, 'past' => false, 'type' => 'current']);
							?>
					</div> -->
                </div>
		    </div>

	        <div class="tab-pane fade" id="active_avail">
	        	<div class="row">
                    <div class="col-xs-12 padding current_wrapper parent-wrapper" style="" data-parent="current_availability">
						<?php

						echo $this->element('../Settings/partials/available_data', ['avail_data' => $currentavail, 'past' => false, 'type' => 'current']);
							?>
					</div>
                </div>
		    </div>

	        <div class="tab-pane fade" id="upcoming_avail">
                <div class="row">
                    <div class="col-xs-12 padding future_wrapper parent-wrapper" style="" data-parent="future_availability">
						<?php
						echo $this->element('../Settings/partials/available_data', ['avail_data' => $upcoming, 'past' => false, 'type' => 'upcoming']);
						?>
					</div>
                </div>
	        </div>

	        <div class="tab-pane fade" id="past_avail">
                <div class="row">
                    <div class="col-xs-12 padding past_wrapper parent-wrapper" style="">
						<?php
						echo $this->element('../Settings/partials/available_data', ['avail_data' => $pastAvail, 'past' => true, 'type' => 'past']);
						?>
					</div>
                </div>
	        </div>
	    </div>
	</div>

	<div class="modal-footer">
		<!-- <h5 id="avail_message_box" style="float: left; display: inline-block; color: #d73925;"></h5>
		<button class="btn btn-success" id="avail_save_page" >Save</button> -->
		<button class="btn btn-danger" id="close_modal" data-dismiss="modal">Close</button>
	</div>
<style type="text/css">
	.not-to-show {
		display: none;
	}
	.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
	    font-size: 1em !important;
	}
	.ui_tpicker_time_input {
		font-size: 13px !important;
	}
	.ui-timepicker-div dl dt {
	    margin-left: 10% !important;
	    font-size: 14px !important;
	}
	.ui_tpicker_time_label {
	    margin-top: 2px;
	}
	.ui_tpicker_hour_label {
	    margin-top: -3px;
	}
	.ui_tpicker_minute_label {
	    margin-top: -3px;
	}
	.create-availability {
		padding: 7px;
		line-height: 21px;
		background-color: #f7f7f9;
		-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
		box-shadow: 0 1px 1px rgba(0,0,0,.05);
	}
	/*.current_wrapper.parent-wrapper {
		height: 320px;
		overflow-y: auto;
	}*/
	.parent-wrapper {
		height: 300px;
		overflow-y: auto;
		padding: 3px 15px 5px 15px !important;
	}
	.avail_section .reason_txt {
	    display:block;
	    float: left;
	}
	.avail_section .showAvailReason {
	    display:none;
	}

	.showStartCalendar, .showEndCalendar, .calendar-openup {
		cursor: pointer;
	}
	/*.avail_section.edit .avail_update {
	    background-color: #67a028;
    	border-color: #5F9323;
	}*/
	.avail_section .avail_update i.fa::before {
	    content: "\f303";
	}
	.avail_section.edit .avail_update i.fa::before {
	    content: "\f00c";
	}
	.avail_section .avail_discard {
	    display: none;
	}
	.avail_section.edit .avail_discard {
	    display: block;
	}
	.avail_section .right-tick {
	    display: block;
	}
	.avail_section.edit .right-tick {
	    display: none;
	}
	.avail_section .update-full-day {
	    display: none;
	}
	.avail_section.edit .update-full-day {
	    display: block;
	}
	.avail_section.edit .reason_txt {
	    display:none;
	}
	.avail_section.edit .showAvailReason {
	    display:block;
	}
	 .cal-open, .non-cal-open, .edit .calendar-edit, .edit .full-calendar-edit {
		cursor: pointer;
	}
	.no-avail-msg {
		text-align: center;
		color: #bbbbbb;
		font-size: 20px;
		text-transform: uppercase;
	}
	.reason-wrap {
		display: block;
		clear: both;
	}
	.calendar-row {
	    display: block;
	    clear: both;
	}

	.create-row {
		display: inline-block;
		width: 100%;
		padding: 5px 0px;
	}
	@media (max-width:375px) {
		.show-date-time {
			font-size: 12px;
		}
	}
</style>
<script type="text/javascript" >
$(function(){
	$('.tabs').on('show.bs.tab', function (e) {
	  if (e.relatedTarget === undefined) {
	    $($(e.target).attr('href')).slideDown('slow');
	  }
	  else {
	    $($(e.relatedTarget).attr('href')).slideUp({ duration: 'fast', queue: true,
	      done: function() {
	        $($(e.target).attr('href')).slideDown('slow');
	      }
	    });
	  }
	});
	/*============= Create Availability Start  =================*/
	var start = '<?php echo date("Y-m-d"); ?>';
    var end = '<?php echo date("Y-m-d"); ?>';

	function getDate( element ) {
	  var date;
	  try {
		date = $.datetimepicker.parseDate( dateFormat, element.value );
	  } catch( error ) {
		date = null;
	  }
	  return date;
   }

	function cleanDatepicker() {
	   var old_fn = $.datepicker._updateDatepicker;

	   $.datepicker._updateDatepicker = function(inst) {
	      old_fn.call(this, inst);

	      var buttonPane = $(this).datepicker("widget").find(".ui-datepicker-buttonpane");

	      $("<button type='button' class='ui-datepicker-clean ui-state-default ui-priority-primary ui-corner-all'>Delete</button>").appendTo(buttonPane).click(function(ev) {
	          $.datepicker._clearDate(inst.input);
	      }) ;
	   }
	}

    $(".create-avail_reason").keypress(function(event) {
	    var character = String.fromCharCode(event.keyCode);
	    //return isValid(character);
	});

	function isValid(str) {
	    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
	}

	$(".create-avail_reason").bind('paste', function(e) {
	    var character = e.originalEvent.clipboardData.getData('Text');
	      // return isValid(character);
	});

	$('body').on('click', '.avail_save', function(event){
		event.preventDefault();
		$.availability_save = true;

		var $parentDivId = $(this).parents('.avail_section:first'),
			avail_data_id = $parentDivId.find('.avail_data_id').val(),
			start_date = $parentDivId.find('.start-calendar').val(),
			end_date = $parentDivId.find('.end-calendar').val(),
			avail_reason = $parentDivId.find('.showAvailReason').val()

		var full_day = ($parentDivId.find(".update-full-day").prop('checked')) ? 1 : 0;
            if($parentDivId.find(".update-full-day").prop('checked')) {
                start_date = $parentDivId.find(".full-start-calendar").val();
                end_date = $parentDivId.find(".full-end-calendar").val();
            }
		var data = {id: avail_data_id, full_day: full_day, avail_start_date: start_date, avail_end_date: end_date, avail_reason: avail_reason};

		$.ajax({
			url: $js_config.base_url + 'settings/update_availability',
			type: 'POST',
			dataType: 'json',
			data: data,
			success: function(response) {
				if(response.success) {
					$.availability_triggered = true;
					$('#avail_clear').trigger('click')
					$('#activeavail span,#upcomingavail span,#pastavail span').html('<i class="fa fa-refresh fa-spin"></i>')
					// $parentWrapper.html('').load( $js_config.base_url + 'settings/' + url );
					$( ".current_wrapper" ).html('').load( $js_config.base_url + 'settings/current_availability', function() {
                        $('#activeavail span').html('').text(''+$('.current_wrapper .avail_section').length+'')
                    } );
					$( ".future_wrapper" ).load( $js_config.base_url + 'settings/future_availability', function() {
                        $('#upcomingavail span').html('').text(''+$('.future_wrapper .avail_section').length+'')
                    });
					$( ".past_wrapper" ).load( $js_config.base_url + 'settings/past_availability', function() {
                        $('#pastavail span').html('').text(''+$('.past_wrapper .avail_section').length+'')
                    });
					$('.error-message.error').html('');
					$.available_updated = true;
				}
				else{
					$parentDivId.find('.date-error').html(response.content)
				}
			}
		})
	})

	$("body").on("click", ".avail_discard", function(event) {
		event.preventDefault();
		var $parentDivId = $(this).parents('.avail_section:first'),
			$avail_update = $parentDivId.find('.avail_update:first');
		$parentDivId.removeClass('edit');

		var title = 'Update';
		if($parentDivId.hasClass('edit')) {
			title = 'Save';
		}
		$('.avail_update').not($avail_update).attr('title', 'Update').attr('data-original-title', 'Update');
		$avail_update.attr('title', '').attr('data-original-title', title);
		$(this).tooltip('hide');

		var $parentWrapper = $(this).parents('.parent-wrapper:first')
		if($parentWrapper.length > 0) {
			var parentData = $parentWrapper.data(),
				url = parentData.parent;
			$parentWrapper.html('').load( $js_config.base_url + 'settings/' + url );
			$('.error-message.error').html('');
		}
	});

})

</script>