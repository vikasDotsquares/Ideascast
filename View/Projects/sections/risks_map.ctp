<?php
$project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;

$user_risks1 = my_risks($this->Session->read('Auth.User.id'), $project_id);
if(isset($user_risks1) && !empty($user_risks1)){
    $user_risks1 = Set::extract($user_risks1, '{n}.rd.rid');
    $user_risks1 = implode(',', $user_risks1);
}
$exposer_risk = exposer_risk($user_risks1);
$map_data = [];
if(isset($exposer_risk) && !empty($exposer_risk)){
    foreach ($exposer_risk as $key => $value) {
        $map_data[$value['map']['imp'].'_'.$value['map']['per']][] = ['status' => $value['map']['rd_status'], 'risks' => $value['map']['all_risks']];
    }
}
$rows = [
    'ALMOST CERTAIN' => [
            '0' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '1',
                'col' => '5',
            ],
            '1' => [
                'class' => 'red',
                'text' => 'HIGH',
                'row' => '2',
                'col' => '5',
            ],
            '2' => [
                'class' => 'red',
                'text' => 'HIGH',
                'row' => '3',
                'col' => '5',
            ],
            '3' => [
                'class' => 'red-light',
                'text' => 'SEVERE',
                'row' => '4',
                'col' => '5',
            ],
            '4' => [
                'class' => 'red-light',
                'text' => 'SEVERE',
                'row' => '5',
                'col' => '5',
            ]
        ],
    'Likely' => [
            '0' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '1',
                'col' => '4',
            ],
            '1' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '2',
                'col' => '4',
            ],
            '2' => [
                'class' => 'red',
                'text' => 'HIGH',
                'row' => '3',
                'col' => '4',
            ],
            '3' => [
                'class' => 'red',
                'text' => 'HIGH',
                'row' => '4',
                'col' => '4',
            ],
            '4' => [
                'class' => 'red-light',
                'text' => 'SEVERE',
                'row' => '5',
                'col' => '4',
            ]
        ],
    'POSSIBLE' => [
            '0' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '1',
                'col' => '3',
            ],
            '1' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '2',
                'col' => '3',
            ],
            '2' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '3',
                'col' => '3',
            ],
            '3' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '4',
                'col' => '3',
            ],
            '4' => [
                'class' => 'red',
                'text' => 'HIGH',
                'row' => '5',
                'col' => '3',
            ]
        ],
    'UNLIKELY' => [
            '0' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '1',
                'col' => '2',
            ],
            '1' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '2',
                'col' => '2',
            ],
            '2' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '3',
                'col' => '2',
            ],
            '3' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '4',
                'col' => '2',
            ],
            '4' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '5',
                'col' => '2',
            ]
        ],
    'RARE' => [
            '0' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '1',
                'col' => '1',
            ],
            '1' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '2',
                'col' => '1',
            ],
            '2' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '3',
                'col' => '1',
            ],
            '3' => [
                'class' => 'green-light',
                'text' => 'LOW',
                'row' => '4',
                'col' => '1',
            ],
            '4' => [
                'class' => 'yellow',
                'text' => 'MEDIUM',
                'row' => '5',
                'col' => '1',
            ]
        ],
];
?>

        <div class="projectriskmap">
            <div class="projectriskmapscroll">
                <div class="projectriskmap-heading">
                    <div class="projectriskbg col-one"></div>
                    <div class="projectriskbg col-two">Negligible</div>
                    <div class="projectriskbg col-three">Minor</div>
                    <div class="projectriskbg col-four">Moderate</div>
                    <div class="projectriskbg col-five">Major</div>
                    <div class="projectriskbg col-six">Critical</div>
                </div>
                <?php
            foreach ($rows as $key1 => $value1) {
                 ?>
                <div class="projectriskmap-contant">
                    <div class="projectriskbg col-one lgray"><?php echo $key1; ?></div>
                    <?php
                        foreach ($value1 as $map_key => $map_value) {
                            $section = $map_value['row'].'_'.$map_value['col'];
                            $exists = array_key_exists($section, $map_data);
                            $total_satatues = 0;
                            $total_open = $total_review = $total_signoff = $total_overdue = 0;
                            $open_status = $review_status = $signoff_status = $overdue_status = null;
                            $popup_open = $popup_review = $popup_signoff = $popup_overdue = '';
                            if($exists) {

                                $open_data = arraySearch($map_data[$section], 'status', 'Open');
                                $prg_data = arraySearch($map_data[$section], 'status', 'Progress');
                                $soff_data = arraySearch($map_data[$section], 'status', 'SignOff');
                                $ovd_data = arraySearch($map_data[$section], 'status', 'Overdue');

                                $open_status = (!empty($open_data[0]['risks'])) ? json_decode($open_data[0]['risks'], true) : [];
                                $review_status = (!empty($prg_data[0]['risks'])) ? json_decode($prg_data[0]['risks'], true) : [];
                                $signoff_status = (!empty($soff_data[0]['risks'])) ? json_decode($soff_data[0]['risks'], true) : [];
                                $overdue_status = (!empty($ovd_data[0]['risks'])) ? json_decode($ovd_data[0]['risks'], true) : [];

                                $total_open = (isset($open_status) && !empty($open_status)) ? count($open_status) : 0;
                                $total_review = (isset($review_status) && !empty($review_status)) ? count($review_status) : 0;
                                $total_signoff = (isset($signoff_status) && !empty($signoff_status)) ? count($signoff_status) : 0;
                                $total_overdue = (isset($overdue_status) && !empty($overdue_status)) ? count($overdue_status) : 0;


                                $total_satatues = $total_open + $total_review + $total_signoff + $total_overdue;

                                if(isset($open_status) && !empty($open_status)){
                                    $popup_open = '<div class="risk_popup">';
                                    foreach ($open_status as $key => $value) {
                                        $risk_id = $value['id'];
                                        $risk_title = htmlentities(htmlspecialchars($value['title']), ENT_QUOTES, "UTF-8");
                                        // $risk_title = htmlentities($value['title'], ENT_QUOTES, "UTF-8");
                                        $popup_open .= '<a class="risk_detail" href="'.Router::url(['controller' => 'risks', 'action' => 'index', $value['project_id'], 'risk' => $risk_id, 'admin' => false], true).'">'.$risk_title.'</a>';
                                    }
                                    $popup_open .= '</div>';
                                }
                                if(isset($review_status) && !empty($review_status)){
                                    $popup_review = '<div class="risk_popup">';
                                    foreach ($review_status as $key => $value) {
                                        $risk_id = $value['id'];
                                        $risk_title = htmlentities(htmlspecialchars($value['title']), ENT_QUOTES, "UTF-8");
                                        $popup_review .= '<a class="risk_detail" href="'.Router::url(['controller' => 'risks', 'action' => 'index', $value['project_id'], 'risk' => $risk_id, 'admin' => false], true).'">'.$risk_title.'</a>';
                                    }
                                    $popup_review .= '</div>';
                                }
                                if(isset($signoff_status) && !empty($signoff_status)){
                                    $popup_signoff = '<div class="risk_popup">';
                                    foreach ($signoff_status as $key => $value) {
                                        $risk_id = $value['id'];
                                        $risk_title = htmlentities(htmlspecialchars($value['title']), ENT_QUOTES, "UTF-8");
                                        $popup_signoff .= '<a class="risk_detail" href="'.Router::url(['controller' => 'risks', 'action' => 'index', $value['project_id'], 'risk' => $risk_id, 'admin' => false], true).'">'.$risk_title.'</a>';
                                    }
                                    $popup_signoff .= '</div>';
                                }
                                if(isset($overdue_status) && !empty($overdue_status)){
                                    $popup_overdue = '<div class="risk_popup">';
                                    foreach ($overdue_status as $key => $value) {
                                        $risk_id = $value['id'];
                                        $risk_title = htmlentities(htmlspecialchars($value['title']), ENT_QUOTES, "UTF-8");
                                        $popup_overdue .= '<a class="risk_detail" href="'.Router::url(['controller' => 'risks', 'action' => 'index', $value['project_id'], 'risk' => $risk_id, 'admin' => false], true).'">'.$risk_title.'</a>';
                                    }
                                    $popup_overdue .= '</div>';
                                }
                            }
                             ?>
                            <div class="projectriskbg <?php echo $map_value['class']; ?> <?php if(!$exists){ ?>mapopacity<?php } ?>">
                                <div class="whitebg">
                                    <?php if($exists) { ?>
                                    <span class="risk_heading"><?php echo $map_value['text']; ?>: <?php echo $total_satatues; ?></span>
                                    <?php } ?>

                                    <?php if(isset($total_open) && !empty($total_open)) { ?>
                                    <span>
                                        <span <?php if(isset($popup_open) && !empty($popup_open)) { ?> class="risk_popover" title="Status: Open" data-content='<?php echo $popup_open; ?>' <?php } ?>>Open: <?php echo $total_open; ?></span>
                                    </span>
                                    <?php } ?>
                                    <?php if(isset($total_review) && !empty($total_review)) { ?>
                                    <span>
                                        <span <?php if(isset($popup_review) && !empty($popup_review)) { ?> class="risk_popover" title="Status: In Progress" data-content='<?php echo $popup_review; ?>' <?php } ?>>In Progress: <?php echo $total_review; ?></span>
                                    </span>
                                    <?php } ?>
                                    <?php if(isset($total_signoff) && !empty($total_signoff)) { ?>
                                    <span>
                                        <span <?php if(isset($popup_signoff) && !empty($popup_signoff)) { ?> class="risk_popover" title="Status: Completed" data-content='<?php echo $popup_signoff; ?>' <?php } ?>>Completed: <?php echo $total_signoff; ?></span>
                                    </span>
                                    <?php } ?>
                                    <?php if(isset($total_overdue) && !empty($total_overdue)) { ?>
                                    <span>
                                        <span <?php if(isset($popup_overdue) && !empty($popup_overdue)) { ?> class="risk_popover" title="Status: Overdue" data-content='<?php echo $popup_overdue; ?>' <?php } ?>>Overdue: <?php echo $total_overdue; ?></span>
                                    </span>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php

                        }
                         ?>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <?php

            $total_open = $total_review = $total_signoff = $total_overdue = 0;
            $risk_details = risk_details($user_risks1);

            if(isset($risk_details) && !empty($risk_details)) {
                $rbeat = [];
                foreach ($risk_details as $key => $value) {
                    $rbeat[] = $value[0];
                }
				 
                $open_status = arraySearch($rbeat, 'rd_status', 1);
                $review_status = arraySearch($rbeat, 'rd_status', 2);
                $signoff_status = arraySearch($rbeat, 'rd_status', 3);
                $overdue_status = arraySearch($rbeat, 'rd_status', 4);

                $total_open = (isset($open_status) && !empty($open_status)) ? count($open_status) : 0;
                $total_review = (isset($review_status) && !empty($review_status)) ? count($review_status) : 0;
                $total_signoff = (isset($signoff_status) && !empty($signoff_status)) ? count($signoff_status) : 0;
                $total_overdue = (isset($overdue_status) && !empty($overdue_status)) ? count($overdue_status) : 0;
            }

            $lowRisks = exposer_type_risks($user_risks1, 'low');
            $mediumRisks = exposer_type_risks($user_risks1, 'medium');
            $highRisks = exposer_type_risks($user_risks1, 'high');
            $severeRisks = exposer_type_risks($user_risks1, 'severe');

            $total_risks = $lowRisks + $mediumRisks + $highRisks + $severeRisks;
            $lowPercent = $mediumPercent = $highPercent = $severePercent = 0;
            if(isset($total_risks) && !empty($total_risks) && !empty($user_risks1)){
                $lowPercent = ($lowRisks/$total_risks)*100;
                $mediumPercent = ($mediumRisks/$total_risks)*100;
                $highPercent = ($highRisks/$total_risks)*100;
                $severePercent = ($severeRisks/$total_risks)*100;
            }
        ?>
        <div class="map-summary">
            <div class="summary-title">
                Summary
            </div>
            <div class="summary-container">
                <div class="summary-sec">
                    <div class="total-risk">Risks Status</div>
                    <div class="summary-info"><span class="summary-text">Open</span><span class="summary-count"><?php echo $total_open; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">In Progress</span><span class="summary-count"><?php echo $total_review; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">Overdue</span><span class="summary-count"><?php echo $total_overdue; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">Completed</span><span class="summary-count"><?php echo $total_signoff; ?></span> </div>
                </div>
                <div class="summary-sec exposure">
                    <div class="total-risk">Risks Exposure</div>
                    <div class="summary-info"><span class="summary-text">Severe </span><span class="summary-count severe"><?php echo $severeRisks; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">High</span><span class="summary-count high"><?php echo $highRisks; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">Medium</span><span class="summary-count medium"><?php echo $mediumRisks; ?></span> </div>
                    <div class="summary-info"><span class="summary-text">Low</span><span class="summary-count low"><?php echo $lowRisks; ?></span> </div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chat-header">Exposure Chart</div>
                <div class="chat-data">
                    <div id="" class="count"><span style="">Count</span></div>
                    <div class="chart-target"></div>
                </div>
            </div>

        </div>
<?php
$url_project = $project_id;
?>
<script type="text/javascript">
    $(function(){
        $('.risk_popover').popover({
            // placement : 'bottom',
            placement: function(context, element) {
                var position = $(element).offset();
                if (position.top > ($(document).height() - 115)) {
                // if ($(document).height() - position.top < 220) {
                    return "top";
                }
                return "bottom";
            },
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
        .on('show.bs.popover', function(){
            var data = $(this).data(),
                bs = data['bs.popover'];
                $tip = bs.$tip;

            $tip.css({'max-width': '210px', 'min-width': '210px'});
            $tip.find('.popover-title').css({'text-align': 'center'});
        });

        $.exposure_chart = function() {
            var values = [ '<?php echo $lowPercent; ?>', '<?php echo $mediumPercent; ?>', '<?php echo $highPercent; ?>', '<?php echo $severePercent; ?>' ];
            var total = [ '<?php echo $lowRisks; ?>', '<?php echo $mediumRisks; ?>', '<?php echo $highRisks; ?>', '<?php echo $severeRisks; ?>' ];
            var url_project = '<?php echo (isset($url_project) && !empty($url_project)) ? $url_project : 0; ?>';
            var options =
            {
                progress_text: [ 'LOW','MED','HGH','SEV'],
                bg_classes: ['green','yellow','dark-red','red' ],
                borders: [ '#5f9322','#ffc000','#8a0000','#e5030d' ],
            }

            var $wrapper = $('<div style="vertical-align: bottom;" id="chart_wrapper" class="chart_wrapper"></div>');
            // $element.append($wrapper);
                var n = 0,
                    bar_left = 0;
                for (i = 0; i < values.length; i++) {
                    var x = (60*i)+30,
                        h = parseFloat(values[i]).toFixed(2),
                        hstr = (Math.round( values[i] * 100 )/100 ),
                        hstr = hstr.toFixed(2)
                        hout = h+2,
                        to_px = '';

                    bar_left = (60 * i) + 15,
                    // bar_left = (53 * i) + 15,
                    top_px = '-30px';
                    to_px = '40%';

                    total_css = '';
                    if(values[i] <= 12){
                        total_css = 'bar-top';
                    }

                    var link = '#',
                        tip = '',
                        data_val = '';
                    if( options.progress_text[i] == 'LOW' ) {
                        link = $js_config.base_url + 'risks/index/'+url_project+'/exposure:low';
                        tip = 'Low';
                        data_val = 'low';
                    }
                    else if( options.progress_text[i] == 'MED' ) {
                        link = $js_config.base_url + 'risks/index/'+url_project+'/exposure:medium';
                        tip = 'Medium';
                        data_val = 'medium';
                    }
                    else if( options.progress_text[i] == 'HGH' ) {
                        link = $js_config.base_url + 'risks/index/'+url_project+'/exposure:high';
                        tip = 'High';
                        data_val = 'high';
                    }
                    else if( options.progress_text[i] == 'SEV' ) {
                        link = $js_config.base_url + 'risks/index/'+url_project+'/exposure:severe';
                        tip = 'Severe';
                        data_val = 'severe';
                    }
                    var style = '';
                    if(options.bg_classes[i] == 'yellow') {
                        style = 'style="color:#333"';
                    }
                    var anchor_start = '<a href="#" class="chart-links tipText" data-exp="'+data_val+'" data-toggle="tooltip" data-original-title="'+tip+'">',
                        anchor_end = '</a>',
                        main_start = '<div class="main_bar" style="left: '+bar_left+'px; height: '+hout+'%; border: 1px solid '+options.borders[i]+'; border-bottom: none;">',
                        main_end = '</div>',
                        middle = '<div class="back-'+options.bg_classes[i]+' bar_middle" data-height="'+hstr+'%" style=""></div>',
                        scale_text = '<div class="status_text">'+options.progress_text[i]+'</div>',
                        percentage_text = '<div style="top: '+top_px+'; " class="percentage_text">'+hstr+'%</div>',
                        total_text = '<div class="total_text '+total_css+'" '+style+'>'+total[i]+'</div>';

                    if( h <= 0 ) {
                        total_text = '';
                    }

                    var all = main_start + anchor_start + middle + total_text + anchor_end + scale_text +/* percentage_text + */main_end;

                    $wrapper.append(all);

                }
            return $wrapper;
        }
        $('.chart-target').html($.exposure_chart())
        setTimeout(function(){
            // $('.bar_middle').css('height', '99%')
            $('.bar_middle').each(function(i, e) {
                // $(this).animate({height: '99%'}, i * 300);
            })
        }, 1)

        $('.chart-links').on('click', function(event) {
            event.preventDefault();
            var $parent = $("#tab_risk");
            var exposure = $(this).data("exp");
            $('#dd_risk_types').val('');
            $('#dd_statuses').val('');
            $('#dd_impacts').val('');
            $('#dd_percentages').val('');
            $('#dd_exposers').val('');
            $('#dd_exposers').val(exposure).trigger('change');
            $('.risk-switch.detail').trigger('click');
        });

    })
</script>