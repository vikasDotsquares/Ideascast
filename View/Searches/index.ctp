<?php
echo $this->Html->css('projects/smart_search', array(
    'inline' => true
));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->css('projects/dropdown');
echo $this->Html->css('projects/dd-menus');
echo $this->Html->css('projects/bs-selectbox/bootstrap-select');
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->script('projects/smart_search', array(
    'inline' => true
));
echo $this->Html->script('projects/plugins/dd-menus/dd-menus', array(
    'inline' => true
));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array(
    'inline' => true
));
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array(
    'inline' => true
));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array(
    'inline' => true
));
echo $this->Html->css('projects/paginations');
?>
<style type="text/css">
    .tipText {
        text-transform: none !important;
    }

    .mark, mark {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
        color: #f00;
        padding: 0.2em;
        text-transform: none !important;
    }

    .has-danger {
        border: 1px solid red;
    }

    .search_wrapper .left-container, .search_wrapper .right-container {
        min-height: 700px;
    }

    .searching-ac {
        min-height: 662px;
    }

    .search_wrapper .right-container {
        border: 1px solid #ccc;
    }

    .search_wrapper .left-container .panel-green, .search_wrapper .left-container .panel-heading h5
    {
        margin: 0;
    }

    .search_wrapper .left-container .panel-green.items {
        margin-bottom: 5px;
    }

    .toggle-search-items {
        color: #fff;
    }

    .toggle-search-items:hover, .toggle-search-items:focus {
        color: #fff;
    }

    .toggle-search-items[aria-expanded="true"] i.fa::before {
        content: "\f068";
    }

    .toggle-search-items[aria-expanded="false"] i.fa::before {
        content: "\f067";
    }

    .left-container ul.search-items {
        margin: 0;
        padding: 0;
    }

    .left-container ul.search-items li {
        color: #333;
        list-style-type: none;
        padding: 3px;
    }

    .left-container ul.search-items-all {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .left-container ul.search-items-all li {
        color: #333;
        list-style-type: none;
        padding: 3px;
        margin: 0;
        padding: 3px;
    }

    #project_accordion ul.search-items.first {
        border-bottom: 1px solid #ccc;
        margin: 0 0 10px;
        padding: 0 0 10px;
    }

    .ico_badge {
        background-repeat: no-repeat;
        background-size: 80% auto;
        cursor: pointer;
        display: inline-block;
        height: 20px;
        width: 23px;
        margin-bottom: -4px;
    }

    .result-item {
        padding: 15px 0 5px;
        border-bottom: 1px solid #ccc;
        width: 100%;
        display: inline-block;
    }

    .ico_badge_blank {
        background-image: url("<?php echo SITEURL; ?>images/icons/badge.png");
    }

    .ico_badge_user {
        background-image: url("<?php echo SITEURL; ?>images/icons/badge-user.png");
    }

    .details-title-wrapper {
        display: block;
    }

    .details-title {
        font-size: 13px;
        font-weight: 600;
        color: #1A79CE;
    }

    .details-title .rows {
        padding-bottom: 2px;
    }

    .details-title .participate-link {
        font-size: 13px;
        font-weight: 600;
        color: #1A79CE;
    }

    .details-title .participate-link:hover {
        color: #67a028;
    }

    .search-div-main .details .type {
        font-size: 13px;
        text-align: left;
    }

    .search-div-main .details .type a {
        float: left;
    }

    .find-in {
        font-size: 13px;
        font-weight: normal;
        color: #696969;
        clear: both;
    }

    .search_result_section {
        padding-right: 0px;
        padding-left: 0px;
    }

    .find-in .type {
        font-weight: 600;
    }

    .highlighted {
        font-weight: 600;
        color: #cc0000;
    }

    .smart-s-select {
        width: 17%;
        float: left;
    }

    .keyword-search {
        float: left;
        width: 23%;
    }

    .sm-search {
        float: left;
        width: 77%;
        padding-left: 30px;
    }

    .smartexact-words {
        float: left;
        width: 80%;
    }

    .smt-search-but {
        float: right;
        width: 20%;
    }

    @media ( min-width :1365px) and (max-width:1440px) {
        .smart-s-select {
            padding: 0px;
        }
        .smart_search .radio.radio-warning {
            margin: 5px 8px 4px 0;
        }
        .sm-search {
            padding-left: 15px;
            width: 75%;
        }
        .smartexact-words {
            padding: 0px;
        }
        .keyword-search {
            width: 25%;
        }
        .smart-s-select {
            width: 17%;
        }
        .smt-search-but {
            width: 18%;
        }
        .smartexact-words {
            width: 82%;
        }
    }

    @media ( min-width :1280px) and (max-width:1364px) {
        .smart-s-select {
            padding: 0px;
        }
        .smart_search .radio.radio-warning {
            margin: 5px 8px 4px 0;
        }
        .sm-search {
            padding-left: 15px;
            width: 82%;
        }
        .smartexact-words {
            padding: 0px;
        }
        .keyword-search {
            width: 18%;
        }
        .smart-s-select {
            width: 17%;
        }
        .smt-search-but {
            width: 18%;
        }
        .smartexact-words {
            width: 82%;
        }
    }

    @media ( max-width :991px) {
        .smart-s-select {
            width: 50%;
        }
        .keyword-search {
            width: 50%;
        }
        .sm-search {
            padding-left: 0;
            width: 100%;
            margin-top: 7px;
        }
        .smt-search-but {
            width: 18%;
        }
        .smartexact-words {
            width: 82%;
        }
    }

    @media ( min-width :767px) and (max-width:991px) {
        .search_wrapper .left-container, .search_wrapper .right-container {
            min-height: inherit;
            margin-bottom: 20px;
            padding: 0px;
        }
        .searching-ac {
            min-height: inherit;
        }
    }

    @media ( max-width :767px) {
        .search_wrapper .left-container, .search_wrapper .right-container {
            min-height: inherit;
            margin-bottom: 20px;
            padding: 0px;
        }
        .searching-ac {
            min-height: inherit;
        }
        .sm-search {
            padding: 10px 10px;
            margin-top: 0px;
        }
        .smart-s-select {
            padding-left: 10px;
        }
        .keyword-search {
            padding-right: 10px;
        }
        .smt-search-but {
            width: 100%;
        }
        .smartexact-words {
            width: 100%;
        }
    }

    @media ( max-width :479px) {
        .smart-s-select, .keyword-search {
            width: 100%;
            padding: 0px 10px;
        }
    }
</style>
<script type="text/javascript">
    $(function() {

        $('body').delegate('.submit_list', 'click', function(event) {
            event.preventDefault();

            var $this = $(this),
                    $form = $this.closest('form#modelFormAddSearchList'),
                    $title = $("#title", $form),
                    title_text = $title.val();

            if (title_text != '') {
                $.ajax({
                    url: $js_config.base_url + 'searches/save_people_list',
                    type: "POST",
                    data: $form.serialize(),
                    dataType: "JSON",
                    global: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal_box').modal('hide')
                        }

                    },
                })// end ajax
            } else {
                $title.parent().find('.error-message.text-danger').text('List title is required.')
            }
        })



    })

</script>

<div class="row smart_search">
    <!-- Modal Large -->
    <div class="modal modal-success fade" id="modal_box" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
    <!-- /.modal -->

    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php
                    echo $page_heading;
                    ?>
                    <p class="text-muted date-time">
                        <span style="text-transform: none;"><?php
                            echo $page_subheading;
                            ?></span>
                    </p>
                </h1>
            </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder">
                        <div class="box-header"
                             style="background: #f1f3f4 none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 15px 10px 10px; min-height: 48px;">
                            <div class="keyword-search">
                                <input autocomplete="off" required minlength="3" type="text" class="form-control" id="ajax_inp_keyword" placeholder="(Min of 3 chars Keyword)">
                            </div>
                            <div class="sm-search">
                                <div class="content_search search-section" id="content_search">
                                    <div class="smartexact-words">
                                        <div class="radio-left">
                                            <div class="radio radio-warning">
                                                <input type="radio" value="0" class="fancy_input" checked="checked"
                                                       name="keyword_type" id="radio_any"> <label for="radio_any"
                                                       class="fancy_labels">All words, any
                                                    order</label>
                                            </div>
                                            <div class="radio radio-warning">


                                                <input type="radio" data-target="#people_search"
                                                       value="1" class="fancy_input"
                                                       name="keyword_type" id="radio_exw_any"> <label
                                                       for="radio_exw_any" class="fancy_labels">All words, exact
                                                    order </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="smt-search-but text-right">
                                        <div class="btn-group">
                                            <a style="" class="btn btn-sm btn-success disabled" href="#"
                                               id="btn_ajax_search_content"> <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                        <div class="btn-group">
                                            <a class="btn btn-sm btn-danger pull-right" href="<?php echo SITEURL; ?>searches" id="reset_search_type_"> Reset </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="box-body clearfix"
                             style="margin-left: -17px ! important;">

                            <div style="display: none;" class="people_search search-section"
                                 id="people_search">

                                <div class="clearfix" style="display: block;">
                                    <span
                                        class="btn btn-sm btn-warning pull-right create_people_list"
                                        data-selection="2"
                                        data-remote="<?php
                                        echo Router::Url(array(
                                            "controller" => "searches",
                                            "action" => "create_people_list",
                                            "selection" => 2
                                                ), true);
                                        ?>"
                                        style="margin-left: 5px"><i class="fa fa-plus"></i> Create
                                        People List</span> <span
                                        class="btn btn-sm btn-success pull-right" id="open_list">Open
                                        List</span>
                                </div>

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#people_user"><i
                                                class="fa fa-user"></i> User</a></li>
                                    <li><a data-toggle="tab" href="#people_skills"><i
                                                class="fa fa-cogs"></i> Skills</a></li>
                                    <li><a data-toggle="tab" href="#people_group"><i
                                                class="fa fa-users"></i> Group</a></li>
                                    <li><a data-toggle="tab" href="#people_recommend"><i
                                                class="fa fa-thumbs-o-up"></i> Recommend</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="people_user" class="tab-pane fade in active">
                                        <div class="col-sm-12 margin-top">
                                            <div class="col-sm-12 col-md-6 col-lg-5">
                                                <label class="input-label col-sm-2"
                                                       style="margin-bootom: 0;"> <span>People: </span> <span> <i
                                                            class="fa fa-plus btn btn-sm btn-success create_people_list"
                                                            data-selection="1" data-target="#people_user_select"
                                                            data-remote="<?php
                                                            echo Router::Url(array(
                                                                "controller" => "searches",
                                                                "action" => "create_people_list",
                                                                "selection" => 1
                                                                    ), true);
                                                            ?>"></i>
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <?php
                                                    $users = $this->Search->users_list();
                                                    // pr($users);
                                                    $userArr = $selected = null;
                                                    if ($users) {
                                                        foreach ($users as $k => $val) {
                                                            $userArr [$val ["User"] ["id"]] = $val [0] ["User__name"];
                                                        }
                                                    }
                                                    echo $this->Form->select("people_user_id", $userArr, array(
                                                        "title" => "Select User",
                                                        "multiple" => "multiple",
                                                        "default" => $selected,
                                                        "id" => "people_user_select",
                                                        "class" => "form-control aqua",
                                                        "label" => false,
                                                        "div" => false,
                                                        "style" => "display: none;",
                                                        "data-width" => "100%"
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-7"></div>
                                        </div>
                                    </div>
                                    <div id="people_skills" class="tab-pane fade">
                                        <div class="col-sm-12 margin-top">
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <div class="row">
                                                    <label class="input-label col-sm-2"
                                                           style="margin-bootom: 0;"> <span>Skills: </span> <span> <i
                                                                class="fa fa-search btn btn-sm btn-default tipText"
                                                                title="Search for People" id="btn_user_by_skills"></i> <i
                                                                class="fa fa-times btn btn-sm btn-danger tipText"
                                                                title="Clear List" id="clear_skills"></i>
                                                        </span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <textarea rows="3" cols="50" name="txa_people_skills"
                                                                  id="txa_people_skills" class="form-control"
                                                                  placeholder=""></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <div class="row">
                                                    <label class="input-label col-sm-2"
                                                           style="margin-bootom: 0;"> <span>Names: </span> <span> <i
                                                                class="fa fa-plus btn btn-sm btn-success create_people_list"
                                                                data-selection="1" data-target="#skill_user_select"
                                                                data-remote="<?php
                                                                echo Router::Url(array(
                                                                    "controller" => "searches",
                                                                    "action" => "create_people_list",
                                                                    "selection" => 1
                                                                        ), true);
                                                                ?>"></i>
                                                        </span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                        $users = $this->Search->users_list();
                                                        // pr($users);
                                                        $userArr = $selected = null;
                                                        if ($users) {
                                                            foreach ($users as $k => $val) {
                                                                $userArr [$val ["User"] ["id"]] = $val [0] ["User__name"];
                                                            }
                                                        }
                                                        echo $this->Form->select("skill_user_id", $userArr, array(
                                                            "title" => "Select User",
                                                            "multiple" => "multiple",
                                                            "default" => $selected,
                                                            "id" => "skill_user_select",
                                                            "class" => "form-control aqua",
                                                            "label" => false,
                                                            "div" => false,
                                                            "style" => "display: none;",
                                                            "data-width" => "100%"
                                                        ));
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="people_group" class="tab-pane fade">
                                        <div class="col-sm-12 margin-top">
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <label class="input-label col-sm-2"
                                                       style="margin-bootom: 0;">Group: </label>
                                                <div class="col-sm-10">
                                                    <label class="custom-dropdown"
                                                           style="width: 100%; margin-top: 3px;"> <select
                                                            class="aqua" name="group_select" id="group_select">
                                                            <option value="">Select</option>
                                                            <?php
                                                            if (isset($all_groups) && !empty($all_groups)) {
                                                                ?>
                                                                <?php
                                                                foreach ($all_groups as $key => $val) {
                                                                    ?>
                                                                    <?php
                                                                    if (!empty($key)) {
                                                                        $v = (strlen($val) > 65) ? substr($val, 0, 65) . '...' : $val;
                                                                        ?>
                                                                        <option
                                                                            value="<?php
                                                                            echo $key;
                                                                            ?>"><?php
                                                                                echo $v;
                                                                                ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <label class="input-label col-sm-2"
                                                       style="margin-bootom: 0;"> <span>People: </span> <span> <i
                                                            class="fa fa-plus btn btn-sm btn-success create_people_list"
                                                            data-selection="1" data-target="#group_user_select"
                                                            data-remote="<?php
                                                            echo Router::Url(array(
                                                                "controller" => "searches",
                                                                "action" => "create_people_list",
                                                                "selection" => 1
                                                                    ), true);
                                                            ?>"></i>
                                                    </span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <?php
                                                    $users = $this->Search->users_list();

                                                    $userArr = $selected = null;
                                                    if ($users) {
                                                        foreach ($users as $k => $val) {
                                                            $userArr [$val ["User"] ["id"]] = $val [0] ["User__name"];
                                                        }
                                                    }
                                                    echo $this->Form->select("group_user_id", $userArr, array(
                                                        "title" => "Select User",
                                                        "multiple" => "multiple",
                                                        "default" => $selected,
                                                        "id" => "group_user_select",
                                                        "class" => "form-control aqua",
                                                        "label" => false,
                                                        "div" => false,
                                                        "style" => "display: none;",
                                                        "data-width" => "100%"
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="people_recommend" class="tab-pane fade">
                                        <div class="col-sm-12 margin-top">
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <div class="row">
                                                    <label class="input-label col-sm-2"
                                                           style="margin-bootom: 0;"> <span>Skills: </span> <span> <i
                                                                class="fa fa-search btn btn-sm btn-default tipText"
                                                                title="Search People" id="btn_user_by_keyword"></i> <i
                                                                class="fa fa-times btn btn-sm btn-danger tipText"
                                                                title="Clear List" id="clear_keyword"></i>
                                                        </span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <textarea rows="5" cols="50" name="txa_keyword" id="txa_keyword" class="form-control" placeholder="" style="resize: vertical;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6">
                                                <div class="row">
                                                    <label class="input-label col-sm-2"
                                                           style="margin-bootom: 0;"> <span>Names: </span> <span> <i
                                                                class="fa fa-plus btn btn-sm btn-success create_people_list" data-selection="1" data-target="#recommend_user_select" data-remote="<?php
                                                                echo Router::Url(array(
                                                                    "controller" => "searches",
                                                                    "action" => "create_people_list",
                                                                    "selection" => 1
                                                                        ), true);
                                                                ?>"></i>
                                                        </span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                        $users = $this->Search->users_list();
                                                        $userArr = $selected = null;
                                                        if ($users) {
                                                            foreach ($users as $k => $val) {
                                                                $userArr [$val ["User"] ["id"]] = $val [0] ["User__name"];
                                                            }
                                                        }
                                                        echo $this->Form->select("recommend_user_id", $userArr, array(
                                                            "title" => "Select User",
                                                            "multiple" => "multiple",
                                                            "default" => $selected,
                                                            "id" => "recommend_user_select",
                                                            "class" => "form-control aqua",
                                                            "label" => false,
                                                            "div" => false,
                                                            "style" => "display: none;",
                                                            "data-width" => "100%"
                                                        ));
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xs-12 padding-top margin-top border-top"
                                     id="create_list_box">
                                    <div class="col-xs-4">
                                        <label style="width: 100%;">Lists:</label>

                                        <div href="#" data-dd="#my_saved_people"
                                             class="btn btn-sm dd-trigger">My Saved List</div>
                                        <ul class="dd-menu" id="my_saved_people">
                                            <?php
                                            if (isset($my_search_list) && !empty($my_search_list)) {
                                                ?>
                                                <?php
                                                foreach ($my_search_list as $key => $val) {
                                                    ?>
                                                    <li class=""><i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i> <a href="#" data-id="<?php echo $key; ?>"><?php echo $val; ?></a></li>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            } else {
                                                ?>
                                                <li class=""><a href="#">No
                                                        saved list</a></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>

                                    </div>
                                    <div class="col-xs-3"></div>
                                    <div class="col-xs-5">
                                        <label style="width: 100%;">People:</label>

                                        <div href="#" data-dd="#my_people_list"
                                             class="btn btn-sm dd-trigger" style="width: 95%;">People in
                                            List</div>
                                        <ul class="dd-menu" id="my_people_list">
                                            <li class=""><i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
                                                <i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i> <a
                                                    href="#">quae ab</a></li>
                                            <li class=""><i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
                                                <i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i> <a
                                                    href="#">Eaque ipsa</a></li>
                                            <li class=""><i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
                                                <i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i> <a
                                                    href="#">illo inventore</a></li>
                                        </ul>
                                        <span class="fa fa-spinner fa-pulse" id="my_list_spinner"></span>

                                    </div>
                                </div>

                            </div>

                            <div style="display: block;" class="col-xs-12 search_result_section" id="content_search_result"></div>
                            <div style="display: none;" class="col-xs-12  search_result_section" id="people_search_result"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
