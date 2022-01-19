<style>
    .content {
        padding: 0 15px 0 15px;
    }

    .box-header.filters {
        background-color: #f1f3f4;
        border-color: #ddd;
        border-image: none;
        border-style: solid;
        border-width: 1px;
    }

	 svg.brusher {
        float: none !important;

     }

    .studios-row-h .custom-dropdown{
        min-width: 400px;
    }
    .border-top {

        border-top: 0px !important;
        overflow:hidden;
    }
    .select-project-sec {
        /* border: 1px solid #ccc; */
        min-height: 177px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
    .select-project-sec .no-data {
        color: #bbbbbb;
        font-size: 30px;
        text-align: center;
        text-transform: uppercase;
    }



    @media (max-width:480px){
        .studios-row-h .custom-dropdown{
            min-width: 10px;
        }
    }

    .search{ margin: 0 0 6px 0;}

    .search input{ width: 148px;}

    .row section.content-header h1 p.text-muted span{ text-transform: none; }

    .box.border-top{/*  min-height: 700px; */ }

    .menuGroup {
        position: absolute;
        height: 116px;
        top: 0px;
        right: 0px;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-left: 1px solid #ddd;
        padding: 8px 8px 8px 8px;
        border-bottom-left-radius: 5px;
        display: none;
    }

    .findTextInput {
        /* position: absolute;
        top: 6px;
        right:8px; */
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
        padding: 3px 6px 3px 6px;
        width: 132px;
    }

    .minmaxLabel {
        position: absolute;
        visibility:hidden;
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
    }

    .greenButtonAll {
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 30px;
        width: 148px;
        border-radius: 3px;
        border: 1px solid #5f9323;
    }

    .greyButtonAll {
        top: 54px;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        background-color: #eee;
        font-weight: 400;
        height: 30px;
        width: 148px;
        border-radius: 3px;
        border: 1px solid #ddd;
    }

    .greenButton {
        position:absolute;
        right:86px;
        top: 77px;
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 30px;
        width: 70px;
        border-radius: 3px;
        border: 1px solid #5f9323;
    }

    .greyButton {
        position:absolute;
        top: 77px;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        background-color: #eee;
        font-weight: 400;
        height: 30px;
        width: 70px;
        border-radius: 3px;
        border: 1px solid #ddd;
    }

    .hiddenLabel {
        position:absolute;
        right:8px;
        top: 37px;
        width:148px;
        text-align:center;
        visibility:hidden;
        font-family: Open Sans;
        font-size: 8px;
        color: #ddd;
        font-weight: 400;
    }

    .rangeLabel {
        position:absolute;
        right:8px;
        top: 175px;
        width:148px;
        text-align:center;
        visibility:hidden;
    }

    button:focus {
        outline: 0 !important;
    }

    .rangeLabel {
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
    }

    .valueSlider {
        position:absolute;
        right:8px;
        top: 72px;
        visibility:hidden;
    }

    .levelSlider {
        position:absolute;
        right:8px;
        top: 110px;
        visibility:hidden;
    }

    input[type=range] {
        height: 27px;
        -webkit-appearance: none;
        margin: 43px 0;
        width: 100%;
        background-color:#f5f5f5;
    }

        input[type=range]:focus {
            outline: none;
        }

        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            animate: 0.2s;
            box-shadow: 0px 0px 0px #000000;
            background: #CCCCCC;
            border-radius: 25px;
            border: 0px solid #000101;
        }

        input[type=range]::-webkit-slider-thumb {
            box-shadow: 0px 0px 0px #000000;
            border: 0px solid #67A028;
            height: 21px;
            width: 11px;
            border-radius: 7px;
            background: #67A028;
            cursor: pointer;
            -webkit-appearance: none;
            margin-top: -8.5px;
        }

        input[type=range]:focus::-webkit-slider-runnable-track {
            background: #CCCCCC;
        }

        input[type=range]::-moz-range-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            animate: 0.2s;
            box-shadow: 0px 0px 0px #000000;
            background: #CCCCCC;
            border-radius: 25px;
            border: 0px solid #000101;
        }

        input[type=range]::-moz-range-thumb {
            box-shadow: 0px 0px 0px #000000;
            border: 0px solid #67A028;
            height: 21px;
            width: 11px;
            border-radius: 7px;
            background: #67A028;
            cursor: pointer;
        }

        input[type=range]::-ms-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            animate: 0.2s;
            background: transparent;
            border-color: transparent;
            color: transparent;
        }

        input[type=range]::-ms-fill-lower {
            background: #CCCCCC;
            border: 0px solid #000101;
            border-radius: 50px;
            box-shadow: 0px 0px 0px #000000;
        }

        input[type=range]::-ms-fill-upper {
            background: #CCCCCC;
            border: 0px solid #000101;
            border-radius: 50px;
            box-shadow: 0px 0px 0px #000000;
        }

        input[type=range]::-ms-thumb {
            margin-top: 1px;
            box-shadow: 0px 0px 0px #000000;
            border: 0px solid #67A028;
            height: 21px;
            width: 11px;
            border-radius: 7px;
            background: #67A028;
            cursor: pointer;
        }

        input[type=range]:focus::-ms-fill-lower {
            background: #CCCCCC;
        }

        input[type=range]:focus::-ms-fill-upper {
            background: #CCCCCC;
        }

    /* SVG styles */
    svg {
        min-width: 200px;
        min-height: 100px;
        float: left;
    }

	svg.brusher {
        min-width: auto;
        min-height: auto;

    }

    .links path {
        fill: none;
        opacity: 1;
    }

    .ownerLink {
        stroke: #000;
        stroke-opacity: 0.6;
    }

    .sharerLink {
        stroke: #000;
        stroke-opacity: 0.3;
    }

    .nodes circle {
        stroke: #cccccc;
        fill: #ebf1fb;
        stroke-width: 2;
        pointer-events: all;
    }
    .search input{
        border: 1px solid rgb(118, 118, 118);
    }
    .search input:focus{
        outline-width:0;
        outline-offset:0;
        border: 1px solid #3c8dbc;
    }
    #selection_list {
        min-width: 400px;
    }
    @media (max-width: 480px) {
        .social-select-dropdown {
            width: 100%;
        }
        #selection_list {
            min-width: 10px;
        }
    }
</style>

<style>
    /*group/project background*/
    .series-group {
        fill: #f1f3f4 !important;
    }
    /*segment*/
    .series-segment {
        fill-opacity: 1 !important;
    }
    /*legend text*/
    .color-slot text {
        font-family: Open Sans;
        font-weight: 400;
        font-size: 13px !important;
        fill: #fff !important;
    }
    /*left axis text*/
    .tick text {
        font-family: Open Sans;
        font-weight: 400;
        fill: #444 !important;
    }
    /*segment hover panel*/
    .chart-tooltip {
        font-family: Open Sans;
        background-color: #444;
    }
    /*marker date line*/
    .x-axis-date-marker {
        stroke: #ca0000 !important;
    }
    /*reset zoom button*/
    .reset-zoom-btn {
        fill: #3c8dbc !important;
        opacity: 1 !important;
    }
    /*timeline selection box*/
    .chart-zoom-selection, .brusher .brush .selection {
        stroke: #3c8dbc;
        stroke-opacity: 1;
        fill: #3c8dbc;
    }
    .grid-background {
        fill: #dcdcdc !important;
    }
    /*legend*/
    .legendG {
        transform: translateX(140px) !important;
    }
    /*menu panel*/
    .locationMenuGroup {
        position: absolute;
        height: 46px;
        top: 0px;
        right: 0px;
        width: 254px;
        background-color: #eee;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-left: 1px solid #ddd;
        border-bottom-left-radius: 5px;
    }
    /*expand button*/
    .expandButton {
        position: absolute;
        top: 9px;
        right: 8px;
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 28px;
        width: 30px;
        border-radius: 3px;
        border: 1px solid #5f9323;
    }
    /*sort chrono button*/
    .sortChrono {
        position: absolute;
        top: 9px;
        right: 46px;
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 28px;
        width: 30px;
        border-radius: 3px;
        border: 1px solid #5f9323;
    }
    /*sort alpha button*/
    .sortAlpha {
        position: absolute;
        top: 9px;
        right: 78px;
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 28px;
        width: 30px;
        border-radius: 3px;
        border: 1px solid #5f9323;
    }
    /*marker date*/
    .markerDate {
        position: absolute;
        top: 8px;
        right: 116px;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        height: 30px;
        width: 130px;
        border-radius: 3px;
        padding-left: 8px;
        border: 1px solid #dcdcdc;
    }
    .expandImg {
        position: absolute;
        top: 4px;
        right: 5px;
    }
    .sortChronoImg {
        position: absolute;
        top: 4px;
        right: 5px;
    }
    .sortAlphaImg {
        position: absolute;
        top: 4px;
        right: 5px;
    }
    .barChart {
        position: absolute;
        top: 17px;
        left: 140px;
        width: 200px;
        border: none;
        overflow: hidden;
        cursor: default;
        visibility: hidden;
    }
	/*.barChart {
		position: relative;
		top: 0;
		left: 0;
		width: 100%;
		cursor: default;
		visibility: hidden;
		overflow: hidden;
		margin-top: 10px;
		margin-bottom: 5px;
		padding-left: 140px;
		padding-right: 408px;
	}*/

    .atHomeBar {
        background-color:#77b64c;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
        margin-right: 2px;
    }
    .inOfficeBar {
        background-color:#74a9da;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
        margin-right: 2px;
    }
    .inTransitBar {
        background-color:#af7ad6;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
        margin-right: 2px;
    }
    .atClientBar {
        background-color:#ee8640;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
        margin-right: 2px;
    }
    .onSiteBar {
        background-color:#ffc000;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
        margin-right: 2px;
    }
    .privateBar {
        background-color:#dcdcdc;
        display: inline-block;
        font-family: Open Sans;
        font-size: 12px;
        font-weight: 400;
        color: #fff;
        height: 19px;
        padding:2px 0px 0px 3px;
        text-align: center;
    }
    .locationMenuGroup {
        display: none;
    }

    .select-project-th {
        min-height: 81px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
    .select-project-th .no-data {
        color: #bbbbbb;
        font-size: 30px;
        text-align: center;
        text-transform: uppercase;
    }
    .nav-analytic li.active a{
        font-weight: 600;
    }
    .box {
        margin: 0;
    }
</style>
<!--Copyright 2010-2020 Mike Bostock All rights reserved.-->
<?php echo $this->Html->script('projects/d3.v5.min', array('inline' => true)); ?>
<!--Copyright (c) 2016 vasturiano-->
<?php echo $this->Html->script('projects/timeline-chart.min', array('inline' => true)); ?>
<link href="//fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

<div class="row">
    <div class="col-xs-12">
        <section class="main-heading-wrap">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><span><?php echo $page_subheading; ?> </span></div>
            </div>

            <div class="header-right-side-icon"></div>
        </section>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
					<div class="nav-analytic">
                        <ul class="nav nav-tabs" style="cursor: default !important;">
                            <li class="active">
                                <a data-toggle="tab" class="active" data-type="sharing" data-target="#sharing" href="#sharing" aria-expanded="true">Sharing</a>
                            </li>
							<li>
                                <a data-toggle="tab" class="active" data-type="org_chart" data-target="#org_chart" href="#org_chart" aria-expanded="true">Org Chart</a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" data-type="location" data-target="#location" href="#location" aria-expanded="false">WORKPLACE</a>
                            </li>
                        </ul>
                    </div>


                    <div class="box noborder">

                        <div class="box-body clearfix" style=" padding: 0; position: relative;" id="box_body">

                            <div class="tab-content" id="myTabContent">

                                <div id="sharing" class="tab-pane fade active in">

                                    <div class="clearfix col-sm-12 inner-first cost-label-sec nopadding">
                                        <div class="box-header filters" style="padding: 10px;">
                                            <div class="select-filds-header studios-row-h">
                                                <label class="custom-dropdown social-select-dropdown">
                                                    <select class="aqua" name="project_id" id="selection_list"   style=" width: 100%; max-width: 400px; margin-bottom: 0;">
                                                        <option value="">Select View</option>
                                                        <option value="all">All Organization</option>
                                                        <option value="my">All My Projects</option>
                                                        <?php
                                                        if(isset($projects) && !empty($projects)) {

                                                            foreach($projects as $key => $val ) {
                                                                $sel = '';
                                                                if(isset($project_id) && !empty($project_id)) {
                                                                    if($key == $project_id) $sel = 'selected="selected"';
                                                                }
                                                                if( !empty($key)){
                                                                    echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                                                                }
                                                            }
                                                        } ?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="analytics-sec-cont border1">

                                        <div id="menuPanel" class="menuGroup">
                                            <div class="search"><input type="text" class="findTextInput" placeholder="Find Name..." maxlength="64" spellcheck="false"  id="findUserInput" onkeyup="if ((event.keyCode === 13) || (this.value.length===0)) { $.findUser() };"/></div>

                                            <button id="showAllButton" class="greenButtonAll" onclick="viewChange('All');">Show All</button>
                                            <button id="ownerButton" style="right:86px;" class="greyButton" onclick="viewChange('Owner');">Owner</button>
                                            <button id="sharerButton" style="right:8px;" class="greyButton" onclick="viewChange('Sharer');">Sharer</button>
                                            <label id="viewLabel" class="hiddenLabel">All</label>


                                            <input id="valueInput" class="valueSlider" style="width:148px;" type="range" min="1" max="6" step="1" value="6" onclick="$.linksShow();">
                                            <label id="minLabel" class="minmaxLabel" style="right:97px; top: 141px; width:56px; text-align:left;">0</label>
                                            <label id="rangeLabel" class="minmaxLabel" style="right:58px; top: 141px; width:70px; text-align:center;">#Shares</label>
                                            <label id="maxLabel" class="minmaxLabel" style="right:38px; top: 141px; width:56px; text-align:right;">0</label>
                                            <label id="allLabel" class="minmaxLabel" style="right:8px; top: 141px; width:20px; text-align:right;">All</label>


                                            <input id="levelInput" class="levelSlider" style="width:148px;" type="range" min="1" max="3" step="1" value="1" onclick="$.levelChange();">
                                            <label id="levelLabel"class="rangeLabel">Show Project Shares</label>

                                        </div>

                                        <div id="analyticsIframe" style="width:100%; border: none; min-height :700px; overflow: hidden;" >

                                            <div class="col-sm-12 partial_data box-borders " style="padding: 10px;">
                                                <div class="col-sm-12 box-borders select-project-sec">
                                                    <div class="no-data">SELECT VIEW</div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>


                                </div>

								<div id="org_chart" class="tab-pane fade ">


                                </div>

                                <div id="location" class="tab-pane fade ">

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



<script>
    $('#analyticsIframe').css('min-height', ($(window).height()-$('#analyticsIframe').offset().top) - 30);
    function getVisible(el) {
        var $el = el,
            scrollTop = $(window).scrollTop(),
            scrollBot = scrollTop + $(window).height(),
            elTop = $el.offset().top,
            elBottom = elTop + $el.outerHeight(),
            visibleTop = elTop < scrollTop ? scrollTop : elTop,
            visibleBottom = elBottom > scrollBot ? scrollBot : elBottom;
        return (visibleBottom - visibleTop);
    }
    $(function(){
        $('.nav-analytic .nav').css('cursor', 'default');
        // console.log(getVisible($('#analyticsIframe')))
        $('#analyticsIframe').css('min-height', ($(window).height()-$('#analyticsIframe').offset().top) - 30);
        $('.nav.nav-tabs').on('show.bs.tab', function(event){
            var type = $(event.target).data('type');
            $("#"+type).html("");
            $.load_analytics(type)
        })

        ;($.load_analytics = function(type){
            var type = type || 'sharing';

            var data = {type: type}
            if($js_config.project_id){
                data.project_id = $js_config.project_id;
            }
            $.ajax({
                url: $js_config.base_url + 'subdomains/load_analytics',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    $("#"+type).html(response)
                }
            })

        })("sharing");
    })

</script>
