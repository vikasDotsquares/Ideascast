<?php

// $project_id = 46;
// $summary_options = $this->Permission->summary_options($project_id); // 9s
// $project_progress = $this->Permission->project_progress($project_id); // 18s
// $project_summary = $this->Permission->summary_data($project_id); // 10s

?>
<style type="text/css">
    .list {
        width: 100px;
        border: 1px solid #ccc;
        padding: 1px 5px;
    }
    .down-list {
        display: none;
        width: 200px;
        border: 1px solid #ccc;
    }
    .down-list span {
        display: block;
        width: 100%;
    }
    /*.list:hover .down-list {
        display: block;
    }*/
    .list.new-class .down-list {
        display: block;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    Samples
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;">Create & Check your sample code here</span>
                    </p>
                </h1>
            </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                        </div>
                        <div class="box-body clearfix" style="overflow: auto;" id="box_body">
                            <div class="list"><span>test</span>
                                <div class="down-list">
                                    <span>1111111111111</span>
                                    <span>2222222222222</span>
                                    <span>333333333333</span>
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
    $(function(){

        var t;
        function starts(th) {
            $(th).addClass('new-class')
        }

        function stop(th) {
            clearTimeout(t);
            $(th).removeClass('new-class')
        }

        $('.list').on('mouseenter', function(event) {
            event.preventDefault();
            var $this = this;
            t = setTimeout(() => {
                starts($this);
            }, 300);
        });

        $('.list').on('mouseleave', function(event) {
            event.preventDefault();
            var $this = this;
            stop($this);
        });


        // RESIZE MAIN FRAME
        ($.adjust_resize = function(){
            $('.box-body').animate({
                minHeight: (($(window).height() - $('.box-body').offset().top) ) - 17,
                maxHeight: (($(window).height() - $('.box-body').offset().top) ) - 17
            }, 1)
        })();

        // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                $.adjust_resize();
                clearInterval(interval);
            }
        }, 1);

        // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
        $(".sidebar-toggle").on('click', function() {
            $.adjust_resize();
            const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
            setTimeout( () => clearInterval(fix), 1500);
        })

        // RESIZE FRAME ON WINDOW RESIZE EVENT
        var resizeTimer;
        $(window).resize(function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $.adjust_resize();
            }, 50);
        })

    })
</script>