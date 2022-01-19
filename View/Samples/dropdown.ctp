
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    Samples
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;">Create & Check your sample pages here</span>
                    </p>
                </h1>
            </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xs">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                        </div>
                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">
                            <div class="col-sm-3" style="margin-left: 600px">
                                <a class="btn btn-default btn-sm my-menus" href="#"> click </a>
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
<script type="text/javascript">
    $(function(){
        $.getCoordinates = function(el, abs_element, direction) {
            var self = this;
            var coordinates = el.offset();
            var $elem = el;
            var $abs_element = abs_element;
            var adjustment = 0;
            switch (direction) {
                case 'top':
                    return {
                        left: coordinates.left - ($abs_element.width() / 2) + ($elem.outerWidth() / 2),
                        top: coordinates.top - $abs_element.outerHeight() - adjustment,
                        right: 'auto'
                    };
                case 'left':
                    return {
                        left: coordinates.left - ($abs_element.width()) + ($elem.outerWidth() / 2) - 20,
                        top: coordinates.top - ($abs_element.height() / 2) + ($elem.outerHeight() / 2) - adjustment,
                        right: 'auto'
                    };
                case 'right':
                    return {
                        left: coordinates.left + ($elem.outerWidth()) + adjustment,
                        top: coordinates.top ,// - ($abs_element.height() / 2) + ($elem.outerHeight() / 4),
                        right: 'auto'
                    };
                case 'bottom':
                    return {
                        left: coordinates.left - ($abs_element.width() / 2) + ($elem.outerWidth() / 2),
                        top: coordinates.top + $elem.outerHeight() + adjustment,
                        right: 'auto'
                    };
            }
        }

        $('.opt-list a').on('click', function(event) {
            event.preventDefault();
        });


        $('.my-menus').on('click', function(event) {
            event.preventDefault();
            var $ul = $('.my-dropdown');
            var coordinates = $.getCoordinates($(this), $ul, 'right');
            $ul.css(coordinates).css('position', 'fixed').show()
            $ul.data('btn', $(this));
            $(this).data('list', $ul);
        });

        $('.opt-list .menuarrow').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var $parent_ul = $(this).parents('ul:first');
            var $li = $(this).parents('li:first');
            var $ul = $li.find('ul:first');
            var $all_ul = $parent_ul.find('ul');
            $all_ul.hide()//.removeClass('open');
            $('.opt-list').not($li).removeClass('open')
            if($li.hasClass('open')){
                $li.removeClass('open');
                $ul.hide();
            }
            else{
                $li.addClass('open');
                var ul_position = $parent_ul.offset(),
                    li_position = $li.offset(),
                    width = $parent_ul.outerWidth(),
                    dd_left = (ul_position.left + width),
                    dd_top = (li_position.top) - 1,
                    pos = { // RIGHT TO ELEMENT
                        left: dd_left - 1,
                        top: ul_position.top - $(window).scrollTop(),
                        position: 'fixed'
                    }
                $ul.css(pos);
                if(pos.top + $ul.height() > ($(window).height() - $(window).scrollTop())){
                    pos.top = ($(window).height() - $ul.outerHeight());
                    $ul.css(pos);
                }
                if((pos.left + $ul.width()) > $('body').innerWidth()){
                    pos.left = (ul_position.left - width);
                    $ul.css(pos);
                }
                /*$ul.show('slide', {
                    direction: 'right'
                }, 300);*/
                $ul.show();
                $ul.data('li', $li);
                $li.data('list', $ul);
            }
        })

        $('body').on('click', function(e) {
            $('.my-menus').each(function() {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.my-dropdown').has(e.target).length === 0) {
                    var list = $(this).data('list');
                    if (list) {
                        $(this).removeData('list');
                        list.removeData('btn');
                        list.fadeOut(300, function() {
                            $(this).removeClass('open');
                        })
                    }
                }
            });
            $('.opt-list').each(function() {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.sub-menu').has(e.target).length === 0) {
                    var list = $(this).data('list');
                    var $this = $(this);
                    if (list) {
                        $(this).removeData('list');
                        list.removeData('li');
                        list.hide().removeClass('open');
                    }
                }
            });
        });
        // $(".sub-menu").slimScroll({height: 230, alwaysVisible: true});
    })
</script>

<?php
$projectArray = $this->ViewModel->getMoveCopyArea(93);

$project_list = array();
$wsp_list = array();
$area_list = array();
if( isset($projectArray) && !empty($projectArray) ){
    foreach($projectArray as $list_data){

        $project_list[$list_data['projects']['id']]['project_title'] = $list_data['projects']['project_title'];


        $wsp_list[$list_data['projects']['id']][$list_data['workspaces']['workspace_id']]['workspace_id']=$list_data['workspaces']['workspace_id'];
        $wsp_list[$list_data['projects']['id']][$list_data['workspaces']['workspace_id']]['wsp_title']=$list_data['workspaces']['wsp_title'];

        $area_list[$list_data['workspaces']['workspace_id']][$list_data['areas']['area_id']]['id'] = $list_data['areas']['area_id'];
        $area_list[$list_data['workspaces']['workspace_id']][$list_data['areas']['area_id']]['area_title'] = $list_data['areas']['area_title'];

    }
}
 ?>
<div id="copy_to_list">
    <?php

    if( isset( $project_list ) && !empty( $project_list ) ) {
    ?>
    <ul class="my-dropdown opt-main">
        <li class="dd-header">Projects</li>
        <?php foreach( $project_list as $key => $data ) { ?>

        <li class="opt-list" data-id="<?php echo $key; ?>">
            <a tabindex="-1" href="#">
                <i class="fa fa-briefcase"></i>
                <span class="menutext"><?php echo _substr_text( $data['project_title'], 20, true ); ?></span>
                <span class="menuarrow"></span>
            </a>
            <?php if( isset( $wsp_list[$key] ) && !empty( $wsp_list[$key] ) ) { ?>
            <ul class="sub-menu">

                <li class="dd-header">Workspaces</li>
                <?php foreach( $wsp_list[$key] as $wk => $wv ) {   ?>
                <li class="opt-list" data-id="<?php echo $wv['workspace_id']; ?>">
                    <a href="#">
                        <i class="fa fa-th"></i>
                        <span class="menutext"><?php echo _substr_text( $wv['wsp_title'], 20, true ); ?></span>
                        <span class="menuarrow"></span>
                    </a>
                    <?php if( isset( $area_list[$wk] ) && !empty( $area_list[$wk] ) ) { ?>
                    <ul class="sub-menu">
                        <li class="dd-header">Areas</li>
                        <?php foreach( $area_list[$wk] as $ak => $av ) { ?>
                        <li class="opt-list">
                            <a href="#" data-id="<?php echo $ak ?>" data-list-id="<?php echo $key . '_' . $wk . '_' . $ak ?>" name='copy_to' class="target">
                                <i class="fa fa-list-alt"></i>
                                <span class="menutext"><?php echo _substr_text( $av['area_title'], 20, true ); ?></span>
                            </a>
                        </li>
                        <?php } // foreach $wv['area'] ?>
                    </ul>
                    <?php }  // if $wv['area'] ?>
                </li>

                <?php } // foreach $data['workspace'] ?>
            </ul>
            <?php } // if $data['workspace'] ?>
        </li>
        <?php } // foreach context_list ?>
    </ul>
    <?php } // if context_list ?>
</div>

<ul class="my-dropdown11 opt-main11" >
    <li class="opt-list">
        <a href="">
            <span class="menutext">abcd</span>
            <span class="menuarrow"></span>
        </a>
        <ul class="sub-menu" id="f1">
            <li class="dd-header">Projects</li>
            <li class="opt-list">
                <a href="">
                    <span class="menutext">sub menu 1</span>
                    <span class="menuarrow"></span>
                </a>
                <ul class="sub-menu" id="f2">
                    <li class="dd-header">Workspaces</li>
                    <li class="opt-list">
                        <a href="">
                            <span class="menutext">sub menu 2.1</span>
                            <span class="menuarrow"></span>
                        </a>
                        <ul class="sub-menu" id="f3">
                            <li class="dd-header">Areas</li>
                            <li class="opt-list">
                                <a href="">
                                    <span class="menutext">sub menu 3.1</span>
                                </a>
                            </li>
                            <li class="opt-list">
                                <a href="">
                                    <span class="menutext">sub menu 3.2</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="opt-list">
                        <a href="">
                            <span class="menutext">sub menu 2.2</span>
                            <!-- <span class="menuarrow"></span> -->
                        </a>
                    </li>
                    <li class="opt-list">
                        <a href="">
                            <span class="menutext">sub menu 2.3</span>
                            <!-- <span class="menuarrow"></span> -->
                        </a>
                    </li>
                    <li class="opt-list">
                        <a href="">
                            <span class="menutext">sub menu 2.4</span>
                            <!-- <span class="menuarrow"></span> -->
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li class="opt-list">
        <a href="">
            <span class="menutext">3333333333333</span>
            <span class="menuarrow"></span>
        </a>
    </li>
    <li class="opt-list">
        <a href="">
            <span class="menutext">vbnbvnbvnbv</span>
            <span class="menuarrow"></span>
        </a>
    </li>
    <li class="opt-list">
        <a href="">
            <span class="menutext">6787876</span>
            <span class="menuarrow"></span>
        </a>
    </li>
</ul>

<style type="text/css">
    .my-dropdown, .sub-menu {
        z-index: 1000;
        float: left;
        min-width: 242px;
        max-width: 242px;
        padding: 0;
        margin: 0;
        text-align: left;
        list-style: none;
        background-color: #fff;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        border-radius: 0;
        overflow: auto;
        border: 1px solid #909090;
        display: none;
        transition: all 0.01s;
        box-shadow: none;
        max-height: 230px;
        overflow: auto;
    }
    .my-dropdown li, .sub-menu li {
        border-bottom: 1px solid #dedede;
        font-size: 14px;
        position: relative;
        cursor: pointer;
    }
    .my-dropdown li a {
        display: flex;
        width: 100%;
        max-width: 240px;
        font-weight: 400;
        line-height: 1.42857143;
        color: #777;
        padding: 6px 13px 6px 6px;
        align-items: center;
    }
    .my-dropdown li a:hover, .my-dropdown li a:active {
        background-color: #ebf1fb;
        text-decoration: none;
        outline: none;
    }
    .my-dropdown .menutext {
        text-overflow: ellipsis;
        overflow: hidden;
        flex-grow: 1;
        padding-right: 10px;
        padding-left: 5px;
        white-space: nowrap;
    }
    .my-dropdown .menuarrow {
        background-repeat: no-repeat;
        background-position: center center;
        width: 18px;
        min-width: 18px;
        height: 18px;
        display: inline-block;
        background-image: url(../images/icons/RightChevronBlack18x18.png);
    }
    .my-dropdown li a:hover .menuarrow {
        background-color: #fff;
        border-radius: 50%;
    }
    .my-dropdown li:last-child, .sub-menu li:last-child {
        border-bottom: none;
    }
    li.dd-header {
        padding: 5px 20px;
        background-color: #585858;
        color: #fff;
    }
</style>