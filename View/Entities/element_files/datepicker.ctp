<?php
if( empty($mindate_elm_cal) ){
	$mindate_elm_cal = date("d M Y");
}
if( empty($maxdate_workspace) ){
	$maxdate_workspace = date("d M Y");
}
$element_start_date = (isset($this->request->data['Element']['start_date']) && !empty(($this->request->data['Element']['start_date']))) ? $this->request->data['Element']['start_date'] : $mindate_elm_cal;
//pr($this->request->data);
?>

<script type="text/javascript">
    $(function () {
        $('.open-start-date-picker').on('click', function (event) {
            event.preventDefault();
            $("#start_date").datepicker('show').focus();
        });

        $('.open-end-date-picker').on('click', function (event) {
            event.preventDefault();
            $("#end_date").datepicker('show').focus();
        });

        $('.open-start-date-picker_feedback1').on('click', function (event) {
            event.preventDefault();
            //$("#feedbackstart_date").datepicker("option", "minDate", 0);
            $("#feedbackstart_date").datepicker('show').focus();
        });

        $('.open-end-date-picker_feedback').on('click', function (event) {
            event.preventDefault();
            //$("#feedbackend_date").datepicker("option", "minDate", 0);
            $("#feedbackend_date").datepicker('show').focus();
        });

        $('.open-start-date-picker1').on('click', function (event) {
            event.preventDefault();
            $("#start_date1").datepicker('show').focus();
        });

        $('.open-end-date-picker1').on('click', function (event) {
            event.preventDefault();
            $("#end_date1").datepicker('show').focus();

        });

        // element
        var start = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
		var end_start = '<?php echo date("d M Y", strtotime($element_start_date)); ?>';
        var end = '<?php echo date("d M Y", strtotime($maxdate_workspace)); ?>';
        $(".open-start-date-picker").click(function () {
            $("#start_date_elm").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $("#end_date_elm").datepicker('show').focus();
        })
        $("#start_date_elm").datepicker({
            minDate: start,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate == '') {
                    $("#end_date_elm").datepicker("option", "minDate", start);
                } else {
                    $("#end_date_elm").datepicker("option", "minDate", selectedDate);
                }

            },
            onSelect: function (selectedDate) {
                if (start == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                } else {

                    //$("#end_date_elm").datepicker("setDate", selectedDate);
                   // $("#end_date_elm").datepicker("option", "minDate", selectedDate);
                }
            }
        });

		 /*$("#end_date_elm").datepicker({
            minDate: start,
            maxDate: end,
            //defaultDate: "+1w",
            dateFormat: 'dd M yy',
            changeMonth: true,
			changeYear: false,
            //numberOfMonths: 3,
            onClose: function (selectedDate) {
                //$("#start_date").datepicker("option", "maxDate", selectedDate);
            },
            onSelect: function (selectedDate) {
                if (end == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                }

            }
        });*/
		//console.log(start);

        $("#end_date_elm").datepicker({
            minDate: end_start,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,

			onClose: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                  //  $("#end_date_elm").datepicker("setDate", selectedDate);
                   // $("#end_date_elm").datepicker("option", "minDate", start);
                   // $("#end_date_elm").datepicker("option", "maxDate", end);
                    //$("#start_date_elm").datepicker("option", "maxDate", start);
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                    $("#end_date_elm").datepicker("setDate", selectedDate);
                    //$("#end_date_elm").datepicker("option", "minDate", start);
                   // $("#end_date_elm").datepicker("option", "maxDate", end);
                    //$("#start_date_elm").datepicker("option", "maxDate", start);
                }
            }


        });
        // end


        // feedbacks
        //var feedback_start_elm = '<?php echo $mindate_elm_cal; ?>';
        //var feedback_end_elm = '<?php echo $maxdate_elm; ?>';

		//Changed on July 19 2019
		//var feedback_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
		var feedback_start_elm = '<?php echo date("d M Y"); ?>';
        var feedback_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';

        //console.log(feedback_start_elm +"  "+ feedback_end_elm);
        $("#feedbackstart_date").datepicker({
            minDate: feedback_start_elm,
            maxDate: feedback_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                   //$("#feedbackend_date").datepicker("option", "minDate", feedback_start_elm);
                } else {
                   // $("#feedbackend_date").datepicker("option", "minDate", feedback_start_elm);
                }

            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                  //  $("#feedbackend_date").datepicker("setDate", feedback_start_elm);
                   // $("#feedbackend_date").datepicker("option", "minDate", feedback_start_elm);
                   // $("#feedbackend_date").datepicker("option", "maxDate", feedback_end_elm);
                }
            }
        });
        $("#feedbackend_date").datepicker({
            minDate: feedback_start_elm,
            maxDate: feedback_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                  //  $("#feedbackend_date").datepicker("setDate", selectedDate);
                   // $("#feedbackend_date").datepicker("option", "minDate", feedback_start_elm);
                   // $("#feedbackend_date").datepicker("option", "maxDate", feedback_end_elm);
                   // $("#feedbackstart_date").datepicker("option", "maxDate", feedback_start_elm);
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                  //  $("#feedbackend_date").datepicker("setDate", selectedDate);
                   // $("#feedbackend_date").datepicker("option", "minDate", feedback_start_elm);
                   // $("#feedbackend_date").datepicker("option", "maxDate", feedback_end_elm);
                   // $("#feedbackstart_date").datepicker("option", "maxDate", feedback_start_elm);
                }
            }
        });



        /// votes
		//Changed on July 19 2019
		//var vote_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
		var vote_start_elm = '<?php echo date("d M Y"); ?>';
        var vote_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';

        $("#start_date1").datepicker({
            minDate: vote_start_elm,
            maxDate: vote_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {

                 //   $("#end_date1").datepicker("option", "minDate", selectedDate);
                } else {
                  //  $("#end_date1").datepicker("option", "minDate", vote_start_elm);
                }

            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    vote_start_elm = selectedDate;
                   // $("#end_date1").datepicker("setDate", selectedDate);
                   // $("#end_date1").datepicker("option", "minDate", selectedDate);
                   // $("#end_date1").datepicker("option", "maxDate", vote_end_elm);
                }
            }
        });
        $("#end_date1").datepicker({
            minDate: vote_start_elm,
            maxDate: vote_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {
                   // $("#end_date1").datepicker("setDate", selectedDate);
                   // $("#end_date1").datepicker("option", "minDate", vote_start_elm);
                   // $("#end_date1").datepicker("option", "maxDate", vote_end_elm);
                   // $("#start_date1").datepicker("option", "maxDate", selectedDate);
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                  //  $("#end_date1").datepicker("setDate", selectedDate);
                  //  $("#end_date1").datepicker("option", "minDate", vote_start_elm);
                  //  $("#end_date1").datepicker("option", "maxDate", vote_end_elm);
                   // $("#start_date1").datepicker("option", "maxDate", selectedDate);
                }
            }
        });

        // end



    });




    $(function () {

    $('.open-start-date-picker-update').on('click', function (e) {
        e.preventDefault();

		//var vote_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
   
        var rel = $(this).siblings('.start_date').attr('rel');
		
		var today_d = '<?php echo date("d M Y"); ?>';
		
		var vote_start_elm = $(this).siblings('.start_date').attr('value');
		var vote_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';
		
		var today_d_time = new Date(today_d).getTime();
		var vote_start_elm_time = new Date(vote_start_elm).getTime();
		
		if(today_d_time < vote_start_elm_time){
			vote_start_elm = today_d;
			 
		}else{
			vote_start_elm = vote_start_elm;
			vote_end_elm = vote_start_elm;
			 
		}

		//var feedback_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';		
		
		
        //console.log(rel);
        $("#start_date_" + rel).datepicker({
            minDate: vote_start_elm,
            maxDate: vote_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {

                   // $("#end_date_" + rel).datepicker("option", "minDate", selectedDate);
                } else {
                   // $("#end_date_" + rel).datepicker("option", "minDate", vote_start_elm);
                }

            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    vote_start_elm = selectedDate;
                 //   $("#end_date_" + rel).datepicker("setDate", selectedDate);
                  //  $("#end_date_" + rel).datepicker("option", "minDate", selectedDate);
                  //  $("#end_date_" + rel).datepicker("option", "maxDate", vote_end_elm);
                }
            }
        });
        $("#start_date_" + rel).datepicker('show');
    });

    $('.open-end-date-picker-update').on('click', function (event) {
        event.preventDefault();
        var rel = $(this).siblings('.end_date').attr('rel');

		//var vote_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
        
		var today_d = '<?php echo date("d M Y"); ?>';
		//var feedback_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
        var vote_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';
        var vote_start_elm = $(this).siblings('.end_date').attr('value');
		
		var today_d_time = new Date(today_d).getTime();
		var vote_start_elm_time = new Date(vote_start_elm).getTime();
		
		if(today_d_time < vote_start_elm_time){
			vote_start_elm = today_d;
		}else{
			vote_start_elm = vote_start_elm;
			vote_end_elm = vote_end_elm;
		 
		}
		
		

        $("#end_date_" + rel).datepicker({
            minDate: vote_start_elm,
            maxDate: vote_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {
                if (selectedDate != '') {
                   // $("#end_date_" + rel).datepicker("setDate", selectedDate);
                   // $("#end_date_" + rel).datepicker("option", "minDate", vote_start_elm);
                   // $("#end_date_" + rel).datepicker("option", "maxDate", vote_end_elm);
                   // $("#start_date_" + rel).datepicker("option", "maxDate", selectedDate);
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                   // $("#end_date_" + rel).datepicker("setDate", selectedDate);
                   // $("#end_date_" + rel).datepicker("option", "minDate", vote_start_elm);
                    //$("#end_date_" + rel).datepicker("option", "maxDate", vote_end_elm);
                   // $("#start_date_" + rel).datepicker("option", "maxDate", selectedDate);
                }
            }
        });
        $("#end_date_" + rel).datepicker('show');
    });


    $('.open-start-date-picker-update_feedback').on('click', function (e) {
        e.preventDefault();
        var rel = $(this).siblings('.start_date').attr('rel');
        
		var today_d = '<?php echo date("d M Y"); ?>';
		
		var feedback_start_elm = $(this).siblings('.start_date').attr('value');
		var feedback_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';
		
		var today_d_time = new Date(today_d).getTime();
		var feedback_start_elm_time = new Date(feedback_start_elm).getTime();
		
		if(today_d_time < feedback_start_elm_time){
			feedback_start_elm = today_d;
		}else{
			feedback_start_elm = feedback_start_elm;
			feedback_end_elm = feedback_start_elm;
		}

		//var feedback_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
        

        $("#feedbackstart_date_" + rel).datepicker({
            minDate: feedback_start_elm,
            maxDate: feedback_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {

                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                   // $("#feedbackend_date_" + rel).datepicker("option", "minDate", feedback_start_elm);
                } else {
                    //$("#feedbackend_date_" + rel).datepicker("option", "minDate", feedback_start_elm);
                }

            },
            onSelect: function (selectedDate) {

                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                   // $("#feedbackend_date_" + rel).datepicker("setDate", feedback_start_elm);
                   // $("#feedbackend_date_" + rel).datepicker("option", "minDate", feedback_start_elm);
                   // $("#feedbackend_date_" + rel).datepicker("option", "maxDate", feedback_end_elm);
                }
            }
        });
        $("#feedbackstart_date_" + rel).datepicker('show');
    });




    $('.open-end-date-picker-update_feedback').on('click', function (event) {
        event.preventDefault();
        var rel = $(this).siblings('.end_date').attr('rel');
		
		var today_d = '<?php echo date("d M Y"); ?>';
		//var feedback_start_elm = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
        var feedback_end_elm = '<?php echo date("d M Y", strtotime($maxdate_elm)); ?>';
        var feedback_start_elm = $(this).siblings('.end_date').attr('value');
		
		var today_d_time = new Date(today_d).getTime();
		var feedback_start_elm_time = new Date(feedback_start_elm).getTime();
		
		if(today_d_time < feedback_start_elm_time){
			feedback_start_elm = today_d;
		}else{
			feedback_start_elm = feedback_start_elm;
			feedback_end_elm = feedback_end_elm;
		 
		}


        $("#feedbackend_date_" + rel).datepicker({
            minDate: feedback_start_elm,
            maxDate: feedback_end_elm,
            dateFormat: 'dd M yy',
            changeMonth: true,
            onClose: function (selectedDate) {

                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                  //   $("#feedbackend_date_" + rel).datepicker("setDate", selectedDate);
                   // $("#feedbackend_date_" + rel).datepicker("option", "minDate", feedback_start_elm);
                  //  $("#feedbackend_date_" + rel).datepicker("option", "maxDate", feedback_end_elm);
                  //  $("#feedbackstart_date_" + rel).datepicker("option", "maxDate", selectedDate);
                }

            },
            onSelect: function (selectedDate) {

                if (selectedDate != '') {
                    feedback_start_elm = selectedDate;
                   //  $("#feedbackend_date_" + rel).datepicker("setDate", selectedDate);
                   // $("#feedbackend_date_" + rel).datepicker("option", "minDate", feedback_start_elm);
                  //  $("#feedbackend_date_" + rel).datepicker("option", "maxDate", feedback_end_elm);
                   // $("#feedbackstart_date_" + rel).datepicker("option", "maxDate", selectedDate); 
                }
            }
        });
        $("#feedbackend_date_" + rel).datepicker('show');
    });


    $('#modal_medium').on('hidden.bs.modal', function () {
        if ($(this).data('bs.modal'))
            $(this).removeData('bs.modal');
        $('#modal_medium').find('.modal-content').html('')
    });

    $('body').delegate('.disable', 'click', function () {
        return false;
    })

    })

    $(window).load(function (event) {
        setTimeout(function () {

        }, 1000)

    })
</script>

