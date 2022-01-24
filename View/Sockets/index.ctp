

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
                                    <div class="col-xs-6">
                                        <input type="text" name="term" class="input-tag-text1 " />
                                        <input type="text" name="term" class="input-tag-text2" />

                                        <a href="#" id="get">Get</a>
                                    </div>
                                </div>
                            </div>
<div class="col-xs-12" style="margin: 20px;" id="iframe_wrapper">

</div>
                        </div><!-- /.box-body -->

                    </div><!-- /.box -->
                </div>
           </div>
        </div>
    </div>
</div>


<script>
$(function(){




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

});

</script>
