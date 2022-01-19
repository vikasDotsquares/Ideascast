<!--Copyright 2010-2020 Mike Bostock All rights reserved.-->
<?php echo $this->Html->script('projects/d3.v6.min', array('inline' => true)); ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
// pr($passed_project_id);
?>
<div class="box-header filters" style="padding: 10px;">
    <div class="select-dm-wrap popup-select-icon">
        <div class="select-dm-filed">
            <label class="custom-dropdown social-select-dropdown">
                <select class="aqua" name="demand_competency_type" id="demand_competency_type">
                    <option value="">Select Competency</option>
                    <option value="skills" <?php echo ($passed_type == 'skill') ? 'selected' : ''; ?>>Skills</option>
                    <option value="subjects" <?php echo ($passed_type == 'subject') ? 'selected' : ''; ?>>Subjects</option>
                    <option value="domains" <?php echo ($passed_type == 'domain') ? 'selected' : ''; ?>>Domains</option>
                </select>
            </label>
       </div>
        <div class="select-dm-filed">
            <label class="custom-dropdown">
                <select class="aqua" name="demand_competency_from_1" id="demand_competency_from_1">
                    <option value="">Select From</option>
                    <option value="community">All Community</option>
                    <option value="all_projects">All My Projects</option>
                    <option value="created_projects">Projects I Created</option>
                    <option value="owner_projects">Projects I Own</option>
                    <option value="shared_projects">Projects Shared With Me</option>
                    <option value="project">Specific Projects</option>
                </select>
            </label>
             <label class="social-select-dropdown specific-section">
                <select class="aqua" name="demand_specific_items_1" id="demand_specific_items_1" multiple="">
                </select>
            </label>
       </div>
        <div class="select-dm-filed">
            <label class=" social-select-dropdown">
                <select class="aqua" name="demand_status" id="demand_status" multiple="">
                    <option value="not_set" selected="selected">Not Set</option>
                    <option value="not_started" selected="selected">Not Started</option>
                    <option value="in_progress" selected="selected">In Progress</option>
                    <option value="overdue" selected="selected">Overdue</option>
                    <option value="completed" selected="selected">Completed</option>
                </select>
            </label>
       </div>
        <span class="vs">VS</span>
        <div class="select-dm-filed">
            <label class="custom-dropdown">
                <select class="aqua" name="demand_competency_from_2" id="demand_competency_from_2">
                    <option value="">Select People From</option>
                    <option value="community">All Community</option>
                    <option value="organizations">Specific Organizations</option>
                    <option value="locations">Specific Locations</option>
                    <option value="departments">Specific Departments</option>
                    <option value="users">Specific People</option>
                    <option value="competencies">Specific Competencies</option>
                    <option value="all_projects">All My Projects</option>
                    <option value="created_projects">Projects I Created</option>
                    <option value="owner_projects">Projects I Own</option>
                    <option value="shared_projects">Projects Shared With Me</option>
                    <option value="project">Specific Projects</option>
                </select>
            </label>
            <label class="social-select-dropdown specific-section">
                <select class="aqua" name="demand_specific_items_2" id="demand_specific_items_2" multiple="">
                </select>
            </label>
        </div>
		<div class="select-dm-filed select-level-filed-from">
            <label class="custom-dropdown">
                <select class="aqua" name="by_level" id="by_level">
                    <option value="level" selected="">By Level</option>
                    <option value="exp">By Experience</option>
                </select>
            </label>

        </div>
        <div class="select-ka-btn">
            <button class="btn btn-success btn-show-demand dm-show" type="button">Show</button>
       </div>

    </div>
</div>
<div class="box-body clearfix border1"  style=" padding: 0;" id="box_body" >
    <div id="sd_analyticsIframe" style="width: 100%; border: none; height: 100%; overflow: none; padding-top: 12px; min-height: 700px;">
        <div class="" style="padding-top: 10px;">
            <div class="col-sm-12 box-borders select-project-sec">
                <div class="no-data">SELECT COMPETENCIES, PROJECTS AND PEOPLE</div>
            </div>
        </div>
    </div>
</div>


<script>
    $('#sd_analyticsIframe').animate({
            minHeight: (($(window).height() - $('#sd_analyticsIframe').offset().top) ) - 17,
        }, 1)
    $(function(){

        $.show_demand_btn = function(){
            $.btn_clicked = false;
            var demand_competency_type = $('#demand_competency_type').val();

            var from_1 = $('#demand_competency_from_1').val();
            var items_1 = $('#demand_specific_items_1').val() || [];

            var from_2 = $('#demand_competency_from_2').val();
            var items_2 = $('#demand_specific_items_2').val() || [];

            var demand_status = $('#demand_status').val() || [];


            $('.btn-show-demand').prop('disabled', true).addClass('dm-show');
            if(demand_competency_type != '' && from_1 != '' && from_2 != '' && demand_status.length > 0) {

                if((from_1 == 'community' || from_1 == 'all_projects' || from_1 == 'created_projects' || from_1 == 'owner_projects' || from_1 == 'shared_projects') && (from_2 == 'community' || from_2 == 'all_projects' || from_2 == 'created_projects' || from_2 == 'owner_projects' || from_2 == 'shared_projects')){
                    $('.btn-show-demand').prop('disabled', false).removeClass('dm-show');
                }
                else if((from_1 == 'community' || from_1 == 'all_projects' || from_1 == 'created_projects' || from_1 == 'owner_projects' || from_1 == 'shared_projects')){
                    if((from_2 == 'organizations' || from_2 == 'locations' || from_2 == 'departments' || from_2 == 'owner_projects' || from_2 == 'users' || from_2 == 'competencies' || from_2 == 'project') && items_2.length > 0){
                        $('.btn-show-demand').prop('disabled', false).removeClass('dm-show');
                    }
                }
                else if((from_2 == 'community' || from_2 == 'all_projects' || from_2 == 'created_projects' || from_2 == 'owner_projects' || from_2 == 'shared_projects')){
                    if((from_1 == 'organizations' || from_1 == 'locations' || from_1 == 'departments' || from_1 == 'owner_projects' || from_1 == 'users' || from_1 == 'competencies' || from_1 == 'project') && items_1.length > 0){
                        $('.btn-show-demand').prop('disabled', false).removeClass('dm-show');
                    }
                }
                else if(((from_1 == 'organizations' || from_1 == 'locations' || from_1 == 'departments' || from_1 == 'owner_projects' || from_1 == 'users' || from_1 == 'competencies' || from_1 == 'project') && items_1.length > 0) && ((from_2 == 'organizations' || from_2 == 'locations' || from_2 == 'departments' || from_2 == 'owner_projects' || from_2 == 'users' || from_2 == 'competencies' || from_2 == 'project') && items_2.length > 0)){
                    $('.btn-show-demand').prop('disabled', false).removeClass('dm-show');
                }
            }
        }

        $.get_demand_data = function(data){
            $('#demand_specific_items_1').empty();
            $.ajax({
                url: $js_config.base_url + 'subdomains/get_demand_data',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.success){
                        if(response.content){
                            var content = response.content.sort(sort_object);

                            $("#demand_specific_items_1").prop('disabled', false).multiselect('dataprovider', content);
                            $.show_demand_btn();
                        }
                    }
                }
            })
        }

        $.get_demand_items = function(data){

            $('#demand_specific_items_2').empty();
            $.ajax({
                url: $js_config.base_url + 'subdomains/get_demand_data',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.success){
                        if(response.content){
                            var content = response.content.sort(sort_object);
                            $("#demand_specific_items_2").prop('disabled', false).multiselect('dataprovider', content);
                            $.show_demand_btn();
                        }
                    }
                }
            })
        }


        $('#demand_specific_items_2, #demand_specific_items_2').on('change', function(event) {
            event.preventDefault();
            $.show_demand_btn();
        });

        $('#demand_status').on('change', function(event) {
            event.preventDefault();
            var status = $(this).val();
            $.show_demand_btn();
        })

        $('#demand_competency_type').on('change', function(event) {
            event.preventDefault();
            var competency = $(this).val();
            var type_1 = $('#demand_competency_from_1').val();
            var type_2 = $('#demand_competency_from_2').val();
            $.show_demand_btn();
            if(type_1 == '') {
                $("#demand_specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
            }
            else if(type_1 == 'competencies' ){
                if(competency != ''){
                    var data = {competency_type: competency, type: type_1};
                    $.get_demand_data(data);
                }
                else{
                    $("#demand_specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
                }
            }

            if(type_2 == '') {
                $("#demand_specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
            }
            else if(type_2 == 'competencies' ){
                if(competency != ''){
                    var data = {competency_type: competency, type: type_2};
                    $.get_demand_items(data);
                }
                else{
                    $("#demand_specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
                }
            }
        })

        $('#demand_competency_from_1').on('change', function(event) {
            event.preventDefault();

            var competency = $('#demand_competency_type').val();
            var type = $(this).val()
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                $(this).parents('.select-dm-filed:first').find('.specific-section').slideUp(500, function(){
                    $.resize_container();
                });
            }
            else{
                $(this).parents('.select-dm-filed:first').find('.specific-section').slideDown(500, function(){
                    $.resize_container();
                });
            }

            $.show_demand_btn();
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                $("#demand_specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
                return;
            }
            else if(type != '') {
                $("#demand_specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
                var data = {type: type};
                $.get_demand_data(data);
            }

        });

        $('#demand_competency_from_2').on('change', function(event) {
            event.preventDefault();
            var competency = $('#demand_competency_type').val();
            var type = $(this).val()
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                $(this).parents('.select-dm-filed:first').find('.specific-section').slideUp(500, function(){
                    $.resize_container();
                });
            }
            else{
                $(this).parents('.select-dm-filed:first').find('.specific-section').slideDown(500, function(){
                    $.resize_container();
                });
            }

            $.show_demand_btn();
            $("#demand_specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                return;
            }
            else if(type == 'competencies' && competency != '') {
                var data = {competency_type: competency, type: type};
                $.get_demand_items(data);
            }
            else if(type != 'competencies') {
                var data = {type: type};
                $.get_demand_items(data);
            }
        });

        function sort_object (a, b){
            var aName = a.label.toLowerCase();
            var bName = b.label.toLowerCase();
            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
        }



        ($.resize_container = function(){
            $('#sd_analyticsIframe').animate({
                minHeight: (($(window).height() - $('#sd_analyticsIframe').offset().top) ) - 17,
                maxHeight: (($(window).height() - $('#sd_analyticsIframe').offset().top) ) - 17,
            }, 1)
        })();

        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                if($('#ka_tabs li.active a').attr('href') == '#ka_demand'){
                    $.resize_container();
                }
                clearInterval(interval);
            }
        }, 1);

        $(".sidebar-toggle").click(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_demand'){
                $.resize_container();
            }
        })

        $(window).resize(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_demand'){
                $.resize_container();
            }
        })

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        var sd_data;

        //set width and height
        const sd_width = document.getElementById('sd_analyticsIframe').clientWidth;
        const sd_margin = ({top: 40, right: 30, bottom: 0, left: 240});


        //get data
        $('.btn-show-demand').on('click', function(event) {
            $("#sd_analyticsIframe").html('');
            // $js_config.base_url + "subdomains/ka_demand_json/"
            // "http://192.168.7.20/ideascast/app/webroot/demand.json"
            var competency_type = $("#demand_competency_type").val();
            var competency_from_1 = $("#demand_competency_from_1").val();
            var specific_items_1 = $("#demand_specific_items_1").val();
            var competency_from_2 = $("#demand_competency_from_2").val();
            var specific_items_2 = $("#demand_specific_items_2").val();
            var demand_status = $("#demand_status").val();
            var by_level = $("#by_level").val();

            d3.json($js_config.base_url + "subdomains/ka_demand_json/", {
                          method:"POST",
                          body: JSON.stringify({
                            competency_type: competency_type,
                            competency_from_1: competency_from_1,
                            specific_items_1: specific_items_1,
                            competency_from_2: competency_from_2,
                            specific_items_2: specific_items_2,
                            demand_status: demand_status,
                            by_level: by_level,
                          }),
                          headers: {
                            "Content-type": "application/json; charset=UTF-8"
                          }
                        }
                ).then(function (sd_data) {


                    //extract and remove totals
                    const sd_projects = sd_data.filter(d => d.name == "Total Projects" && d.category == "System")[0].value;
                    const sd_people = sd_data.filter(d => d.name == "Total People" && d.category == "System")[0].value;
                    sd_data = sd_data.filter(d => d.category != "System");

                    if(sd_data.length <= 0){
                        $("#sd_analyticsIframe").html('<div class="col-sm-12 box-borders select-project-sec">\
                                    <div class="no-data">NO SUPPLY OR DEMAND RESULTS</div>\
                                </div>');
                        throw new Error("No Supply or Demand Data - Managed Exit.");
                    }

                    sd_data = Object.assign(sd_data, {
                        format: ",d",
                        negative: "← Demand",
                        positive: "Supply →",
                        negatives: ["None", "Not Started", "In Progress", "Overdue", "Completed"],
                        // 19-march
                        positives: ["Beginner", "Intermediate", "Advanced", "Expert", "1 Year", "2 Years", "3 Years", "4 Years", "5 Years", "6-10 Years", "11-15 Years", "16-20 Years", "Over 20 Years"]
                        // positives: ["People"]
                    });
                    sd_data.negative = "← Demand (From " + sd_formatValue(sd_projects) + (sd_projects == 1 ? " Project)" : " Projects)");
                    sd_data.positive = "Supply (From " + sd_formatValue(sd_people) + (sd_people == 1 ? " Person) →" : " People) →");
                    const sd_signs = new Map([].concat(
                        sd_data.negatives.map(d => [d, -1]),
                        sd_data.positives.map(d => [d, +1])
                        ));

                    const sd_bias = d3.rollups(sd_data, v => d3.sum(v, d => d.value * Math.min(0, sd_signs.get(d.category))), d => d.name)
                        .sort(([, a], [, b]) => d3.ascending(a, b));

                    const sd_height = sd_bias.length * 30 + sd_margin.top + sd_margin.bottom + 40;

                    const sd_series = d3.stack()
                        .keys([].concat(sd_data.negatives.slice().reverse(), sd_data.positives))
                        .value(([, value], category) => sd_signs.get(category) * (value.get(category) || 0))
                        .offset(d3.stackOffsetDiverging)
                    (d3.rollups(sd_data, data => d3.rollup(data, ([d]) => d.value, d => d.category), d => d.name));

                    const sd_color = d3.scaleOrdinal()
                        .domain([].concat(sd_data.negatives, sd_data.positives))
                         .range(["#a6a6a6", "#666666", "#e3a809", "#e5030d", "#5f9322", "#b1d2e5", "#8abbd7", "#62a4ca", "#3c8dbc", "#b1d2e5", "#9ec6de", "#8abbd7", "#77afd0", "#62a4ca", "#4f98c3", "#3c8dbc", "#367faa", "#2f7197"]);


                    var x = d3.scaleLinear()
                        .domain(d3.extent(sd_series.flat(2)))
                        .rangeRound([sd_margin.left, sd_width - sd_margin.right]);

                    var y = d3.scaleBand()
                        .domain(sd_bias.map(([name]) => name))
                        .rangeRound([sd_margin.top, sd_height - sd_margin.top])
                        .padding(2/30)
                        .align(0);

                    const sd_svg = d3.select("#sd_analyticsIframe").append("svg")
                        .attr("viewBox", [0, 0, sd_width, sd_height]);

                    //rects
                    sd_svg.append("g")
                        .selectAll("g")
                        .data(sd_series)
                        .join("g")
                            .attr("fill", d => sd_color(d.key))
                            .attr("opacity", 1)
                            .selectAll("rect")
                            .data(d => d.map(v => Object.assign(v, {key: d.key})))
                            .join("rect")
                                .attr("x", d => x(d[0]))
                                .attr("y", ({data: [name]}) => y(name) + 30)
                                .attr("width", d => x(d[1]) - x(d[0]))
                                .attr("height", y.bandwidth())

                                // 19-march
                                .style("cursor", d => sd_data.positives.includes(d.key) ? "pointer" : "default")
                                .on("click", (event, d) => (sd_data.positives.includes(d.key) ? sd_gotoCACompetency(d.data[0]) : null, event.preventDefault(), event.stopPropagation()))


                                .attr("class", 'tipOver')
                                // 19 march
                                .attr("title", ({key, data: [name, value]}) => sd_data.positives.includes(key) ?
                                    `${name} (${sd_formatPC(value.get(key)/sd_people)})\n${sd_formatValue(value.get(key))} ${sd_formatValue(value.get(key)) == 1 ? 'Person' : 'People'} (${key}) of ${sd_formatValue(sd_people)} ${sd_formatValue(sd_people)==1 ? 'Person' : 'People'} can Supply\nClick to Goto Competencies` :
                                    `${name} (${sd_formatPC(value.get(key)/sd_projects)})\n${sd_formatValue(value.get(key))} ${key} Project(s) of ${sd_formatValue(sd_projects)} Total Project(s) have Demand`)
                                /*.append("title")

                                .text(({key, data: [name, value]}) => sd_data.positives.includes(key) ?
                            `${name} (${sd_formatPC(value.get(key)/sd_people)})\n${sd_formatValue(value.get(key))} ${sd_formatValue(value.get(key)) == 1 ? 'Person' : 'People'} (${key}) of ${sd_formatValue(sd_people)} ${sd_formatValue(sd_people)==1 ? 'Person' : 'People'} can Supply\nClick to Goto Competencies` :

                                .text(({key, data: [name, value]}) => key == "People" ?
                                    `${name} (${sd_formatPC(value.get(key)/sd_people)})\n${sd_formatValue(value.get(key))} of ${sd_formatValue(sd_people)} People can Supply` :
                                    `${name} (${sd_formatPC(value.get(key)/sd_projects)})\n${sd_formatValue(value.get(key))} ${key} Project(s) of ${sd_formatValue(sd_projects)} Total Project(s) have Demand`);*/

                    //labels
                    sd_svg.append("g")
                        .selectAll("g")
                        .data(sd_series)
                        .join("g")
                            .attr("fill", "#fff")
                            .style("font" ,"12px Open Sans")
                            .selectAll("text")
                            .data(d => d.map(v => Object.assign(v, {key: d.key})))
                            .join("text")
                                .attr("x", d => sd_data.positives.includes(d.key) ? x(d[1]) : x(d[0]))
                                .attr("y", ({data: [name]}) => y(name) + 30)
                                .attr("dy", 17)
                                .attr("dx", d => sd_data.positives.includes(d.key) ? -4 : 4 )
                                .style("visibility", d => Math.abs(x(d[1])) - Math.abs(x(d[0])) < 24 ? "hidden" : "visible")
                                .style("text-anchor", d => sd_data.positives.includes(d.key) ? "end" : "start")
                                .attr("pointer-events", "none")
                                .text(({key, data: [name, value]}) => value.get(key) == 0 ? "" : value.get(key));

                    //x axis
                    sd_svg.append("g")
                        .call(sd_xAxis);

                    //y axis
                    sd_svg.append("g")
                        .call(sd_yAxis);

                    function sd_formatValue(sd_val) {
                        const sd_format = d3.format(sd_data.format || "");
                        return sd_format(Math.abs(sd_val));
                    }

                    function sd_formatPC(sd_val2) {
                        const sd_formatPC = d3.format(".1%" || "");
                        return sd_formatPC(Math.abs(sd_val2));
                    }

                    function sd_xAxis(g) {
                        g
                        .style("font-family", "Open Sans")
                        .style("font-size", "12px")
                        .style("color", "#444")
                        .style("font-weight", "400")
                        .style("text-transform", "uppercase")
                        .attr("transform", `translate(0,${sd_margin.top + 20})`)
                        .call(d3.axisTop(x)
                            .ticks((Math.abs(x.domain()[0]) + Math.abs(x.domain()[1]))<20 ? Math.abs(x.domain()[0]) + Math.abs(x.domain()[1]) : 20)
                            .tickFormat(sd_formatValue)
                            .tickSizeOuter(0))
                        .call(g => g.select(".domain").remove())
                        .call(g => g.append("text")
                            .style("font-size", "14px")
                            .style("font-weight", "600")
                            .style("text-transform", "uppercase")
                            .attr("x", x(0) + 20)
                            .attr("y", -34)
                            .attr("fill", "currentColor")
                            .attr("text-anchor", "start")
                            .text(sd_data.positive))
                        .call(g => g.append("text")
                            .style("font-size", "14px")
                            .style("font-weight", "600")
                            .style("text-transform", "uppercase")
                            .attr("x", x(0) - 20)
                            .attr("y", -34)
                            .attr("fill", "currentColor")
                            .attr("text-anchor", "end")
                            .text(sd_data.negative));
                    }

                    function sd_yAxis(g) {
                        g
                        .style("font-family", "Open Sans")
                        .style("font-size", "12px")
                        .style("color", "#444")
                        .style("cursor", "pointer")
                        .attr("class", "tipOver")
                        .call(d3.axisLeft(y).tickSizeOuter(0))
                        .call(g => g.selectAll(".tick").data(sd_bias).attr("transform", ([name, min]) => `translate(${x(min)},${y(name) + 30 + y.bandwidth() / 2})`))
                        .call(g => g.select(".domain").attr("transform", `translate(${x(0)}, 20)`))
                        .call(g => g.selectAll(".tick").data(sd_bias).call(d => g.selectAll(".tick").on("click", (event, d) => (sd_showProfileDialog(d[0]), event.preventDefault(), event.stopPropagation())))

                        .attr("title", d =>
                    d[0] + " (" + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && sd_data.negatives.includes(n.category)), v => v.value) / sd_projects) + " : " + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && sd_data.positives.includes(n.category)), v => v.value) / sd_people) + ")\n" +
                    "Demand: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && sd_data.negatives.includes(n.category)), v => v.value)) + " of " + sd_formatValue(sd_projects) + " Project(s)\n" +
                    "Supply: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && sd_data.positives.includes(n.category)), v => v.value)) + " of " + sd_formatValue(sd_people) + " People\n" +
                    "Click to View Profile"));


                          /*.attr("title", d =>
                            d[0] + " (" + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && n.category !== "People"), v => v.value) / sd_projects) + " : " + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && n.category == "People"), v => v.value) / sd_people) + ")\n" +
                            "Demand: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && n.category !== "People"), v => v.value)) + " of " + sd_formatValue(sd_projects) + " Project(s)\n" +

                            "Supply: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && n.category == "People"), v => v.value)) + " of " + sd_formatValue(sd_people) + " People\n" +
                            "Click to View Profile"));*/
                        /*.append("title").text(d =>
                            d[0] + " (" + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && n.category !== "People"), v => v.value) / sd_projects) + " : " + sd_formatPC(d3.sum(sd_data.filter(n => n.name == d[0] && n.category == "People"), v => v.value) / sd_people) + ")\n" +
                            "Demand: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && n.category !== "People"), v => v.value)) + " of " + sd_formatValue(sd_projects) + " Project(s)\n" +

                            "Supply: " + sd_formatValue(d3.sum(sd_data.filter(n => n.name == d[0] && n.category == "People"), v => v.value)) + " of " + sd_formatValue(sd_people) + " People\n" +
                            "Click to View Profile"))*/
                        $('.tipOver,.tick').tooltip({
                            html: false,
                            template: '<div class="tooltip image-tip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner tip-demand"></div></div>',
                            placement: 'top',
                            container: 'body',
                            delay: 100
                        })

                    }

                    function sd_gotoCACompetency(sd_comp_name) { //NEW FUNCTION

                        var comp_type = $('#demand_competency_type').val();
                        var selected_id = sd_data.filter(d => d.name == sd_comp_name)[0].id;

                        var comp_type = (comp_type == 'skills') ? 'skill' : ((comp_type=='subjects') ? 'subject' : 'domain');
                        var url = $js_config.base_url + 'analytics/knowledge/' + comp_type + '/competency:' + selected_id
                        var sel_2 = $('#demand_competency_from_2').val();
                        var sel_spf_2 = $('#demand_specific_items_2').val() || [];
                        if(sel_2 != ''){
                            if(sel_spf_2.length > 20 && (sel_2 != 'community' || sel_2 != 'all_projects' || sel_2 != 'created_projects' || sel_2 != 'owner_projects' || sel_2 != 'shared_projects')){
                                if(sel_2 == 'organizations' || sel_2 == 'locations' || sel_2 == 'departments' || sel_2 == 'users' || sel_2 == 'competencies'){
                                    sel_2 = 'community';
                                    url = url + '/pfrom:' + sel_2;
                                }
                                else if(sel_2 == 'project'){
                                    sel_2 = 'all_projects';
                                    url = url + '/pfrom:' + sel_2;
                                }
                            }
                            else{
                                url = url + '/pfrom:' + sel_2 + '/spf2:' + sel_spf_2.join(',');
                            }

                        }
                        // console.log(url)
                        location.href = url

                        //Call CA Competency Tab Analytic with drop down values:
                        //Select Competency: same as selected on Demand tab
                        //Select From: Specific Competencies
                        //Select Specific Items: sd_data.filter(d => d.name == sd_comp_name)[0].id
                        //Select People From: same as selected on Demand tab
                        //Select Specific Items: same as selected on Demand tab
                    }


                    function sd_skillProfile(id) {
                        $('#modal_view_skill').modal({
                            remote: $js_config.base_url + 'competencies/view_skills/' + id
                        }).show();
                    }
                    function sd_subjectProfile(id) {
                        $('#modal_view_skill').modal({
                            remote: $js_config.base_url + 'competencies/view_subjects/' + id
                        }).show();
                    }
                    function sd_domainProfile(id) {
                        $('#modal_view_skill').modal({
                            remote: $js_config.base_url + 'competencies/view_domains/' + id
                        }).show();
                    }

                    function sd_showProfileDialog(sd_comp_name) {
                        var itemId = sd_data.filter(d => d.name == sd_comp_name)[0].id;
                        var competency_type = $('#demand_competency_type').val();
                        switch (competency_type) { //need type variable for value here
                            case "skills":
                                sd_skillProfile(itemId);
                                break;
                            case "subjects":
                                sd_subjectProfile(itemId);
                                break;
                            case "domains":
                                sd_domainProfile(itemId);
                                break;
                        }
                    }
                    $.resize_container();
            });
        });

        $specific_items_1 = $('#demand_specific_items_1').multiselect({
                enableUserIcon: false,
                buttonClass: 'btn btn-info aqua',
                buttonWidth: '100%',
                buttonContainerWidth: '100%',
                numberDisplayed: 0,
                maxHeight: '327',
                checkboxName: 'dept[]',
                includeSelectAllOption: true,
                enableFiltering: true,
                disableIfEmpty: true,
                filterPlaceholder: 'Search Specific Items',
                enableCaseInsensitiveFiltering: true,
                nonSelectedText: 'Select Specific Items',
                onSelectAll:function(){
                    $.show_demand_btn();
                },
                onDeselectAll:function(){
                    $.show_demand_btn();
                },
                onChange: function(element, checked) {
                    $.show_demand_btn();
                }
            });
        $specific_items_2 = $('#demand_specific_items_2').multiselect({
                enableUserIcon: false,
                buttonClass: 'btn btn-info aqua',
                buttonWidth: '100%',
                buttonContainerWidth: '100%',
                numberDisplayed: 0,
                maxHeight: '327',
                checkboxName: 'dept[]',
                includeSelectAllOption: true,
                enableFiltering: true,
                disableIfEmpty: true,
                filterPlaceholder: 'Search Specific Items',
                enableCaseInsensitiveFiltering: true,
                nonSelectedText: 'Select Specific Items',
                onSelectAll:function(){
                    $.show_demand_btn();
                },
                onDeselectAll:function(){
                    $.show_demand_btn();
                },
                onChange: function(element, checked) {
                    $.show_demand_btn();
                }
            });
        $demand_status = $('#demand_status').multiselect({
                enableUserIcon: false,
                buttonClass: 'btn btn-info aqua',
                buttonWidth: '100%',
                buttonContainerWidth: '100%',
                numberDisplayed: 0,
                maxHeight: '327',
                checkboxName: 'dept[]',
                includeSelectAllOption: true,
                enableFiltering: true,
                disableIfEmpty: true,
                filterPlaceholder: 'Search Status',
                enableCaseInsensitiveFiltering: true,
                nonSelectedText: 'Select Status',
                selectAllText: 'All Status',
                onSelectAll:function(){
                    $.show_demand_btn();
                },
                onDeselectAll:function(){
                    $.show_demand_btn();
                },
                onChange: function(element, checked) {
                    $.show_demand_btn();
                }
            });
    })
</script>
