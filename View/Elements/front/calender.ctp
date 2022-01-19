<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" > var SITEURL='<?php echo SITEURL; ?>'</script>
<?php
        //echo $this->Html->meta('icon');
		echo $this->Html->meta('icon', $this->Html->url(SITEURL.'favicon.png'));
        echo $this->Html->css(
                array(
                    'font-awesome.min',
                    'ionicons.min',
                    'AdminLTE.min',
                    'skins/_all-skins.min',
					'/plugins/iCheck/minimal/_all.min',
                    '/plugins/iCheck/flat/blue.min',
					'/plugins/iCheck/flat/_all.min',
                    '/plugins/morris/morris',
                    '/plugins/jvectormap/jquery-jvectormap-1.2.2',
					'/twitter-cal/components/bootstrap3/css/bootstrap.min',
					'/twitter-cal/components/bootstrap3/css/bootstrap-theme',
					'/twitter-cal/css/calendar',
					'styles-inner.min',
                    'projects/user_themes.min',
					'projects/socket_notifications',
					'/plugins/jquery-ui-1.11.4.custom/jquery-ui.min',
                    'projects/competency_global',
					'projects/custom.min'
                )
        );

		 echo $this->Html->css('/plugins/fullcalendar/fullcalendar.print', array('media' => 'print'));

	?>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
      <?php  echo $this->Html->script(array(
            '/plugins/jQuery/jQuery-2.1.3.min',
			'jquery-ui.min.js',
			'moment.min',
			'/twitter-cal/components/underscore/underscore-min',
			'dashboard',
			'jstz',
            'projects/reminders',
			)
		   );
      	echo $this->Html->css('projects/datetime/datetime-addon');
        echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));
        echo $this->Html->script('projects/plugins/datetime/datetime-addon', array('inline' => true));

if( $this->Session->read('Auth.User.role_id') == 2 ){
	   ?>
<!-- <script type="text/javascript" src="<?php echo CHATURL; ?>/socket.io/socket.io.js"></script> -->
<?php }
	// Add JS object to accessible in every view file.
	echo $this->Html->scriptBlock('var $js_config = '.$this->Js->object($jsVars).';');
?>
<?php
echo $this->Html->script(array(
			//'drag-drop-context/jquery.cookie',
		)
    );

?>
<style>
.ui-datepicker-inline{display: none !important; }
#workspace {
    margin-top: 0px;
}
</style>

<script>
$(function(){

$('.sidebar-toggle').click(function(){

	$('.gantt_wrapper').hide();
})
        $.availability_triggered = false;
        // save availability
        $('body').delegate('#avail_save_page', 'click', function(event) {
            event.preventDefault();
            $(this).attr('disabled', true);
            $parent = $(".create-availability");
            var start_date = $parent.find(".non-start-cal").val();
            var end_date = $parent.find(".non-end-cal").val();
            var avail_reason = $parent.find(".create-avail_reason").val();
            var full_day = ($parent.find(".chk-full-day").prop('checked')) ? 1 : 0;
            if($parent.find(".chk-full-day").prop('checked')) {
                start_date = $parent.find(".start-cal").val();
                end_date = $parent.find(".end-cal").val();
            }

            if( start_date && end_date ){
                    $.when(
                        $.ajax({
                            type: 'POST',
                            data: $.param({'full_day': full_day, 'avail_start_date': start_date,'avail_end_date': end_date,'avail_reason': avail_reason}),
                            url: $js_config.base_url + 'settings/create_availability/',
                            global: false,
                            dataType: 'JSON',
                            success: function(response) {
                                $.availability_triggered = true;
                                if( response.success ){

                                    $parent.find(".start-cal").val('');
                                    $parent.find(".end-cal").val('');
                                    $parent.find(".create-avail_reason").val('');

                                    var $dates = $('#availStartdates, #availEnddates').datetimepicker();
                                    $(".setStartAvailDate").text('Day, Month Year: Time');
                                    $(".setEndAvailDate").text('Day, Month Year: Time');
                                    $('#avail_message_box').css('color','#67a028').html(response.msg).slideDown(500);

                                    setTimeout(function(){
                                        $('#avail_message_box').slideUp(500).html('').removeAttr('color')
                                    }, 3000)
                                    $( ".create_avail_form" ).load( $js_config.base_url + 'settings/create_avail_form' );
                                }
                                else{
                                    $parent.find('.date-error.text-center').html(response.content);
                                }

                            }
                        })

                    )
                    .then(function(rdata, textStatus, jqXHR) {
                        $('#activeavail span,#upcomingavail span,#pastavail span').html('<i class="fa fa-refresh fa-spin"></i>')
                        if(rdata.success) {
                            $( ".current_wrapper" ).load( $js_config.base_url + 'settings/current_availability', function() {
                                $('#activeavail span').html('').text(''+$('.current_wrapper .avail_section').length+'')
                            } );
                            $( ".future_wrapper" ).load( $js_config.base_url + 'settings/future_availability', function() {
                                $('#upcomingavail span').html('').text(''+$('.future_wrapper .avail_section').length+'')
                            });
                            $( ".past_wrapper" ).load( $js_config.base_url + 'settings/past_availability', function() {
                                $('#pastavail span').html('').text(''+$('.past_wrapper .avail_section').length+'')
                            });
                        }
                        $(this).attr('disabled', false);
                    })

            } else {
                $('.date-error.text-center').html("Please select from and to dates.").slideDown(500);
                setTimeout(function(){
                    // $('.date-error.text-center').slideUp(500).html('').removeAttr('color')
                }, 3000)
                $(this).attr('disabled', false);
            }

        })



    $("body").delegate("#avail_clear", "click", function(event) {
        event.preventDefault();
        $( ".create_avail_form" ).load( $js_config.base_url + 'settings/create_avail_form' );
    })

    $("body").delegate(".avail_update:not(.avail_save)", "click", function(event) {
        event.preventDefault();
        var $parentDivId = $(this).parents('.avail_section:first');
        $('.avail_section').not($parentDivId).removeClass('edit');
        $parentDivId.toggleClass('edit');
        var title = 'Update';
        $(this).removeClass('avail_save');
        if($parentDivId.hasClass('edit')) {
            title = 'Save';
            $(this).addClass('avail_save');
        }
        $('.avail_update').not($(this)).attr('data-original-title', 'Update');
        $(this).attr('data-original-title', title);


        // $parentDivId.find('.calendar-input').datetimepicker(dynamic_calendar);
        $('.error-message.error').html('');

    });

    $('body').delegate('.update-full-day', 'change', function(e) {
        if($(this).prop('checked')) {
            $('.full-calendar-edit').show();
            $('.calendar-edit').hide();
        }
        else{
            $('.full-calendar-edit').hide();
            $('.calendar-edit').show();
        }
    });



})
</script>