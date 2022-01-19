<?php
echo $this->Html->script('plugins/gs/tag.text', array('inline' => true));
?>

<style>
    .tag-container {
        width: 100%;
        margin-top: 10px;
        padding: 5px;
        border: 1px solid #00B3DB;
        cursor: text;
    }
    .tag-container.red {
        border: 1px solid #ff0000;
    }
    .tag-container.green {
        border: 1px solid #00a83b;
    }
    .tag-container.yellow {
        border: 1px solid #ffc000;
    }
    .tag-container.scrollable {
        overflow: auto;
    }
    .tag-container .tagtext, .tag-container .input-tag-text {
        background: none;
        width: 60px;
        min-width: 30px;
        border: 0;
        height: 25px;
        padding: 0;
        margin-bottom: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    .tag-container .tagtext:focus, .tag-container .input-tag-text:focus {
        border: none;
        outline: none;
    }
    .tag-row {
        display: inline-block;
        padding: 2px 5px;
        border: 1px solid #ccc;
        border-radius: 3px;
        min-width: 100px;
        margin: 0 0 2px 2px;
        transition: all 0.6s ease-in-out;
    }
    .tag-text {
        display: inline-block;
        cursor: default;
    }

    .tag-delete {
        float: right;
        cursor: pointer;
        padding: 0 3px;
        margin-left: 5px;
        border-radius: 3px;
    }
    .tag-delete:hover {
        background-color: #c00;
        color: #fff;
    }
    .uploader {
        display: none;
        width: auto;
        height: auto;
        position: absolute;
        background-color: #effcff;
    }
    .text-selector {
        display: none;
        width: 350px;
        position: absolute;
        border: 1px solid #00B3DB;
        background-color: #effcff;
        border-radius: 3px;
        padding: 0;
        transition: all 0.6s ease-in-out;
        max-height: 300px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .text-selector.red {
        border: 1px solid #ff0000;
        background-color: #ffcccc;
    }
    .text-selector.green {
        border: 1px solid #00a83b;
        background-color: #d3ffe3;
    }
    .text-selector.yellow {
        border: 1px solid #ffc000;
        background-color: #fff5d5;
    }
    .text-selector ul {
        background-color: transparent;
        padding: 0;
        margin: 0;
    }
    .list-tag {
        display: block;
        width: 100%;
        padding: 5px 10px;
        color: #333;
        cursor: pointer;
    }
    .list-tag:hover {
        background-color: #00B3DB;
        color: #fff;
    }
    .list-tag.selected {
        background-color: #ccc;
        pointer-events: none;
    }
    .text-selector.red .list-tag:hover {
        background-color: #ff0000;
        color: #fff;
    }
    .text-selector.green .list-tag:hover {
        background-color: #00a83b;
        color: #fff;
    }
    .text-selector.yellow .list-tag:hover {
        background-color: #ffc000;
        color: #fff;
    }
    .close-selector {
        position: absolute;
        right: -12px;
        top: -10px;
        padding: 0px 2px;
        color: #c00;
        font-size: 20px;
        cursor: pointer;
        background: #fff;
        border-radius: 2px;
        border: 1px solid #ccc;
    }

    /* managment box start  */
    .management-box{
        border: 1px solid #98c8ef;
        display: inline-block;
        width: 420px;
        background: #fff;
        border-radius: 5px;
        /* margin-top: 30px; */
    }
    .management-box h5{
        border-bottom: 1px solid #98c8ef;
        padding: 10px 15px;
        margin: 0;

    }
    .management-box-in {
        display: inline-block;
        width: 100%;
        padding: 15px;
    }
    .management-attach{
        display: inline-block;
        width: 100%;
        margin-bottom: 10px;
    }
    .management-attach .btn-file {
        font-size: 14px;
        line-height: 17px;
    }
    .management-action{margin-top: 5px;}

    .pdf-size {
        text-transform: uppercase;
    }
    .pdf-list{
        padding: 0px;
        padding-top: 10px;
        margin-top: 10px;
        max-height: 160px;
        overflow: auto;
        transition: all 0.6s ease-in-out;
    }
    .pdf-list li:first-child {
        border-top: 1px solid #ccc;
        padding-top: 5px;
    }
    .pdf-list li {
        list-style-type: none;
        position: relative;
        margin: 3px 0px;
        padding-right: 30px;
        padding-left: 3px;
        display: inline-block;
        width: 100%;
    }
    .pdf-list li .btn{
        position: absolute;
        right: 0px;
    }
    .not-working {
        pointer-events: none;
        opacity: 0.6;
    }
    /* managment box end */

    .loading-bar {
        height: 2px;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .loading-bar:before {
        display: block;
        position: absolute;
        content: "";
        left: -200px;
        width: 200px;
        height: 2px;
        background-color: #2980b9;
        animation: running_bar 2s linear infinite;
    }


    @keyframes running_bar {
        from {
            left: -200px;
            width: 30%;
        }

        50% {
            width: 30%;
        }

        70% {
            width: 70%;
        }

        80% {
            left: 50%;
        }

        95% {
            left: 120%;
        }

        to {
            left: 100%;
        }
    }
</style>


<div class="row manage-costs">
    <div class="col-xs-12">
        <div class="row">
           <section class="content-header clearfix">
                <h1 class="pull-left">
                    <?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
           </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header border-bottom-two filters" style="">
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                        </div>

                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">
                            <a href="#" class="btn btn-warning btn-lg save-mongo">Save to MongoDB</a>
                            <span id="result">data</span>
                            <input type="text" name="check" id="check">
                            <div class="col-xs-12" style="margin: 20px;">
                                <div class="form-group">
                                    <!-- <div class="col-xs-6">
                                        <div class="tag-container">
                                            <input type="text" name="term" class="tagtext" />
                                        </div>
                                    </div> -->
                                    <div class="col-xs-6">
                                        <input type="text" name="term" class="input-tag-text1 " />
                                        <input type="text" name="term" class="input-tag-text2" />

                                        <a href="#" id="get">Get</a>
                                    </div>
                                </div>
                            </div>
<div class="col-xs-12" style="margin: 20px;">
    <form method="post" action="http://192.168.8.29:3001/api/login" class="form-signin" target="iframe_chat_7" id="form_chat_7" style="display: none" >
        <input type="text" name="email" id="email_chat_7" value="piyush@mailinator.com" />
        <input type="text" name="projectId" value="1" id="project_id_chat_7" />
        <input type="hidden" name="NoAuth" value="true" />
        <input type="submit" name="submit_chat_7" value="" id="submit_chat_7" />
    </form>
    <iframe src="http://192.168.8.29:3001/api/login" target='_parent' Access-Control-Allow-Origin="*" allow="microphone; camera" style="width: 700px; height: 400px; border: 1px solid #ccc;" name="iframe_chat_7" id="iframe_chat_7"></iframe>
</div>
<ul class="pull-right">
    <li class="dropdown" id="accountmenu">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Account Settings<b class="caret"></b></a>
        <ul class="dropdown-menu">
            <li><a href="#">Login</a></li>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="#">More options</a>
                <ul class="dropdown-menu">
                    <li><a tabindex="-1" href="#">Second level</a></li>
                    <li class="dropdown-submenu">
                        <a href="#">More..</a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu"><a href="#">One More..</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">4rd level</a></li>
                                    <li><a href="#">4rd level</a></li>
                                </ul>
                            </li>
                            <li><a href="#">3rd level</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Second level</a></li>
                    <li><a href="#">Second level</a></li>
                </ul>
            </li>
            <li><a href="#">Register</a></li>
            <li class="divider"></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </li>
</ul>
                        </div><!-- /.box-body -->

                    </div><!-- /.box -->
                </div>
           </div>
        </div>
    </div>
</div>


<script>
$(function(){


    // window.document.domain = 'http://192.168.8.29:3001';
    // $('.form-signin').submit();
    // document.getElementById('iframe_chat_7').contentWindow.location.reload(true);
		$.ajax({
        url: "http://192.168.8.29:3001/login",
        data: {
            email: 'piyush@mailinator.com',
            project: 1
        } ,
        headers: {
            "Access-Control-Allow-Origin": "*",
            'NoAuth': true,
        },
        crossDomain: true,
        beforeSend: function(request) {
            // request.setRequestHeader("Access-Control-Allow-Origin", "*");
          },
        // dataType: 'json',
        success: function(res){
            console.log(res);
            // $('#iframe_chat_7').attr('src', "http://192.168.8.29:3001/chat");
            document.getElementById('iframe_chat_7').src = "http://192.168.8.29:3001/chat";
            // document.getElementById('iframe_chat_7').contentWindow.location.reload(true);
        },
        type: 'post',
        contentType : "application/json"
    });
    /* document.getElementById('iframe_chat_7').onload = function() {
        // just do anything
        console.log(document.getElementById('iframe_chat_7').contentWindow);
    }; */





    $('#check').on('keyup', function(event){
        console.log(event.which)
    })
    $('.save-mongo').on('click', function(event) {
        event.preventDefault();
        $.ajax({
            url: $js_config.base_url + 'sockets/mongo_operations',
            type: 'post',
            dataType: 'json',
            data: { project_id: 1, title: 'Project name' },
            success: function(response){
                var html = ''
                $.each(response, function (index, item) {
                    //console.log(item);
                    html += "<ul>" ;
                    if(typeof item == 'object'){
                        $.each(item , function (index1, item1) {
                            html += "<ul><li>" + item1 + "</li></ul>";
                        });
                    }
                    else {
                        html += "<li>" + item + "</li>";
                    }
                    html+="</ul>";
                });
                $("#result").html(html);
                // $('#result').html($.parseJSON(response))
            }
        })

    });

    $('.input-tag-text1').tagText({
        autoHeight: false,
        duration: 100,
        theme: 'green',
        remote: [{
            id: 1,
            text: 'Abc Test data 1'
        },
        {
            id: 2,
            text: 'Pqr Test data 2'
        },
        {
            id: 3,
            text: 'Xyz Test data 3'
        },
        {
            id: 4,
            text: 'Ghi Test data 4'
        }],
        preselected: [{
            id: 2,
            text: 'Pqr Test data 2'
        },
        {
            id: 4,
            text: 'Ghi Test data 4'
        }],
        showSelected: true
    });
    $('.input-tag-text2').tagText({
        autoHeight: true,
        maxHeight: 60, // digits/string
        duration: 100,
        theme: 'yellow',
        remote: $js_config.base_url + 'projects/get_skill_object',
        preselected: [{
            id: 15,
            text: 'Aerospace Industries'
        },{
            id: 32,
            text: 'Applied Mathematics'
        }],
        showSelected: true,
        onSelect: function(values){
            console.log('return values: ', values);
        }
    });
    $('#get').on('click', function(event) {
        event.preventDefault();
        $('.input-tag-text1').tagText('get_tags');
    });
});

/*
$(function(){

        $(document).on('click', function(e) {

            $('.tag-row').each(function() {
                var $this = $(this);
                var $uploader = $('.uploader');
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $uploader.has(e.target).length === 0) {
                    if($uploader) {
                        $uploader.slideUp(300, function(){
                            $(this).remove();
                            $('.tag-row').removeClass('expended');
                            $.setInputWidth();
                        });
                    }
                }
            });
            $.setInputWidth();
        });

        $('body').delegate('.tag-container', 'click', function(event) {
            event.preventDefault();
            if($(event.target).is($(this))) {
                $(this).find('.tagtext').focus();
            }
        })

        $('body').delegate('.close-selector', 'click', function(event) {
            event.preventDefault();
            $('.selector').slideUp(100, function(){
                $(this).remove();
                $('.tagtext').val('')
            });
        })

        $('body').delegate('.tag-container .tagtext', 'focus', function(event) {
            event.preventDefault();
            if($(this).val().length >= 1){
                if($('.selector').length > 0 && !$('.selector').is(':visible')) {
                    $('.selector').slideDown(100);
                }
            }
            if($(this).val().length >= 1){
                $.tag_selector($(this).get(0));
            }
        })
        $('body').delegate('.tag-container .tagtext', 'keyup', function(event) {
            event.preventDefault();
            var $this = $(this);
            if($(this).val().length < 1){
                if($('.selector').length > 0) {
                    $('.selector').slideUp(100, function(){
                        $(this).remove();
                    });
                }
                return;
            }
            $(this).addClass('selector_visible');
            setTimeout(function(){
                $.tag_selector($this.get(0));
                $this.data('selector', $('.selector'));
                $('.selector').data('input', $this);
            }, 1000)
        })

        $(document).on('click', function(e) {

            $('.tag-container .tagtext').each(function() {
                var $this = $(this);
                var $selector = $('.selector');
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $selector.has(e.target).length === 0) {
                    if($selector) {
                        $selector.slideUp(300, function(){
                            $(this).remove();
                            $this.val('')
                        });
                    }
                }
            });
        });


        $.tag_selector = function(t){
            var $this = $(t),
                $parent = $this.parents('.tag-row:first'),
                coordinates = $this.offset();
            var css = {
                left: coordinates.left - ($this.width() / 2) + ($this.outerWidth() / 2),
                top: coordinates.top + $this.outerHeight() + 10,
                right: 'auto'
            }

            if($('.selector').length <= 0) {
                var $selector = $('<div />', {
                    'class': 'selector'
                })
                .css(css)
                $selector.appendTo($('body'));
                $.addSkills($this.val());
            }
            else{
                $.addSkills($this.val());
            }
        }

        $.addSkills = function(val){
            var $div = $('.selector');
            var $ul = $('<ul />', {
                    'class': 'loading-bar'
                });
            $ul.appendTo($div);
            $.ajax({
                url: $js_config.base_url + 'projects/get_skills?term='+val,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    if(response.success) {
                        var data = response.content;
                        $div.find('ul').remove();
                        var $ul = $('<ul />', {
                            'class': ''
                        });
                        $.each(data, function(index, text) {
                            var $li = $('<li />', {
                                'class': 'list-tag'
                            })
                            .text(text)
                            .data('index', index);
                            $li.appendTo($ul)
                        });
                        $ul.appendTo($div);
                        $div.slideDown(500);
                    }
                    else{
                        $div.find('ul').remove();
                    }
                }
            })

        }

        $.adjust_selector = function(){
            var $this = $('.tagtext'),
                coordinates = $this.offset();
            var css = {
                left: coordinates.left - ($this.width() / 2) + ($this.outerWidth() / 2),
                top: coordinates.top + $this.outerHeight() + 10,
                right: 'auto'
            }
            $('.selector').css(css);
        }

        ;($.setInputWidth = function(){
            var $container = $('.tag-container'),
                container_width = $container.innerWidth();
            var wid = 0;
            $('.tag-row').each(function(index, el) {
                wid += $(this).width();
            });
            var input_width = container_width - wid - 130;
            $('.tag-container input.tagtext').css('width', input_width + 'px')
        })();

        $("body").on("click", ".list-tag", function(event) {
            var tagHtml = '';
            $that = $(this);
            var tagId = $that.data('index');
            var tagName = $that.text();
            var checktagid = 'entered';

            var existingindex = $(".tag-row span:first-child");
            existingindex.each(function(){
                if( tagId == $(this).data('tagid') ){
                    checktagid = 'outfromhere';
                }
            })

            if( checktagid == 'entered' ){
                tagHtml = '<div class="tag-row"><span data-tagid="'+tagId+'" class="tag-text">'+tagName+'</span><span class="tag-delete">✖</span></div>';
                $( tagHtml ).insertBefore('.tagtext');
                setTimeout(function(){
                    $.adjust_selector();
                    $.setInputWidth();
                }, 300)
            }
        })

        $('body').delegate('.tag-delete', 'click', function(event) {
            event.preventDefault();
            var $parentRow = $(this).parents('.tag-row:first');
            $parentRow.remove();

            $.setInputWidth();
            $('.uploader').remove();
        })

})*/
</script>
