<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->css('projects/scenario');
echo $this->Html->script('projects/plugins/colored_tooltip', array('inline' => true));
?>

<?php

$current_user_id = $this->Session->read('Auth.User.id');
$users = null;

if(isset($owner_projects) && !empty($owner_projects)) {
    $alluserarray = $this->TaskCenter->userByProject($owner_projects);
    $all_users = array_unique($alluserarray['all_project_user']);
    $all_users = $this->TaskCenter->user_exists($all_users);

    if(isset($all_users) && !empty($all_users)) {
        asort($all_users);
        foreach ($all_users as $key => $value) {
            //if($current_user_id != $value){
                $users[$value] = ucwords($this->Common->userFullname($value));
            //}
        }
        asort($users);
    }
}

?>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title annotationeleTitle" id="myModalLabel">Project Workload</h3>
</div>
<div class="modal-body clearfix">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-9">
            <div class="panel panel-default" >
                <div class="panel-body" >
					<div class="row dates_wraper">
						<div class="col-sm-12" style="top:-7px; font-weight: bold;">Select a 3 month period:</div>
					</div>
                    <div class="row form-group parent-wrap">

                        <div class="col-xs-12 col-sm-6 col-md-5  col-lg-4 nopadding-right">
							<div class="row dates_wraper">
                                <div class="col-xs-4 col-sm-4 nopadding-right" style="width: auto;">Start: <i class="fa fa-calendar-check-o trigger-cal tipText" title="" aria-hidden="true" data-original-title="Start Date"></i></div>
                                <div class="col-xs-8 col-sm-7 nopadding-left"><input id="sel_start" type="text" name="sel_start_date" class="sel-cal sel-start" /><span class="disp-dates">Day, Month Year</span></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="row dates_wraper">
                                <div class="col-xs-4 col-sm-4  nopadding-right" style="width: auto;">End: <i class="fa fa-calendar-check-o trigger-cal tipText" title="" aria-hidden="true" data-original-title="End Date"></i></div>
                                <div class="col-xs-8 col-sm-8 nopadding-left"><input id="sel_end" type="text" name="sel_end_date" class="sel-cal sel-end" /><span class="disp-dates">Day, Month Year</span></div>
                            </div>
                        </div>
                        <div class="error-message error text-danger" style="display: inline-block; padding-left: 15px;"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-8 select-wrap">
                            <select id="os_users_list"  name="os_users_list"  placeholder="Select Users" multiple="multiple" >
                            <?php
                            foreach($users as $id => $user){
                            ?>
                                <option value="<?php echo $id;?>"><?php echo $user;?></option>
                            <?php } ?>
                        </select>
                        <div class="error-message error text-danger"><?php if(empty($users)){ echo "No user found"; } ?></div>
                        </div>
                        <div class="col-xs-12 col-sm-4">
                            <div class="sel-btns">
                                <a class="btn btn-success btn-sm view-scenario">View Activity</a>
                                <a class="btn btn-danger btn-sm clear-scenario">Clear</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-3 nopadding-left" >
            <div class="measurements">
                <ul class="legend">
                    <li class="before-gray">Working on Project</li>
                    <li class="before-red">Not Available (Day)</li>
                    <li class="before-yellow">Not Available (Partial day)</li>
                    <li class="before-green">Date Range</li>
                    <li class="before-white">No Projects</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-sm-12 nopadding chart-container">
        <figure>
            <div class="x-axis"></div>
            <div class="graphic"></div>
        </figure>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<script type="text/javascript">
    $(function(){

        var dpick_opt = {
            // changeMonth: true,
            // changeYear: true,
            minDate: 0,
            dateFormat: 'yy-mm-dd',
            onClose: function(selected, inst){
                var $input = inst.input,
                    $parent = $input.parents('.parent-wrap:first'),
                    $otherInput = ($input.hasClass('sel-start')) ? $parent.find('.sel-end') : $parent.find('.sel-start');

					var maxDateSet = new Date(selected);
					maxDateSet.setMonth(maxDateSet.getMonth()+3);

                if(selected) {
                    // Restrict end date not be less than start date.
                    if($input.hasClass('sel-start')) {
                        if($otherInput.val() != '' && $otherInput.val() != undefined) {
                            if(new Date(selected) > new Date($otherInput.val())) {
                                $otherInput.parents('.dates_wraper:first').find('.disp-dates').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'))
                            }
                        }
                        $otherInput.datepicker("option", "minDate", $input.val());
						$otherInput.datepicker("option", "maxDate", maxDateSet);
                    }
                    // Restrict start date not be greater than end date.
                    else{
                        if($otherInput.val() != '' && $otherInput.val() != undefined) {
                            if(new Date(selected) < new Date($otherInput.val())) {
                                $otherInput.datepicker('setDate', selected);
                                $otherInput.parents('.dates_wraper:first').find('.disp-dates').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'))
                            }
							$otherInput.datepicker("option", "maxDate", $input.val());
                        }
                    }
                }
            },
            onSelect: function(selected, inst) {
                var $input = inst.input
                var $parent = $input.parents('.dates_wraper:first');
                if(selected) {
                    var correctFormat = moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY');
                    $parent.find(".disp-dates").text(correctFormat);
                 }
            }
        };
        var calndr = $('.sel-cal').datepicker(dpick_opt);



     /*Add This to Block SHIFT Key*/
    var shiftClick = jQuery.Event("click");
    shiftClick.shiftKey = true;
    $(".multiselect-container li *").click(function(event) {
        if (event.shiftKey) {
            event.preventDefault();
            return false;
        }
    });
        // USER'S MULTISELECT BOX INITIALIZATION
        $.os_users_list = $('#os_users_list').multiselect({
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'users',
            includeSelectAllOption: false,
            enableFiltering: true,
            filterPlaceholder: 'Search Users',
            enableCaseInsensitiveFiltering: true,
            disableIfEmpty: true,
            nonSelectedText: 'Select Member',
            enableUserIcon: false,
            includeSelectAllIfMoreThan: 1,
            onChange: function(option, checked, select) {
                // Get selected options.
                var selectedOptions = jQuery('#os_users_list option:selected');

                if (selectedOptions.length >= 1) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = jQuery('#os_users_list option').filter(function() {
                        return !jQuery(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    jQuery('#os_users_list option').each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            },
            onDropdownHidden: function(option, closed, select) {}
        });


        $.getAllDatesBetween = function (startDate, stopDate) {
            var dateArray = [];
            var datesHtml = [];
            var sd = moment(startDate).subtract(7, 'days');
            var en = moment(startDate).subtract(1, 'days');
            var currentDate = moment(sd);
            var enDate = moment(en);
            while (currentDate <= enDate) {
                dateArray.push( moment(currentDate).format('D') )
                var tip = moment(currentDate).format('MMM')+' '+moment(currentDate).format('YYYY')
                datesHtml.push( '<div class="date-box bg-light-gray" data-color="bg-black-active" title="'+tip+'">'+moment(currentDate).format('D')+'</div>' );
                currentDate = moment(currentDate).add(1, 'days');
            }


            var sd = moment(startDate);
            var en = moment(stopDate);
            var currentDate = moment(sd);
            var enDate = moment(en);
            while (currentDate <= enDate) {
                dateArray.push( moment(currentDate).format('D') )
                var tip = moment(currentDate).format('MMM')+' '+moment(currentDate).format('YYYY')
                datesHtml.push( '<div class="date-box bg-green" data-color="bg-green" title="'+tip+'">'+moment(currentDate).format('D')+'</div>' );
                currentDate = moment(currentDate).add(1, 'days');
            }

            var sd = moment(stopDate).add(1, 'days');
            var en = moment(stopDate).add(7, 'days');
            var currentDate = moment(sd);
            var enDate = moment(en);
            while (currentDate <= enDate) {
                dateArray.push( moment(currentDate).format('D') )
                var tip = moment(currentDate).format('MMM')+' '+moment(currentDate).format('YYYY')
                datesHtml.push( '<div class="date-box bg-light-gray" data-color="bg-black-active" title="'+tip+'">'+moment(currentDate).format('D')+'</div>' );
                currentDate = moment(currentDate).add(1, 'days');
            }

            // console.log(datesHtml)
            return datesHtml;
        }

        $('.view-scenario').on('click', function(event) {
            event.preventDefault();
            $('.x-axis').html('').css('border', 'none')
            $('.error-message').text('');
            $('.graphic').html('')

            var $inp_start_date = $('input[name=sel_start_date]'),
                $inp_end_date = $('input[name=sel_end_date]'),
                start_date = $inp_start_date.val(),
                end_date = $inp_end_date.val();

				//var datadi = datediff(start_date,end_date);
				//console.log(datadi);

            var users = new Array();
            users = $('#os_users_list').val();

            if(!start_date || !end_date) {
                $inp_start_date.parents('.parent-wrap').find('.error-message').text('Please select start and end dates.')
            }
            if(!users) {
                $('#os_users_list').parents('.select-wrap').find('.error-message').text('Please select at least one user.')
            }

            if(start_date && end_date && users){
                $('.graphic').html('<div class="loading-bar"></div>')
                var params = {
                    start_date: start_date,
                    end_date: end_date,
                    users: users,
                }
                $.ajax({
                    url: $js_config.base_url + 'work_centers/get_scenario',
                    type: 'POST',
                    dataType: 'json',
                    data: params,
                    success: function(response) {
                        $('.graphic').html('')
                        $('.x-axis').css('border', '1px solid #ccc')
                        var allDays = $.getAllDatesBetween(start_date,end_date);
						$.each(allDays,function(index, el) {
                            $('.x-axis').append(el)
                        });
                        $.availability_calendar(response);
                    }
                })
            }
        });

        // Clear filters
        $('.clear-scenario').on('click', function(event) {
            event.preventDefault();
            $('.sel-cal').val('').datepicker('destroy');
            calndr = $('.sel-cal').datepicker(dpick_opt);
            $('.disp-dates').text('Day, Month Year');
            $("#os_users_list").multiselect('clearSelection');
            $("#os_users_list").multiselect('rebuild');
            $('.graphic').html('');
            $('.x-axis').html('').css('border', 'none')
        });

        // Open datepicker on icons
        $('.trigger-cal').on('click', function(event) {
            event.preventDefault();
            var $input = $(this).parents('.dates_wraper:first').find('.sel-cal');

            $input.datepicker(dpick_opt).datepicker('show');
        });

        $('.value').each(function() {
            var text = $(this).data('width');
            $(this).parent().css('width', text);
        });

        $('.block.tipText').tooltip({placement: 'top', 'container': 'body'});



        function datediff(first, second) {
            var startDay = new Date(second);
                  var endDay = new Date(first);
                  endDay.setDate(endDay.getDate() - 1)

                  var millisecondsPerDay = 1000 * 60 * 60 * 24;

                  var millisBetween = startDay.getTime() - endDay.getTime();
                  var days = millisBetween / millisecondsPerDay;
                  return Math.floor(days)
        }

        function rangeDatediff(first, second) {
            var startDay = new Date(second);
                  var endDay = new Date(first);
                  endDay.setDate(endDay.getDate() - 1)

                  var millisecondsPerDay = 1000 * 60 * 60 * 24;

                  var millisBetween = startDay.getTime() - endDay.getTime();
                  var days = millisBetween / millisecondsPerDay;
                  return Math.floor(days)
        }

        $.chat_popover = function(data) {
            var popover_html = '<div class="chat-popover">';

            popover_html += '<p class="user-name">'+data.user_name+'</p>';
            popover_html += '<p class="job-title">'+data.job_title+'</p>';
            popover_html += '<p class="btn-wrap">';
                popover_html += '<a href="mailto:'+data.email+'?subject=Jeera" class="btn btn-primary chat_start_email btn-xs">Send Email</a>';
            popover_html += '</p>';

            popover_html += '</div>';

            return popover_html;
        }

        $.avail_popover = function(data) {
            var popover_html = '<div class="unavail-popover">';

            popover_html += '<div class="unavail-heading">Available:</div>';
            popover_html += '<p class="unavail-dates">'+data.dates+'</p>';

            popover_html += '</div>';

            return popover_html;
        }

        $.unavail_popover = function(data) {
            var popover_html = '<div class="unavail-popover">';

            popover_html += '<div class="unavail-heading">Unavailable:</div>';
            popover_html += '<p class="unavail-dates">'+data.dates+'</p>';

            popover_html += '</div>';

            return popover_html;
        }

        $.dates_popover = function(popover) {
            var popover_html = '<div class="activity-pop">';
            $.each(popover, function(index, data) {
                popover_html += '<div class="unavail-row">';
                    popover_html += '<div class="unavail-title">';
                        popover_html += data.title;
                    popover_html += '</div>';
                    popover_html += '<div class="permissions-element">';
                        popover_html += data.dates;
                    popover_html += '</div>';
                popover_html += '</div>';
            });
            return popover_html;
        }

        $.project_popover = function(popover) {
            var popover_html = '<div class="activity-pop">';
            $.each(popover, function(index, data) {
                popover_html += '<div class="project-row">';
                    popover_html += '<div class="project-title">';
                        popover_html += data.project_title;
                    popover_html += '</div>';
                    var typeHtml = data.type;
                    if(data.element_count){
                        typeHtml += " - " + data.element_count + " Tasks";
                    }
                    popover_html += '<div class="permissions-element">';
                        popover_html += typeHtml;
                    popover_html += '</div>';
                    if(data.chat_icon) {
                        popover_html += '<i class="fa fa-comment chat-proj chat_start_section" data-member="'+data.user_id+'" data-project="'+data.project_id+'" data-email="'+data.user+'"></i>';
                    }
                popover_html += '</div>';
            });
            return popover_html;
        }

        $.availability_calendar = function(data){
            var minWidth = 25;
            var first = data.range[0];
            var second = data.range[1];
            var dateDiff = rangeDatediff( first, second);
            var dateRange = dateDiff;
            var $scrollable = $('<div />', {'class': 'scrollable'});

            $.each(data['data'], function(index, data) {

                if(data.hasOwnProperty('blocks') && data.blocks != null){

                    var totalOfDates = 0;
                    var $row = $('<div />', {'class': 'chart-row'});

                    var $h6 = $('<h6 />');
                    var userData = {user_name: data.user_name, email: data.email, job_title: data.job_title, };
                    var chat_popover = $.chat_popover(userData);
                    var $userImage = $('<img />', {'class': 'img-circle chart-user', 'src': data.user});
                    $userImage.attr( 'data-content', chat_popover )
                    $userImage.appendTo($h6);
                    $h6.appendTo($row);

                    var $chart = $('<div />', {'class': 'chart'});
                    // $chart.css('width', '320px')
                    $chart.appendTo($row);

                    $.each(data.blocks, function(bindex, block) {
                        var blockDates = block.dates;
                        totalOfDates += datediff( blockDates[0], blockDates[1] );
                    })
                    var blockSpans = new Array();
                    var blockSpansWidth = 0;

                    $.each(data.blocks, function(bindex, block) {
                        var blockColor = block.clscolor;
                        var blockDates = block.dates;
                        var blockDateDiff = datediff( blockDates[0], blockDates[1] );
                        var blockStartDate = moment(blockDates[0], 'YYYY-MM-DD').format('DD, MMM YYYY');
                        var blockEndDate = moment(blockDates[1], 'YYYY-MM-DD').format('DD, MMM YYYY');
                        var hstr = ( blockDateDiff / totalOfDates ) * 100;

                        // var hstr = Math.round( ( blockDateDiff / totalOfDates ) * 100 );

                        var $blockSpan = $('<div />');

                        var blockClasses = 'block block-'+blockColor;
                        var blockTitle = '';
                        if(block.hasOwnProperty('project_popover')){
                            blockClasses += ' block-popover';
                            // blockTitle = blockDates.join(' - ');
                            blockTitle = data.user_name;
                            var popover_html = $.project_popover(block.project_popover);
                            // console.log('popover_html', popover_html)
                            $blockSpan.attr( 'data-content', popover_html )
                            $blockSpan.attr({'data-html': true, 'data-container': 'body'})
                        }
                        else if(block.hasOwnProperty('dates_popover')){
                            blockClasses += ' block-popover';
                            // blockTitle = blockDates.join(' - ');
                            blockTitle = data.user_name;
                            var popover_html = $.dates_popover(block.dates_popover);
                            // console.log('block popover', popover_html);
                            $blockSpan.attr( 'data-content', popover_html )
                            $blockSpan.attr({'data-html': true, 'data-container': 'body'})
                        }
                        else if(block.hasOwnProperty('unavail_popover')){
                            // blockClasses += ' tipText';
                            // blockTitle = blockStartDate+' - '+blockEndDate;
                            blockClasses += ' block-popover';
                            blockTitle = data.user_name;
                            var unavail_html = $.unavail_popover(block.unavail_popover);
                            // console.log('block popover', popover_html);
                            $blockSpan.attr( 'data-content', unavail_html )
                            $blockSpan.attr({'data-html': true, 'data-container': 'body'})
                        }
                        else if(block.hasOwnProperty('avail_popover')){
                            // blockClasses += ' tipText';
                            // blockTitle = blockStartDate+' - '+blockEndDate;
                            blockClasses += ' block-popover';
                            blockTitle = data.user_name;
                            var avail_html = $.avail_popover(block.avail_popover);
                            // console.log('block popover', popover_html);
                            $blockSpan.attr( 'data-content', avail_html )
                            $blockSpan.attr({'data-html': true, 'data-container': 'body'})
                        }
                        else {
                            blockClasses += ' tipText';
                            blockTitle = blockStartDate+' - '+blockEndDate;
                        }

                        $blockSpan.attr('class', blockClasses).attr('title', blockTitle).css('width', ((blockDateDiff <= 0) ? minWidth : (blockDateDiff*minWidth))+'px')

                        var $blockSpanValue = $('<span />', {'class': 'value'}).data('width', (blockDateDiff*minWidth));
                        blockSpansWidth += ((blockDateDiff <= 0) ? minWidth : (blockDateDiff*minWidth));
                        $blockSpanValue.appendTo($blockSpan);
                        blockSpans.push($blockSpan);
                    });

                    $chart.css('width', (dateRange*minWidth)+'px')
                    $chart.append(blockSpans)
                    // $('.graphic').css('width', (blockSpansWidth + 20)+'px')
                    $row.appendTo($scrollable);
                    setTimeout(function(){
                    },500)
                }

            });
            $scrollable.appendTo($('.graphic'));
            $scrollable.width($('.chart').width())
            $('.x-axis').width($('.chart').width() )

            var lines = '<div class="lines first"></div><div class="lines second" style="right:775px;"></div>';
            // $('.scrollable').append(lines);
            $('.chart-user').popover({
                placement : 'bottom',
                trigger : 'hover',
                html : true,
                container: 'body',
                delay: {show: 50, hide: 400}
            })
            $('.date-box').colored_tooltip();

        }


        $('body').delegate(".block-popover", 'mouseenter', function(e) {
            var $this = $(this);
            $(this).popover({
                placement : 'bottom',
                trigger : 'hover',
                html : true,
                container: 'body',
                delay: {show: 50, hide: 400}
            })
            $(this).popover('show');
            var data = $(this).data('bs.popover')
            var $popover = data.$tip;
            var $arrow = data.$arrow;
            var offset = $(this).offset();
            var left = e.pageX;
            var top = e.pageY;
            var theWidth = $popover.outerWidth();
            var theHeight = $popover.height();
            $arrow.hide();
            $popover.animate({
                    left: parseInt( left - (theWidth/2) ) + 'px',
                    top: parseInt(($this.offset().top + $this.height() - 8) ) + 'px',
                    // top: parseInt( (top+10) ) + 'px',
                }, 200, function(){
            })
            /*$popover.animate({
                    left: parseInt( (left+10) ) + 'px',
                    top: parseInt( (top - (theHeight/2)) ) + 'px',
                }, 200, function(){
            })*/
        })

        $('body').delegate(".block-popover", 'mouseleave', function(e) {
            setTimeout($.proxy(function(){
                $(this).popover('hide')
            }, 500),this)
        })

        $('body').delegate(".block-popover", 'mousemove', function(e) {
            var $this = $(this);
            var data = $(this).data('bs.popover')
            var $popover = data.$tip;
            var $arrow = data.$arrow;
            var offset = $(this).offset();
            var left = e.pageX;
            var top = e.pageY;
            var theWidth = $popover.outerWidth();
            var theHeight = $popover.height();
            // $arrow.hide();
            $popover.animate({
                    left: parseInt( left - (theWidth/2) ) + 'px',
                    top: parseInt( ($this.offset().top + $this.height() - 8) ) + 'px',
                }, 1 )
        })
    })
</script>