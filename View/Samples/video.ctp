<?php
$testData = [
    "0" => [
        "In_progress" => [
            "0" => [
                "title" => "Task title 2 Added by PA2",
                "count" => 60
            ],
            "1" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
            "2" => [
                "title" => "Task title 2 Added by PA2",
                "count" => 60
            ],
            "3" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
            "4" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
            "5" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
            "6" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
            "7" => [
                "title" => "Added Task By Project Admin On 28 Dec 2021",
                "count" => 20
            ],
        ],
        "Not_commenced" => [
            "0" => [
                "title" => "Task 1",
                "count" => 50
            ],
            "1" => [
                "title" => "Added Task 2",
                "count" => 30
            ],
        ],
        "completed" => [
            "0" => [
                "title" => "Task 123",
                "count" => 10
            ],
        ],
    ],
    "category" => [
        'name' => 'Task Category 2 updated'
    ]
];

 ?>
<div class="row">
    <div class="col-xs-12">

            <section class="main-heading-wrap">
                <div class="main-heading-sec">
                <h1>Samples</h1>
                <div class="subtitles">Create & Check your sample pages here</div>
                </div>

                <div class="header-right-side-icon">
                <div class=""><a class="" href="#">hh</a></div>
                <div class=""><a class="" href="#">hh</a></div>
                </div>
            </section>
        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                        </div>
                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">

                            <div class="chart-container">
                                <div class="chat-header">Exposure Chart</div>
                                <div class="chat-data">
                                    <div id="" class="count"><span style="">Count</span></div>
                                    <div class="chart-target"></div>
                                </div>
                            </div>


                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(() => {
        var data = <?php echo json_encode($testData); ?>;
        ;($.exposure_chart = function() {
            var tasks = data[0];

            var options = {
                bg_classes: ['green', 'orange', 'red' ]
            }

            var $wrapper = $('<div style="vertical-align: bottom;" id="chart_wrapper" class="chart_wrapper"></div>');
            var bar_left = 0;
            var eachCount = 0;
            $.each(tasks, function(index, el) {
                var bg_color = options.bg_classes[eachCount++];
                for (var i = 0; i < tasks[index].length; i++) {
                    var counter = tasks[index][i].count;
                    height = (Math.round( counter * 100 )/100 );
                    var $barWrap = $('<span />', {
                        'class': 'main_bar tipText'
                    })
                    .attr('style', 'height:'+height+'%; background-color: '+bg_color+'; left: '+bar_left+'px;')
                    .attr('title', tasks[index][i].title)

                    var $text = $('<span />', {
                        'class': 'total_text'
                    })
                    .attr('style', '')
                    .text(counter)
                    .appendTo($barWrap)

                    var $text = $('<span />', {
                        'class': 'status_text'
                    })
                    .attr('style', '')
                    .text(tasks[index][i].title)
                    .appendTo($barWrap)

                    $barWrap.appendTo($wrapper)
                    if(i == 0){

                        var $saparator = $('<span />', {
                            'class': 'status_bottom'
                        })
                        .attr('style', 'left: '+(bar_left-5)+'px; width: '+(tasks[index].length * 60)+'px; ')
                        .text(index)
                        .appendTo($wrapper)
                    }
                    bar_left += 60;
                    if(i == tasks[index].length - 1){
                        bar_left += 30;
                        var $saparator = $('<span />', {
                            'class': 'bar_saparator'
                        })
                        .attr('style', 'left: '+(bar_left-15)+'px;')
                        .appendTo($wrapper)
                    }

                }
            });
            $wrapper.appendTo('.chart-target')
        })();
    })
</script>

<style type="text/css">
    .chart-container {
        padding-bottom: 150px;
        overflow: auto;
    }
    .chat-header {
        text-align: center;
        text-transform: uppercase;
        padding: 15px 0;
        display: block;
        font-weight: bold;
    }
    .chat-data {
        display: block;
        position: relative;
        margin: 0;
    }
    .chat-data .count {
        display: inline-block;
        height: calc(100% - 26px);
        overflow: hidden;
        position: absolute;
        width: 15px;
        left: 8px;
    }
    .chat-data div.count>span {
        transform: rotate(-90deg);
        display: block;
        margin: 120px 0px 0px;
    }
    .chat-data .chart-target {
        height: 200px;
        margin: 0 0 25px 45px;
        padding: 0;
        position: relative;
        width: calc(100% - 45px);
    }
    .chat-data .chart-target::before {
        background: #cccccc;
        bottom: -1px;
        content: "";
        height: 1px;
        left: -11px;
        position: absolute;
        width: 100%;
    }
    .chat-data .chart-target::after {
        background: #cccccc;
        bottom: -1px;
        content: "";
        height: 100%;
        left: -12px;
        position: absolute;
        width: 1px;
    }
    .chart_wrapper {
        bottom: 0;
        position: relative;
        vertical-align: bottom;
        height: 200px;
    }
    .chart_wrapper .main_bar {
        position: absolute;
        width: 50px;
        margin: 0 3px 0 0;
        bottom: 0;
        display: inline-block;
    }
    .status_text {
        font-size: 10px;
        position: absolute;
        bottom: -20px;
        left: auto;
        text-align: center;
        width: 100%;
        color: #333333;
        top: 100%;
    }
    .bar_saparator {
        font-size: 10px;
        position: absolute;
        bottom: -20px;
        left: auto;
        text-align: center;
        color: #333333;
        top: 100%;
        background: #ccc;
        width: 1px;
        height: 120px;
    }
    .status_bottom {
        font-size: 10px;
        position: absolute;
        left: auto;
        text-align: center;
        color: #333333;
        font-size: 13px;
        font-weight: bold;
        bottom: 0;
        top: calc(100% + 100px);
        height: auto;
    }
    .main_bar .total_text {
        opacity: 1;
        font-size: 12px;
        font-weight: 600;
        position: absolute;
        left: auto;
        text-align: center;
        width: 100%;
        color: #ffffff;
        top: 40%;
    }
</style>