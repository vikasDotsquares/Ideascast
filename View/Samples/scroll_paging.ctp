<style type="text/css">
    .paging-wrapper {
        max-height: 100px;
        overflow: auto;
    }
    #end_of_page {
        display: none;
    }

</style>


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
                            <?php
                                $project = 2;
                                $all_task_count = $this->ViewModel->getUserTasksProjectCount($project);
                                $all_tasks = $this->ViewModel->getUserTasksProjectPaging($project);
                            ?>
                            <div class="col-sm-6 paging-wrapper">
                                <?php
                                    if( isset($all_tasks) && !empty($all_tasks) ) {
                                        foreach($all_tasks as $key => $row) {
                                            $user_permissions = $row['user_permissions'];
                                            e($user_permissions['e_id']);
                                        }
                                    }
                                 ?>
                                 <div id="end_of_page" class="center">
                                    <hr/>
                                    <span>You've reached the end of the feed.</span>
                                </div>
                            </div>
                            <input type="hidden" id="page" value="1" />
                            <input type="hidden" id="max_page" value="<?php echo $all_task_count ?>" />
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
    var outerPane = $('.paging-wrapper'),
        didScroll = false,
        project = '<?php echo $project; ?>';

    outerPane.scroll(function() { //watches scroll of the window
        didScroll = true;
    });

    //Sets an interval so your window.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
    setInterval(function() {
        if (didScroll){
           didScroll = false;
           // if(($(document).height()-$(window).height()) - $(window).scrollTop() < 10){
            if(outerPane.scrollTop() + outerPane.innerHeight() >= outerPane[0].scrollHeight)
            {
                pageCountUpdate();
            }
       }
    }, 250);

    //This function runs when user scrolls. It will call the new posts if the max_page isn't met and will fade in/fade out the end of page message
    function pageCountUpdate(){
        var page = parseInt($('#page').val());
        var max_page = parseInt($('#max_page').val());

        if(page < max_page){
            $('#page').val(page+1);
            getPosts(page);
            $('#end_of_page').hide();
        } else {
            $('#end_of_page').fadeIn();
        }
    }


    //Ajax call to get your new posts
    function getPosts(page){
        $('#loading').remove();
        $.ajax({
            type: "POST",
            url: $js_config.base_url + "samples/get_page", // whatever your URL is
            data: { page: page, project: project },
            dataType: 'JSON',
            beforeSend: function(){ //This is your loading message ADD AN ID
                outerPane.append('<span id="loading" class="fa fa-spinner fa-pulse" ></span>');
            },
            complete: function(){ //remove the loading message
                $('#loading').remove();
            },
            success: function(html) { // success! YAY!! Add HTML to content container
                outerPane.append(html);
            }
         });

    } //end of getPosts function
</script>