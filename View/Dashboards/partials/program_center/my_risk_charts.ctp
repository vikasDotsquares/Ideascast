<?php
    $current_user_id = $this->Session->read('Auth.User.id');

    $selectedProject = (isset($selected_project_id) && !empty($selected_project_id)) ? $selected_project_id : 0;
    $prg_prj_id = (isset($selectedProject) && !empty($selectedProject)) ? $selectedProject : $prg_prj_id;
    
    $lowRisks = selected_risk_by_exposer_type($prg_prj_id, 'low');
    $mediumRisks = selected_risk_by_exposer_type($prg_prj_id, 'medium');
    $highRisks = selected_risk_by_exposer_type($prg_prj_id, 'high');
    $severeRisks = selected_risk_by_exposer_type($prg_prj_id, 'severe');
    $risk_without_exposure = selected_risk_no_exposure($prg_prj_id, 'severe');

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
        <!-- <div class="all-risks">
            <a href="#" class="btn btn-sm btn-success btn-all-risk">All Risks in Projects</a>
        </div> -->
        <div class="summary-sec exposure">
            <div class="total-risk">Risks Exposure</div>
            <div class="summary-info"><span class="summary-text">Severe </span><span class="summary-count severe"><?php echo (isset($severeRisks) && !empty($severeRisks)) ? $severeRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">High</span><span class="summary-count high"><?php echo (isset($highRisks) && !empty($highRisks)) ? $highRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Medium</span><span class="summary-count medium"><?php echo (isset($mediumRisks) && !empty($mediumRisks)) ? $mediumRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">Low</span><span class="summary-count low"><?php echo (isset($lowRisks) && !empty($lowRisks)) ? $lowRisks : 0; ?></span> </div>
            <div class="summary-info"><span class="summary-text">No Start</span><span class="summary-count no-start"><?php echo (isset($risk_without_exposure) && !empty($risk_without_exposure)) ? $risk_without_exposure : 0; ?></span> </div>
        </div>
        <div class="chat-data risk all">
            <div id="" class="count"><span style="">Count</span></div>
            <div class="risk-chart chart-my"></div>
            <h6>EXPOSURE STATUS</h6>
        </div>

<script type="text/javascript">
    $(function(){
        var program_id = '<?php echo $program_id; ?>';
        var selectedProject = '<?php echo $selectedProject; ?>';
        
        $.exposure_chart_my = function() {
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

                    var anchor_start = '<a href="'+link+'" class="chart-links tipText " data-toggle="tooltip" data-original-title="'+tip+'">',
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
        // $('.chat-data.risk .chart-target').html($.exposure_chart_all())
    })
</script>