<!--Copyright 2010-2020 Mike Bostock All rights reserved.-->
<?php echo $this->Html->script('projects/d3.v6.min', array('inline' => true)); ?>
<div class="box-header filters" style="padding: 10px;">
    <div class="clearfix col-sm-12 inner-first nopadding">
        <div class="ka-search-box">
            <input id="ks_search" type="text" class="form-control" placeholder="" autocomplete="off">
            <span class="ka-input-group-btn">
            <button class="btn btn-success btn-ka-search no-value" type="button" id="search_ka">Search</button>
            </span>
        </div>
    </div>
</div>
<div class="box-body clearfix border1"  style=" padding: 0;">
        <div id="searchMenuPanel" class="searchMenuGroup">

            <select id="peopleList" class="peopleList" onchange="listChange('PERSON',this.value);"></select>
            <select id="skillsList" class="skillsList" onchange="listChange('SKILL',this.value);"></select>
            <select id="subjectsList" class="subjectsList" onchange="listChange('SUBJECT',this.value);"></select>
            <select id="domainsList" class="domainsList" onchange="listChange('DOMAIN',this.value);"></select>
            <select id="organizationsList" class="organizationsList" onchange="listChange('ORGANIZATION',this.value);"></select>
            <select id="departmentsList" class="departmentsList" onchange="listChange('DEPARTMENT',this.value);"></select>
            <select id="locationsList" class="locationsList" onchange="listChange('LOCATION',this.value);"></select>
            <select id="storiesList" class="storiesList" onchange="listChange('STORY',this.value);"></select>
            <button id="zoomOutButton" class="zoomOutButton" onclick="zoomOut();">Fit</button>
            <button id="resetButton" class="s-resetButton" onclick="reset();">Reset</button>
        </div>
    <div id="search_analyticsIframe" class="module-wrapper"  >
        <div class="col-sm-12 box-borders select-project-sec">
            <div class="no-data">ENTER SEARCH TEXT</div>
        </div>
    </div>
</div>



<script type="text/javascript" >
if($('#ka_tabs li.active a').attr('href') == '#ka_search'){
    var cmin_height = (($(window).height() - $('#search_analyticsIframe').offset().top) - 20);
    $('#search_analyticsIframe').css('min-height', cmin_height);

    var width = document.getElementById('search_analyticsIframe').clientWidth,
        height = document.getElementById('search_analyticsIframe').clientHeight,
        baseURL = $js_config.base_url,
        iconURL = "images/icons/",
        folderList = ["SEARCH","PEOPLE","STORIES","ORGANIZATIONS","DEPARTMENTS","LOCATIONS","SKILLS","SUBJECTS","DOMAINS"],
        communityCompetencyList = ["ORGANIZATION","DEPARTMENT","LOCATION","SKILL","SUBJECT","DOMAIN","STORY"],
        competencyColors = ["#777777","#bc087c","#3c8dbc","#c55a11","#5f9322","#1b6d6b","#29a3a0","#c89800","#7b35af"],
        highlightColor = "orange",
        linkOpacity = 0.4,
        sizeRange = [20,80],
        skillDiameter, subjectDiameter, domainDiameter, userDiameter, organizationDiameter, departmentDiameter, locationDiameter, storyDiameter,
        userImageDiameter = 40,
        folderImageDiameter = 80,
        searchImageDiameter = 120,
        imageBorderWidth = 2,
        squareBorderWidth = 4,
        squareDiameter = userImageDiameter + (2 * squareBorderWidth),
        collisionDiameter = 90,
        forceYMultiplier = 0.7,
        borderPC = 0.05,
        myZoom,
        svg,
        root,
        links,
        nodes,
        topG;

    $('#ks_search').on('keyup', function(event){
        var val = $(this).val();
        if(val.length <= 0){
            $('#search_ka').addClass('no-value');
        }
        else{
            $('#search_ka').removeClass('no-value');
            if(event.which == 13){
                $('#search_ka').trigger('click');
            }
        }
    })
    $('#search_ka').on('click', function(event){
        event.preventDefault();
        var search_str = $('#ks_search').val();
        $('#ks_search').focus();

        $('#search_analyticsIframe').html('');

        $("#searchMenuPanel").hide();
        if(search_str.length <= 0){
            $('#search_analyticsIframe').html('<div class="col-sm-12 box-borders select-project-sec" > <div class="no-data">ENTER SEARCH TEXT</div></div>');
            return;
        }
        $("#searchMenuPanel").show();

        d3.json($js_config.base_url + "subdomains/search_json?q=" + search_str ).then(function (data) {

            root = d3.hierarchy(data);
            links = root.links();
            nodes = root.descendants();

            userDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "PERSON"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            skillDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "SKILL"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            subjectDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "SUBJECT"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            domainDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "DOMAIN"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            organizationDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "ORGANIZATION"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            departmentDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "DEPARTMENT"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            locationDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "LOCATION"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            storyDiameter = d3.scaleLinear()
            .domain(d3.extent(nodes.filter(d => d.data.type === "STORY"), d => +d.data.value))
            .range(sizeRange)
            .interpolate(d3.interpolateRound)
            .clamp(true);

            const simulation = d3.forceSimulation(nodes)
                .force("link", d3.forceLink(links).id(d => d.id).distance(0).strength(1))
                .force('collision', d3.forceCollide().radius(collisionDiameter))
                .force("charge", d3.forceManyBody().strength(-2000))
                .force("x", d3.forceX())
                .force("y", d3.forceY())
                .alphaDecay(0.01)
                .stop();

            svg = d3.select("#search_analyticsIframe").append("svg")
                .attr("viewBox", [-width  / 2, -height / 2, width, height]);

            //add top level encompassing group for the zoom
            topG = svg.append("g")
                .attr("id", "topG")
                .attr("class", "topG");

            //add zoom capabilities
            myZoom = d3.zoom()
                .on("zoom", function zoomed(event, d) {
                            topG.attr("transform", event.transform);
                        });
                myZoom(svg);

            //handle resize of browser window
            if($('#ka_tabs li.active a').attr('href') == '#ka_search'){              ;
                window.addEventListener('resize', windowResizes);
            }

            //add link lines and color
            const link = topG.append("g")
                .attr("stroke-width", 1.5)
                .attr("stroke-opacity", linkOpacity)
                .selectAll("line")
                .data(links)
                .join("line")
                .attr("stroke", d => d.target.data.type === "PERSON" ? competencyColors[1] :
                    d.target.data.type === "SKILL" ? competencyColors[2] :
                    d.target.data.type === "SUBJECT" ?  competencyColors[3]  :
                    d.target.data.type === "DOMAIN" ? competencyColors[4] :
                    d.target.data.type === "ORGANIZATION" ? competencyColors[5] :
                    d.target.data.type === "DEPARTMENT" ? competencyColors[6] :
                    d.target.data.type === "LOCATION" ? competencyColors[7] :
                    d.target.data.type === "STORY" ? competencyColors[8] :
                    competencyColors[0]); //default line color

            //node squares for competencies only
            const nodeSquare = topG.append("g")
              .selectAll("rect")
              .data(nodes.filter( d => communityCompetencyList.includes(d.data.type)))
              .join("rect")
                    .attr("width", d =>
                        d.data.type === "SKILL" ? Math.floor(skillDiameter(d.data.value) + (skillDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "SUBJECT" ?  Math.floor(subjectDiameter(d.data.value) + (subjectDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "DOMAIN" ?  Math.floor(domainDiameter(d.data.value) + (domainDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "ORGANIZATION" ?  Math.floor(organizationDiameter(d.data.value) + (organizationDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "LOCATION" ?  Math.floor(locationDiameter(d.data.value) + (locationDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "DEPARTMENT" ?  Math.floor(departmentDiameter(d.data.value) + (departmentDiameter(d.data.value) * (borderPC * 2))) :
                        Math.floor(storyDiameter(d.data.value) + (storyDiameter(d.data.value) * (borderPC * 2)))
                        )
                    .attr("height", d =>
                    d.data.type === "SKILL" ? Math.floor(skillDiameter(d.data.value) + (skillDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "SUBJECT" ?  Math.floor(subjectDiameter(d.data.value) + (subjectDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "DOMAIN" ?  Math.floor(domainDiameter(d.data.value) + (domainDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "ORGANIZATION" ?  Math.floor(organizationDiameter(d.data.value) + (organizationDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "LOCATION" ?  Math.floor(locationDiameter(d.data.value) + (locationDiameter(d.data.value) * (borderPC * 2))) :
                        d.data.type === "DEPARTMENT" ?  Math.floor(departmentDiameter(d.data.value) + (departmentDiameter(d.data.value) * (borderPC * 2))) :
                        Math.floor(storyDiameter(d.data.value) + (storyDiameter(d.data.value) * (borderPC * 2)))
                        )
                    .attr("stroke-dasharray", d =>
                        d.data.type === "SKILL" ? Math.floor(skillDiameter(d.data.value) + (skillDiameter(d.data.value) * (borderPC * 2))) + " " + Math.floor(skillDiameter(d.data.value) + (skillDiameter(d.data.value) * (borderPC * 2))) * 4 :
                        d.data.type === "SUBJECT" ?  Math.floor(subjectDiameter(d.data.value) + (subjectDiameter(d.data.value) * (borderPC * 2))) + " " + Math.floor(subjectDiameter(d.data.value) + (subjectDiameter(d.data.value) * (borderPC * 2))) * 4  :
                        d.data.type === "DOMAIN" ?  Math.floor(domainDiameter(d.data.value) + (domainDiameter(d.data.value) * (borderPC * 2))) + " " + Math.floor(domainDiameter(d.data.value) + (domainDiameter(d.data.value) * (borderPC * 2))) * 4 :
                        ''
                        )
                    .attr("stroke-dashoffset", d =>
                        d.data.type === "SKILL" ? -(Math.floor(skillDiameter(d.data.value) + (skillDiameter(d.data.value) * (borderPC * 2))) * 3)  :
                        d.data.type === "SUBJECT" ? -(Math.floor(subjectDiameter(d.data.value) + (subjectDiameter(d.data.value) * (borderPC * 2))) * 3)  :
                        d.data.type === "DOMAIN" ? -(Math.floor(domainDiameter(d.data.value) + (domainDiameter(d.data.value) * (borderPC * 2))) * 3) :
                        ''
                        )
                    .attr("stroke", d =>
                        d.data.type === "SKILL" ? competencyColors[2] :
                        d.data.type === "SUBJECT" ?  competencyColors[3]  :
                        d.data.type === "DOMAIN" ? competencyColors[4] :
                        '#ccc')
                    .attr("fill", d =>
                        d.data.type === "SKILL" ? "#ededed" :
                        d.data.type === "SUBJECT" ? "#ededed" :
                        d.data.type === "DOMAIN" ? "#ededed" :
                        d.data.type === "STORY" ? "#ededed" :
                        "#fff"
                        )
                    .attr("stroke-width", d =>
                        d.data.type === "SKILL" ? skillDiameter(d.data.value) * borderPC :
                        d.data.type === "SUBJECT" ?  subjectDiameter(d.data.value) * borderPC :
                        d.data.type === "DOMAIN" ?  domainDiameter(d.data.value) * borderPC :
                        d.data.type === "ORGANIZATION" ?  organizationDiameter(d.data.value) * borderPC :
                        d.data.type === "DEPARTMENT" ?  departmentDiameter(d.data.value) * borderPC :
                        d.data.type === "LOCATION" ?  locationDiameter(d.data.value) * borderPC :
                        storyDiameter(d.data.value) * borderPC
                        );


            //clipPath for user images
            const nodeClip = topG.append("g")
                .selectAll("clipPath")
                .data(nodes.filter( d => d.data.type === "PERSON"))
                .join("clipPath")
                    .attr('id', d => "clip" + d.index )
                    .append("circle")
                        .attr("cx", d => d.x)
                        .attr("cy", d => d.y)
                        .attr("r", d => userDiameter(d.data.value)/2 );

            //node image
            const nodeImage = topG.append("g")
                .selectAll("image")
                .data(nodes)
                .join("image")
                    .attr("xlink:href", d => folderList.includes(d.data.type) || d.data.type === "SEARCH" ? baseURL + iconURL + d.data.image : d.data.image.length === 0 ? baseURL + iconURL + "No Image.png" : d.data.image)
                    .attr("clip-path", d => d.data.type === "PERSON" ? "url(#clip" + d.index + ")" : null)
                    .attr("width", d =>
                        d.data.type === "SEARCH" ? searchImageDiameter :
                        folderList.includes(d.data.type) ? folderImageDiameter :
                        d.data.type === "SKILL" ? skillDiameter(d.data.value) :
                        d.data.type === "SUBJECT" ? subjectDiameter(d.data.value)  :
                        d.data.type === "DOMAIN" ? domainDiameter(d.data.value) :
                        d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value) :
                        d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value) :
                        d.data.type === "LOCATION" ? locationDiameter(d.data.value) :
                        d.data.type === "STORY" ? storyDiameter(d.data.value) :
                        userDiameter(d.data.value)) //PERSON
                    .attr("height", d =>
                        d.data.type === "SEARCH" ? searchImageDiameter :
                        folderList.includes(d.data.type) ? folderImageDiameter :
                        d.data.type === "SKILL" ? skillDiameter(d.data.value) :
                        d.data.type === "SUBJECT" ?  subjectDiameter(d.data.value)  :
                        d.data.type === "DOMAIN" ? domainDiameter(d.data.value) :
                        d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value) :
                        d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value) :
                        d.data.type === "LOCATION" ? locationDiameter(d.data.value) :
                        d.data.type === "STORY" ? storyDiameter(d.data.value) :
                        userDiameter(d.data.value)) //PERSON
                    .attr("cursor", "pointer")
                    .attr("class", "imageTip")
                    .attr("title", (d => setTooltip(d)))
                    .on("click", function (event, d) { folderList.includes(d.data.type) ? (zoomToFolder(d), event.preventDefault(), event.stopPropagation()) : (showProfileDialog(d), event.preventDefault(), event.stopPropagation())});

            //node image tooltip
            /*nodeImage.append("title")
                .text(d => setTooltip(d) );*/

            //node circles for user images
            const nodeCircle = topG.append("g")
                  .selectAll("circle")
                  .data(nodes.filter( d => d.data.type === "PERSON"))
                  .join("circle")
                        .attr("r", d => userDiameter(d.data.value)/2)
                        .attr("stroke", "#ccc")
                        .attr("fill", "none")
                        .attr("stroke-width", d => userDiameter(d.data.value) * borderPC);

           //Not In My Organization glyph
        const nodeGlyph = topG.append("g")
            .selectAll("image")
            .data(nodes.filter( d => d.data.type === "PERSON" && d.data.notInYourOrganization === "1"))
            .join("image")
                .attr("xlink:href", baseURL + iconURL + "communityglyphGrey48x48.png")
                .attr("width", d => userDiameter(d.data.value)/2) //PERSON
                .attr("height", d => userDiameter(d.data.value)/2) //PERSON
                .attr("cursor", "pointer")
                .on("click", function (event, d) { showProfileDialog(d), event.preventDefault(), event.stopPropagation()});

        //node glyph tooltip
//         nodeGlyph.append("title")
//             .text(d => "Not In Your Organization");
        $('.glyph').tooltip({
                html: true,
                template: '<div class="tooltip image-tip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                placement: 'top',
                container: 'body'
            })


            //run simulation to position nodes
            simulation.tick(600);

            //display nodes in final position
            link
                .attr("x1", d => d.source.x)
                .attr("y1", d => d.source.y)
                .attr("x2", d => d.target.x)
                .attr("y2", d => d.target.y);

            nodeCircle
                .attr("cx", d => d.x)
                .attr("cy", d => d.y);

            nodeClip
                .attr("cx", d => d.x)
                .attr("cy", d => d.y);

            nodeImage
                .attr("x", d =>
                    d.data.type === "SEARCH" ? d.x - (searchImageDiameter/2) :
                    folderList.includes(d.data.type) ? d.x - (folderImageDiameter/2) :
                        d.x - (
                            d.data.type === "SKILL" ? skillDiameter(d.data.value)/2 :
                            d.data.type === "SUBJECT" ? subjectDiameter(d.data.value)/2 :
                            d.data.type === "DOMAIN" ? domainDiameter(d.data.value)/2 :
                            d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value)/2 :
                            d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value)/2 :
                            d.data.type === "LOCATION" ? locationDiameter(d.data.value)/2 :
                            d.data.type === "STORY" ? storyDiameter(d.data.value)/2 :
                            userDiameter(d.data.value)/2 //PERSON
                            )
                        )
                .attr("y", d =>
                    d.data.type === "SEARCH" ? d.y - (searchImageDiameter/2) :
                    folderList.includes(d.data.type) ? d.y - (folderImageDiameter/2) :
                        d.y - (
                            d.data.type === "SKILL" ? skillDiameter(d.data.value)/2 :
                            d.data.type === "SUBJECT" ? subjectDiameter(d.data.value)/2 :
                            d.data.type === "DOMAIN" ? domainDiameter(d.data.value)/2 :
                            d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value)/2 :
                            d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value)/2 :
                            d.data.type === "LOCATION" ? locationDiameter(d.data.value) /2:
                            d.data.type === "STORY" ? storyDiameter(d.data.value)/2 :
                            userDiameter(d.data.value)/2 //PERSON
                            )
                        );

            nodeGlyph
            .attr("x", d => d.x + userDiameter(d.data.value)/10) //PERSON
            .attr("y", d => d.y + userDiameter(d.data.value)/10); //PERSON


            nodeSquare
                .attr("x", d => d.x -
                    (d.data.type === "SKILL" ? skillDiameter(d.data.value)/2 :
                    d.data.type === "SUBJECT" ? subjectDiameter(d.data.value)/2 :
                    d.data.type === "DOMAIN" ? domainDiameter(d.data.value)/2 :
                    d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value)/2 :
                    d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value)/2 :
                    d.data.type === "LOCATION" ? locationDiameter(d.data.value) /2:
                    storyDiameter(d.data.value)/2)
                    -
                    (d.data.type === "SKILL" ? skillDiameter(d.data.value) * borderPC :
                    d.data.type === "SUBJECT" ?  subjectDiameter(d.data.value) * borderPC :
                    d.data.type === "DOMAIN" ?  domainDiameter(d.data.value) * borderPC :
                    d.data.type === "ORGANIZATION" ?  organizationDiameter(d.data.value) * borderPC :
                    d.data.type === "DEPARTMENT" ?  departmentDiameter(d.data.value) * borderPC :
                    d.data.type === "LOCATION" ?  locationDiameter(d.data.value) * borderPC :
                    storyDiameter(d.data.value) * borderPC)
                    )
                .attr("y", d => d.y -
                    (d.data.type === "SKILL" ? skillDiameter(d.data.value)/2 :
                    d.data.type === "SUBJECT" ? subjectDiameter(d.data.value)/2 :
                    d.data.type === "DOMAIN" ? domainDiameter(d.data.value)/2 :
                    d.data.type === "ORGANIZATION" ? organizationDiameter(d.data.value)/2 :
                    d.data.type === "DEPARTMENT" ? departmentDiameter(d.data.value)/2 :
                    d.data.type === "LOCATION" ? locationDiameter(d.data.value) /2:
                    storyDiameter(d.data.value)/2)
                    -
                    (d.data.type === "SKILL" ? skillDiameter(d.data.value) * borderPC :
                    d.data.type === "SUBJECT" ?  subjectDiameter(d.data.value) * borderPC :
                    d.data.type === "DOMAIN" ?  domainDiameter(d.data.value) * borderPC :
                    d.data.type === "ORGANIZATION" ?  organizationDiameter(d.data.value) * borderPC :
                    d.data.type === "DEPARTMENT" ?  departmentDiameter(d.data.value) * borderPC :
                    d.data.type === "LOCATION" ?  locationDiameter(d.data.value) * borderPC :
                    storyDiameter(d.data.value) * borderPC));



            //clear all list
            document.getElementById("peopleList").innerHTML = "";
            document.getElementById("skillsList").innerHTML = "";
            document.getElementById("subjectsList").innerHTML = "";
            document.getElementById("domainsList").innerHTML = "";
            document.getElementById("locationsList").innerHTML = "";
            document.getElementById("departmentsList").innerHTML = "";
            document.getElementById("storiesList").innerHTML = "";
            document.getElementById("organizationsList").innerHTML = "";

            //populate menu panel lists
            loadDropDown("PERSON");
            loadDropDown("SKILL");
            loadDropDown("SUBJECT");
            loadDropDown("DOMAIN");
            loadDropDown("ORGANIZATION");
            loadDropDown("DEPARTMENT");
            loadDropDown("LOCATION");
            loadDropDown("STORY");

            //show menu panel
            document.getElementById("searchMenuPanel").style = "visibility: visible;";

            //fit visualization to visible UI
            zoomToFit(topG);
            windowResizes()

            $('.imageTip').tooltip({
                html: true,
                template: '<div class="tooltip image-tip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                placement: 'top',
                container: 'body'
            })

        });
    });

    function zoomToFit (svgElement) {

        //zoom to fit passed svg element
        const zoomFactor = (svgElement === topG) ? 0.9 : 0.2;
        const bboxTopG = svgElement.node().getBBox();
        const x0 = bboxTopG.x;
        const y0 = bboxTopG.y;
        const x1 = x0 + bboxTopG.width;
        const y1 = y0 + bboxTopG.height;

        svg.transition().duration(750).call(
            myZoom.transform,
            d3.zoomIdentity
                .scale(Math.min(zoomFactor / Math.max((x1 - x0) / width, (y1 - y0) / height)))
                .translate(-(x0 + x1) / 2, -(y0 + y1) / 2)
        );

    }

    function zoomToFolder (d) {

        if (d.children) {

            const zoomFactor = 1;
            const x0 = Math.min(d.children.reduce((min, d) => d.x < min ? d.x : min, d.children[0].x),d.x) - collisionDiameter;
            const x1 = Math.max(d.children.reduce((max, d) => d.x > max ? d.x : max, d.children[0].x),d.x) + collisionDiameter;
            const y0 = Math.min(d.children.reduce((min, d) => d.y < min ? d.y : min, d.children[0].y),d.y) - collisionDiameter;
            const y1 = Math.max(d.children.reduce((max, d) => d.y > max ? d.y : max, d.children[0].y),d.y) + collisionDiameter;

            svg.transition().duration(750).call(
                myZoom.transform,
                d3.zoomIdentity
                    .scale(Math.min(zoomFactor / Math.max((x1 - x0) / width, (y1 - y0) / height)))
                    .translate(-(x0 + x1) / 2, -(y0 + y1) / 2)
            );

        }
    }
/*
    function setTooltip(d) {

        switch (d.data.type) {
        case "SEARCH":
            return d.data.type + " (" + (d.descendants().length - folderList.length) + ((d.descendants().length - folderList.length) === 1 ? " Match)" : " Matches)");
            break;
        case "PERSON":
            return (d.data.name +  " (" + d.data.value + (+d.data.value === 1 ? " Match)" : " Matches)"))
                + ((+d.data.details_value > 0) ? "\n<br>" + d.data.details_value + (+d.data.details_value === 1 ? " Details Match" : " Details Matches") : '')
                + ((+d.data.interests_value >  0) ? "\n<br>" + d.data.interests_value + (+d.data.interests_value === 1 ? " Interest Match" : " Interest Matches") : '')
                + ((+d.data.skills_value >  0) ? "\n<br>" + d.data.skills_value + (+d.data.skills_value === 1 ? " Skill Match" : " Skill Matches") : '')
                + ((+d.data.subjects_value >  0) ? "\n<br>" + d.data.subjects_value + (+d.data.subjects_value === 1 ? " Subject Match" : " Subject Matches") : '')
                + ((+d.data.domains_value >  0) ? "\n<br>" + d.data.domains_value + (+d.data.domains_value === 1 ? " Domain Match" : " Domain Matches") : '')
                + "\n<br>Click to View Profile";
            break;
        case "SKILL":
        case "SUBJECT":
        case "DOMAIN":
            return (d.data.type + ":\n<br>" + d.data.name +  "\n<br>" + d.data.value + (+d.data.value === 1 ? " Related Person" : " Related People") + "\n<br>Click to View Profile");
            break;
        case "ORGANIZATION":
        case "DEPARTMENT":
        case "LOCATION":
        case "STORY":
            return (d.data.name +  " (" + d.data.value + (+d.data.value === 1 ? " Match)" : " Matches)"))
                    + ((+d.data.skills_value >  0) ? "\n<br>" + d.data.skills_value + (+d.data.skills_value === 1 ? " Skill Match" : " Skill Matches") : '')
                    + ((+d.data.subjects_value >  0) ? "\n<br>" + d.data.subjects_value + (+d.data.subjects_value === 1 ? " Subject Match" : " Subject Matches") : '')
                    + ((+d.data.domains_value >  0) ? "\n<br>" + d.data.domains_value + (+d.data.domains_value === 1 ? " Domain Match" : " Domain Matches") : '')
                    + "\nClick to View Profile";
        default:
            //folder
            return d.data.type + " (" + (d.children ? d.children.length : 0) + ((d.children && d.children.length === 1) ? " Match)" : " Matches)" );
        }
    }
*/
function setTooltip(d) {
        switch (d.data.type) {
        case "SEARCH":
            return d.data.type + " (" + (d.descendants().length - folderList.length) + ((d.descendants().length - folderList.length) === 1 ? " Match)" : " Matches)");
            break;
        case "PERSON":
            return "PERSON: " + (d.data.name.length > 30 ? d.data.name.substring(0,30) + "..." : d.data.name)
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.name_value >  0 ? "<br />\n→ " + d.data.name_value + " in Name" : '')
                    + (+d.data.email_value >  0 ? "<br />\n→ " + d.data.email_value + " in Email Address" : '')
                    + (+d.data.bio_value >  0 ? "<br />\n→ " + d.data.bio_value + " in Bio" : '')
                    + (+d.data.interests_value >  0 ? "<br />\n→ " + d.data.interests_value + " in Interests" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "SKILL":
            return "SKILL: " + d.data.name
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.title_matches >  0 ? "<br />\n→ " + d.data.title_matches + " in Title" : '')
                    + (+d.data.description_matches >  0 ? "<br />\n→ " + d.data.description_matches + " in Description" : '')
                    + (+d.data.keyword_matches >  0 ? "<br />\n→ " + d.data.keyword_matches + " in Keywords" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "SUBJECT":
            return "SUBJECT: " + d.data.name
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.title_matches >  0 ? "<br />\n→ " + d.data.title_matches + " in Title" : '')
                    + (+d.data.description_matches >  0 ? "<br />\n→ " + d.data.description_matches + " in Description" : '')
                    + (+d.data.keyword_matches >  0 ? "<br />\n→ " + d.data.keyword_matches + " in Keywords" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "DOMAIN":
            return "DOMAIN: " + d.data.name
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.title_matches >  0 ? "<br />\n→ " + d.data.title_matches + " in Title" : '')
                    + (+d.data.description_matches >  0 ? "<br />\n→ " + d.data.description_matches + " in Description" : '')
                    + (+d.data.keyword_matches >  0 ? "<br />\n→ " + d.data.keyword_matches + " in Keywords" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "ORGANIZATION":
            return "ORGANIZATION: " + (d.data.name.length > 30 ? d.data.name.substring(0,30) + "..." : d.data.name)
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.name_matches >  0 ? "<br />\n→ " + d.data.name_matches + " in Name" : '')
                    + (+d.data.info_matches >  0 ? "<br />\n→ " + d.data.info_matches + " in Information" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "LOCATION":
        return "LOCATION: " + (d.data.name.length > 30 ? d.data.name.substring(0,30) + "..." : d.data.name)
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.name_matches >  0 ? "<br />\n→ " + d.data.name_matches + " in Name" : '')
                    + (+d.data.info_matches >  0 ? "<br />\n→ " + d.data.info_matches + " in Information" : '')
                    + "\n<br />Click to View Profile";
            break;
        case "DEPARTMENT":
        return "DEPARTMENT: " + (d.data.name.length > 30 ? d.data.name.substring(0,30) + "..." : d.data.name)
                    +  "<br />\nMATCHES: " + d.data.value
                    + (+d.data.value >  0 ? "<br />\n→ " + d.data.value + " in Name" : '')
                    + "<br />\nClick to View Profile";
            break;
        case "STORY":
            return "STORY: " + (d.data.name.length > 30 ? d.data.name.substring(0,30) + "..." : d.data.name)
                    +  "\n<br />MATCHES: " + d.data.value
                    + (+d.data.name_matches >  0 ? "<br />\n→ " + d.data.name_matches + " in Name" : '')
                    + (+d.data.summary_matches >  0 ? "<br />\n→ " + d.data.summary_matches + " in Summary" : '')
                    + (+d.data.story_matches >  0 ? "<br />\n→ " + d.data.story_matches + " in Story" : '')
                    + "\n<br />Click to View Profile";
            break;
        default:
            //folder
            return d.data.type + " (" + (d.children ? d.children.length : 0) + ((d.children && d.children.length === 1) ? " Match)" : " Matches)" );
        }
    }

    function showProfile(d) {
        //code to trigger displaying user profile in parent container
        // parent.showProfile(d.data.id);
        var id = '';
        if($('#ka_tabs li.active a').attr('href') == '#ka_search'){
            id = d.data.id;
        }
        else{
            id = d;
        }
            $('#popup_modal').modal({
                remote: $js_config.base_url + 'shares/show_profile/'+ id
            }).show();

    }
    function skillProfile(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_skills/' + d
        }).show();
    }
    function subjectProfile(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_subjects/' + d
        }).show();
    }
    function domainProfile(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_domains/' + d
        }).show();
    }
    function organizationProfile(d) {
        $('#modal_view_org').modal({
            remote: $js_config.base_url + 'communities/view/org/' + d
        }).show();;
    }
    function storyProfile(d) {
        $('#story_view').modal({
            remote: $js_config.base_url + 'stories/view/story/' + d
        }).show();
    }
    function departmentProfile(d) {
        $('#modal_view_dept').modal({
            remote: $js_config.base_url + 'communities/view/dept/' + d
        }).show();
    }
    function locationProfile(d) {
        $('#modal_view_loc').modal({
            remote: $js_config.base_url + 'communities/view/loc/' + d
        }).show();
    }

    function showProfileDialog (d) {

        //highlight node
        listChange (d.data.type, +d.data.id);

        //open corresponding profile dialog
        switch (d.data.type) {
            case "PERSON":
                document.getElementById("peopleList").value = d.data.id;
                //code to trigger displaying user profile in parent container
                showProfile(d);
                break;
            case "SKILL":
                document.getElementById("skillsList").value = d.data.id;
                //code to trigger displaying skill profile in parent container
                parent.skillProfile(d.data.id); //function name is unknown
                break;
            case "SUBJECT":
                document.getElementById("subjectsList").value = d.data.id;
                //code to trigger displaying subject profile in parent container
                parent.subjectProfile(d.data.id); //function name is unknown
                break;
            case "DOMAIN":
                document.getElementById("domainsList").value = d.data.id;
                //code to trigger displaying domain profile in parent container
                parent.domainProfile(d.data.id); //function name is unknown
                break;
            case "ORGANIZATION":
                document.getElementById("organizationsList").value = d.data.id;
                //code to trigger displaying organization profile in parent container
                parent.organizationProfile(d.data.id); //function name is unknown
                break;
            case "DEPARTMENT":
                document.getElementById("departmentsList").value = d.data.id;
                //code to trigger displaying department profile in parent container
                parent.departmentProfile(d.data.id); //function name is unknown
                break;
            case "LOCATION":
                document.getElementById("locationsList").value = d.data.id;
                //code to trigger displaying location profile in parent container
                parent.locationProfile(d.data.id); //function name is unknown
                break;
            case "STORY":
                document.getElementById("storiesList").value = d.data.id;
                //code to trigger displaying story profile in parent container
                parent.storyProfile(d.data.id); //function name is unknown
                break;
            default:
        }

    }

    function loadDropDown(listType) {

        const NamesIDs = nodes.filter(d => d.data.type === listType).sort((a, b) => d3.ascending(a.data.name, b.data.name));
        const NameList = NamesIDs.map(d => d.data.name);
        const IDList = NamesIDs.map(d => d.data.id);

        //add all entry
        let option = document.createElement("option");
        switch (listType) {
        case "PERSON":
            option.text = "People (" + NameList.length + ")";
            option.value = "All People";
            peopleList.add(option);
            break;
        case "SKILL":
            option.text = "Skills (" + NameList.length + ")";
            var skillsList = document.getElementById("skillsList")
            option.value = "All Skills";
            skillsList.add(option);
            break;
        case "SUBJECT":
            option.text = "Subjects (" + NameList.length + ")";
            option.value = "All Subjects";
            subjectsList.add(option);
            break;
        case "DOMAIN":
            option.text = "Domains (" + NameList.length + ")";
            option.value = "All Domains";
            domainsList.add(option);
            break;
        case "ORGANIZATION":
            option.text = "Organizations (" + NameList.length + ")";
            option.value = "All Organizations";
            organizationsList.add(option);
            break;
        case "LOCATION":
            option.text = "Locations (" + NameList.length + ")";
            option.value = "All Locations";
            locationsList.add(option);
            break;
        case "DEPARTMENT":
            option.text = "Departments (" + NameList.length + ")";
            option.value = "All Departments";
            departmentsList.add(option);
            break;
        case "STORY":
            option.text = "Stories (" + NameList.length + ")";
            option.value = "All Stories";
            storiesList.add(option);
            break;
        }

        //add entries
        for (let i = 0; i < NameList.length; i++) {
            let option = document.createElement("option");
            option.text = NameList[i];
            option.value = IDList[i];
            switch (listType) {
            case "PERSON":
                peopleList.add(option);
                break;
            case "SKILL":
                skillsList.add(option);
                break;
            case "SUBJECT":
                subjectsList.add(option);
                break;
            case "DOMAIN":
                domainsList.add(option);
                break;
            case "ORGANIZATION":
                organizationsList.add(option);
                break;
            case "LOCATION":
                locationsList.add(option);
                break;
            case "DEPARTMENT":
                departmentsList.add(option);
                break;
            case "STORY":
                storiesList.add(option);
                break;
            }
        };

    }

    function reset() {

        //remove any selections
        d3.selectAll("circle#highlight").remove();
        d3.selectAll("rect#highlight").remove();

        document.getElementById("resetButton").blur();
        document.getElementById("peopleList").value = "All People";
        document.getElementById("skillsList").value = "All Skills";
        document.getElementById("subjectsList").value = "All Subjects";
        document.getElementById("domainsList").value = "All Domains";
        document.getElementById("organizationsList").value = "All Organizations";
        document.getElementById("locationsList").value = "All Locations";
        document.getElementById("departmentsList").value = "All Departments";
        document.getElementById("storiesList").value = "All Stories";

        zoomToFit(topG);

    }

    function listChange (itemType, id) {

        if (["All People","All Skills","All Subjects","All Domains","All Organizations","All Locations","All Departments","All Stories"].includes(id))
        {
            //no action
        }
        else {

        switch (itemType) {
        case "PERSON":
        case "DEPARTMENT":
            const nodeCircle = topG.append("g")
                .selectAll("circle")
                .data(nodes.filter(d => (d.data.type === itemType && + d.data.id === +id)))
                    .join("circle")
                        .attr("id", "highlight")
                        .attr("cx", d => d.x)
                        .attr("cy", d => d.y)
                        .attr("r", 60)
                        .attr("stroke", highlightColor)
                        .attr("fill", "none")
                        .attr("stroke-width", 10);
            zoomToFit(nodeCircle);
            break;
        default:
            const nodeRect = topG.append("g")
                .selectAll("rect")
                .data(nodes.filter(d => (d.data.type === itemType && + d.data.id === +id)))
                    .join("rect")
                        .attr("id", "highlight")
                        .attr("x", d => d.x - 60)
                        .attr("y", d => d.y - 60)
                        .attr("width", 120)
                        .attr("height", 120)
                        .attr("stroke", highlightColor)
                        .attr("fill", "none")
                        .attr("stroke-width", 10);
            zoomToFit (nodeRect);
            break;
            }

        }

        document.getElementById("peopleList").blur();
        document.getElementById("skillsList").value = "All Skills";
        document.getElementById("subjectsList").blur();
        document.getElementById("domainsList").blur();
        document.getElementById("locationsList").blur();
        document.getElementById("departmentsList").blur();
        document.getElementById("storiesList").blur();
        document.getElementById("organizationsList").blur();

    }

    function zoomOut() {

        document.getElementById("zoomOutButton").blur();

        zoomToFit(topG);

    }


    function windowResizes() {
        if(topG !== undefined){
            zoomToFit(topG);
            // width = document.getElementById('search_analyticsIframe').clientWidth;
            // height = document.getElementById('search_analyticsIframe').clientHeight
            // width = $('.content').width();
        }
        if( $('#search_analyticsIframe').length > 0){
            width = $('.content').width();
            height = (($(window).height() - $('#search_analyticsIframe').offset().top) ) - 17;
            // width = document.getElementById('search_analyticsIframe').clientWidth;
            // height = document.getElementById('search_analyticsIframe').clientHeight
            d3.select("svg")
                .attr("viewBox", [-width / 2, -height / 2, width, height]);
            $('#search_analyticsIframe').animate({
                minHeight: (($(window).height() - $('#search_analyticsIframe').offset().top) ) - 17,
                height: (($(window).height() - $('#search_analyticsIframe').offset().top) ) - 17,
            }, 1)
        }

    }

    $(function(){

        $(".sidebar-toggle").click(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_search'){
                // windowResizes();
                $(window).trigger('resize');
            }
        })

        var resizeTimer;
        var resizeTime = 300;
        $(window).resize(function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {

                if($('#ka_tabs li.active a').attr('href') == '#ka_search'){
                    windowResizes();
                }

            }, resizeTime);
        })

    })
}
</script>