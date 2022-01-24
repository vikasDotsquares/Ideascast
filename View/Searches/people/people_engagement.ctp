<style>
    /*group/user background*/
    .series-group {
        fill: #fff !important;
    }
    /*segment*/
    .series-segment {
        fill-opacity: 0.3 !important;
    }
    .timelines-chart .axises .x-axis {
        font-family: Open Sans !important;
        font-size: 11px !important;
        color: #444 !important;
    }
    /*left axis text*/
    .tick text {
        font-family: Open Sans !important;
        font-weight: 400 !important;
        fill: #444 !important;
        transform: translateY(2px) !important;
    }
    /*x axis tick lines*/
    .tick line {
        stroke: #777 !important;
        opacity: 0.2 !important;
    }
    .timelines-chart .axises line, .timelines-chart .axises path {
        stroke: #777 !important;
        opacity: 0.2 !important;
    }
    .timelines-chart .axises .x-grid line {
        stroke: #777 !important;
    }
    /*segment hover panel*/
    .chart-tooltip {
        font-family: Open Sans !important;
        background-color: #444 !important;
        font-weight: 400 !important;
    }
    /*segment hover panel*/
    .en_tooltip {
        font-family: Open Sans !important;
        background-color: #444 !important;
        font-size: 12px !important;
        color: #fff !important;
        padding: 3px !important;
    }
    /*x-axis*/
    .x-axis {
        transform: translate(0px,-26px) !important;
    }
    .y-axis {
        pointer-events: none !important;
    }
    /*marker date line*/
    .x-axis-date-marker {
        stroke: #ca0000 !important;
    }
    /*timeline selection box*/
    .chart-zoom-selection, .brusher .brush .selection {
        stroke: #dcdcdc !important;
        stroke-opacity: 0 !important;
        fill: #3c8dbc !important;
    }
    /*reset zoom button*/
    .reset-zoom-btn {
        fill: #3c8dbc !important;
        opacity: 1 !important;
        font-family: Open Sans !important;
        font-weight: 400 !important;
        font-size: 10px !important;
        transform: translate(66px,-29px) !important;
    }
    .brusher .grid-background {
        fill: #f1f3f4 !important;
        stroke: #dcdcdc !important;
    }
    /*legend*/
    .legendG {
        display: none !important;
    }

</style>
<div class="tab_engagement_data" id="tab_engagement_data"><div class="no-summary-found">No People</div></div>

<script>

        var en_Chart, en_markerDate, en_formatTime = d3.timeFormat("%d %b, %Y %I:%M %p"), en_formatDate = d3.timeFormat("%d %b, %Y");
        $('.show-engagement').on('click', function(event) {
        	event.preventDefault();

        	$('#tab_engagement_data').html("");
        	$('.chart-tooltip').remove();

	        var eng_data = {};
	        eng_data = $.createParams(eng_data);
	        eng_data.start_date = $("#range_start_date").val();
	        eng_data.end_date = $("#range_end_date").val();
	        eng_data.search_text = $('.search-box').val();

	        //load the data
	        d3.json($js_config.base_url + "searches/people_engagement_json", {
	                          method:"POST",
	                          body: JSON.stringify(eng_data),
	                          headers: {
	                            "Content-type": "application/json; charset=UTF-8"
	                          }
	                        }
	                    ).then(function (en_data) {

	            //sort data by full name
	            en_data.sort((a, b) => d3.ascending(a.group, b.group));

	            en_Chart = TimelinesChart()
	                .data(en_data)
	                .enableAnimations(false)
	                .width(document.getElementById('tab_engagement_data').clientWidth)
	                .leftMargin(180) //left-side project axis labels
	                .rightMargin(120) //right-side user axis labels
	                .topMargin(57) //color legend height
	                .bottomMargin(30) //contains the time axis and labels
	                .onLabelClick(function (a, b) { en_showUserProfileOrProject(a, b); }) //callback function for clicking on the username axis labels
	                .segmentTooltipContent( d =>
	                    d.data.val == 'P' ? '<div class="en_tooltip">' + d.data.name + '<br>Start: ' + en_formatDate(new Date(d.data.start_date)) + '<br>End: ' + en_formatDate(new Date(d.data.end_date)) + '</div>'
	                    : d.data.val == 'T' ? '<div class="en_tooltip">' + d.data.name + '<br>Start: ' + en_formatDate(new Date(d.data.start_date)) + '<br>End: ' + en_formatDate(new Date(d.data.end_date)) + '</div>'
	                    : d.data.val == 'A' ? '<div class="en_tooltip">' + d.data.name + '<br>Start: ' + en_formatTime(new Date(d.data.start_date)) + '<br>End: ' + en_formatTime(new Date(d.data.end_date)) + '</div>'
	                    : '<div class="en_tooltip">' + d.data.name + '<br> Start: ' + en_formatDate(new Date(d.data.start_date)) + '<br>End: ' + en_formatDate(new Date(d.data.end_date)) + '</div>')
	                .zQualitative(true) //segment data color values are categorical (true) or quantitative (false)
	                (document.getElementById('tab_engagement_data')); //chart container

	            //set color palette for segments
	            en_Chart.zColorScale().domain(['P', 'T','A','W']);
	            en_Chart.zColorScale().range(['#3c8dbc','#e3a809','#5f9322','#ca0000']); //blue#2e75b6,orange#c55a11,green#548235,red#ca0000

	            //set marker date
	            en_markerDate = new Date(); //now
	            en_markerDate = new Date(en_markerDate.getTime() + (en_markerDate.getTimezoneOffset() * 60000));
	            en_Chart.dateMarker(en_markerDate);

	            //expand height of chart to be number of people times 12px plus top legend height plus bottom timeline bar
	            en_Chart.maxHeight((en_Chart.getTotalNLines() * 12) + 34 + 72);

	            //resize
	            window.addEventListener('resize', en_windowResize);

	        })
	        .catch(function(tr_err) {

	            $('#tab_engagement_data').html('<div class="no-summary-found">No People</div>');
	            //throw error
	            throw new Error("No People Data - Managed Exit.");

	        });
        });

        setTimeout(()=>{
            if($js_config.selected_tab) {
                // $('.show-engagement').trigger('click');
                // console.log('trigger1')
            }
        },300)



        function en_windowResize() {

            en_Chart.width(document.getElementById('tab_engagement_data').clientWidth); //update chart width

        }

        function en_showUserProfileOrProject(en_fullame, en_activityGroup) {

            //find id of labelName (full name)
            var en_user_id, en_groupObj, en_found = false;
            for (i = 0; i < en_Chart.data().length ; i++) {
                en_groupObj = en_Chart.data()[i];
                if (en_groupObj.group === en_fullame) {
                    en_user_id = en_groupObj.user_id;
                    en_found = true;
                    break;
                }
                if (en_found) break;
            }

            //display user profile
            if (en_found) {
                //call existing page function to display user profile dialog with en_user_id
                // console.log(en_user_id + ' ' + en_fullame);
                $('#popup_modal').modal({
                    remote: $js_config.base_url + 'shares/show_profile/' + en_user_id
                })
                .modal('show');
            }
        }

</script>
