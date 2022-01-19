<?php
echo $this->Html->css('projects/nudge_list');
echo $this->Html->script('projects/nudge_list');

// echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>

<script type="text/javascript">
	$(function(){
	    $('#modal_small').on('hidden.bs.modal', function () {
	    	$(this).removeData('bs.modal');
	    	$(this).find('modal-content').html('');
	    });

	    function debounce(func){
            var timer;
            return function(event){
                if(timer) clearTimeout(timer);
                timer = setTimeout(func, 100, event);
            };
        }

        $('html').addClass('no-scroll');
        var interval = setInterval(function() {
            if (document.readyState === 'complete') {
                // $('html').removeClass('modal-open');
                clearInterval(interval);
            }
        }, 1000);

		window.addEventListener("resize", debounce(function(e){
            var $cd = $(".list-wrapper");
            var cmin_height = (($(window).height() - $cd.offset().top) - 20);
            if(cmin_height > 100){
                $cd.animate({'min-height': cmin_height, 'height': cmin_height}, 30);
            }
            else{
                $cd.animate({'min-height': 100}, 30);
            }
        }));


        ;($.resize_wrapper = function(){
            setTimeout(function(){
                var $cd = $(".list-wrapper");
                var cmin_height = (($(window).height() - $cd.offset().top) - 20);
                if(cmin_height > 100){
                    $cd.animate({'min-height': cmin_height, 'height': cmin_height}, 30);
                }
                else{
                    $cd.animate({'min-height': 100}, 30);
                }

            }, 1)
        })();
	})
</script>
<style type="text/css">
	.no-scroll {
	    overflow: hidden;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="row">
	       <section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
	       </section>
		</div>

     	<div class="box-content nudge-page">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header filters" style="">
							<div class="top-cols">
								<div  class="col-xs-12 col-sm-8 col-md-6 col-lg-4 padding-left0 padd0-m-r">
									<div class="col-xs-10 col-sm-9 no-padding">
										<label class="custom-dropdown" style="width: 100%; ">
											<select class="aqua filter_nudge">
												<option value="2" selected="selected">Received Nudges</option>
												<option value="1">Sent Nudges</option>
												<option value="">Sent and Received Nudges</option>
												<option value="3">Archived Nudges</option>
											</select>
										</label>
									</div>
                           <span class="ico-nudge ico-nudge-list tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'type' => 'nudge', 'admin' => false)); ?>"></span>
								</div>

                       	<form action="#" method="post" onsubmit="return false;" class="pull-right search-form" style=" width: 260px; float: left; ">
									<div class="input-group search-group">
										<input maxlength="50" type="text" name="search_string" class="form-control search_string" placeholder="Search Subject and Message">
										<span class="input-group-btn">
											<button type="submit" name="search" id="search_btn" class="btn btn-flat btn-search bg-gray search-btn"><i class="fa fa-search"></i></button>
										</span>
									</div>
								</form>
							</div>
						</div>

						<div class="box-body clearfix nudges-scroll" style="padding: 0px;">
							<div class="nudges-data-container">
								<div class="nudges-data-header" style="position: sticky; top: 0;">
									<div class="col-data col-data-1">
										<div class="col-bg">Sent
											<span class="short-arrow-wrap">
											<span class="short-arrow sort_order date-sort tipText sort-column"  data-sorted="asc" title="Date Sort">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
										  	</span></span>

											<!--<a class="btn btn-xs btn-control date-sort tipText sort-column" title="Date Sort" data-sorted="asc" >AZ</a>-->

										</div>
									</div>

									<div class="col-data col-data-2">
										<div class="col-bg">From
											<span class="short-arrow-wrap">
												<span class="short-arrow sort_order from-sort tipText sort-column" data-sorted="asc" title="Alphabetical Sort">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
										  	</span></span>
												<!--<a class="btn btn-xs btn-control from-sort tipText sort-column" title="Alphabetical Sort" data-sorted="asc" >AZ</a>-->

										</div>
									</div>
									<div class=" col-data col-data-3">
										<div class="col-bg">To
											<span class="short-arrow-wrap">
												<span class="short-arrow sort_order to-sort tipText sort-column" data-sorted="asc" title="Alphabetical Sort">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
										  	</span></span>
												<!--<a class="btn btn-xs btn-control to-sort tipText sort-column" title="Alphabetical Sort" data-sorted="asc" >AZ</a>-->

										</div>
									</div>
									<div class="col-data col-data-4">
										<div class="col-bg">
								            Subject
										</div>
									</div>
									<div class="col-data col-data-5">
										<div class="col-bg">Message

										</div>
									</div>
									<div class="col-data col-data-6">
										<div class="col-bg">Link </div>
										</div>

									<div class="col-data col-data-7">
										<div class="col-bg">Email </div>
									</div>
									<div class="col-data col-data-8">
										<div class="col-bg">Status
											<span class="short-arrow-wrap">
												<span class="short-arrow sort_order status-sort tipText sort-column" title="Alphabetical Sort" data-sorted="asc">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
										  	</span></span>
												<!--<a class="btn btn-xs btn-control status-sort tipText sort-column" title="Alphabetical Sort" data-sorted="asc" >AZ</a>-->

										</div>
									</div>
									<div class="col-data col-data-9 col-action-n">
										<div class="col-bg">Actions </div>
									</div>
								</div>
								<div class="list-wrapper">
									<?php echo $this->element('../Boards/partial/nudge_listing'); ?>
								</div>
							</div>

								<input type="hidden" id="paging_page" value="1" />
								<input type="hidden" id="paging_max_page" value="<?php echo $total_nudges; ?>" />
						</div><!-- /.box-body -->
					</div><!-- /.box -->
     		    </div>
		   </div>
		</div>
    </div>
</div>