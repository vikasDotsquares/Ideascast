
<style>
    /*group/project background*/
    .series-group {
        fill: #f1f3f4 !important;
    }
    /*segment*/
    .series-segment {
        fill-opacity: 1 !important;
    }
	body,html{overflow: hidden;}

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

	/* #location_analyticsIframe{ overflow-y: auto !important; } */

	.box{ margin-bottom:0px;}
</style>

        <div class="clearfix col-sm-12 inner-first cost-label-sec nopadding">
			<div class="box-header filters" style="padding: 10px;">
                <div class="select-filds-header studios-row-h">
                   <!--<label for="projectId">Projects</label>-->
                    <label class="custom-dropdown social-select-dropdown">
                        <select class="aqua" name="project_id" id="loc_selection_list"   style=" width: 100%; max-width: 400px; margin-bottom: 0;">
                            <option value="">Select View</option>
                            <option value="all">All Community</option>
                            <option value="my">All My Projects</option>
                            <?php
                            if(isset($projects) && !empty($projects)) {

                                foreach($projects as $key => $val ) {
                                    if( !empty($key)){
                                        echo '<option value="'.$key.'">'.$val.'</option>';
                                    }
                                }
                            } ?>
                        </select>
                    </label>
                </div>
    		</div>
        </div>

		<div class="analytics-sec-cont border1" style="padding-top: 8px;">
			 <div id="barChart" class="barChart">
                <table style="width:100%; border-spacing: 0; border-collapse:collapse;" >
                    <tr>
                        <td style="padding:0; display:flex">
                            <div id="atHomeBar" class="atHomeBar" title =""></div>
                            <div id="inOfficeBar" class="inOfficeBar" title =""></div>
                            <div id="inTransitBar" class="inTransitBar" title =""></div>
                            <div id="atClientBar" class="atClientBar" title =""></div>
                            <div id="onSiteBar" class="onSiteBar" title =""></div>
                            <div id="privateBar" class="privateBar" title =""></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="location_analyticsIframe" style="width: 100%; border: none; min-height: 700px; overflow: hidden; margin-top: 40px;">

                <div class="col-sm-12 partial_data box-borders " style="padding: 10px;">
                    <div class="col-sm-12 box-borders select-project-th">
                        <div class="no-data">SELECT VIEW</div>
                    </div>
                </div>

            </div>

            <div id="locationMenuPanel" class="locationMenuGroup">
                <input type="date" id="markerDate" class="markerDate tipText" name="marker-date" title="Set Date Marker" onchange="setMarker();" style="cursor: pointer;">
                <button id="sortChrono" class="sortChrono tipText" title="Sort Chronologically" onclick="sortChrono();"><img class="sortChronoImg" src="../images/icons/sort-chrono-white-18x18.png" /></button>
                <button id="sortAlpha" class="sortAlpha tipText" title="Sort Alphabetically" onclick="sortAlpha();"><img class="sortAlphaImg" src="../images/icons/sort-alpha-white-18x18.png" /></button>
                <button id="expandButton" class="expandButton tipText" title="Expand" onclick="expandHeight();"><img class="expandImg" src="../images/icons/expand-white-18x18.png" /></button>
            </div>

        </div>

<script>

    $('#location_analyticsIframe').css('min-height', $(window).height()-$('#location_analyticsIframe').offset().top, 'height', $(window).height()-$('#location_analyticsIframe').offset().top);
    var myChart, markerDate, formatTime = d3.timeFormat("%Y-%m-%d"),
        formatPC = d3.format(".0%"), chrono = false, alpha = true;

    function windowResize() {
         document.getElementById('barChart').style.visibility = "hidden"; //to avoid potential ugly UI
		if(myChart){
            myChart.width(document.getElementById('location_analyticsIframe').clientWidth); //update chart width

        }
        //width of bar chart once redraw is complete
        setTimeout(() => {
            if(myChart){
                $('#barChart').width((document.getElementById('location_analyticsIframe').clientWidth - 280) * 2/3)
                // myChart.maxHeight($("#location_analyticsIframe").height()-120)
            }
            // document.getElementById('barChart').style.width = document.getElementsByClassName('legend')[0].getBBox().width; //match new width of legend beneath
            // document.getElementById('barChart').style.visibility = "visible"; //show bar chart
            $.resizeWrapper()
        }, 500);
    }


    function showUserProfileOrProject(labelName, activityGroup) {

        //find id of labelName if clicked username
        var user_id, labelObj, groupObj, found = false, foundType;
        for (i = 0; i < myChart.data().length ; i++) {
            groupObj = myChart.data()[i];
            if (groupObj.group === labelName && groupObj.project_id != 'None') {
                project_id = groupObj.project_id;
                found = true;
                foundType = "Project";
                break;
            }
            for (j = 0; j < groupObj.data.length; j++) {
                labelObj = groupObj.data[j];
                if (labelObj.label === labelName) {
                    user_id = labelObj.user_id;
                    found = true;
                    foundType = "Username";
                    break;
                }
            }
            if (found) break;
        }

        //display user profile or open project
        if (found && foundType === "Username") {
            //call existing page function to display user profile dialog
            // parent.showProfile(user_id);
            $('#popup_modal').modal({
                remote: $js_config.base_url + 'shares/show_profile/'+user_id
            }).show();
            return; //completed
        }
        else if (found && foundType === "Project") {
            //open project in current window
            window.open($js_config.base_url + "projects/index/" + project_id, "_self");
        }
    }


    function setMarker() {

        //get and set marker date on chart (red vertical line)
        markerDate = new Date(document.getElementById('markerDate').value);
        markerDate = new Date(markerDate.getTime() + (markerDate.getTimezoneOffset() * 60000));
        myChart.dateMarker(markerDate);
        document.getElementById('markerDate').blur();

        //get bar chart values
        let totalPeople = myChart.getTotalNLines();

        let atHome = 0, InOffice = 0, inTransit = 0, atClient = 0, onSite = 0, privateLocation = 0, found = false;
        for (i = 0; i < myChart.data().length ; i++) {
            groupObj = myChart.data()[i];
            for (j = 0; j < groupObj.data.length; j++) {
                labelObj = groupObj.data[j];
                found = false;
                for (k = 0; k < labelObj.data.length; k++) {
                    locationObj = labelObj.data[k];
                    if (markerDate >= new Date(locationObj.timeRange[0]) && markerDate <= new Date(locationObj.timeRange[1])) { //between data start and end range
                        found = true;
                        switch (locationObj.val) {
                            case "At Home":
                                atHome++;
                                break;
                            case "In Office":
                                InOffice++;
                                break;
                            case "In Transit":
                                inTransit++;
                                break;
                            case "At Client":
                                atClient++;
                                break;
                            case "On Site":
                                onSite++;
                                break;
                            case "Private":
                                privateLocation++;
                                break;
                        }
                    }
                }
                if (!found) { totalPeople-- } //all my projects view, do not count user where project start/end dates are outside markerDate
            }
        }

        //wait 1s for visualization to draw
        setTimeout(() => {

            //make bar chart invisible until chart is rendered
            document.getElementById('barChart').style.visibility = "hidden";

            //width of bar chart
            document.getElementById('barChart').style.width = (document.getElementById('location_analyticsIframe').clientWidth - 280) * 2/3;

            //set bar chart values
            if (atHome === 0) {
                document.getElementById('atHomeBar').style.display = "none";
            }
            else {
                document.getElementById('atHomeBar').innerHTML = formatPC(atHome / totalPeople);
                document.getElementById('atHomeBar').style.width = formatPC(atHome / totalPeople);
                document.getElementById('atHomeBar').title = atHome.toString().concat(" of ", totalPeople, " People");
                document.getElementById('atHomeBar').style.display = "block";
            }
            if (InOffice === 0) {
                document.getElementById('inOfficeBar').style.display = "none";
            }
            else {
                document.getElementById('inOfficeBar').innerHTML = formatPC(InOffice / totalPeople);
                document.getElementById('inOfficeBar').style.width = formatPC(InOffice / totalPeople);
                document.getElementById('inOfficeBar').title = InOffice.toString().concat(" of ", totalPeople, " People");
                document.getElementById('inOfficeBar').style.display = "block";
            }
            if (inTransit === 0) {
                document.getElementById('inTransitBar').style.display = "none";
            }
            else {
                document.getElementById('inTransitBar').innerHTML = formatPC(inTransit / totalPeople);
                document.getElementById('inTransitBar').style.width = formatPC(inTransit / totalPeople);
                document.getElementById('inTransitBar').title = inTransit.toString().concat(" of ", totalPeople, " People");
                document.getElementById('inTransitBar').style.display = "block";
            }
            if (atClient === 0) {
                document.getElementById('atClientBar').style.display = "none";
            }
            else {
                document.getElementById('atClientBar').innerHTML = formatPC(atClient / totalPeople);
                document.getElementById('atClientBar').style.width = formatPC(atClient / totalPeople);
                document.getElementById('atClientBar').title = atClient.toString().concat(" of ", totalPeople, " People");
                document.getElementById('atClientBar').style.display = "block";
            }
            if (onSite === 0) {
                document.getElementById('onSiteBar').style.display = "none";
            }
            else {
                document.getElementById('onSiteBar').innerHTML = formatPC(onSite / totalPeople);
                document.getElementById('onSiteBar').style.width = formatPC(onSite / totalPeople);
                document.getElementById('onSiteBar').title = onSite.toString().concat(" of ", totalPeople, " People");
                document.getElementById('onSiteBar').style.display = "block";
            }
            if (privateLocation === 0) {
                document.getElementById('privateBar').style.display = "none";
            }
            else {
                document.getElementById('privateBar').innerHTML = formatPC(privateLocation / totalPeople);
                document.getElementById('privateBar').style.width = formatPC(privateLocation / totalPeople);
                document.getElementById('privateBar').title = privateLocation.toString().concat(" of ", totalPeople, " People");
                document.getElementById('privateBar').style.display = "block";
            }

            //make bar chart visible
            document.getElementById('barChart').style.visibility = "visible";

        }, 1000);

    }

    function sortAlpha() {

        alpha = !alpha;
        myChart.sortAlpha(alpha);
        document.getElementById('sortAlpha').blur();
    }

    function sortChrono() {

        chrono = !chrono;
        myChart.sortChrono(chrono);
        document.getElementById('sortChrono').blur();
    }

    function expandHeight() {

        //expands height of chart to be number of people time 12px plus top legend height plus bottom timeline bar
        let heightCalc = (myChart.getTotalNLines() * 12) + 34 + 72;
        myChart.maxHeight(heightCalc);
        document.getElementById('expandButton').blur();
        document.getElementById('expandButton').disabled = true;
        document.getElementById('expandButton').style.backgroundColor = "#777";
        document.getElementById('expandButton').style.borderColor = "#777";
        document.getElementById('expandButton').style.cursor = "default";

		$('#location_analyticsIframe').css('overflow-y','auto');
    }
    $(function(){
        $('#loc_selection_list').change(function(){
            $('#location_analyticsIframe').html('');
            $('#location_analyticsIframe').css('overflow','hidden');

            var data_section = $(this).val();

            $("#locationMenuPanel").hide();
		   document.getElementById('barChart').style.visibility = "hidden";
            if(data_section < 1){
                $('#location_analyticsIframe').html('<div class="col-sm-12 partial_data box-borders " style="padding: 10px;"><div class="col-sm-12 box-borders select-project-th"><div class="no-data">SELECT VIEW</div></div></div></div>');
                return;
            }
            $.resizeWrapper();

            document.getElementById('expandButton').disabled = false;
            document.getElementById('expandButton').style.backgroundColor = "#67a028";
            document.getElementById('expandButton').style.borderColor = "#5f9323";
            document.getElementById('expandButton').style.cursor = "pointer";

            //load the data
            d3.json($js_config.base_url + "subdomains/sa_location_json/" + data_section).then(function (data) {
                myChart = TimelinesChart()
                    .data(data)
                    .enableAnimations(false)
                    .width(document.getElementById('location_analyticsIframe').clientWidth)
                    .maxHeight(document.getElementById('location_analyticsIframe').clientHeight - 40) //set to height of location_analyticsIframe
                    .leftMargin(140) //left-side project axis labels
                    .rightMargin(140) //right-side user axis labels
                    .topMargin(34) //color legend height
                    .bottomMargin(40) //contains the time axis and labels
                    .timeFormat('%d %b %Y %-I:%M %p') // tooltip date/time format
                    .onLabelClick(function (a, b) { showUserProfileOrProject(a, b); }) //callback function for clicking on the username axis labels
                    .zQualitative(true) //segment data color values are categorical (true) or quantitative (false)
                    (document.getElementById('location_analyticsIframe')); //chart container

                //set color palette for location types
                myChart.zColorScale().domain(['At Home', 'In Office', 'In Transit', 'At Client', 'On Site', 'Private']);
                myChart.zColorScale().range(['#77b64c', '#74a9da', '#af7ad6', '#ee8640', '#ffc000', '#dcdcdc']);

                //set initial marker date
                markerDate = new Date(); //now
                markerDate = new Date(markerDate.getTime() + (markerDate.getTimezoneOffset() * 60000)); //adjust for timezone
                if (markerDate < myChart.zoomX()[0] || markerDate > myChart.zoomX()[1]) { //out of data range
                    markerDate = myChart.zoomX()[1]; //set to end date of data range
                };
                //set date on marker date picker
                document.getElementById('markerDate').value = formatTime(markerDate);
                document.getElementById('markerDate').min = formatTime(myChart.zoomX()[0]); //data start date
                document.getElementById('markerDate').max = formatTime(myChart.zoomX()[1]); //data end date
                //display marker on chart and update bar chart
                setMarker();

                //resize
                window.addEventListener('resize', windowResize);
                windowResize();
                //$("#barChart").show();
                $("#locationMenuPanel").show();
                // document.getElementById('barChart').style.width = document.getElementsByClassName('legend')[0].getBBox().width;

            });
        });


        $(window).on('resize', function(event) {
            event.preventDefault();
            windowResize();
        });

		$(".sidebar-toggle").click(function() {
			setTimeout(function(){
				windowResize();
			},300)
		})

        ;($.resizeWrapper = function(){
          //  console.log('resizeWrapper==',(($(window).height() - $('#location_analyticsIframe').offset().top) ) - 30)
           // console.log('resize ==',$(window).height()-320)
            setTimeout(() => {
                $('#location_analyticsIframe').animate({
                    minHeight: (($(window).height() - $('#location_analyticsIframe').offset().top) ) - 12,
                    height: (($(window).height() - $('#location_analyticsIframe').offset().top) ) - 12,
                }, 1)
            }, 200);
        })();

    })

</script>
