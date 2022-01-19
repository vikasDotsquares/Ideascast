	<link href="//fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
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

	.box-body.clearfix{
			position:relative;
		}


    .box-content {
        /*overflow: hidden;*/
    }
    .menuGroup {
        position: absolute;
        /*height: 200px;*/
        top: 0px;
        right: 0px;
        width: 250px;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-left: 1px solid #ddd;
        padding: 8px 8px 3px 8px;
        border-bottom-left-radius: 5px;
    }
    .menuGroup-wrap {
        display: inline-block;
        width: 100%;
        padding-top: 26px;
    }
     .menuGroup-wrap label{
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
         margin: 0;
         padding-right: 3px;
    }
   .menuGroup-wrap input[type="radio"] {
        margin:1px 3px 0 0;
        vertical-align: top;
    }

    .findTextInput {
        position: absolute;
        top: 6px;
        right: 8px;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
        padding: 3px 6px 3px 6px;
        width: 230px;
        z-index: 99;
    }

    .searchOption {
        position: relative;
        display: inline-block;
        top: 28px;
        float: left;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
    }

    .skillOption {
        position: relative;
        display: inline-block;
        top: 76px;
        float: left;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
    }

   .peopleOption {
        position: relative;
        display: inline-block;
        top: 124px;
        float: left;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
    }

    .searchLabel {
        position: relative;
        top: 30px;
        float: left;
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
    }

    .skillLabel {
        position: relative;
        top: 78px;
        float: left;
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
    }

    .peopleLabel {
        position: relative;
        top: 126px;
        float: left;
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
    }

    .skillsDropDownList {
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
        padding: 3px 6px 3px 6px;
        width: 210px;
        z-index: 99;
        display: inline-block;
        margin-top: 5px;
    }

    .searchGroup {
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
        padding-left: 0;
        width: 210px;
        display: inline-block;
        padding-bottom: 6px;
    }

    .skillsGroup {
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
        padding-left: 0;
        width: 230px;
        display: inline-block;
        padding-bottom: 6px;
    }
    .peopleDropDownList {
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
        padding: 3px 6px 3px 6px;
        width: 230px;
        z-index: 99;
        display: inline-block;
        margin-top: 5px;
    }
    .peopleGroup {
        font-family: Open Sans;
        font-size: 10px;
        color: #444;
        font-weight: 400;
        padding-left: 0;
        width: 230px;
        display: inline-block;
        padding-bottom: 6px;
    }



    .resetButton {
        font-family: Open Sans;
        font-size: 12px;
        color: #fff;
        background-color: #67a028;
        font-weight: 400;
        height: 30px;
        width: 230px;
        border-radius: 3px;
        border: 1px solid #5f9323;
        display: inline-block;
        margin: 5px 0px;
    }

    .menuGroup-wrap .analysisLabel {
        width: 100%;
        text-align: center;
        font-family: Open Sans;
        font-size: 12px;
        color: #444;
        font-weight: 400;
        display: inline-block;
    }

    input:focus, textarea:focus, select:focus {
        outline-width: 1px;
        outline-color: #3c8dbc;
    }

    button:focus, option:focus {
        outline: none;
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

    .studios-row-h {
        float: left;
    }
    /*#selection_list {
        min-width: 400px;
    }
    #selection_type {
        min-width: 200px;
    }*/

	.nav-analytic li.active a {
		font-weight: 600;
	}
	.ka-search-box {
		display: flex;
		max-width: 350px;
	}
	.ka-search-box .form-control{
		color: #444;
	}

	.ka-search-box .form-control::placeholder{
		color: #444;
	}


	.ka-search-box .ka-input-group-btn{
		padding-left: 3px;
	}
    .specific-section {
        display: none;
    }



    @media (max-width: 480px) {
        .social-select-dropdown {
            width: 100%;
        }
        #selection_list {
            min-width: 10px;
        }
        #selection_type {
            min-width: 10px;
        }
    }

    /*search*/
    .searchMenuGroup {
            position: absolute;
            height: 308px;
            top: 0px;
            right: 0px;
            width: 218px;
            background-color: #eee;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-left: 1px solid #ddd;
            padding: 8px 8px 8px 8px;
            border-bottom-left-radius: 5px;
            visibility: hidden;
        }
        .peopleList {
            position: absolute;
            top: 6px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .skillsList {
            position: absolute;
            top: 39px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .subjectsList {
            position: absolute;
            top: 72px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .domainsList {
            position: absolute;
            top: 105px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .organizationsList {
            position: absolute;
            top: 138px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .departmentsList {
            position: absolute;
            top: 171px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .locationsList {
            position: absolute;
            top: 204px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .storiesList {
            position: absolute;
            top: 237px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #444;
            font-weight: 400;
            padding: 3px 6px 3px 6px;
            height: 27px;
            width: 200px;
            z-index: 99;
        }
        .zoomOutButton {
            position: absolute;
            top: 270px;
            right: 112px;
            font-family: Open Sans;
            font-size: 12px;
            color: #fff;
            background-color: #67a028;
            font-weight: 400;
            height: 30px;
            width: 96px;
            border-radius: 3px;
            border: 1px solid #5f9323;
        }
        .s-resetButton {
            position: absolute;
            top: 270px;
            right: 8px;
            font-family: Open Sans;
            font-size: 12px;
            color: #fff;
            background-color: #67a028;
            font-weight: 400;
            height: 30px;
            width: 96px;
            border-radius: 3px;
            border: 1px solid #5f9323;
			margin: 0;
        }
        .btn-ka-search.no-value {
            opacity: 0.5;
            pointer-events: none;
        }
        .image-tip .tooltip-inner {
            text-align: left;
        }
        .go-to-disabled {
            pointer-events: none;
        }
        .dm-show, .ka-show{
            opacity: 0.5;
            pointer-events: none;
        }
        #sd_analyticsIframe {
            overflow: auto;
        }
        #demand_competency_from_1, #demand_competency_from_2 {
            margin-bottom: 2px;
        }
        .tooltip-inner.tip-demand {
            white-space: pre-wrap;
            min-width: 100px;
        }
        .tooltip.image-tip {
            text-transform: none !important;
        }

        /* TRENDS */
        .drop {
            cursor: pointer;
        }
        .line-label {
            fill: #444 !important;
            font-size: 12px;
            cursor: pointer;
        }
        .start text {
            font-size: 12px;
        }
        .end text {
            font-size: 12px;
        }
        .line-separator {
            stroke: #eee;
            fill: none;
            stroke-width: 1;
        }
        .form-control[readonly] {
             cursor: default;
             background-color: #fff;
            opacity: 1;
        }
</style>


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



        <div class="box-co
        ntent">
            <div class="row ">
                <div class="col-xs-12">
					<div class="nav-analytic">
                        <ul class="nav nav-tabs" style="cursor: default;" id="ka_tabs">
                            <li class="active">
                                <a data-toggle="tab" class="active" data-type="ka_search" data-target="#ka_search" href="#ka_search" aria-expanded="true">SEARCH</a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" data-type="ka_competency" data-target="#ka_competency" href="#ka_competency" aria-expanded="false">COMPETENCIES</a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" data-type="ka_demand" data-target="#ka_demand" href="#ka_demand" aria-expanded="false">Demand</a>
                            </li>
							<?php  ?><li class="">
                                <a data-toggle="tab" data-type="ka_trends" data-target="#ka_trends" href="#ka_trends" aria-expanded="false">Trends</a>
                            </li><?php  ?>
                        </ul>
                    </div>


                    <div class="box noborder" style="margin-bottom: 0;">
						<div class="tab-content" id="myTabContent">
							<div id="ka_search" class="tab-pane fade active in">
							</div><!-- SEARCH TAB -->

							<div id="ka_competency" class="tab-pane fade">
                            </div><!-- COMOETENCY TAB -->

                            <div id="ka_demand" class="tab-pane fad">
                            </div> <!-- DEMAND TAB -->

							<?php  ?><div id="ka_trends" class="tab-pane fade">Trends
							</div><?php  ?><!-- TRENDS TAB -->


                        </div><!-- TAB CONTENT -->
                    <!-- .box -->
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    $(function(){
        $('.nav-analytic .nav').css('cursor', 'default');

        // var cmin_height = (($(window).height() - $('#search_analyticsIframe').offset().top) - 20);
        // $('#search_analyticsIframe').css('min-height', cmin_height);

        $('.nav.nav-tabs').on('show.bs.tab', function(event){
            var type = $(event.target).data('type');
            if((!$js_config.type || $js_config.type == '') ){
                $.load_analytics(type);
            }
        })

        ;($.load_analytics = function(type, passed_type, project_id, competency_id, pfrom, spf2){
            var type = type || 'ka_search';

            var passed_type = passed_type || '';
            if(passed_type != ""){
                type = 'ka_competency';
                $('#ka_tabs a[href="#ka_competency"]').tab('show');
                $js_config.type = '';
                window.history.pushState({},"", $js_config.base_url + 'analytics/knowledge');
            }

            var data = {type: type, passed_type: passed_type}
            if($js_config.project_id){
                data.project_id = project_id;
            }
            if($js_config.competency_id){
                data.competency_id = competency_id;
            }
            if($js_config.pfrom){
                data.pfrom = pfrom;
            }
            if($js_config.spf2.length > 0){
                data.spf2 = spf2;
            }
            $(".tab-pane").html("");
            $.ajax({
                url: $js_config.base_url + 'subdomains/load_ka',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    $("#"+type).html(response);
                }
            })
        })("ka_search", $js_config.type, $js_config.project_id, $js_config.competency_id, $js_config.pfrom, $js_config.spf2);


    })
</script>
