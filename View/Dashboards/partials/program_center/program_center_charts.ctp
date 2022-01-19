<?php
    $current_user_id = $this->Session->read('Auth.User.id');

    // project elements
    $element_keys = null;
    $els = $this->TaskCenter->userElements($current_user_id, $prg_prj_id);
    if (isset($els) && !empty($els)) {
        foreach ($els as $ekey => $evalue) {
            $wsp_area_studio_status = wsp_area_studio_status($evalue);
            if(!$wsp_area_studio_status) {
                $element_keys[] = $evalue;
            }
        }
    }
    $task_status = _elements_status($element_keys);

    $non = arraySearch($task_status, 'status', 'NON');
    $pnd = arraySearch($task_status, 'status', 'PND');
    $prg = arraySearch($task_status, 'status', 'PRG');
    $ovd = arraySearch($task_status, 'status', 'OVD');
    $cmp = arraySearch($task_status, 'status', 'CMP');

    $elements_signedoff = elements_signedoff($element_keys);
    $sgf = arraySearch($elements_signedoff, 'status', 'SGF');
	
    $totalNON = ( isset($non) && !empty($non) )? count($non) : 0;
    $totalCMP = ( isset($cmp) && !empty($cmp) )? count($cmp) : 0;
    $totalPND = ( isset($pnd) && !empty($pnd) )? count($pnd) : 0;
    $totalPRG = ( isset($prg) && !empty($prg) )? count($prg) : 0;
    $totalOVD = ( isset($ovd) && !empty($ovd) )? count($ovd) : 0;
    $total_tasks = $totalNON + $totalPND + $totalPRG + $totalOVD + $totalCMP;
    $nonPercent = $cmpPercent = $pndPercent = $prgPercent = $ovdPercent = $sgfPercent = 0;
    if(isset($total_tasks) && !empty($total_tasks)){
        $nonPercent = ($totalNON / $total_tasks) * 100;
        $cmpPercent = ($totalCMP / $total_tasks) * 100;
        $pndPercent = ($totalPND / $total_tasks) * 100;
        $prgPercent = ($totalPRG / $total_tasks) * 100;
        $ovdPercent = ($totalOVD / $total_tasks) * 100;
    }
     ?>
    <div class="bar-chart-info">
        <div class="summary-sec exposure">
            <div class="total-risk">Task Status</div>
            <div class="summary-info"><span class="summary-text">Overdue </span><span class="summary-count overdue"><?php echo (isset($ovd) && !empty($ovd)) ? count($ovd) : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">In Progress</span><span class="summary-count progressing"><?php echo (isset($prg) && !empty($prg)) ? count($prg) : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Not Started</span><span class="summary-count pending"><?php echo (isset($pnd) && !empty($pnd)) ? count($pnd) : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Completed</span><span class="summary-count signoff"><?php echo (isset($cmp) && !empty($cmp)) ? count($cmp) : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Not Set</span><span class="summary-count notstart"><?php echo (isset($non) && !empty($non)) ? count($non) : 0; ?></span> </div>
        </div>
        <div class="chat-data tasks">
            <div id="" class="count"><span style="">Count</span></div>
            <div class="task-chart"></div>
            <h6>Task Status</h6>
        </div>
    </div>
    <?php
        $selectedProject = (isset($selected_project_id) && !empty($selected_project_id)) ? $selected_project_id : 0;
        $lowRisks = risk_by_exposer_type($prg_prj_id, 'low');
        $mediumRisks = risk_by_exposer_type($prg_prj_id, 'medium');
        $highRisks = risk_by_exposer_type($prg_prj_id, 'high');
        $severeRisks = risk_by_exposer_type($prg_prj_id, 'severe');
        $risk_without_exposure = risk_no_exposure($prg_prj_id, 'severe');

        $total_risks = $lowRisks + $mediumRisks + $highRisks + $severeRisks + $risk_without_exposure;
        $lowPercent = $mediumPercent = $highPercent = $severePercent = $withoutPercent = 0;
        if(isset($total_risks) && !empty($total_risks)){
            $lowPercent = ($lowRisks/$total_risks)*100;
            $mediumPercent = ($mediumRisks/$total_risks)*100;
            $highPercent = ($highRisks/$total_risks)*100;
            $severePercent = ($severeRisks/$total_risks)*100;
            $withoutPercent = ($risk_without_exposure/$total_risks)*100;
        }
     ?>
    <div class="bar-chart-info bar-chart-info-right">
        <!-- <div class="all-risks">
            <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
            <a href="#" class="btn btn-sm btn-primary btn-my-risk" style="display: none;">My Risks in Projects</a>
            <a href="#" class="btn btn-sm btn-success btn-all-risk" >All Risks in Projects</a>
        </div> -->
        <div class="charts">
            <?php echo $this->element('../Dashboards/partials/program_center/all_risk_charts'); ?>
        </div>
        <!-- <div class="summary-sec exposure">
            <div class="total-risk">Risks Exposure</div>
            <div class="summary-info"><span class="summary-text">Severe </span><span class="summary-count severe"><?php echo (isset($severeRisks) && !empty($severeRisks)) ? $severeRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">High</span><span class="summary-count high"><?php echo (isset($highRisks) && !empty($highRisks)) ? $highRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Medium</span><span class="summary-count medium"><?php echo (isset($mediumRisks) && !empty($mediumRisks)) ? $mediumRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Low</span><span class="summary-count low"><?php echo (isset($lowRisks) && !empty($lowRisks)) ? $lowRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">No Start</span><span class="summary-count no-start"><?php echo (isset($risk_without_exposure) && !empty($risk_without_exposure)) ? $risk_without_exposure : 0; ?></span> </div>
        </div>
        <div class="chat-data risk">
            <div id="" class="count"><span style="">Count</span></div>
            <div class="risk-chart"></div>
            <h6>EXPOSURE STATUS</h6>
        </div> -->
    </div>
<script type="text/javascript">
    $(function(){
        var program_id = '<?php echo $program_id; ?>';
        var selectedProject = '<?php echo $selectedProject; ?>';
        var allProjects = '<?php echo json_encode($prg_prj_id); ?>';
        // console.log(allProjects)
        $.tasks_chart = function() {
            var values = [ '<?php echo $nonPercent; ?>', '<?php echo $cmpPercent; ?>', '<?php echo $pndPercent; ?>', '<?php echo $prgPercent; ?>', '<?php echo $ovdPercent; ?>' ];
            var total = [ '<?php echo $totalNON; ?>', '<?php echo $totalCMP; ?>', '<?php echo $totalPND; ?>', '<?php echo $totalPRG; ?>', '<?php echo $totalOVD; ?>' ];
            var options =
            {
                progress_text: [ 'NON','CMP','PND','PRG','OVD'],
                bg_classes: ['gray','green','pending','yellow','red' ],
                borders: [ '#A6A6A6','#00B050','#948a54','#FFC000','#FF0000' ],
            }

            var $wrapper = $('<div style="vertical-align: bottom;" id="chart_wrapper" class="chart_wrapper"></div>');
            // $element.append($wrapper);
                var n = 0,
                    bar_left = 0;
                for (i = 0; i < values.length; i++) {
                    var x = (60*i) + 30,
                        h = parseFloat(values[i]).toFixed(2),
                        hstr = (Math.round( values[i] * 100 )/100 ),
                        hstr = hstr.toFixed(2)
                        hout = h+2,
                        to_px = '';

                    bar_left = (70 * i) + 15,
                    // bar_left = (53 * i) + 15,
                    top_px = '-30px';
                    to_px = '40%';

                    total_css = '';
                    if(values[i] <= 12){
                        total_css = 'bar-top';
                    }

                    var link = '#',
                        tip = '',
                        //chart_link = $js_config.base_url + 'dashboards/task_center/';
                        chart_link = $js_config.task_centers;

                    if(selectedProject != 0) {
                        chart_link += selectedProject + '/';
                    }
                    else{
                        chart_link += '0/program:' + program_id + '/';
                    }
                    if( options.progress_text[i] == 'NON' ) {
                        chart_link += 'status:4';
                        link = chart_link;
                        // link = $js_config.base_url + 'dashboards/task_center/status:4/program:'+program_id;
                        // tip = 'None Set';
                        tip = total[i];
                    }
                    if( options.progress_text[i] == 'CMP' ) {
                        chart_link += 'status:5';
                        link = chart_link;
                        // link = $js_config.base_url + 'dashboards/task_center/status:5/program:'+program_id;
                        // tip = 'Low';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'PND' ) {
                        chart_link += 'status:6';
                        link = chart_link;
                        // link = $js_config.base_url + 'dashboards/task_center/status:6/program:'+program_id;
                        link = $js_config.task_centers + 'status:6/program:'+program_id;
                        // tip = 'Medium';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'PRG' ) {
                        chart_link += 'status:7';
                        link = chart_link;
                        // link = $js_config.base_url + 'dashboards/task_center/status:7/program:'+program_id;
                        // tip = 'High';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'OVD' ) {
                        chart_link += 'status:1';
                        link = chart_link;
                        // link = $js_config.base_url + 'dashboards/task_center/status:1/program:'+program_id;
                        // tip = 'Severe';
                        tip = total[i];
                    }
                    var css = '';
                    if(tip == 0){
                        link = 'javascript:;';
                        css = 'cursor: default;';
                    }

                    var anchor_start = '<a href="'+link+'" class="chart-links tipText " data-toggle="tooltip" data-original-title="'+tip+'" style="'+css+'">',
                        anchor_end = '</a>',
                        main_start = '<div class="main_bar outer-bar-'+i+'" style="left: '+bar_left+'px; height: '+hout+'%; border: 1px solid '+options.borders[i]+'; border-bottom: none;">',
                        main_end = '</div>',
                        middle = '<div class="back-'+options.bg_classes[i]+' bar_middle" data-height="'+hstr+'%" style=""></div>',
                        scale_text = '<div class="status_text">'+options.progress_text[i]+'</div>',
                        percentage_text = '<div style="top: '+top_px+'; " class="percentage_text">'+hstr+'%</div>',
                        total_text = '<div class="total_text '+total_css+'">'+total[i]+'</div>';

                    if( h <= 7 ) {
                        total_text = '';
                    }
                    total_text = '';

                    var all = main_start + anchor_start + middle + total_text + anchor_end + scale_text +/* percentage_text + */main_end;

                    $wrapper.append(all);

                }
            return $wrapper;
        }
        $.exposure_chart = function() {
            var values = [ '<?php echo $withoutPercent; ?>', '<?php echo $lowPercent; ?>', '<?php echo $mediumPercent; ?>', '<?php echo $highPercent; ?>', '<?php echo $severePercent; ?>' ];
            var total = [ '<?php echo $risk_without_exposure; ?>', '<?php echo $lowRisks; ?>', '<?php echo $mediumRisks; ?>', '<?php echo $highRisks; ?>', '<?php echo $severeRisks; ?>' ];
            var options =
            {
                progress_text: [ 'NON','LOW','MED','HGH','SEV'],
                bg_classes: ['gray','green','yellow','dark-red','red' ],
                borders: [ '#A6A6A6','#00B050','#FFC000','#c00000','#FF0000' ],
            }

            var $wrapper = $('<div style="vertical-align: bottom;" id="chart_wrapper" class="chart_wrapper"></div>');
            // $element.append($wrapper);
                var n = 0,
                    bar_left = 0;
                for (i = 0; i < values.length; i++) {
                    var x = (60*i) + 30,
                        h = parseFloat(values[i]).toFixed(2),
                        hstr = (Math.round( values[i] * 100 )/100 ),
                        hstr = hstr.toFixed(2)
                        hout = h+2,
                        to_px = '';

                    bar_left = (70 * i) + 15,
                    // bar_left = (53 * i) + 15,
                    top_px = '-30px';
                    to_px = '40%';

                    total_css = '';
                    if(values[i] <= 12){
                        total_css = 'bar-top';
                    }

                    var link = '#',
                        tip = '',
                        chart_link = $js_config.base_url + 'risks/index/';

                    if(selectedProject != 0) {
                        chart_link += selectedProject + '/';
                    }
                    else{
                        chart_link += '0/program:' + program_id + '/';
                    }
                    if( options.progress_text[i] == 'NON' ) {
                        // link = $js_config.base_url + 'risks/index/0/exposure:low';
                        // tip = 'None Set';
                        tip = total[i];
                    }
                    if( options.progress_text[i] == 'LOW' ) {
                        chart_link += 'exposure:low';
                        link = chart_link;
                        // console.log('chart_link', chart_link)
                        // link = $js_config.base_url + 'risks/index/0/exposure:low/program:'+program_id;
                        // tip = 'Low';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'MED' ) {
                        chart_link += 'exposure:medium';
                        link = chart_link;
                        // link = $js_config.base_url + 'risks/index/0/exposure:medium/program:'+program_id;
                        // tip = 'Medium';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'HGH' ) {
                        chart_link += 'exposure:high';
                        link = chart_link;
                        // link = $js_config.base_url + 'risks/index/0/exposure:high/program:'+program_id;
                        // tip = 'High';
                        tip = total[i];
                    }
                    else if( options.progress_text[i] == 'SEV' ) {
                        chart_link += 'exposure:severe';
                        link = chart_link;
                        // link = $js_config.base_url + 'risks/index/0/exposure:severe/program:'+program_id;
                        // tip = 'Severe';
                        tip = total[i];
                    }
                    var css = '';
                    if(tip == 0){
                        link = 'javascript:;';
                        css = 'cursor: default;';
                    }

                    var anchor_start = '<a href="'+link+'" class="chart-links tipText " data-toggle="tooltip" data-original-title="'+tip+'" style="'+css+'">',
                        anchor_end = '</a>',
                        main_start = '<div class="main_bar outer-bar-'+i+'" style="left: '+bar_left+'px; height: '+hout+'%; border: 1px solid '+options.borders[i]+'; border-bottom: none;">',
                        main_end = '</div>',
                        middle = '<div class="back-'+options.bg_classes[i]+' bar_middle" data-height="'+hstr+'%" style=""></div>',
                        scale_text = '<div class="status_text">'+options.progress_text[i]+'</div>',
                        percentage_text = '<div style="top: '+top_px+'; " class="percentage_text">'+hstr+'%</div>',
                        total_text = '<div class="total_text '+total_css+'">'+total[i]+'</div>';

                    if( h <= 7 ) {
                        total_text = '';
                    }
                    total_text = '';

                    var all = main_start + anchor_start + middle + total_text + anchor_end + scale_text +/* percentage_text + */main_end;

                    $wrapper.append(all);

                }
            return $wrapper;
        }


        $.loader_icon = $('.loader-icon');

        $('.btn-all-risk').click(function(event){
            event.preventDefault();
            $.loader_icon.removeClass('stop');
            $(this).hide();
            $('.btn-my-risk').show();
            var $parent = $(this).parents('.panel:first');
            var params = {
                program_id: program_id,
                selected_project_id: selectedProject,
                projects: allProjects
            }
            $.ajax({
                url: $js_config.base_url + 'dashboards/all_risk_charts',
                type: 'POST',
                data: params,
                success: function(response){
                    $('.bar-chart-info-right .charts').html(response);
                    $('.bar-chart-info-right .risk-chart.chart-all', $parent).html($.exposure_chart_all());
                    $.loader_icon.addClass('stop');
                }
            })
        })

        $('.btn-my-risk').click(function(event){
            event.preventDefault();
            $.loader_icon.removeClass('stop');
            $(this).hide();
            $('.btn-all-risk').show();
            var $parent = $(this).parents('.panel:first');
            var params = {
                program_id: program_id,
                selected_project_id: selectedProject,
                projects: allProjects
            }
            $.ajax({
                url: $js_config.base_url + 'dashboards/my_risk_charts',
                type: 'POST',
                data: params,
                success: function(response){
                    $('.bar-chart-info-right .charts').html(response);
                    $('.bar-chart-info-right .risk-chart.chart-my', $parent).html($.exposure_chart_my());
                    $.loader_icon.addClass('stop');
                }
            })
        })
        // $('.chat-data.risk .chart-target').html($.exposure_chart())
    })
</script>