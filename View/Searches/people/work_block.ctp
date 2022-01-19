<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<div class="modal-data">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title annotationeleTitle" id="myModalLabel">Work Blocks</h4>
</div>
<div class="modal-body popup-select-icon ">
    <div class="workblock-manager ">
		<div class="workblock-card-sec">
            <input name="work_id" value="" type="hidden" id="work_id" >
			<div class="workblock-card-col1">
				<label>Block Work From:</label>
                <div class="input-group">
    				<input name="start_date" class="form-control dates input-small start_date" value="" type="text" id="start_date_pop" autocomplete="off">
    				<div class="input-group-addon op-cal-start">
    					<i class="fa fa-calendar"></i>
    				</div>
				</div>
			</div>
			<div class="workblock-card-col2">
				<label>To Date:</label>
				<div class="input-group">
    				<input name="end_date"  class="form-control dates input-small end_date" value="" type="text" id="end_date" autocomplete="off">
    				<div class="input-group-addon op-cal-end">
    					<i class="fa fa-calendar"></i>
    				</div>
				</div>
			</div>

			<div class="workblock-card-col3">
				<label>Comment:</label>
				<input type="text" class="form-control" id="work_comments" placeholder="50 chars" maxlength="50" autocomplete="off">
			</div>
			<div class="workblock-card-col4">
				<button type="button" id="" class="btn btn-success set_work" disabled="disabled">Add</button>
			</div>
            <!-- <span class="common-error">Both dates are required</span> -->
		</div>

		<div class="workblock-rate-project" style="min-height: 318px;">

        </div>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <!-- <button type="button" id="submit_rates" class="btn btn-success disabled">Save</button> -->
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
</div>
<style type="text/css">

    .error {
	    color: #dd4b39;
	    font-size: 11px;
	}
    .high-z-index {
        z-index: 1052 !important;
    }
    .op-cal {
        cursor: pointer;
    }
</style>

<script type="text/javascript">
	$(()=>{

        $.checkDates = () => {
            var otherdate = $("#start_date_pop").datepicker("getDate");
            var thisdate = $("#end_date").datepicker("getDate")
            if(!thisdate || !otherdate){
                $(".set_work").prop('disabled', true);
            }
            else if(thisdate && otherdate){
                $(".set_work").prop('disabled', false);
            }
        }

        $.startCals = {
            minDate: 0,
            dateFormat: 'dd M yy',
            changeMonth: true,
            changeYear: true,
            beforeShow: function(el, inst){
                inst.dpDiv.addClass('high-z-index');
                $.checkDates();
            },
            onClose: function (selectedDate, inst) {
                inst.dpDiv.removeClass('high-z-index');
            },
            onSelect: function (selectedDate, inst) {
                var thisdate = $("#start_date_pop").datepicker("getDate");
                var otherdate = $("#end_date").datepicker("getDate")
                $("#end_date").datepicker("option", "minDate", thisdate);

                $.checkDates();
            }
        };
        $.endCal = {
            minDate: 0,
            dateFormat: 'dd M yy',
            changeMonth: true,
            changeYear: true,
            beforeShow: function(el, inst){
                inst.dpDiv.addClass('high-z-index');
            },
            onClose: function (selectedDate, inst) {
                inst.dpDiv.removeClass('high-z-index');
                $.checkDates();
            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    $("#start_date_pop").datepicker("option", "maxDate", selectedDate);
                }

                $.checkDates();
            }
        };

        ;($.work_block_list = () => {
            var dfd = new $.Deferred();
            $( ".workblock-rate-project" ).load( $js_config.base_url + "searches/work_block_list/" + $js_config.USER.id, function(data) {
                $(".workblock-data-list").slimScroll({width: '100%', height: 282, alwaysVisible: true});
                dfd.resolve();
            });
            return dfd.promise();
        })();

        $(".modal-body").on('click', '.delete-work',function () {
            event.preventDefault();
            var id = $(this).parents('.workblock-data-row:first').data('id');
            $.ajax({
                url: $js_config.base_url + 'searches/delete_work_block',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(response) {
                    if(response.success){
                        $.work_block_list().done(() => {
                            $('.tooltip').remove();
                            $.work_updated = true;
                        })
                    }
                }
            })
        })

        $(".set_work").off('click').on('click', function () {
            event.preventDefault();
            var $start = $('#start_date_pop'),
                $end = $('#end_date'),
                $comment = $('#work_comments');
            var sdate = $("#start_date_pop").datepicker("getDate");
            var edate = $("#end_date").datepicker("getDate")

            // $(this).prop('disabled', true);
            sdate = $.datepicker.formatDate("yy-mm-dd", sdate)
            edate = $.datepicker.formatDate("yy-mm-dd", edate)

            $.ajax({
                url: $js_config.base_url + 'searches/work_block',
                type: 'POST',
                dataType: 'json',
                data: { start_date: sdate, end_date: edate, comments: $comment.val() },
                success: function(response) {
                    if(response.success){
                        $.work_block_list().done(() => {
                            $start.val('');
                            $end.val('');
                            $comment.val('');
                            $start.datepicker("destroy");
                            $start.datepicker($.startCals);
                            $end.datepicker("destroy");
                            $end.datepicker($.endCal);
                            // $.datepicker._clearDate($start);
                            // $.datepicker._clearDate($end);
                            $.work_updated = true;
                        })
                    }
                }
            })
        })

        $(".op-cal-start").off('click').on('click', function () {
            $('input#start_date_pop').datepicker('show');
        })

        $(".op-cal-end").off('click').on('click', function () {
            $('input#end_date').datepicker('show');
        })

        $("#start_date_pop").datepicker(
            $.startCals
        );

        $("#end_date").datepicker(
            $.endCal
        );

	    $(".workblock-data-list").slimScroll({width: '100%', height: 282, alwaysVisible: true});

	})
</script>