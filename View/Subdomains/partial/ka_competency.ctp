<!--Copyright 2010-2020 Mike Bostock All rights reserved.-->
<?php echo $this->Html->script('projects/d3.v5.min', array('inline' => true)); ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
$pfrom = (isset($pfrom) && !empty($pfrom)) ? $pfrom : false;
// pr($competency);
?>
    <div class="box-header filters" style="padding: 10px;">
        <div class="select-ka-wrap popup-select-icon">
            <div class="select-ka-filed">
                <label class="custom-dropdown social-select-dropdown">
                    <select class="aqua" name="competency_type" id="competency_type">
                        <?php if(isset($passed_type) && $passed_type == 'skill'){ ?>
                            <option value="skills" selected="selected">Skills</option>
                        <?php }else if(isset($passed_type) && $passed_type == 'subject'){ ?>
                            <option value="subjects" selected="selected">Subjects</option>
                        <?php }else if(isset($passed_type) && $passed_type == 'domain'){  ?>
                            <option value="domains" selected="selected">Domains</option>
                        <?php }else{ ?>
                            <option value="">Select Competency</option>
                            <option value="skills">Skills</option>
                            <option value="subjects">Subjects</option>
                            <option value="domains">Domains</option>
                        <?php } ?>
                    </select>
                </label>
           </div>
            <div class="select-ka-filed">
                <label class="custom-dropdown social-select-dropdown">
                    <select class="aqua" name="competency_from_1" id="competency_from_1">
                    <?php if(isset($projects) && !empty($projects)){ ?>
                        <option value="project" selected="selected">Specific Projects</option>
                    <?php }else if(isset($competency) && !empty($competency)){ ?>
                        <option value="competencies" selected="selected">Specific Competencies</option>
                    <?php }else{ ?>
                        <option value="">Select From</option>
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
                    <?php } ?>
                    </select>
                </label>
           </div>
            <div class="select-ka-filed">

                <label class=" social-select-dropdown">
                    <?php if(isset($projects) && !empty($projects)){ ?>
                        <select class="aqua" name="specific_items_1" id="specific_items_1" multiple="">
                            <?php foreach ($projects as $key => $value) {
                            ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                            <?php
                            } ?>
                        </select>
                    <?php }else if(isset($competency) && !empty($competency)){ ?>
                        <select class="aqua" name="specific_items_1" id="specific_items_1" multiple="">
                            <?php foreach ($competency as $key => $value) {
                            ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                            <?php
                            } ?>
                        </select>
                    <?php }else{ ?>
                        <select class="aqua" name="specific_items_1" id="specific_items_1" multiple="">
                        </select>
                    <?php } ?>
                </label>
           </div>
            <span class="vs">VS</span>
            <div class="select-ka-filed">
                <label class="custom-dropdown social-select-dropdown">
                    <select class="aqua" name="competency_from_2" id="competency_from_2">
                    <?php if(isset($projects) && !empty($projects)){ ?>
                        <option value="project" selected="selected">Specific Projects</option>
                    <?php }else if($pfrom && $pfrom == 'community'){ ?>
                        <option value="community" selected="selected">All Community</option>
                    <?php }else if($pfrom && $pfrom == 'organizations'){ ?>
                        <option value="organizations">Specific Organizations</option>
                    <?php }else if($pfrom && $pfrom == 'locations'){ ?>
                        <option value="locations">Specific Locations</option>
                    <?php }else if($pfrom && $pfrom == 'departments'){ ?>
                        <option value="departments">Specific Departments</option>
                    <?php }else if($pfrom && $pfrom == 'users'){ ?>
                        <option value="users">Specific People</option>
                    <?php }else if($pfrom && $pfrom == 'competencies'){ ?>
                        <option value="competencies">Specific Competencies</option>
                    <?php }else if($pfrom && $pfrom == 'all_projects'){ ?>
                        <option value="all_projects">All My Projects</option>
                    <?php }else if($pfrom && $pfrom == 'created_projects'){ ?>
                        <option value="created_projects">Projects I Created</option>
                    <?php }else if($pfrom && $pfrom == 'owner_projects'){ ?>
                        <option value="owner_projects">Projects I Own</option>
                    <?php }else if($pfrom && $pfrom == 'shared_projects'){ ?>
                        <option value="shared_projects">Projects Shared With Me</option>
                    <?php }else if($pfrom && $pfrom == 'project'){ ?>
                        <option value="project">Specific Projects</option>
                    <?php }else{ ?>
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
                    <?php } ?>
                    </select>
                </label>
            </div>
            <div class="select-ka-filed">
                <label class=" social-select-dropdown">
                    <?php if(isset($projects) && !empty($projects)){ ?>
                    <select class="aqua"  name="specific_items_2" id="specific_items_2" multiple="">
                        <?php foreach ($projects as $key => $value) {
                        ?>
                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php
                        } ?>
                    </select>
                    <?php }elseif(isset($spf2_data) && !empty($spf2_data)){ ?>
                        <select class="aqua"  name="specific_items_2" id="specific_items_2" multiple="">
                            <?php foreach ($spf2_data as $key => $value) { ?>
                            <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['label']; ?></option>
                            <?php } ?>
                        </select>
                    <?php }else{ ?>
                        <select class="aqua"  name="specific_items_2" id="specific_items_2" multiple=""></select>
                    <?php } ?>
                </label>
           </div>
            <div class="select-ka-btn">
                <button class="btn btn-success btn-show-graph ka-show" type="button">Show</button>
                <button class="btn btn-danger btn-reset" style="display: none;" type="button">Reset</button>
           </div>

        </div>
    </div>
    <div class="box-body clearfix border1"  style=" padding: 0;" id="box_body" >
        <div id="analyticsIframe" class="module-wrapper" style="width:100%;   border: none; overflow: hidden; min-height: 700px;" >
            <div class="" style="padding-top: 10px;">
                <div class="col-sm-12 box-borders select-project-sec">
                    <div class="no-data">SELECT COMPETENCIES AND PEOPLE</div>
                </div>
            </div>
        </div>
        <div id="menuPanel" class="menuGroup" style="display: none;">
            <input id="findTextInput" class="findTextInput" placeholder="Find..." maxlength="64" spellcheck="false" onkeyup="if ((event.keyCode === 13) || (this.value.length===0)) { peopleList.value = 'All People'; findText() };">
            <div class="menuGroup-wrap">
                <div class="searchGroup">
                    <input id="skillsOption" name="searchOptionChoice"  type="radio" value="Skill" onclick="peopleList.value = 'All People'; findTextInput.focus();" /><label for="skillsOption">Skills</label>
                    <input id="peopleOption" name="searchOptionChoice"  type="radio" value="People" onclick="peopleList.value = 'All People'; findTextInput.focus();" /><label for="peopleOption">People</label>
                    <input id="bothOption" name="searchOptionChoice" type="radio" value="Both" checked onclick="peopleList.value = 'All People'; findTextInput.focus();" /><label for="bothOption">Both</label>
                </div>

                <select id="skillsList" class="skillsDropDownList" onchange="skillsListChange(this.value);">
                </select>
                <span class="ka-competencies-icon"><a href="#" class="tipText go-to-competency go-to-disabled" data-original-title="Go To Competency Search"> <i class="competenciesblackicon"></i></a></span>
                <div class="skillsGroup">
                    <input id="withPeopleOption" name="skillOptionChoice" type="radio" value="With People" onclick="loadSkills(); skillsList.focus(); nodeZoom(null);" /><label for="withPeopleOption">With People</label>
                    <input id="noPeopleOption" name="skillOptionChoice"  type="radio" value="No People" onclick="loadSkills(); skillsList.focus(); nodeZoom(null);" /><label for="noPeopleOption">No People</label>
                    <input id="allPeopleOption" name="skillOptionChoice" type="radio" value="All" checked onclick="loadSkills(); skillsList.focus(); nodeZoom(null);" /><label for="allPeopleOption">All</label>
                </div>

                <select id="peopleList" class="peopleDropDownList" onchange="filterUser(this.value);">
                </select>
                <div class="peopleGroup">
                    <input id="withSkillsOption" name="peopleOptionChoice"  type="radio" value="With Skills" onclick="loadPeople(); peopleList.focus(); nodeZoom(null);" /><label  for="withSkillsOption">With Skills</label>
                    <input id="noSkillsOption" name="peopleOptionChoice" type="radio" value="No Skills" onclick="peopleList.focus(); loadPeople();" /><label  for="noSkillsOption">No Skills</label>
                    <input id="allSkillsOption" name="peopleOptionChoice" type="radio" value="All" checked onclick="loadPeople(); peopleList.focus(); nodeZoom(null)" /><label for="allSkillsOption">All</label>
                </div>

                <button id="resetAll" class="resetButton" onclick="resetAll();">Reset</button>

                <label id="analysisLabel" class="analysisLabel">Running Analysis...</label>
            </div>
        </div>
    </div>


<script>
    $(function(){

        $.get_data = $.get_item = false;
        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                if($js_config.project_id != '' && $js_config.project_id != null){
                    $('.btn-show-graph').trigger('click').hide();
                    $('.btn-reset').show();
                    $js_config.project_id = '';
                    clearInterval(interval);
                }
                else if($js_config.competency_id != '' && $js_config.competency_id != null){
                    $('.btn-show-graph').trigger('click').hide();
                    $('.btn-reset').show();
                    $js_config.competency_id = '';
                    clearInterval(interval);
                }
            }
        }, 1000);

        $('.btn-reset').on('click', function(event) {
            event.preventDefault();
            $.load_analytics('ka_competency');
            $('.btn-show-graph').show();
            $(this).hide();
        });

        $.show_btn = function(){
            $.btn_clicked = false;
            var competency_type = $('#competency_type').val();
            var competency_from_1 = $('#competency_from_1').val();
            var specific_items_1 = $('#specific_items_1').val() || [];
            var competency_from_2 = $('#competency_from_2').val();
            var specific_items_2 = $('#specific_items_2').val() || [];

            $('.btn-show-graph').show();
            $('.btn-reset').hide();

            $('.btn-show-graph').prop('disabled', true).addClass('ka-show');
            if(competency_type != '' && competency_from_1 != '' && competency_from_2 != '') {

                var all_1 = (competency_from_2 == 'community' || competency_from_2 == 'all_projects' || competency_from_2 == 'created_projects' || competency_from_2 == 'owner_projects' || competency_from_2 == 'shared_projects') ? true : false;
                var all_2 = (competency_from_2 == 'community' || competency_from_2 == 'all_projects' || competency_from_2 == 'created_projects' || competency_from_2 == 'owner_projects' || competency_from_2 == 'shared_projects') ? true : false;

                if( !all_1 && specific_items_1.length > 0 ){
                    if( !all_2 && specific_items_2.length > 0){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                    else if( all_2 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                }
                else if( !all_2 && specific_items_2.length > 0){
                    if( !all_1 && specific_items_1.length > 0){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                    else if( all_1 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                }
                else if( all_1 ){

                    if( all_2 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                    else if( !all_2 && specific_items_2.length > 0 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }

                }
                else if( all_2 ){
                    if( all_1 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                    else if( !all_1 && specific_items_1.length > 0 ){
                        $('.btn-show-graph').prop('disabled', false).removeClass('ka-show');
                    }
                }
            }
        }

        $.get_specific_data = function(data){
            $('#specific_items_1').empty();
            $.ajax({
                url: $js_config.base_url + 'subdomains/get_specific_data',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.success){
                        if(response.content){
                            var content = response.content.sort(sort_arr_obj);
                            $("#specific_items_1").prop('disabled', false).multiselect('dataprovider', content);
                            $("#specific_items_1").val([$js_config.project_id]).multiselect('select', [$js_config.project_id]);;
                            $.show_btn();
                            if($js_config.project_id){
                                $.get_data = true;
                            }
                        }
                    }
                }
            })
        }

        $.get_specific_items = function(data){

            if($js_config.project_id){
                data.project_id = $js_config.project_id;
            }
            $('#specific_items_2').empty();
            $.ajax({
                url: $js_config.base_url + 'subdomains/get_specific_data',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.success){
                        if(response.content){
                            var content = response.content.sort(sort_arr_obj);
                            $("#specific_items_2").prop('disabled', false).multiselect('dataprovider', content);
                            if($js_config.project_id){
                                $("#specific_items_2").val([$js_config.project_id]).multiselect('select', [$js_config.project_id]);
                                $.get_item = true;
                            }
                            $.show_btn();
                        }
                    }
                }
            })
        }

        $.btn_clicked = false;
        $specific_items_1 = $('#specific_items_1').multiselect({
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
                    $.show_btn();
                },
                onDeselectAll:function(){
                    $.show_btn();
                },
                onChange: function(element, checked) {
                    $.show_btn();
                }
            });

        $specific_items_2 = $('#specific_items_2').multiselect({
                enableUserIcon: false,
                buttonClass: 'btn btn-info aqua',
                buttonWidth: '100%',
                buttonContainerWidth: '100%',
                numberDisplayed: 0,
                maxHeight: '327',
                checkboxName: 'dept[]',
                includeSelectAllOption: true,
                disableIfEmpty: true,
                enableFiltering: true,
                filterPlaceholder: 'Search Specific Items',
                enableCaseInsensitiveFiltering: true,
                nonSelectedText: 'Select Specific Items',
                onSelectAll:function(){
                    console.log('selectAll')
                    $.show_btn();
                },
                onDeselectAll:function(){
                    $.show_btn();
                },
                onChange: function(element, checked) {
                    $.show_btn();
                }
            });


        $('#specific_items_2, #specific_items_2').on('change', function(event) {
            event.preventDefault();
            $.show_btn();
        });
        $('#competency_type').on('change', function(event) {
            event.preventDefault();
            var competency = $(this).val();
            var type_1 = $('#competency_from_1').val();
            var type_2 = $('#competency_from_2').val();
            $.show_btn();
            if(type_1 == '') {
                $("#specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
            }
            else if(type_1 == 'competencies' ){
                if(competency != ''){
                    var data = {competency_type: competency, type: type_1};
                    $.get_specific_data(data);
                }
                else{
                    $("#specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
                }
            }


            if(type_2 == '') {
                $("#specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
            }
            else if(type_2 == 'competencies' ){
                if(competency != ''){
                    var data = {competency_type: competency, type: type_2};
                    $.get_specific_items(data);
                }
                else{
                    $("#specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
                }
            }
        })

        $('#competency_from_1').on('change', function(event, val, pid) {
            event.preventDefault();

            var competency = $('#competency_type').val();
            var type = $(this).val();

            $.show_btn();
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects' ) {
                $("#specific_items_1").prop('disabled', true).multiselect('dataprovider', []);
                return;
            }
            else if(type == 'competencies' && competency != '') {
                var data = {competency_type: competency, type: type};
                $.get_specific_data(data);
            }
            else if(type != 'competencies') {
                var data = {competency_type: competency, type: type};
                if((val != '' && val != undefined) && (pid != '' && pid != undefined)){
                    data.project_id = pid;
                }
                $.get_specific_data(data);
            }
        });
        if($js_config.project_id){
            // $('#competency_from_1').val('project').trigger('change', ['project', $js_config.project_id]);
        }

        $('#competency_from_2').on('change', function(event, val, pid) {
            event.preventDefault();
            var competency = $('#competency_type').val();
            var type = $(this).val();
            $.show_btn();
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                $("#specific_items_2").prop('disabled', true).multiselect('dataprovider', []);
                return;
            }
            else if(type == 'competencies' && competency != '') {
                var data = {competency_type: competency, type: type};
                $.get_specific_items(data);
            }
            else if(type != 'competencies') {
                var data = {type: type};
                $.get_specific_items(data);
            }
        });
        if($js_config.project_id){
            // $('#competency_from_2').val('project').trigger('change', ['project', $js_config.project_id]);
        }

        function sort_arr_obj (a, b){
            var aName = a.label.toLowerCase();
            var bName = b.label.toLowerCase();
            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
        }
    })

if($('#ka_tabs li.active a').attr('href') == '#ka_competency'){
    var width = $('.content').width(),
        margin = 0,
        height = window.innerHeight-232,
        diameter = height,
        clipRadius,
        peopleBorderPc = 0.1,
        labelHeightPC = 0.05,
        svg,
        zoomActive = false,
        svgBackgroundColor = "transparent",
        highlightOpacity = 0.6,
        noPeopleBackgroundColor = "#7f7f7f",
        noSkillsBackgroundColor = "#3b3838",
        searchBorderColor = "#ee8640",
        labelFill = "#c55a11";
        format = d3.format(",d"),
        color = ["#f6f6f6", "#2e75b6", "#9bbbdf", "#fff", "#ccc"];
        var minHeight= (($(window).height() - $('#analyticsIframe').offset().top) ) - 17;
        height = minHeight;

    var fontSize = d3.scaleLinear()
        .domain([20, height])
        .range([6, 28])
        .interpolate(d3.interpolateRound)
        .clamp(true);

    var labelLength = d3.scaleLinear()
        .domain([20, height])
        .range([5, 32])
        .interpolate(d3.interpolateRound)
        .clamp(true);

    var pack = d3.pack()
        .size([diameter - margin, diameter - margin])
        .padding(2);

    var view, root, focus, totalPeople, totalSkills, totalNoSkills, label, node, image, packRootDescendants;

    var topG, myZoom;


    function showUserProfile(d) {
        //code to trigger displaying user profile in parent container
        parent.showProfile(d.data.id);
        d3.event.preventDefault();

    }

    function disablePanel() {

        var divElements = document.getElementById("menuPanel").getElementsByTagName('*');
        for (var node of divElements) {
            node.disabled = true;
        }

    };

    function showNodes(dataView) {
        var competency_type = $('#competency_type option:selected').text();

        //node circles
        node = topG.selectAll("circle#node")
        .data(dataView)
          .join(
            enter => enter.append("circle")
                .attr("id", "node")
                .attr("class", d => d.data.type + ' svg-node')
                .attr("fill", d => d.data.id === "No Skills" ? noSkillsBackgroundColor : (d.data.type === "Skill" && !d.children) ? noPeopleBackgroundColor : color[d.depth])
                .attr("pointer-events", d => (d.data.type !== "Skill" && d.data.id !== "No Skills" && !d.children) ? "none" : null)
                .attr("stroke-width", d => (d.data.type === "People") ? d.r * peopleBorderPc : null)
                .attr("stroke", d => (d.data.type === "People") ? color[4] : null)
                .style("cursor", "pointer")
                .on("mouseover", function () { d3.select(this).attr("opacity", d => (d === root || d.data.type === "People" || d === focus) ? 1 : highlightOpacity); })
                .on("mouseout", function () { d3.select(this).attr("opacity", 1); })
                .on("click", d => (zoomActive === false && focus !== d) ? (nodeZoom(d), d3.event.stopPropagation()) : null)
                ,
            update => update
                .attr("id", "node")
                .attr("class", d => d.data.type)
                .attr("fill", d => d.data.id === "No Skills" ? noSkillsBackgroundColor : (d.data.type === "Skill" && !d.children) ? noPeopleBackgroundColor : color[d.depth])
                .attr("pointer-events", d => (d.data.type !== "Skill" && d.data.id !== "No Skills" && !d.children) ? "none" : null)
                .attr("stroke-width", d => (d.data.type === "People") ? d.r * peopleBorderPc : 0)
                .attr("stroke", d => (d.data.type === "People") ? color[4] : null)
                .style("cursor", "pointer")
                .on("mouseover", function () { d3.select(this).attr("opacity", d => (d === root || d.data.type === "People" || d === focus) ? 1 : highlightOpacity); })
                .on("mouseout", function () { d3.select(this).attr("opacity", 1); })
                .on("click", d => (zoomActive === false && focus !== d) ? (nodeZoom(d), d3.event.stopPropagation()) : null)
                ,
            exit => exit
                .remove()
          );

        //remove any existing tooltips
        topG.selectAll("title").remove();

        //node tooltip
        /*node.append("title")
            .text(d => d.data.id === "All Skills" ? peopleNumbers(d) : d.data.id === "No Skills" ? "No "+competency_type + "\n" + peopleNumbers(d) : d.data.type === "People" ? d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → ") : d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → ") + "\n" + peopleNumbers(d));*/
        node.attr('title', d => d.data.id === "All Skills" ? peopleNumbers(d) : d.data.id === "No Skills" ? "No "+competency_type + "\n<br>" + peopleNumbers(d) : d.data.type === "People" ? d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → ") : d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → ") + "\n<br>" + peopleNumbers(d))
        //remove any existing images
        topG.selectAll("image").remove();

        //user images
        image = topG.selectAll("image")
            .data(dataView.filter(d => d.data.type === "People"))
            .join(
                enter => enter.append('image')
                    .attr("class", "People")
                    .attr('xlink:href', d => d.data.image)
                    .attr("data-target", "#popup_modal")
                    .attr("data-toggle", "modal")
                    .attr("xlink:data-remote", d => $js_config.base_url+"shares/show_profile/" + d.data.id)
                    .attr("clip-path", "url(#avatar-clip)")
                    .style("cursor", "pointer")
                    .on("click", d => (zoomActive === false && focus !== d) ? (showUserProfile(d), d3.event.stopPropagation()) : null)
                    ,
                update => update
                    .attr("class", d => d.data.type)
                    .attr('xlink:href', d => d.data.image)
                    .attr("data-target", "#popup_modal")
                    .attr("data-toggle", "modal")
                    .attr("xlink:data-remote", d => $js_config.base_url+"shares/show_profile/" + d.data.id)
                    .attr("clip-path", "url(#avatar-clip)")
                    .style("cursor", "pointer")
                    .on("click", d => (zoomActive === false && focus !== d) ? (showUserProfile(d), d3.event.stopPropagation()) : null)
                    ,
                    exit => exit
                .remove()
          );

        //image tooltip
        image.attr('title', d => (d.parent.data.id == "No Skills") ? ("No "+competency_type+" → " + d.data.name) : d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → "))
        /*image.append("title")
            .text(d => (d.parent.data.id == "No Skills") ? ("No "+competency_type+" → " + d.data.name) : d.ancestors().map(d => d.data.name).reverse().slice(1).join(" → "));*/
            /*.text(d => (d.parent.data.id == "No Skills") ? ("No Skills → " + d.data.name) : d.ancestors().map(d => d.data.name).reverse().join(" → "));*/

		$('.People, .Level, .Experience, .Skill, .svg-node').tooltip('destroy');
		$('.tooltip').tooltip('hide');


        $('.People, .Level, .Experience, .Skill, .svg-node').tooltip({
            html: true,
            template: '<div class="tooltip image-tip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            placement: 'top',
            container: 'body',
            delay: 100,

        })




    }

    function showLabels(dataLabels) {

        //remove any existing labels
        topG.selectAll("text").remove();

        //node label text
        label = topG.selectAll("text")
            .data(dataLabels)
            .join(
                enter => enter.append("text")
                .attr("class", "Label")
                .style("text-shadow", d => d.depth > 1 ? "-1px 0px 2px #fff, -1px -1px 2px #fff, 0px 1px 2px #fff, 0px -1px 2px #fff" : null)
                .attr("pointer-events", "none")
                .attr("text-anchor", "middle")
                .style("font", d => fontSize(d.r * 2) + "px Open Sans")
                .style("font-weight", 600)
                .style("text-transform", "uppercase")
                .style("fill", d=> d.depth === 1 ? "#fff" : labelFill)
                .style("display", "inline")
                .text(d => (d.data.name.length > labelLength(d.r)) ? d.data.name.slice(0, labelLength(d.r)).trim() + "..." : d.data.name)
                ,
                update => update
                .attr("class", "Label")
                .style("text-shadow", d => d.depth > 1 ? "-1px 0px 2px #fff, -1px -1px 2px #fff, 0px 1px 2px #fff, 0px -1px 2px #fff" : null)
                .attr("pointer-events", "none")
                .attr("text-anchor", "middle")
                .style("font", d => fontSize(d.r * 2) + "px Open Sans")
                .style("font-weight", 600)
                .style("text-transform", "uppercase")
                .style("fill", d=> d.depth === 1 ? "#fff" : labelFill)
                .style("display", "inline")
                .text(d => (d.data.name.length > labelLength(d.r * 2)) ? d.data.name.slice(0, labelLength(d.r * 2)).trim() + "..." : d.data.name)
                ,
                exit => exit
                    .remove()
            );

    }

    function loadSkills() {
        var competency_type = $('#competency_type option:selected').text();
        //empty drop down
        document.getElementById("skillsList").innerHTML = "";

        let skillNamesIDs;

        switch (true) {
            case (document.getElementsByName("skillOptionChoice")[0].checked) : //with people

                skillNamesIDs = packRootDescendants.filter(d => d.data.type === "Skill" && d.children).sort((a, b) => d3.ascending(a.data.name, b.data.name));
                break;

            case (document.getElementsByName("skillOptionChoice")[1].checked): //no people

                skillNamesIDs = packRootDescendants.filter(d => d.data.type === "Skill" && !d.children).sort((a, b) => d3.ascending(a.data.name, b.data.name));
                break;

            default: //all

                skillNamesIDs = packRootDescendants.filter(d => d.data.type === "Skill").sort((a, b) => d3.ascending(a.data.name, b.data.name));
        }

        let skillNameList = d3.map(skillNamesIDs, d => d.data.name).keys();
        let skillIDs = d3.map(skillNamesIDs, d => d.data.id).keys();

        //add all entry
        let option = document.createElement("option");
        option.text = competency_type+" (" + format(skillNameList.length) + ")";
        option.value = "All Skills";
        skillsList.add(option);

        //add entries
        for (let i = 0; i < skillNameList.length; i++) {
            let option = document.createElement("option");
            option.text = skillNameList[i];
            option.value = skillIDs[i];
            skillsList.add(option);
        };

    }

    function loadPeople() {

        //empty drop down
        document.getElementById("peopleList").innerHTML = "";

        let peopleNamesIDs;

        switch (true) {
            case (document.getElementsByName("peopleOptionChoice")[0].checked): //with skills

                peopleNamesIDs = packRootDescendants.filter(d => d.data.type === "People" && d.parent.data.id !== "No Skills").sort((a, b) => d3.ascending(a.data.name, b.data.name));
                break;

            case (document.getElementsByName("peopleOptionChoice")[1].checked): //no skills

                peopleNamesIDs = packRootDescendants.filter(d => d.data.type === "People" && d.parent.data.id === "No Skills").sort((a, b) => d3.ascending(a.data.name, b.data.name));
                break;

            default: //all

                peopleNamesIDs = packRootDescendants.filter(d => d.data.type === "People").sort((a, b) => d3.ascending(a.data.name, b.data.name));
        }

        let peopleNameList = d3.map(peopleNamesIDs, d => d.data.name).keys();
        let peopleIDs = d3.map(peopleNamesIDs, d => d.data.id).keys();

        //add all entry
        let option = document.createElement("option");
        option.text = "People (" + format(peopleNameList.length) + ")";
        option.value = "All People";
        peopleList.add(option);

        //add entries
        for (let i = 0; i < peopleNameList.length; i++) {
            let option = document.createElement("option");
            option.text = peopleNameList[i];
            option.value = peopleIDs[i];
            peopleList.add(option);
        };

        //zoom to no skills if selected
        if (focus.data.id != "No Skills" && document.getElementsByName("peopleOptionChoice")[1].checked) {
            nodeZoom(packRootDescendants.filter(d => d.data.id === "No Skills")[0])
        }

    }

    function nodeZoom(clickedNode) {
        if (zoomActive === true) { return; }

        if (clickedNode === null) { clickedNode = root; }

        //reset data to skills only if clicked root
        (clickedNode === root) ? showNodes(packRootDescendants.filter(d => d.data.id === "All Skills" || d.data.id === "No Skills" || d.data.type === "Skill")) : null;

        //set skill list if clicked all skills, no skills, skill
        let skillID = null;
        switch (true) {
            case (clickedNode.data.type === "All Skills" || clickedNode.data.type === "Skill"):
                skillID = clickedNode.data.id;
                break;
            case (clickedNode.data.type === "Level"):
                skillID = clickedNode.parent.data.id;
                break;
            case (clickedNode.data.type === "Experience"):
                skillID = clickedNode.parent.parent.data.id;
                break;
            default:
                skillID = root.data.id;
        }
        if (skillID !== null) {
            let exists = false;
            for (var i = 0, opts = document.getElementById('skillsList').options; i < opts.length; i++) {
                if (opts[i].value == skillID) { exists = true; break; }
            }
            if (!exists) {
                document.getElementsByName("skillOptionChoice")[2].checked = true;
                loadSkills();
            }
            document.getElementById("skillsList").value = skillID;
        }

        //if skill then update displayed nodes to include selected skill descendants
        (clickedNode.data.type === "Skill" || clickedNode.data.id === "No Skills") ? showNodes(packRootDescendants.filter(d => d.data.id === "All Skills" || d.data.id === "No Skills" || d.data.type === "Skill").concat(clickedNode.descendants())) : null;

        //display labels
        showLabels(packRootDescendants.filter(d =>
            //if clickedNode is All Skills then include all Skill and No Skills large enough
            (clickedNode.data.id === "All Skills" && d.parent === clickedNode && (d.r / d.parent.r > labelHeightPC))
            ||
            //if Skill then Levels node children
            (clickedNode.data.type === "Skill" && d.parent === clickedNode)
            ||
            //if No Skills then people large enough
            (clickedNode.data.id === "No Skills" && d.parent === clickedNode && (d.r / d.parent.r > labelHeightPC))
            ||
            //if Level then Experience node children
            (clickedNode.data.type === "Level" && d.parent === clickedNode)
            ||
            //if Experience then people large enough
            (clickedNode.data.type === "Experience" && d.parent === clickedNode && (d.r / d.parent.r > labelHeightPC))
            ||
            //if Skill or No Skills with no children
            (d === clickedNode && (d.data.type === "Skill" || d.data.id === "No Skills") && !d.children)
            ));

        //zoom to clicked node
        // topG.call(myZoom.transform, d3.zoomIdentity.translate(0, 0).scale(1));
        zoom(clickedNode);

        //update people list if no skills selected
        if (clickedNode.data.id === "No Skills") {
            document.getElementsByName("peopleOptionChoice")[1].checked = true;
            loadPeople();
            if (!document.getElementsByName("skillOptionChoice")[2].checked) {
                document.getElementsByName("skillOptionChoice")[2].checked = true;
                loadSkills();
            }
        }
    }

    function skillsListChange(skillID) {

        let skillNode;

        if (skillID == "All Skills") { skillNode = root; }
        else { skillNode = packRootDescendants.filter(d => d.data.id == skillID)[0]; }

        //zoom to node
        nodeZoom(skillNode);
        console.log('skillsListChange')
        var url = '';
        $('.go-to-competency').addClass('go-to-disabled').attr('href', '');
        if(skillID != 'All Skills'){
            var ctp = $('#competency_type').val();
            if(ctp != '' && ctp == 'skills'){
                url = $js_config.base_url + 'competencies/index/sk/' + skillID;
            }
            else if(ctp != '' && ctp == 'subjects'){
                url = $js_config.base_url + 'competencies/index/sb/' + skillID;
            }
            else if(ctp != '' && ctp == 'domains'){
                url = $js_config.base_url + 'competencies/index/dm/' + skillID;
            }
            if(url != ''){
                $('.go-to-competency').removeClass('go-to-disabled').attr('href', url);
            }

        }


    }

    function windowResize() {
        setTimeout(function(){
            width = $('.content').width();//document.body.clientWidth-30; //$('.content').width();
            height = window.innerHeight - 232;//$('#analyticsIframe').innerHeight();
            var minHeight= (($(window).height() - $('#analyticsIframe').offset().top) ) - 17;
            height = minHeight;
            diameter = height;
            if(minHeight > 5){
                d3.select("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .attr("viewBox", [-diameter / 2, -diameter / 2, diameter, diameter]);
                if($('#analyticsIframe svg').length > 0){
                    zoomTo([focus.x, focus.y, focus.r * 2]);
                }
            }
        },500)
    }

    function zoomTo(v) {
        const k = diameter / v[2];

        view = v;

        image
            .attr("transform", function (d) { return "translate(" + (((d.x - v[0]) * (k)) - ((d.r) * k)) + "," + (((d.y - v[1]) * (k)) - ((d.r) * k)) + ")"; })
            .attr("width", function (d) { return d.r * k * 2; })
            .attr("height", function (d) { return d.r * k * 2 });

        if (clipRadius > 0) {
            var defClipCircle = document.getElementById("clipCircle");
            defClipCircle.setAttribute("cx", clipRadius * k);
            defClipCircle.setAttribute("cy", clipRadius * k);
            defClipCircle.setAttribute("r", clipRadius * k);
        }

        label.attr("transform", d => `translate(${(d.x - v[0]) * k},${(d.y - v[1]) * k})`);
        label.style("font", d => "600 " + fontSize(d.r * 2 * k) + "px Open Sans");
        label.text(d => (d.data.name.length > labelLength(d.r * 2 * k)) ? d.data.name.slice(0, labelLength(d.r * 2 * k)).trim() + "..." : d.data.name);

        node.attr("transform", d => `translate(${(d.x - v[0]) * k},${(d.y - v[1]) * k})`);
        node.attr("r", d => d.r * k);
        node.attr("stroke-width", d => d.data.type === "People" ? (d.r * k * peopleBorderPc) : null);

    }

    function zoom(d) {

        const focus0 = focus;

        focus = d;

        var hasFocus = (document.activeElement === document.getElementById("skillsList") ? true : false);

        const transition = topG.transition()
            .duration(1000)
            .tween("zoom", d => {
                const i = d3.interpolateZoom(view, [focus.x, focus.y, focus.r * 2]);
                return t => zoomTo(i(t));
            })
        .on('start', function () {
            zoomActive = true;
            // topG.call(myZoom).on("wheel.zoom", null).on("mousedown.zoom", null).on("click.zoom", null).on("dblclick.zoom", null).on("drag.zoom", null);
            (hasFocus) ? document.getElementById("skillsList").disabled = true : null;
        })
        .on('end', function () {
            zoomActive = false;
            // topG.call(myZoom).on("drag", null).on("mousedown.zoom", null);
            if (hasFocus) {
                document.getElementById("skillsList").disabled = false;
                document.getElementById("skillsList").focus();

            };
            findText();
            if (document.getElementById("peopleList")[document.getElementById("peopleList").selectedIndex].value != "All People") { filterUser(document.getElementById("peopleList")[document.getElementById("peopleList").selectedIndex].value); };
        });

        label
            .style("display", d => "none");

        label
            .transition(transition)
            .on("end", function (d) { this.style.display = "inline"; });


            var url = '';
            $('.go-to-competency').addClass('go-to-disabled').attr('href', '');
            var skillID = $('.skillsDropDownList').val();

            if(skillID != 'All Skills' && skillID != undefined) {
                var ctp = $('#competency_type').val();
                if(ctp != '' && ctp == 'skills'){
                    url = $js_config.base_url + 'competencies/index/sk/' + skillID;
                }
                else if(ctp != '' && ctp == 'subjects'){
                    url = $js_config.base_url + 'competencies/index/sb/' + skillID;
                }
                else if(ctp != '' && ctp == 'domains'){
                    url = $js_config.base_url + 'competencies/index/dm/' + skillID;
                }
                if(url != ''){
                    $('.go-to-competency').removeClass('go-to-disabled').attr('href', url);
                }

            }
    }

    function peopleNumbers(nodeRoot) {
        var competency_type = $('#competency_type option:selected').text();
        if (nodeRoot === root) {
            return format(totalSkills) + " Total "+competency_type+" \n" + format(totalPeople) + " Total People";
        }
        else {
            let peopleCount = d3.map(nodeRoot.descendants().filter(d => d.data.type === "People"), d => d.data.name).keys().length;
            return peopleCount == 0 ? "No People" : format(peopleCount) + (peopleCount == 1 ? " Person (" : " People (") + (Math.floor(peopleCount / totalPeople * 100) < 1 ? "<1% of Total People)" : format(Math.floor(peopleCount / totalPeople * 100)) + "% of Total People)");
        }

    }

    function findText() {

        if (zoomActive === true) { return; };

        //reset node circles
        d3.selectAll("circle#node")
            .attr('stroke-width', (d => (d.data.type !== "People") ? 0 : (d.r * peopleBorderPc * (diameter / (focus.r*2)))))
            .attr("stroke", (d => (d.data.type !== "People") ? "none" : color[4]));

        //get text to find
        let findText = document.getElementById("findTextInput").value.toUpperCase();

        //check there is text to search for
        if (findText.length > 0) {
            switch (true) {
                case (document.getElementsByName("searchOptionChoice")[0].checked || document.getElementsByName("searchOptionChoice")[2].checked): //skill or both
                    node.filter(d => (d.data.type === "Skill" && d.data.name.toUpperCase().indexOf(findText) >= 0))
                        .attr("stroke", searchBorderColor)
                        .transition()
                        .duration(500)
                        .attr('stroke-width', 8)
                        .transition()
                        .duration(500)
                        .attr('stroke-width', 4);
                    if (!document.getElementsByName("searchOptionChoice")[2].checked) { break; }

                case (document.getElementsByName("searchOptionChoice")[1].checked || document.getElementsByName("searchOptionChoice")[2].checked) : //people or both
                    let skillGrandparents = packRootDescendants.filter(d => (d.data.type === "Skill" || d.data.id === "No Skills") && d.children && d.leaves().filter(d => (d.data.name.toUpperCase().indexOf(findText) >= 0)).length > 0).map(d => d.data.id);
                    node.filter(d => (d.data.type === "People" && d.data.name.toUpperCase().indexOf(findText) >= 0) || ((d.data.type === "Skill" || d.data.id === "No Skills") && skillGrandparents.indexOf(d.data.id) >= 0))
                        .attr("stroke", searchBorderColor)
                        .transition()
                        .duration(500)
                        .attr('stroke-width', 8)
                        .transition()
                        .duration(500)
                        .attr('stroke-width', 4);
            }
        }
    }

    function filterUser(selectedUser) {

        //clear search text and any current highlighting
        document.getElementById("findTextInput").value = "";
        findText();

        if (selectedUser !== "All People") {
            let skillGrandparents = packRootDescendants.filter(d => (d.data.type === "Skill" || d.data.id === "No Skills") && d.children && d.leaves().filter(d => (d.data.id == selectedUser)).length > 0).map(d => d.data.id);
            node.filter(d => (d.data.type === "People" && d.data.id == selectedUser) || ((d.data.type === "Skill" || d.data.id === "No Skills") && skillGrandparents.indexOf(d.data.id) >= 0))
                .attr("stroke", searchBorderColor)
                .transition()
                .duration(500)
                .attr('stroke-width', 8)
                .transition()
                .duration(500)
                .attr('stroke-width', 4);
        }

    }

    function resetAll() {

        document.getElementById("resetAll").blur();

        document.getElementById("findTextInput").value = "";
        document.getElementsByName("searchOptionChoice")[2].checked = true;

        //reset node circles
        topG.selectAll("circle#node")
            .attr("stroke", "none")
            .attr("stroke-width", peopleBorderPc * 20);

        //reset skills list
        document.getElementsByName("skillOptionChoice")[2].checked = true;
        document.getElementById("skillsList").disabled = true;
        loadSkills();
        document.getElementById("skillsList").disabled = false;

        //reset people list
        document.getElementsByName("peopleOptionChoice")[2].checked = true;
        document.getElementById("peopleList").disabled = true;
        loadPeople();
        document.getElementById("peopleList").disabled = false;

        // topG.call(myZoom.transform, d3.zoomIdentity.translate(0, 0).scale(1));
        nodeZoom(root);

    }
    $(function(){

        ($.resize_stuff = function(){
            width = $('.content').width();
            $('#analyticsIframe').animate({
                minHeight: (($(window).height() - $('#analyticsIframe').offset().top) ) - 17,
            }, 1)
        })();

        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                if($('#ka_tabs li.active a').attr('href') == '#ka_competency'){
                    $.resize_stuff();
                }

                clearInterval(interval);
            }
        }, 1000);



        $(".sidebar-toggle").click(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_competency'){
                $.resize_stuff();
                windowResize();
            }
        })

        $(window).resize(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_competency'){
            $.resize_stuff();
            windowResize();
            }
        })

        $("#menuPanel").hide();
        document.getElementById("analysisLabel").style.display = "none";

        $('.btn-show-graph').on('click', function(event) {
            var competency_type = $("#competency_type").val();
            var competency_from_1 = $("#competency_from_1").val();
            var specific_items_1 = $("#specific_items_1").val();
            var competency_from_2 = $("#competency_from_2").val();
            var specific_items_2 = $("#specific_items_2").val();
            //
            var competency_text = $('#competency_type option:selected').text();

            $('#analyticsIframe').html('');
            document.getElementById("analysisLabel").style.display = "block";
            document.getElementById("analysisLabel").textContent = "Running Analysis..."
            $('.btn-show-graph').prop('disabled', true);
            /*if(project_id == "" || competency_value == ""){
                $("#menuPanel").hide();
                $('#analyticsIframe').html('<div class="col-sm-12 partial_data box-borders " style="padding: 10px;"><div class="col-sm-12 box-borders select-project-sec"><div class="no-data">SELECT VIEW</div></div></div></div>');
                return;
            }*/
            $("#menuPanel").show();

            $("[for='skillsOption']").text(competency_text);
            $("[for='withSkillsOption']").text("With "+competency_text);
            $("[for='noSkillsOption']").text("No "+competency_text);
            $.btn_clicked = true;
            d3.json($js_config.base_url + "subdomains/get_json_skills/", {
                          method:"POST",
                          body: JSON.stringify({
                            competency_type: competency_type,
                            competency_from_1: competency_from_1,
                            specific_items_1: specific_items_1,
                            competency_from_2: competency_from_2,
                            specific_items_2: specific_items_2
                          }),
                          headers: {
                            "Content-type": "application/json; charset=UTF-8"
                          }
                        }
                    ).then(function (dataRoot) {
                    // d3.json($js_config.base_url + "subdomains/get_json_skills/" + project_id + "/" + competency_value).then(function (dataRoot) {

                //check for no content
                if (Object.entries(dataRoot).length == 0) {
                    document.getElementById("analysisLabel").style.display = "block";
                    document.getElementById("analysisLabel").textContent = "No Data."
                    disablePanel();
                    throw new Error('No Data.');
                };

                root = d3.hierarchy(dataRoot)
                    .sum(d => d.value)
                    .sort((a, b) => b.value - a.value);

                //check for no skills content
                if (root.data.children.length == 0) {
                    document.getElementById("analysisLabel").style.display = "block";
                    document.getElementById("analysisLabel").textContent = "No "+competency_type+"."
                    disablePanel();
                    throw new Error('No '+competency_type+'.');
                };

                focus = root;
                packRootDescendants = pack(root).descendants();
                totalPeople = d3.map(packRootDescendants.filter(d => d.data.type === "People"), d => d.data.name).keys().length;
                totalSkills = packRootDescendants.filter(d => d.data.type === "Skill").length;
                totalNoSkills = packRootDescendants.filter(d => d !== root && d.parent.data.id === "No Skills").length;

                //create SVG
                svg = d3.select("#analyticsIframe").append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .attr("viewBox", [-diameter / 2, -diameter / 2, diameter, diameter])
                    .style("display", "block")
                    .style("background", svgBackgroundColor);

                //define clippath to make user images round
                packRootDescendants.filter(d => d.data.type === "People").length > 0 ? clipRadius = packRootDescendants.filter(d => d.data.type === "People")[0].r : 0;
                const defs = svg.append("defs");
                defs.append("clipPath")
                    .attr("id", "avatar-clip")
                    .append("circle")
                    .attr("id", "clipCircle")
                    .attr("cx", clipRadius)
                    .attr("cy", clipRadius)
                    .attr("r", clipRadius)

                //add encompassing group for the zoom
                topG = svg.append("g")
                    .attr("class", "topG");

                //add zoom capabilities
                // myZoom = d3.zoom()
                //               .on("zoom", d => topG.attr("transform", d3.event.transform));
                // myZoom(svg);
                //topG.on("mousedown.zoom", null); //no drag

                //create nodes
                showNodes(packRootDescendants.filter(d => d.data.id === "All Skills" || d.data.id === "No Skills" || d.data.type === "Skill"));

                //create labels
                showLabels(packRootDescendants.filter(d => (d.data.type === "Skill" || d.data.id === "No Skills") && (d.r / d.parent.r > labelHeightPC)));

                loadSkills();
                loadPeople();

                //zoom to root node
                // zoomTo([329, 329, 658]);
                zoomTo([root.x, root.y, root.r * 2]);
                window.addEventListener('resize', $.resize_stuff());
                resetAll();
                //visualization loaded
                document.getElementById("analysisLabel").style.display = "none";


            });
                setTimeout(function(){
                    $('.btn-show-graph').prop('disabled', false);
                }, 100)

        })
    })
}
</script>
