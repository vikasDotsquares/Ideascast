<?php echo $this->Session->flash(); ?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">Vote Requests
                <p class="text-muted date-time">
                    <span>Requests received for your Vote</span>
                </p>
                </h1>
            </section>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-body">
                            <div id="workspace">
                            	<div class="row">
                                	<div class="col-sm-12">
										<div class="fliter" style="padding :15px; margin: 15px 0 0;   border-top-left-radius: 3px;    background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;"  >
										<?php
											if(isset($this->params->query['status']) && !empty($this->params->query['status'])){
												$status = $this->params->query['status'];
											}
											if(isset($this->params->query['search']) && !empty($this->params->query['search'])){
												$search = $this->params->query['search'];
											}
										?>
											<form name="search_filter" >
                                            <div class="row">
												<div class="col-sm-6 col-md-7"><div class="status_filter">

													<select name="status" class="form-control" id="status_filter">
														<option <?php if(isset($status) && empty($status)) echo 'selected'; ?> value="0"> - All- </option>
														<option <?php if(isset($status) && ($status == 'P')) echo 'selected'; ?> value="P"> Open </option>
														<option <?php if(isset($status) && ($status == 'E')) echo 'selected'; ?> value="E"> Expired </option>
														<option <?php if(isset($status) && ($status == 'R')) echo 'selected'; ?> value="R"> Declined </option>
														<option <?php if(isset($status) && ($status == 'C')) echo 'selected'; ?> value="C"> Completed </option>
														<option <?php if(isset($status) && ($status == 'N')) echo 'selected'; ?> value="N"> Not Started </option>
													</select>
												 </div></div>
                                                 <div class="col-sm-6 col-md-5">
												<div class="block-inline block3">
												 <div class="title_filter  ">
													 <input value="<?php if(isset($search) && !empty($search)) echo $search; ?>" id="search"  type="text" class="form-control" name="search" >
													 <button class="btn btn-success bg-gray"><i class="fa fa-search"></i></button>
													 <?php /* if((isset($status) && !empty($status)) || (isset($search) && !empty($search))){ ?>
													 <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo SITEURL; ?>entities/vote_request'">Clear Filter</button>
													 <?php } */ ?>
												</div></div>
												</div>
                                                </div>
											</form>
										</div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button data-target="#element_tabs" data-toggle="collapse" type="button" class="btn cd-toggle cd-tabs-button"> <span class="fa fa-bars"></span> </button>
                                        <div class="cd-tabs is-ended vote-requests-table">
                                            <ul class="cd-tabs-content clearfix " style="height: auto;">
                                                <li data-content="votes" style="" class="selected">
                                                    <div data-limit="5" data-model="vote" id="vote_table" class="table_wrapper clearfix">
                                                        <div class="table_head">
                                                            <div class="row">
                                                                <div class="col-sm-4 resp">
                                                                    <h5> Title</h5>
                                                                </div>
                                                                <!--<div class="col-sm-2 resp">
                                                                    <h5> Status</h5>
                                                                </div>-->
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Start</h5>
                                                                </div>
                                                                <div class="col-sm-2 resp">
                                                                    <h5> End</h5>
                                                                </div>
                                                                <div class="col-sm-2 resp">
                                                                    <h5> Vote </h5>
                                                                </div>
                                                                <div class="col-sm-2 text-center resp">
                                                                    <h5> Action</h5>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="table-rows data_catcher">
                                                            <?php
                                                            if (isset($votes) && !empty($votes)) {
                                                                foreach ($votes as $vote) {
                                                                   // pr($vote);
																 	$disabledSignOff = '';
																	if(isset($vote['Vote']['is_completed']) && !empty($vote['Vote']['is_completed'])){
																		$disabledSignOff = 'disabled';
																	}
																	?>
                                                                    <div class="row">
                                                                        <div class="col-sm-4 resp vtitle"> <?php if (isset($vote['Vote']['title'])) echo $vote['Vote']['title']; ?></div>
                                                                        <!--<div class="col-sm-2 resp"> <?php if (isset($vote['Vote']['VoteResult']['vote_question_option_id']) && !empty($vote['Vote']['VoteResult']['vote_question_option_id'])){ echo 'Completed'; }else if(isset($vote['Vote']['VoteResult']['vote_question_option_id']) && empty($vote['Vote']['VoteResult']['vote_question_option_id'])){ echo 'Rejected'; }else if((strtotime($vote['Vote']['start_date']) <= time() && $vote['Vote']['end_date'] >= date('Y-m-d'))){ echo 'Pending'; }else{ echo 'Expired';} ?></div>-->
                                                                        <div class="col-sm-2 resp"><?php
																		if (isset($vote['Vote']['start_date'])){
																			//echo date('d M, Y', strtotime($vote['Vote']['start_date']));
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($vote['Vote']['start_date'])),$format = 'd M, Y');
																		}
																		?></div>


																		<div class="col-sm-2 resp"> <?php if(isset($disabledSignOff) && !empty($disabledSignOff) && $vote['Vote']['end_date'] > date('Y-m-d 00:00:00')){
																			//echo date('d M, Y', $vote['Vote']['modified']);
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$vote['Vote']['modified']),$format = 'd M, Y');
																		}elseif(isset($vote['Vote']['end_date'])){
																			//echo date('d M, Y', strtotime($vote['Vote']['end_date']));
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($vote['Vote']['end_date'])),$format = 'd M, Y');
																		} ?></div>

																		<div class="col-sm-2 resp">
																			<?php
																			//pr($vote['VoteUser']['vote_status']);
																			//echo $vote['Vote']['start_date'].'<br>';
																			if(isset($vote['VoteUser']['vote_status']) && !empty($vote['VoteUser']['vote_status']) && $vote['VoteUser']['vote_status'] == '2'){ echo 'Declined'; }else if(isset($vote['VoteUser']['vote_status']) && !empty($vote['VoteUser']['vote_status']) && $vote['VoteUser']['vote_status'] == '1'){
																			//echo date('d M,Y H:i',$vote['Vote']['VoteResult']['created']);
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$vote['Vote']['VoteResult']['created']),$format = 'd M, Y H:i');

																			}else if(isset($disabledSignOff) && !empty($disabledSignOff)){ echo 'Closed'; }else if(isset($vote['VoteUser']['vote_status']) && empty($vote['VoteUser']['vote_status']) && $vote['Vote']['start_date'] > date('Y-m-d 00:00:00')){ echo 'Not Started'; }else if(isset($vote['VoteUser']['vote_status']) && empty($vote['VoteUser']['vote_status']) && $vote['Vote']['end_date'] >= date('Y-m-d 00:00:00')){ echo 'Open'; } else{ echo 'Expired';} ?>
																		</div>
                                                                        <div class="col-sm-2 text-center resp">
                                                                            <div class="btn-group">

																				<?php if(isset($vote['VoteUser']['vote_status']) && empty($vote['VoteUser']['vote_status']) && $vote['Vote']['end_date'] >= date('Y-m-d 00:00:00')){ ?>

																				<a title="" data-id="<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>"  href="<?php echo SITEURL; ?>entities/voting/<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>" class="btn btn-sm   view_mindmap tipText" data-original-title="Vote">
                                                                                    <i class="voteblack18"></i>
                                                                                </a>
																				<?php }else{ ?>

																				<a title="" data-id="<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>"  href="<?php echo SITEURL; ?>entities/voting/<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>" class="btn btn-sm   view_mindmap tipText" data-original-title="View">
                                                                                    <i class="visibleblack"></i>
                                                                                </a>

																				<?php }  ?>


																				<?php

																				$disabled = '';
																				if($vote['Vote']['start_date'] <= date('Y-m-d 00:00:00') && $vote['Vote']['end_date'] >= date('Y-m-d 00:00:00')){

																					if(isset($vote['Vote']['VoteResult']['vote_change_datetime']) && !empty($vote['Vote']['VoteResult']['vote_change_datetime']) && $vote['Vote']['VoteResult']['vote_change_datetime'] > time() && empty($disabledSignOff)){
																				?>
																					<a title="" data-action="update" data-id="<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>" href="<?php echo SITEURL; ?>/entities/re_voting/<?php if (isset($vote['Vote']['id'])) echo $vote['Vote']['id']; ?>" class="btn btn-sm   update_vote tipText re_vote"  data-original-title="Re-Vote">
																					<i class="voteblack18"></i>
																					</a>
																				<?php }else{
																				?>
																					<?php /* ?><a title="" data-action="update"  class="btn btn-sm     re_vote re-vote-disabled"  data-original-title="Re-Vote  ">
																					<i class="voteblack18"></i>
																					</a> <?php */ ?>
																				<?php }
																				}else{
																					?>
																					<?php /* ?><a title="" data-action="update"   class="btn btn-sm     re_vote re-vote-disabled"  data-original-title="Re-Vote ">
																					<i class="voteblack18"></i>
																					</a> <?php */ ?>
																				<?php }
																				$viewURL = SITEURL."entities/view_result/".$vote['Vote']['id'];
																				?>


																					<a   rel="<?php echo $vote['Vote']['id']; ?>"   class="viewuser btn btn-sm   tipText"  data-target="#Recordview"   title="Results" data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="resultsblack"></i></a>


                                                                            </div>
                                                                        </div>
																	<div class="prt_user_row">
                                                                    <div class="col-sm-12 col-sm-12 resp">
																	<div id="View-result-form<?php echo $vote['Vote']['id']; ?>" class="View-result-form" style="display:none;">

																	</div>
																	</div>
																	</div>


                                                                    </div>

                                                                    <?php
                                                                }
                                                            }else{ ?>
																<div class="row">
																	<div class="col-lg-12 text-center" style="padding: 20px 0px;">
																		No Requests.
																	</div>
																</div>
															<?php
															}
                                                            ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- End conversations Tab	-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" >

	$(function(){
		$('.re-vote-disabled').tooltip({
		  template: '<div class="tooltip custom_class"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
		  container: 'body'
		})


		// RESIZE MAIN FRAME
	    $('html').addClass('no-scroll');
	    ($.adjust_resize = function(){
	        $(".cd-tabs-content.clearfix").animate({
	            minHeight: (($(window).height() - $(".cd-tabs-content.clearfix").offset().top) ) - 27,
	            maxHeight: (($(window).height() - $(".cd-tabs-content.clearfix").offset().top) ) - 27
	        }, 1)
	    })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
	    $(".sidebar-toggle").on('click', function() {
	        $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })
	})


    $("body").delegate(".decline_vote", "click", function (event) {
    	vote_id = $(this).attr('data-id');
    $('#confirm-boxs').find('#modal_body').text("Are you sure, you would like to decline this Vote request?");

         $('#confirm-boxs').modal({keyboard: true})
                .on('click', '#s_off_yes', function () {
                    $.ajax({
                        type: 'POST',
                        data: 'vote_id='+vote_id,
                        url: '<?php echo Router::url("/").'entities/decline_vote'; ?>',
                        global: false,
                        dataType: 'JSON',
                        beforeSend: function () {

                        },
                        complete: function () {
                            setTimeout(function () {
                                $('#confirm-boxs').modal('hide');
                            }, 300)
                        },
                        success: function (response, statusText, jxhr) {
                            if(response == 'success')
                            $('#confirm-boxs').modal('hide');
                        }
                    });
        });
    });

	$(document).ready(function(){
		//$('#status_filter').val('');
	});

	$(document).on('change','#status_filter',function(){
		window.location.href="<?php echo SITEURL; ?>entities/vote_request?status="+$(this).val()+"&search="+$('#search').val();
	});

	    // Open View Form
/*   $(document).on('click','.viewuser', function (e) {
  var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){
			if($.trim(response) != 'success'){
				$('#Recordview').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		}
	});
})  */

$("body").delegate(".viewuser", "click", function (event) {
	vote_id = $(this).attr('rel');


	event.preventDefault()
		var $that = $(this),
		$icon = $that.find("i"),
		$row = $that.parents(".row:first");
		if ($row.hasClass('bg-warning')) {
            $row.find('.View-result-form').slideUp('slow');
            $row.removeClass('bg-warning')
			//console.log('dd');
			return;
        }

		$('.View-result-form').each(function () {
			   $(this).slideUp('slow');
			   $rowdata = $(this).parents(".row:first");
			   if ($rowdata.hasClass('bg-warning')) {
					$rowdata.removeClass('bg-warning');
				}

        });

        if ($row.hasClass('bg-warning')) {
            $row.removeClass('bg-warning')
            $row.find('.vote-result-form').slideUp('slow')
        } else {

            $row.addClass('bg-warning')
            $row.find('.vote-result-form').slideDown('slow')
        }




	//if($.trim($('#View-result-form'+vote_id).text()) == ''){
	var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
		 $('#View-result-form'+vote_id).html('<img src="<?php echo SITEURL;?>images/ajax-loader.gif" alt="loading..."  style="margin: auto; padding: 10px 0px 25px;"/>');
		 //console.log('#View-result-form'+vote_id);
		$.ajax({
			url : formURL,
			async:false,
			success:function(response){
					if($.trim(response) != 'success'){
						$('#View-result-form'+vote_id).html(response);
					}else{
						$('#View-result-form'+vote_id).html('<span style="text-align: center; display: block; padding: 0px 0px 20px;">There is some techanical issue. Please try again. </span>');
					}
				}
			});
	//}

	 $('#View-result-form'+vote_id).slideDown('slow');

});
$(document).ready(function(){
	 $('body').tooltip({
		selector: '[data-tooltip="tooltip"]'
	});
});
</script>

<style>
.no-scroll {
	    overflow: hidden;
	}

    .cd-tabs-content.clearfix{
        overflow: auto;
        margin-bottom: 0px;
    }
.vtitle {
    max-width: 100%;
    overflow: hidden;
    display: inline-block !important;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.table_head {
  background: #e0e0e0 none repeat scroll 0 0;
  border: 1px solid #d2d2d2;
  border-radius: 5px 5px 0 0;
  color: #000000;
}
.row section.content-header h1 p.text-muted span{
	text-transform: none;
}
.title_filter button {
    display: inline-block;
    line-height: 18px;
    margin-left: 0px;
    padding: 5px 10px;
    vertical-align: top;
	min-height: 34px;
}
/*.form-control{height:31px;}*/
	.status_filter label{
		margin-bottom: 0;
		line-height: 34px;
	}
.title_filter input[type="text"] {
    display: inline-block;
    vertical-align: top;
    width: 40%;
}

.title_filter {
    display: block;
    float: right;
    line-height: 18px;
    margin: 0;
    text-align: right;
    width: 100%;
}
.re-vote-disabled {
    opacity: 0.6;
    cursor: default;
}
.tooltip.custom_class {
	text-transform: none !important;
}

	.vote-requests-table .btn {
		padding: 5px 4px;
	}







</style>