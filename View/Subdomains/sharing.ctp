 <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: 0;
            overflow: hidden;
        }

        .menuGroup {
            position: absolute;
            height: 67px;
            top: 0px;
            right: 0px;
            background-color: #eee;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-left: 1px solid #ddd;
            padding: 8px 8px 8px 8px;
            border-bottom-left-radius: 5px;
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
            top: 46px;
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
            top: 46px;
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
            top: 145px;
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
            margin: 10px 0;
            width: 100%;
            background-color:#eee;
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
                 
    </style>
</head>
<body>
    
    <div id="menuPanel" class="menuGroup">

        <button id="showAllButton" class="greenButtonAll" onclick="viewChange('All');">Show All</button>
        <button id="ownerButton" style="right:86px;" class="greyButton" onclick="viewChange('Owner');">Owner</button>
        <button id="sharerButton" style="right:8px;" class="greyButton" onclick="viewChange('Sharer');">Sharer</button>
        <label id="viewLabel" class="hiddenLabel">All</label>

        
        <input id="valueInput" class="valueSlider" style="width:148px;" type="range" min="1" max="6" step="1" value="6" onclick="linksShow();">
        <label id="minLabel" class="minmaxLabel" style="right:97px; top: 107px; width:56px; text-align:left;">0</label>
        <label id="rangeLabel" class="minmaxLabel" style="right:58px; top: 107px; width:70px; text-align:center;">#Shares</label>
        <label id="maxLabel" class="minmaxLabel" style="right:38px; top: 107px; width:56px; text-align:right;">0</label>
        <label id="allLabel" class="minmaxLabel" style="right:8px; top: 107px; width:20px; text-align:right;">All</label>
        
        
        <input id="levelInput" class="levelSlider" style="width:148px;" type="range" min="1" max="3" step="1" value="1" onclick="levelChange();">
        <label id="levelLabel"class="rangeLabel">Show Project Shares</label>
        
    </div>

    <script src="//d3js.org/d3.v5.min.js"></script>

    <script>

        // initialize variables
        var projectID, forceProperties, svg, myPromise, topG, sharing, link, node, myCircles, linearScale, myMin, myMax, simulation, myZoom;

        //get parent project selection
                projectID = parent.document.getElementById("selection_list").value;
console.log(parent.document.getElementById("selection_list"));
console.log(parent.document);
        //should not be needed as parent should not call without valid selection
        if (projectID === '' || projectID === null) {
            document.getElementById("menuPanel").hidden = true;
            throw new Error('No Selection');
        };

        //diameter of the user nodes
        var nodeWidth = 40;

        //get dimensions of available browser area
        var width = document.body.clientWidth;
        var height = document.body.clientHeight;

        //create svg container for visualization
        svg = d3.select("body").append("svg")
            .attr("width", width)
            .attr("height", height)
        
        //define clippath to make user images round
        const defs = svg.append("defs");
        defs.append("clipPath")
            .attr("id", "avatar-clip")
            .append("circle")
            .attr("cx", 0)
            .attr("cy", 0)
            .attr("r", (nodeWidth/2));
        
        //define marker for owner links
        defs.append("svg:marker")
            .attr("id", "owner")
            .attr("class", "ownerMarker")
            .attr("viewBox", "-4 -3 10 10")
            //x and y position the arrow point at the edge of the node circle
            .attr("refX", 17)
            .attr("refY", -1.8)
            .attr("markerWidth", 16)
            .attr("markerHeight", 16)
            //fixed size
            .attr("markerUnits", "userSpaceOnUse")
            .attr("orient", "auto")
            .append("svg:path")
                .attr("d", "M-3,4L5,-1L-4,-3")
                .style("fill", "#666");

        //define marker for sharer links
        defs.append("svg:marker")
            .attr("id", "sharer")
            .attr("class", "sharerMarker")
            .attr("viewBox", "-4 -3 10 10")
            //x and y position the arrow point at the edge of the node circle
            .attr("refX", 17)
            .attr("refY", -1.8)
            .attr("markerWidth", 16)
            .attr("markerHeight", 16)
            //fixed size
            .attr("markerUnits", "userSpaceOnUse")
            .attr("orient", "auto")
            .append("svg:path")
                .attr("d", "M-3,4L5,-1L-4,-3")
                .style("fill", "#aaa");

        // load data - replace with php file and parameter variable ProjectID
        myPromise = d3.json("http://192.168.7.20/ideascast/sharing.json").then(function (data) {
            sharing = data;
        
            //add encompassing group for the zoom
            topG = svg.append("g")
                .attr("class", "topG");

            //add zoom capabilities
            myZoom = d3.zoom()
                .on("zoom", d => topG.attr("transform", d3.event.transform));
            myZoom(svg);

            //create links
            link = topG.append("g")
              .attr("class", "links")
              .selectAll("line")
              .data(sharing.links)
              .enter().append("path")
                    //class is used to be able to select owner or sharer links
                    .attr("class", d => (d.role === "Owner") ? "ownerLink" : "sharerLink")
                    .attr("marker-end", d => (d.role === "Owner") ? "url(#owner)" : "url(#sharer)");
                    
            //create link tooltip
            link.append("title")
                .attr("class", "linkTooltip");

            //create nodes
            node = topG.append("g")
              .attr("class", "nodes")
              .selectAll("image")
              .data(sharing.nodes)
              .enter().append("image")
                  //dev path
                  .attr("xlink:href", d => "http://192.168.7.20/ideascast/D3/uploads/user_images/" + d.image)
                  //prod path
                  //.attr("xlink:href", d => d.image)
                  .attr("x", -(nodeWidth/2))
                  .attr("y", -(nodeWidth/2))
                  .attr("width", nodeWidth)
                  .attr("height", nodeWidth)
                  .attr("clip-path", "url(#avatar-clip)")
                  //.attr("opacity", 0.1)
                  .call(d3.drag()
                        .on("start", dragstarted)
                        .on("drag", dragged)
                        .on("end", dragended));

             //create node tooltip
            node.append("title")
                .attr("class", "nodeTooltip")
                .text(d => d.name+"\n(right-click for profile)");

            //create node borders
            myCircles = topG.append("g")
              .attr("class", "circles")
              .selectAll("circle")
              .data(sharing.nodes)
              .enter().append("circle")
                    .attr("r", (nodeWidth/2))
                    .attr("stroke", "#ccc")
                    .attr("fill", "none")
                    .attr("stroke-width", 2);

            // force simulator
            simulation = d3.forceSimulation();
            // set up the simulation and event to update locations after each tick
            simulation
                .nodes(sharing.nodes);
            //initializeForces();
            simulation
                .force("link", d3.forceLink())
                .force("charge", d3.forceManyBody())
                .force("collide", d3.forceCollide())
                .force("center", d3.forceCenter())
                .force("forceX", d3.forceX())
                .force("forceY", d3.forceY());

            // set and apply properties to each of the forces
            setForceProperties();
            applyForceProperties();
            
            //step through simulation updates
            simulation.on("tick", ticked);

            //create scale to map counts to links line width
            linearScale = d3.scaleLinear().range([0, 5]).interpolate(d3.interpolateRound); //ensure integer

            //display links with relevant attributes set
            linksShow();

            //resize();
            d3.select(window).on("resize", resize);

            //double click on user image to show profile
            d3.selectAll("image").on("contextmenu", showUserProfile);

        });

        function showUserProfile (d) {
            
            //code to trigger displaying user profile in parent container
            parent.showProfile(d.id);

            //prevent right click browser menu appearing
            d3.event.preventDefault();

        }

        function resize() {

            width = document.body.clientWidth;
            height = document.body.clientHeight;

            svg
           .attr("width", width)
           .attr("height", height);
            
            applyForceProperties();
            simulation.alpha(1).restart();

        }

        function setForceProperties() {

            //values for all force properties
            forceProperties = {
                charge: {
                    enabled: true,
                    strength: -450,
                    distanceMin: 1,
                    distanceMax: setDistanceMax()
                },
                collide: {
                    enabled: true,
                    strength: 0.7,
                    iterations: 1,
                    radius: nodeWidth / 2
                },
                forceX: {
                    enabled: false,
                    strength: .7,
                    x: .5
                },
                forceY: {
                    enabled: true,
                    strength: .01,
                    y: .5
                },
                link: {
                    enabled: true,
                    distance: 100,
                    iterations: 1
                }
            }
        }

        function setDistanceMax() {

            //vary strength based on node count to improve layout
            var nodesCount = d3.selectAll("image").size();
            switch (true) {
                case nodesCount < 50:
                    return 200;
                    break;
                case nodesCount < 100:
                    return 275;
                    break;
                case nodesCount < 200:
                    return 350;
                    break;
                case nodesCount < 500:
                    return 400;
                    break;
                default:
                    return 500;
            }

        }

        function applyForceProperties() {

            simulation.force("center")
                    .x(width/2)
                    .y(height/2);
            simulation.force("charge")
                .strength(forceProperties.charge.strength * forceProperties.charge.enabled)
                .distanceMin(forceProperties.charge.distanceMin)
                .distanceMax(forceProperties.charge.distanceMax);
            simulation.force("collide")
                .strength(forceProperties.collide.strength * forceProperties.collide.enabled)
                .radius(forceProperties.collide.radius)
                .iterations(forceProperties.collide.iterations);
            simulation.force("forceX")
                .strength(forceProperties.forceX.strength * forceProperties.forceX.enabled)
                .x(width * forceProperties.forceX.x);
            simulation.force("forceY")
                .strength(forceProperties.forceY.strength * forceProperties.forceY.enabled)
                .y(height * forceProperties.forceY.y);
            simulation.force("link")
                    .id(d => d.id)
                    .distance(forceProperties.link.distance)
                    .iterations(forceProperties.link.iterations)
                    .links(forceProperties.link.enabled ? sharing.links : []);

        }

        // update the display positions after each simulation tick
        function ticked() {

            //update nodes
            node
                .attr("transform", d=> "translate(" + d.x + "," + d.y + ")");

            //update links
            link.attr("d", function (d) {
                var dx = d.target.x - d.source.x,
                    dy = d.target.y - d.source.y,
                    dr = Math.sqrt(dx * dx + dy * dy) + 50; //+50 to tighten angle
                return "M" +
                    d.source.x + "," +
                    d.source.y + "A" +
                    dr + "," + dr + " 0 0,1 " +
                    d.target.x + "," +
                    d.target.y;
            });

            //update user circles
            myCircles
                .attr("cx", d => d.x)
                .attr("cy", d => d.y);
        }

        function dragstarted(d) {
            if (!d3.event.active) simulation.alphaTarget(0.3).restart();
            d.fx = d.x;
            d.fy = d.y;
        }

        function dragged(d) {
            d.fx = d3.event.x;
            d.fy = d3.event.y;
        }

        function dragended(d) {
            if (!d3.event.active) simulation.alphaTarget(0);
            d.fx = null;
            d.fy = null;
        }

        function viewChange(viewSelected) {

            //set hidden label
            document.getElementById("viewLabel").innerHTML = viewSelected;

            //change button styles appropriately
            switch (viewSelected) {
                case "Owner":
                    //change button appearance
                    document.getElementById("showAllButton").className = "greyButtonAll";
                    document.getElementById("ownerButton").className = "greenButton";
                    document.getElementById("sharerButton").className = "greyButton";

                    //show relevant sliders panel content
                    document.getElementById("menuPanel").style.height = "118px";
                    document.getElementById("levelInput").style.visibility = "hidden";
                    document.getElementById("levelLabel").style.visibility = "hidden";
                    document.getElementById("valueInput").style.visibility = "visible";
                    document.getElementById("minLabel").style.visibility = "visible";
                    document.getElementById("rangeLabel").style.visibility = "visible";
                    document.getElementById("maxLabel").style.visibility = "visible";
                    document.getElementById("allLabel").style.visibility = "visible";
                    
                    break;

                case "Sharer":
                    //change button appearance
                    document.getElementById("showAllButton").className = "greyButtonAll";
                    document.getElementById("ownerButton").className = "greyButton";
                    document.getElementById("sharerButton").className = "greenButton";

                    //show relevant sliders panel content
                    document.getElementById("menuPanel").style.height = "154px";
                    document.getElementById("levelInput").style.visibility = "visible";
                    document.getElementById("levelLabel").style.visibility = "visible";
                    document.getElementById("valueInput").style.visibility = "visible";
                    document.getElementById("minLabel").style.visibility = "visible";
                    document.getElementById("rangeLabel").style.visibility = "visible";
                    document.getElementById("maxLabel").style.visibility = "visible";
                    document.getElementById("allLabel").style.visibility = "visible";

                    break;

                default: //All
                    //change button appearance
                    document.getElementById("showAllButton").className = "greenButtonAll";
                    document.getElementById("ownerButton").className = "greyButton";
                    document.getElementById("sharerButton").className = "greyButton";

                    //hide sliders panel
                    document.getElementById("menuPanel").style.height = "67px";
                    document.getElementById("levelInput").style.visibility = "hidden";
                    document.getElementById("levelLabel").style.visibility = "hidden";
                    document.getElementById("valueInput").style.visibility = "hidden";
                    document.getElementById("minLabel").style.visibility = "hidden";
                    document.getElementById("rangeLabel").style.visibility = "hidden";
                    document.getElementById("maxLabel").style.visibility = "hidden";
                    document.getElementById("allLabel").style.visibility = "hidden";
            }

            //set range value to all
            document.getElementById("valueInput").value = 6;

            //set level to projects
            document.getElementById("levelInput").value = 1;

            //update range and then show links
            levelChange();

        }

        function levelChange() {

            //enable value slider
            document.getElementById("valueInput").disabled = false;

            //default mouse cursor
            document.getElementById("valueInput").style.cursor = "default";

            //set value slider to all
            document.getElementById("valueInput").value = 6;

            switch (document.getElementById("viewLabel").innerHTML) {

                case "Owner":

                    //set level label
                    document.getElementById("levelLabel").innerHTML = "Show Project Shares";

                    //only show shared projects
                    myMin = d3.min(sharing.links.filter(d => d.role === "Owner"), d => d.project_count);
                    myMax = d3.max(sharing.links.filter(d => d.role === "Owner"), d => d.project_count);

                    break;

                case "Sharer":

                    //calculate min and max based on level selection
                    switch (document.getElementById("levelInput").value) {
                        case "3": //Tasks

                            //set level label
                            document.getElementById("levelLabel").innerHTML = "Show Task Shares";

                            myMin = Math.max(d3.min(sharing.links.filter(d => d.role === "Sharer"), d => d.task_count), 1);
                            myMax = d3.max(sharing.links.filter(d => d.role === "Sharer"), d => d.task_count);

                            break;

                        case "2": //Workspaces

                            //set level label
                            document.getElementById("levelLabel").innerHTML = "Show Workspace Shares";

                            myMin = Math.max(d3.min(sharing.links.filter(d => d.role === "Sharer"), d => d.workspace_count), 1);
                            myMax = d3.max(sharing.links.filter(d => d.role === "Sharer"), d => d.workspace_count);

                            break;

                        default: //Projects

                            //set level label
                            document.getElementById("levelLabel").innerHTML = "Show Project Shares";

                            myMin = Math.max(d3.min(sharing.links.filter(d => d.role === "Sharer"), d => d.project_count), 1);
                            myMax = d3.max(sharing.links.filter(d => d.role === "Sharer"), d => d.project_count);

                }

                break;

                default: //All

                        myMin = 0;
                        myMax = 0;
    
            }

            //0 or 1 shares case - effective only for sharer workspaces and tasks, always have >0 project shares
            if (myMax == 0 || myMax == 1) {
                //disable value slider
                document.getElementById("valueInput").disabled = true;
                document.getElementById("valueInput").style.cursor = "not-allowed";
                //set min and max to zero
                //myMin = 0;
                //myMax = 0;
            }

            //set scale domain for min and max
            linearScale.domain([myMin, myMax]);

            //show min and max for the user to see
            document.getElementById("minLabel").innerHTML = myMin;
            document.getElementById("maxLabel").innerHTML = myMax;

            //update links displayed
            linksShow();

        }

        function linksShow() {
            
            switch (document.getElementById("viewLabel").innerHTML) {
                case "Owner":
                    
                    d3.selectAll(".ownerLink")
                        .attr("stroke-width", setLinkWidth)
                        .attr("visibility", d => (document.getElementById("valueInput").value == "6") ? "visible" : (setLinkWidth(d) == document.getElementById("valueInput").value) ? "visible" : "hidden")
                        .attr("stroke-dasharray", "none");
                        
                    d3.selectAll(".ownerMarker")
                        .attr("visibility", "visible");

                    d3.selectAll(".sharerLink")
                        .attr("stroke-width", 0)
                        .attr("visibility", "hidden");

                    d3.selectAll(".sharerMarker")
                        .attr("visibility", "hidden");

                    d3.selectAll(".linkTooltip")
                        .text(linkText);

                    break;

                case "Sharer":
                    
                    d3.selectAll(".sharerLink")
                        .attr("stroke-width", setLinkWidth)
                        .attr("visibility", d => (setLinkWidth(d) == 0) ? "hidden" : (document.getElementById("valueInput").value == "6") ? "visible" : (setLinkWidth(d) == document.getElementById("valueInput").value) ? "visible" : "hidden")
                        .attr("stroke-dasharray", "none");                        

                    d3.selectAll(".sharerMarker")
                        .attr("visibility", "visible");
                    
                    d3.selectAll(".ownerLink")
                        .attr("stroke-width", 0)
                        .attr("visibility", "hidden");

                    d3.selectAll(".ownerMarker")
                        .attr("visibility", "hidden");
                    
                    d3.selectAll(".linkTooltip")
                        .text(linkText);

                    break;

                default: //All
                    
                    //display all links
                    d3.selectAll(".sharerLink")
                        .attr("visibility", "visible")
                        .attr("stroke-width", 2);
                        
                    d3.selectAll(".ownerLink")
                         .attr("visibility", "visible")
                         .attr("stroke-dasharray", "4")
                         .attr("stroke-width", 2);
                        
                    d3.selectAll(".sharerMarker")
                        .attr("visibility", "visible");

                    d3.selectAll(".ownerMarker")
                        .attr("visibility", "visible");

                    d3.selectAll(".linkTooltip")
                    .text(function (d) { return "Shared as " + d.role + ":\nProjects: " + d.project_count + "\nWorkspaces: " + d.workspace_count + "\nTasks: " + d.task_count; });

            }
        }

        function setLinkWidth(d) {

            switch (document.getElementById("viewLabel").innerHTML) {

                case "Owner":

                    //only project count shown for owner, and ensure min value of 1 as there are always projects
                    return Math.max(linearScale(d.project_count), 1);

                    break;

                case "Sharer":

                    switch (document.getElementById("levelInput").value) {
                        case "3": //Tasks
                            return (d.task_count == 0) ? 0 : Math.max(linearScale(d.task_count), 1);
                            break;
                        case "2": //Workspaces
                            return (d.workspace_count == 0) ? 0 : Math.max(linearScale(d.workspace_count), 1);
                            break;
                        default: //Projects
                            return (d.project_count == 0) ? 0 : Math.max(linearScale(d.project_count), 1);
                    }

                    break;

                default: //all, not called but for completeness

                    //return default line width
                    return 2;
            }
            
        }

         function linkText(d) {

             switch (document.getElementById("levelInput").value) {

                case "3": //Tasks
                    return "Shared as " + d.role + ":\nTasks: " + d.task_count;
                    break;

                case "2": //Workspaces
                    return "Shared as " + d.role + ":\nWorkspaces: " + d.workspace_count;
                    break;

                default: //Projects
                    return "Shared as " + d.role + ":\nProjects: " + d.project_count;

            }
        }

     </script>
