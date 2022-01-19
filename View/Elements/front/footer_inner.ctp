<style>

    .conversation {
        background: #f9fffb none repeat scroll 0 0;
        border: 2px solid #d5fbf1;
        border-radius: 5px 5px 0 0;
        bottom: 0;
        min-height: 325px;
        position: fixed;
        right: 15px;
        width: 20%;
        z-index: 2147483647;
    	display: none;
    }
    .conversation-inner {
        display: block;
        height: 275px;
        min-height: 100%;
        overflow-y: auto;
        position: relative;
    }
    .msg-box {
    	bottom: 0;
    	position: absolute;
    	width: 100%;
    }
    .conversation-inner > p {
        background: #ccc none repeat scroll 0 0;
        border-radius: 15px;
        display: table;
        margin: 9px 3px 2px 3px;
        padding: 5px;
        width: auto;
    }
    .conversation-inner p b {
    	color: #ffffff;
    	font-size: 10px;
    	font-weight: 500;
    }

	.fa, .fas {
		font-weight: 900 !important;
	}

	.fa, .far, .fas {
		font-family: "Font Awesome 5 Free" !important;
	}
    .fixed-position {
        position: fixed;
        right: 130px;
        bottom: 10px;
        z-index: 2147483647;
    }

    .chat-icon-box {
        width: 54px;
        height: 54px;
        background-color: #58c6ff;
        border-radius: 50%;
        cursor: pointer;
    }

    .chat-icon-box:hover {
        background-color: #67a028;
    }

    .circle {
        background-color: rgba(255, 255, 255, 0.2);
        border: 2px solid #fff;
        border-radius: 50%;
        color: #fff;
        font-size: 28px;
        font-weight: bold;
        height: 100%;
        left: 50%;
        line-height: 45px;
        position: absolute;
        text-align: center;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
    }

    .notify {
        animation: animSpin 4s ease-in-out forwards infinite, animFade 0s ease forwards infinite;
    }

    @keyframes animSpin {
        0% {
            -webkit-transform: perspective(120px) rotateY(180deg) rotateX(0deg);
            transform: perspective(120px) rotateY(181deg) rotateX(0deg);
        }
        50% {
            -webkit-transform: perspective(120px) rotateY(0deg) rotateX(0deg);
            transform: perspective(120px) rotateY(0deg) rotateX(0deg);
        }
        100% {
            -webkit-transform: perspective(120px) rotateY(180deg);
            transform: perspective(120px) rotateY(181deg);
        }
    }

    @keyframes animFade {
        0% {
            opacity: .95;
        }
        100% {
            opacity: 1;
        }
    }

    .ico_cht {
        background-image: url("<?php echo SITEURL;?>images/icons/tollfree.png");
        background-repeat: no-repeat;
        background-size: 100% auto;
        display: block;
        /* filter: invert(100%); */
        height: 35px;
        margin: 6px 0 0 7px;
        width: 35px;
        border-radius: 50%;
    }

    .chat-icon-box:hover .ico_cht {
        background-color: #fbc760;
    }


    .ui-ellipsis {
        white-space: nowrap;
        overflow: hidden;
    }

    .ui-ellipsis-helper {
        display: inline !important;
    }
</style>
<div class="conversation" id="conversation">
	<div class="conversation-inner">
	</div>
	<input type="text" class="msg-box" name="msg">
</div>

<?php

if (
    ($this->request->params['action'] !== 'knowledge_analytics' && $this->request->params['controller'] !== 'subdomains') &&
    ( $this->request->params['action'] !== 'index' && $this->request->params['controller'] !== 'subdomains') &&
    ( $this->request->params['controller'] != 'competencies') &&
    ( $this->request->params['controller'] != 'tags') &&
    ( $this->request->params['action'] !== 'nudge_list' &&  $this->request->params['controller'] != 'boards') &&
    ($this->request->params['action'] !== 'summary' && $this->request->params['controller'] !== 'projects') &&
    ($this->request->params['action'] !== 'circles' && $this->request->params['controller'] !== 'samples')
    ){ ?>
    <style type="text/css">
        section.content {
            min-height: 800px;
        }
    </style>
   <?php /* ?> <footer class="footer clearfix">
        <div class="container ">
            <div class="footer-content"> <a href="#" class="cd-top"><i class="fa fa-angle-up"></i></a>
                <p>
                    &copy; <?php echo date('Y'); ?> IdeasCast Limited. All rights reserved.
                </p>
            </div>
        </div>
    </footer><?php */ ?>
<?php } ?>
    <script type="text/javascript">
      $.widget.bridge('uibutton', $.ui.button);
    </script>

    <?php
        echo $this->Html->script(array(
            //'bootstrap',
			'bootstrap-dialog/bootstrap3.3.5.min'
         )
        );

        /*echo $this->Html->script(array(
            '/plugins/sparkline/jquery.sparkline.min',
            '/plugins/knob/jquery.knob',
            '/plugins/daterangepicker/daterangepicker',
            '/plugins/slimScroll/jquery.slimscroll',
            'app',
            'jquery.cookie',
            'pages/dashboard',
            'demo',
            'front.custom',
            'projects/plugins/colored_tooltip',
            'star-rating',
        ));*/
        echo $this->Html->script(array(
            // 'bootstrap-dialog/bootstrap3.3.5.min'
            '/plugins/sparkline/jquery.sparkline.min',
            '/plugins/knob/jquery.knob.min',
            '/plugins/daterangepicker/daterangepicker',
            '/plugins/slimScroll/jquery.slimscroll',
            'app.min',
            'jquery.cookie.min',
            'pages/dashboard.min',
            'demo.min',
            'front.custom.min',
            'projects/plugins/colored_tooltip.min',
            'star-rating.min',
        ));

    	if( !empty($this->request->params['action']) && ($this->request->params['action'] !== 'update_element' && $this->request->params['action'] !== 'manage_project') ) {
			// echo $this->Html->script(array('/js/projects/plugins/wysihtml5.no.min'));
    	}

        if( $_SERVER['SERVER_NAME'] != SERVER_NAME &&  $this->Session->read('Auth.User.role_id') == 2 ){

        }
        echo $this->Html->script('projects/plugins/jquery.autoellipsis-1.0.10', array('inline' => true));

        echo $this->Html->css('projects/datetime/datetime-addon');
        echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));
        echo $this->Html->script('projects/plugins/datetime/datetime-addon', array('inline' => true));

        if ( ($this->request->params['action'] != 'domain_settings' && $this->request->params['controller'] !== 'organisations') && ( ($this->request->params['action'] !== 'update_element') || ($this->request->params['action'] !== 'group') || ($this->request->params['action'] !== 'task_list')) && ( $this->request->params['controller'] !== 'skills'  )   && ($this->request->params['controller'] !== 'settings' && ($this->request->params['action'] !== 'notifications' || $this->request->params['action'] !== 'notification' || $this->request->params['action'] !== 'manage_users' || $this->request->params['action'] !== 'client_email_domain' || $this->request->params['action'] !== 'client_manage_users' || $this->request->params['action'] !== 'domain_list' ) ) && ($this->request->params['controller'] !== 'work_centers')  && ($this->request->params['controller'] !== 'tags') && ($this->request->params['controller'] !== 'knowledge_domains') && ($this->request->params['controller'] !== 'subjects' && $this->request->params['controller'] !== 'competencies') && ($this->request->params['action'] != 'myaccountedit' && $this->request->params['controller'] !== 'users') ) {
            echo $this->Html->script(array('/plugins/iCheck/icheck.min'));
        }
    ?>

<script type="text/javascript">

    $(function () {


        $.sortList = function($elem) {
            var $li = $elem.data('ptitles'),
                $listLi = $($li,$elem).get();

            $elem.sort(function (a, b) {
                var contentA = $(a).attr('data-ptitles').toLowerCase();
                var contentB = $(b).attr('data-ptitles').toLowerCase();
                return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
            }).insertAfter($('.prevcrntprjt')).show()

        }

        var elem = $(".sidebar").find("#sidebar_menu li.prevcrntprjt").parent().find('.currentproject');

        if(elem.length > 0  ){
            //$.sortList(elem);
        }

        $('.footer-content a.cd-top').on('click', function(e) {
            e.preventDefault()
            $('html, body').animate({
                scrollTop: 0
            }, 1500, 'easeOutCirc')
        })

        $('.history').attr('title','Open History');

        $('body').delegate('.history', 'click', function(e) {

            $(this).toggleAttr('data-original-title', 'Open History', 'Close History');
        })

        $('.modal').on('show.bs.modal', function (event) {
            $('body').css('padding-right', 0);
            $('html').addClass('modal-open');
        })
        $('.modal').on('shown.bs.modal', function (event) {
            $(event.relatedTarget).tooltip('destroy')

        })
        $('.modal').on('hidden.bs.modal', function (event) {
            $('.tooltip').hide();
            $('html').removeClass('modal-open');
            $(this).find('.modal-content').html("")
            $(this).removeData('bs.modal')
        })
        /*
         * @todo  Hide each success flash message after 4 seconds
         * */


		var time_extend = 4000;
		if( window.location.pathname.split("/")[2] == 'update_element' ){
			time_extend = 10000;
		}


        if ($("#successFlashMsg").length > 0) {
            setTimeout(function () {
                $("#successFlashMsg").animate({
                    opacity: 0,
                    height: 0
                }, 1000, function () {
                    $(this).remove()
                })

            }, time_extend)
        }

        /*
         * @todo  Global setup of AJAX on document. It can be used when any ajax call return response.
         * */

        $(document).ajaxSuccess(function (event, jqXHR, ajaxSettings, data) {
            $('.tooltip').hide()
            if ($(".ajax_overlay_preloader").length > 0) {
                $(".ajax_overlay_preloader").fadeOut(150);
                $("body").removeClass('noscroll');
            }

            if ($.inArray('msg', data) == -1) {
                if (data['msg'] != '' && !data['success'] && !data['msg'] == 'undefined') {
                    $(".ajax_flash").text(data['msg']).fadeIn(500)
                    setTimeout(function () {
                        if ($(".ajax_flash").length > 0) {
                            $(".ajax_flash").fadeOut(600).text('');
                        }
                    }, 3000)
                }
            }
        });

        /*
         * @todo  Global setup of AJAX on document. It can be used when any ajax call is performed
         * */
        $(document).ajaxSend(function (e, xhr) {

            window.theAJAXInterval = 1;
            /*$(".ajax_overlay_preloader")
                    .fadeIn(300)
                    .bind('click', function (e) {
                        //$(this).fadeOut(300);
                    });*/

            $("body").addClass('noscroll');
        })
        .ajaxComplete(function () {
            setTimeout(function () {
                $(".ajax_overlay_preloader").fadeOut(300);
                $("body").removeClass('noscroll');
                clearInterval(window.theAJAXInterval);
            }, 2000)

            // get response header on each ajax request
            // logout if header is not set from AppController
            var requiresAuth;
            try { requiresAuth = request.getResponseHeader('Requires-Auth') }
            catch(e) {}

            if (requiresAuth == 1) {
                window.location = "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'login')); ?>";
                return false;
            }
        });
        /*
         * @todo  Initially stop all global AJAX events.
         * */
        $.ajaxSetup({
            global: false,
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            }
        })

        $(".modal").on('show', function (e) {
            $.ajax({
                global: false
            });
        })

        $.get_not_avail_dates = function() {
            var current_user_id = $js_config.USER['id'];
            if(!$('#myc').prop('checked')) {
                if( $('#project_owner_users').val() !== undefined && $('#project_owner_users').val() != '' ) {
                    current_user_id = $('#project_owner_users').val();
                }
            }
            $.ajax({
                url: $js_config.base_url + 'work_centers/not_available_dates',
                type: 'post',
                dataType: 'json',
                data: {user: current_user_id},
                success: function(response) {
                    $('.not_available_dates_wrap').html(response);
                }
            })
        }

        $.get_not_avail_data = function() {
            var post_data = {}
            var current_user_id = $js_config.USER['id'];
            if(!$('#myc').prop('checked')) {
                if( $('#project_owner_users').val() !== undefined && $('#project_owner_users').val() != '' ) {
                    current_user_id = $('#project_owner_users').val();
                }
            }
            post_data.user_id = current_user_id;

            var cdata = 0;
            if($('.show-hide-program[aria-expanded=true]', $('#accordion')).length > 0) {
                var cal_data = $('.show-hide-program[aria-expanded=true]', $('#accordion')).parents('.right-options').find('.show_calendar').data();
                cdata = cal_data.pid;
                post_data.pid = cdata;
            }

            if(cdata > 0) {
                var memberExtraHtml = '';
                $.ajax({
                    url: $js_config.base_url + 'work_centers/usernotavaildateswithstatus',
                    type: 'post',
                    dataType: 'json',
                    data: post_data,
                    success: function(response) {
                        if( response.userdates ){
                            calendar_click = true;
                            var resultdates = response.userdates;
                            var stdate = 'N/A';
                            var endate = 'N/A';

                            $.each(resultdates , function (index,value){

                                var firstdate = '';
                                var lastdate = '';
                                var fullday = '';
                                var lastfullday = '';

                                if( value.Availability.avail_start_date ){

                                    firstdate = value.Availability.avail_start_date.split(" ");
                                    if ( firstdate[1] == '00:00:00' ){
                                        // fullday = 'Fullday';
                                        stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY')+" "+fullday;
                                    } else {
                                        stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY h:mm a');
                                    }

                                }
                                if( value.Availability.avail_end_date ){

                                    lastdate = value.Availability.avail_end_date.split(" ");
                                    if ( lastdate[1] == '00:00:00' ){
                                        // lastfullday = 'Fullday';
                                        endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY')+" "+lastfullday;
                                    } else {
                                        endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY h:mm a');
                                    }
                                }

                                memberExtraHtml += '<div class="con" ><div class="col-sm-12">';
                                memberExtraHtml += '<div class="datestartlist">Start: ' + stdate + '</div>';
                                memberExtraHtml += '<div class="dateendlist">End: ' + endate + '</div>';

                                if( value.Availability.avail_reason.length > 0 ){
                                    memberExtraHtml += '<div class="noavailreason  ">Reason: ' + value.Availability.avail_reason + '</div>';
                                } else {
                                    memberExtraHtml += '<div class="noavailreason  ">Reason: N/A</div>';
                                }
                                memberExtraHtml += '</div></div>';

                            });

                            $('.freeuserdatereasonstatus').html(memberExtraHtml);
                        }
                    }
                })
            }
        }

        $('#availability_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            // if($.availability_save){
                $.availability_save = false;
                $.get_availability_status({user_id: $js_config.USER.id}).done(function(response){
                    if(response.success){
                        var status = response.content;
                        $('.current-status').html(status);
                    }
                })
            // }
        })

        /*$('#availability_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');

            if(window.location.href.indexOf("work_centers") > -1) {

                $.get_not_avail_dates();
                $.get_not_avail_data();

                $('.show_calendar:visible').trigger('click', ['triggerfunction']);
            }
        })*/

        $.current_theme = '<?php echo $user_theme ?>';

        $('#modal_medium').on('hidden.bs.modal', function () {

            var ms_theme = $('.main-sidebar').data('theme'),
                mh_theme = $('.main-header').data('theme'),
                ch_theme = $('.open-chat-win').data('theme');

            $('.main-header').removeClass(mh_theme).addClass($.current_theme)
            $('.main-sidebar').removeClass(ms_theme).addClass($.current_theme)
            $('.open-chat-win').removeClass(ch_theme).addClass($.current_theme)

            $('.main-header').data({'theme': $.current_theme})
            $('.main-sidebar').data({'theme': $.current_theme})
            $('.open-chat-win').data({'theme': $.current_theme})
            var $img = $('header.main-header a.logo img'),
                logo = 'logo_white.png';

            if ($img.length) {
                $img[0].src = $js_config.base_url + 'images/' + logo;
            }
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('')

        });


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

                                    if(window.location.href.indexOf("people") > 0){
                                        $.available_updated = true;
                                    }

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
    function msToTime(duration) {
        var milliseconds = parseInt((duration % 1000) / 100),
            seconds = Math.floor((duration / 1000) % 60),
            minutes = Math.floor((duration / (1000 * 60)) % 60),
            hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        return hours + ":" + minutes + ":" + seconds + "." + milliseconds;
    }
    function getPageLoadTime() {
        var loadedSeconds = (new Date().getTime() - $js_config.start_time);
        if($js_config.page_load) {
            console.log('Page load time ::  ' + msToTime(loadedSeconds));
        }
    }
    if ($js_config.page_load) {
        window.onload = getPageLoadTime;
    }

  /******************************* End Page Load Time ************************************/

	$.add_project_sidebar = function(newitems) {
		var birdList = $(".project-items"); //get birds unordered list
		var newBirdItem = $(newitems, {'style': 'display: none'}); // create new li element
		var bird = newitems // get data from form field
		birdList.append(newBirdItem); // append li element to bird list
		$(newBirdItem).hide();
		$(birdList).find("li").sort(function(a, b) {
			return $(a).text().toLowerCase().localeCompare($(b).text().toLowerCase());
		}).each(function() {
			$(birdList).append(this);
		});
		$(newBirdItem).fadeIn(300)
	}

	$.add_task_sidebar = function(newitems) {
		var birdList = $(".task-items"); //get birds unordered list
		var newBirdItem = $(newitems, {'style': 'display: none'}); // create new li element
		var bird = newitems // get data from form field
		birdList.append(newBirdItem); // append li element to bird list
		$(newBirdItem).hide();
		$(birdList).find("li").sort(function(a, b) {
			return $(a).text().toLowerCase().localeCompare($(b).text().toLowerCase());
		}).each(function() {
			$(birdList).append(this);
		});
		$(newBirdItem).fadeIn(300)
	}

$(function(){

    $('#modal_view_skill, #modal_view_dept, #modal_view_loc, #modal_view_org, #modal_user_profile').on('hidden.bs.modal', function(event) {
        event.preventDefault();
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
})
</script>



<div class="modal modal-success fade " id="modal_user_profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content "></div>
    </div>
</div>


<div class="modal modal-success fade " id="modal_view_skill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg skill-profile-view">
        <div class="modal-content "></div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_view_loc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg skill-profile-view">
        <div class="modal-content "></div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_view_org" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg skill-profile-view">
        <div class="modal-content "></div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_view_dept" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md department-profile-view">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade " id="story_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg story-modal">
        <div class="modal-content"></div>
    </div>
</div>
