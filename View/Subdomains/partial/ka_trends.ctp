<!--Copyright 2010-2020 Mike Bostock All rights reserved.-->
<?php echo $this->Html->script('projects/d3.v5.min', array('inline' => true)); ?>
<!--Copyright (c) 2014 marmelab-->
<?php echo $this->Html->script('projects/event-drops', array('inline' => true)); ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

?>
<div class="box-header filters" style="padding: 10px;">
    <div class="ka-trends-wrap popup-select-icon popup-multselect-list">
        <div class="ka-trends-filed tnum-results">
            <label class="custom-dropdown">
                <select class="aqua" name="trends_limit" id="trends_limit">
                    <option value="">10</option>
                    <option value="20" selected="">20</option>
                    <option value="50" >50</option>
                    <option value="100" >100</option>
					<option value="250" >250</option>
                </select>
            </label>
        </div>
        <div class="ka-trends-filed tmost-viewed">
            <label class="custom-dropdown">
                <select class="aqua" name="trends_order" id="trends_order">
                    <option value="DESC" selected="">Most Viewed</option>
                    <option value="ASC">Least Viewed</option>
                </select>
            </label>
        </div>
        <div class="ka-trends-filed ttypeitems">
            <label class="custom-dropdown">
                <select class="aqua" name="trends_type" id="trends_type">
                    <option value="" >Select Type</option>
                    <option value="organizations" >Organizations</option>
                    <option value="locations" >Locations</option>
                    <option value="departments" >Departments</option>
                    <option value="people" >People</option>
                    <option value="skill" >Skills</option>
                    <option value="subject" >Subjects</option>
                    <option value="domain" >Domains</option>
                </select>
            </label>
        </div>
		<div class="ka-trends-filed tviewedby">
            <label class="custom-dropdown">
                <select class="aqua" name="trends_viewed_by" id="trends_viewed_by">
                    <option value="" >Viewed By</option>
                    <option value="community" >All Community</option>
                    <option value="organizations" >Specific Organizations</option>
                    <option value="locations" >Specific Locations</option>
                    <option value="departments" >Specific Departments</option>
                    <option value="users" >Specific People</option>
                    <option value="skills" >Specific Skills</option>
                    <option value="subjects" >Specific Subjects</option>
                    <option value="domains" >Specific Domains</option>
                    <option value="all_projects" >All My Projects</option>
                    <option value="created_projects" >Projects I Created</option>
                    <option value="owner_projects" >Projects I Own</option>
                    <option value="shared_projects" >Projects Shared With Me</option>
                    <option value="project" >Specific Projects</option>
                </select>
            </label>
            <label class="social-select-dropdown specific-section">
                <select class="aqua" name="trends_specific_items" id="trends_specific_items" multiple=""></select>
            </label>
       </div>

		<div class="ka-trends-filed t-date">
		     <div class="input-group">
                <input name="start_date" value="" id="trends_start_date" class="form-control input-small" type="text" readonly="">
                <div class="input-group-addon data-new calendar-trigger">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
	   </div>
		<div class="ka-trends-filed t-date">
            <div class="input-group">
                <input name="end_date" value="" id="trends_end_date" class="form-control input-small" type="text" readonly="">
                <div class="input-group-addon data-new calendar-trigger">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>
        <div class="ka-trends-btn">
            <button class="btn btn-success btn-show-trends dm-show" type="button">Show</button>
       </div>

    </div>
</div>
<div class="box-body clearfix border1"  style=" padding: 0;" id="box_body" >
    <div id="tr_analyticsIframe" style="width: 100%; border: none; height: 100%;  padding-top: 12px; min-height: 700px; overflow-y: auto; overflow-x: hidden;">
        <div class="" style="padding-top: 10px;">
            <div class="col-sm-12 box-borders select-project-sec">
                <div class="no-data">SELECT TYPE AND RANGE</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

        var global = window;
        var tr_d3 = window.d3;

        var tr_data;



    $(function(){

        $.default_state = () => {
            $("#tr_analyticsIframe").html('<div class="" style="padding-top: 10px;">\
                        <div class="col-sm-12 box-borders select-project-sec">\
                            <div class="no-data">SELECT TYPE AND RANGE</div>\
                        </div>\
                    </div> ');
        }
        // SORT AJAX DATA
        function sort_object (a, b){
            var aName = a.label.toLowerCase();
            var bName = b.label.toLowerCase();
            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
        }

        // SHOW/HIDE SUBMIT BUTTON
        $.show_trends_btn = () => {
            var limit = $('#trends_limit').val() || '',
                order = $('#trends_order').val() || '',
                type = $('#trends_type').val() || '',
                viewed_by = $('#trends_viewed_by').val() || '',
                specific_id = $('#trends_specific_items').val() || [],
                start_date = $('#trends_start_date').val() || '',
                end_date = $('#trends_end_date').val() || '',
                specific_flag = false;

            if(viewed_by != '' && ( viewed_by == 'community' || viewed_by == 'all_projects' || viewed_by == 'created_projects' || viewed_by == 'owner_projects' || viewed_by == 'shared_projects') ) {
                specific_flag = true;
            }
            else if(viewed_by != '' && (viewed_by != 'organizations' || viewed_by != 'locations' || viewed_by != 'departments' || viewed_by != 'users' || viewed_by != 'skills' || viewed_by != 'subjects' || viewed_by != 'domains' || viewed_by != 'project') && specific_id.length > 0) {
                specific_flag = true;
            }

            $(".btn-show-trends").addClass('dm-show');
            $.default_state();

            if(limit != '' && order != '' && type != '' && specific_flag && start_date != '' && end_date != '' ) {
                /*if(( viewed_by == 'community' || viewed_by == 'all_projects' || viewed_by == 'created_projects' || viewed_by == 'owner_projects' || viewed_by == 'shared_projects') ) {
                    $(".btn-show-trends").removeClass('dm-show');
                }
                else if((viewed_by != 'organizations' || viewed_by != 'locations' || viewed_by != 'departments' || viewed_by != 'users' || viewed_by != 'skills' || viewed_by != 'subjects' || viewed_by != 'domains' || viewed_by != 'project') && specific_id.length > 0) {
                    $(".btn-show-trends").removeClass('dm-show');
                }*/
                $(".btn-show-trends").removeClass('dm-show');
            }
        };

        // GET SPECIFIC ITEMS WITH AJAX
        $.get_specific = function(data){
            $('#trends_specific_items').empty();
            $.ajax({
                url: $js_config.base_url + 'subdomains/get_trends_data',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.success){
                        if(response.content){
                            var content = response.content.sort(sort_object);
                            // console.log(content)
                            $("#trends_specific_items").prop('disabled', false).multiselect('dataprovider', content);
                            $.show_trends_btn();
                        }
                    }
                }
            })
        }

        // SHOW/HIDE SPECIFIC ITEM DD AND FILL DATA ACCORDING TO THE TYPE
        $('#trends_limit, #trends_order, #trends_type').on('change', function(event) {
            $.show_trends_btn();
        })

        // SHOW/HIDE SPECIFIC ITEM DD AND FILL DATA ACCORDING TO THE TYPE
        $('#trends_viewed_by').on('change', function(event) {
            event.preventDefault();

            var type = $(this).val()

            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                $(this).parents('.tviewedby').find('.specific-section').slideUp(500, function(){
                    $.resize_frame();
                });
            }
            else {
                $(this).parents('.tviewedby').find('.specific-section').slideDown(500, function(){
                    $.resize_frame();
                });
            }

            $.show_trends_btn();
            $("#trends_specific_items").prop('disabled', true).multiselect('dataprovider', []);
            if(type == '' || type == 'community' || type == 'all_projects' || type == 'created_projects' || type == 'owner_projects' || type == 'shared_projects') {
                return;
            }
            else if(type == 'skills' || type == 'subjects' || type == 'domains') {
                var data = {type: type};
                $.get_specific(data);
            }
            else if(type != '') {
                var data = {type: type};
                $.get_specific(data);
            }
        });
        // RESIZE MAIN FRAME
        ($.resize_frame = function(){
            $('#tr_analyticsIframe').animate({
                minHeight: (($(window).height() - $('#tr_analyticsIframe').offset().top) ) - 17,
                maxHeight: (($(window).height() - $('#tr_analyticsIframe').offset().top) ) - 17
            }, 1)
            setTimeout(() => {
                //setup click handler on row labels
                d3.selectAll('text.line-label')
                    .on("click", d => (tr_showUserProfile(d.id, d.type), d3.event.stopPropagation(), d3.event.preventDefault()));
            }, 1500)

        })();

        // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                if($('#ka_tabs li.active a').attr('href') == '#ka_trends'){
                    $.resize_frame();
                }
                clearInterval(interval);
            }
        }, 1);

        // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
        $(".sidebar-toggle").on('click', function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_trends'){
                $.resize_frame();
                const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
                setTimeout( () => clearInterval(fix), 1500);
            }


        })

        // RESIZE FRAME ON WINDOW RESIZE EVENT
        $(window).resize(function() {
            if($('#ka_tabs li.active a').attr('href') == '#ka_trends'){
                $.resize_frame();
            }
        })

        /////////// DATEPICKERS ///////////
        // START DATE
        $("#trends_start_date").datepicker({
            dateFormat : 'dd M yy',
            maxDate: "-1",
            onSelect: function(selected) {
                var minDate = new Date(Date.parse(selected));
                minDate.setDate(minDate.getDate() + 1);
                $("#trends_end_date").datepicker("option","minDate", minDate);
                $.show_trends_btn();
            }
        });
        var d = new Date(); // today
        d.setMonth(d.getMonth() - 1); // one month ago
        $("#trends_start_date").datepicker('setDate', (d));

        // END DATE
        $("#trends_end_date").datepicker({
            dateFormat : 'dd M yy',
            setDate: new Date(),
            onSelect: function(selected) {
                var maxDate = new Date(Date.parse(selected));
                maxDate.setDate(maxDate.getDate() - 1);
               $("#trends_start_date").datepicker("option","maxDate", maxDate);
               $.show_trends_btn();
            }
        });

        $("#trends_end_date").datepicker('setDate', new Date());

        // SET MAX DATE OF START DATE FIELD TO TODAY THAT IS END DATE FIELD VALUE
        // $("#trends_start_date").datepicker("option","maxDate", new Date());
        // SET MIN DATE OF END DATE FIELD TO ONE MONTH AGO THAT IS START DATE FIELD VALUE
        d.setDate(d.getDate() + 1); // one month ago
        $("#trends_end_date").datepicker("option", "minDate", d);
        $("#trends_end_date").datepicker("option", "maxDate", new Date());

        // SHOW CALENDAR ON ICON CLICK
        $('.calendar-trigger').on('click', function(event) {
            event.preventDefault();
            $(this).parent().find('input').datepicker("show");
        });

        /////////// DATEPICKERS ///////////

        // MULTISELECT FOR SPECIFIC ITEMS
        $trends_specific_items = $('#trends_specific_items').multiselect({
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
                    $.show_trends_btn();
                },
                onDeselectAll:function(){
                    $.show_trends_btn();
                },
                onChange: function(element, checked) {
                    $.show_trends_btn();
                }
            });


        // D3 FUNCTIONALITY
        $('.btn-show-trends').on('click', function(event) {
            event.preventDefault();
            if($('#ka_tabs li.active a').attr('href') == '#ka_trends'){
                //get data
                $("#tr_analyticsIframe").html('');

                var post_data = {
                            limit: $('#trends_limit').val(),
                            order: $('#trends_order').val(),
                            type: $('#trends_type').val(),
                            viewed_by: $('#trends_viewed_by').val(),
                            specific_id: $('#trends_specific_items').val(),
                            start_date: $('#trends_start_date').val(),
                            end_date: $('#trends_end_date').val(),
                        }
                d3.json($js_config.base_url + "subdomains/ka_trends_json/", {
                    method:"POST",
                    body: JSON.stringify(post_data),
                    headers: {
                        "Content-type": "application/json; charset=UTF-8"
                    }
                })
                .then(function ( tr_data) {

                    //get max and min dates for range
                    const tr_dates = tr_data.filter(e => e.data).map(e => e.data.map(data => data.date)).reduce((a, b) => a.concat(b), []);
                    // var tr_maxdate = new Date(tr_dates.reduce((a, b) => new Date(a) > new Date(b) ? a : b));
                    // var tr_mindate = new Date(tr_dates.reduce((a, b) => new Date(a) < new Date(b) ? a : b));

                    var tr_maxdate = new Date($('#trends_end_date').val());
                    var tr_mindate = new Date($('#trends_start_date').val());
                    // var tr_maxdate = new Date(maxdate.getFullYear() + '/' + (maxdate.getMonth()+1) + '/' + maxdate.getDate());
                    // var tr_mindate = new Date(mindate.getFullYear() + '/' + (mindate.getMonth()+1) + '/' + mindate.getDate());
                    //pad the range by 5 percent, min 1 day
                    const tr_paddingdays = Math.ceil((tr_maxdate - tr_mindate) / (1000*60*60*24) * 0.05);
                    tr_maxdate.setDate(tr_maxdate.getDate() + tr_paddingdays);
                    tr_mindate.setDate(tr_mindate.getDate() - tr_paddingdays);
                    // tr_maxdate.setDate(tr_maxdate.getDate() + tr_paddingdays);
                    // tr_mindate.setDate(tr_mindate.getDate() - tr_paddingdays);

                    //drop color vertically from red to blue
                    tr_dropColor = d3.scaleLinear()
                        .domain([0, tr_data.length/2, tr_data.length])
                        .range(["#e5030d", "#ee8640", "#e3a809"]) //red organge yellow
                        .unknown("#3c8dbc"); //blue

                    const tr_chart = eventDrops({
                        tr_d3,
                        range: {
                            start: tr_mindate,
                            end: tr_maxdate,
                        },
                        line: {
                            color: (line, index) => tr_dropColor(index),
                            height: 30,
                        },
                        label: {
                            text: row => row.name.length < 22 ? `${row.name} (${row.data.length})` : row.name.substring(0,22) + "..." + `(${row.data.length})`,
                        },
                        drop: {
                            date: d => new Date(d.date),
                            onClick: d =>  {tr_showUserProfile(d.id, 'Person');d3.event.stopPropagation();d3.event.preventDefault();},
                        },
                        margin: {
                            top: 40,
                            right: 10,
                            bottom: 40,
                            left: 10,
                        },
                    });

                    d3
                        .select('#tr_analyticsIframe')
                        .data([tr_data])
                        .call(tr_chart);

                    //setup click handler on row labels
                    d3.selectAll('text.line-label')
                        .on("click", d => (tr_showUserProfile(d.id, d.type), d3.event.stopPropagation(), d3.event.preventDefault()));

                })
                 .catch(function(tr_err) {

                        $("#tr_analyticsIframe").html('<div class="col-sm-12 box-borders select-project-sec">\
                                    <div class="no-data">NO MATCHING ACTIVITIES</div>\
                                </div>');
                        throw new Error("No Activity Data - Managed Exit.");
                });
            }

        });
    })


    function showUserDialog(d) {
        $('#popup_modal').modal({
            remote: $js_config.base_url + 'shares/show_profile/'+ d
        }).show();

    }
    function showSkillsDialog(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_skills/' + d
        }).show();
    }
    function showSubjectsDialog(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_subjects/' + d
        }).show();
    }
    function showDomainsDialog(d) {
        $('#modal_view_skill').modal({
            remote: $js_config.base_url + 'competencies/view_domains/' + d
        }).show();
    }
    function showOrgDialog(d) {
        $('#modal_view_org').modal({
            remote: $js_config.base_url + 'communities/view/org/' + d
        }).show();;
    }
    function storyProfile(d) {
        $('#story_view').modal({
            remote: $js_config.base_url + 'stories/view/story/' + d
        }).show();
    }
    function showDeptDialog(d) {
        $('#modal_view_dept').modal({
            remote: $js_config.base_url + 'communities/view/dept/' + d
        }).show();
    }
    function showLocDialog(d) {
        $('#modal_view_loc').modal({
            remote: $js_config.base_url + 'communities/view/loc/' + d
        }).show();
    }

    function tr_showUserProfile (tr_id, tr_type) {
        console.log('tttt2222222222222222')
        switch (tr_type) {
            case "Organization":
                showOrgDialog(tr_id);
                break;
            case "Location":
                showLocDialog(tr_id);
                break;
            case "Department":
                showDeptDialog(tr_id);
                break;
            case "Person":
                showUserDialog(tr_id);
                break;
            case "Skill":
                showSkillsDialog(tr_id);
                break;
            case "Subject":
                showSubjectsDialog(tr_id);
                break;
            case "Domain":
                showDomainsDialog(tr_id);
                break;
            }
    }
</script>
