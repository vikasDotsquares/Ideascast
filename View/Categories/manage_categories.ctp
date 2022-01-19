<?php
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_categories', array('inline' => true));
?>
<style type="text/css">
	.no-res {
	    color: #bbbbbb;
	    font-size: 30px;
	    text-align: center;
	    text-transform: uppercase;
	    width: 100%;
	    padding: 50px 0 0 0;
	    display: none;
	}

</style>
<script type="text/javascript" >
$(function() {
	$('input#search_category').val('')
	$('input#search_category').on('keyup', function(event) {

		var that = this, $allListElements = $('#multi_list ul > li');

		if (event.keyCode == 27 || $(this).val() == '') {
			$allListElements.show(1)

			if($(this).val() == ''){
				$('.no-res').hide();
			}
			return;
		}

		var $matchingListElements = $allListElements.filter(function(i, li){
			var listItemText = $(li).text().toUpperCase(),
				searchText = that.value.toUpperCase();
			return ~listItemText.indexOf(searchText);
		});

		$allListElements.hide(0);
		$matchingListElements.show(1);
		$('.no-res').hide();

		if($matchingListElements.length <= 0 || $matchingListElements.length <= '0'){
			$('.no-res').show();
		}
	});

	$('.action_buttons').on('click', function(event) {

		event.preventDefault();

		var $this = $(this),
			action = $this.data('action');

			if( action == 'collapse_all') {
				$('#multi_list li.has-sub-cat ul').slideUp(500,  function(){
					$('#multi_list i.opened').removeClass('opened fa-minus').addClass('closed fa-plus')
				})

			}
			else if( action == 'expand_all') {
				$('#multi_list li.has-sub-cat ul').slideDown(500, function(){
					$('#multi_list i.closed').removeClass('closed fa-plus').addClass('opened fa-minus')
				})

			}

		$(this).addClass('disabled');
		$('.action_buttons').not(this).removeClass('disabled')
	})

	$('#multi_list li.has-sub-cat').slideDown()
	$('#multi_list li.has-sub-cat ul').slideUp()
	$('#multi_list i.opened').removeClass('opened fa-minus').addClass('closed fa-plus')
});

</script>

<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">

				<h1 class="pull-left"><?php echo $page_heading; ?>

					<p class="text-muted date-time"> <span>Organize your Projects by using Categories</span> </p>

				</h1>
			</section>

		</div>

		<div class="row">
			<section class="content-header clearfix" style="margin:15px 15px 0 15px;  border-top-left-radius: 3px; background-color: #f5f5f5; border: 1px solid #ddd;  border-top-right-radius: 3px;">
				<div class="col-sm-6 info-wrap">
					<div class="form-group">
					    <span class="btn btn-info btn-xs info-button" style="cursor: default;" data-content='<span class="into-tip">Right click on Root to start creating Categories.</span>' >
							<i class="fa fa-info"></i>
						</span>
				    </div>
				</div>

				<div class="box-tools pull-right" style="padding: 3px 0px 10px 10px; ">
					      <input placeholder="Search" class="search_category pull-left" id="search_category" style="padding: 4px 4px 5px">
						 <div id="sidetreecontrol" class="pull-right">
							 <a class="btn btn-primary btn-sm action_buttons pull-right disabled tipText" title="Collapse All" data-action="collapse_all" href="#" style="margin: 0 0 0 5px;"><i class="fa fa-compress"></i></a>
							 <a class="btn btn-primary btn-sm action_buttons tipText" title="Expand All" data-action="expand_all" href="#"><i class="fa fa-expand"></i></a> </div>
					</div>

			</section>


		</div>


		<!-- END HEADING AND MENUS -->


		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder ">

						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<div class="modal fade" id="confirm-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-md">
									<div class="modal-content border-radius-top">
										<div class="modal-header border-radius-top bg-red" id="modal_header"> </div>
										<div class="modal-body" id="modal_body" style="padding: 25px 0 25px 10px;"></div>
										<div class="modal-footer" id="modal_footer" style="padding: 10px;">
											<a class="btn btn-success btn-success" id="confirm_yes">Yes
											</a>
											<a class="btn btn-success btn-danger" id="confirm_no" data-dismiss="modal">Cancel
											</a>
										</div>
									</div>
								</div>
							</div>


                        </div>
						<!-- END CONTENT HEADING -->


                        <div class="box-body border-top clearfix" style="min-height: 500px;">

							<div id="categoryContextOptions" class="dropdown clearfix">

								<ul id="context_menu" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block; margin-bottom:5px;">

								</ul>
							</div>

						<div class="popup_project_list" style="display: none; ">
							<button type="button" class="close close_category_projects" >
								<span aria-hidden="true">&times; </span> <span class="sr-only">Close</span>
							</button>
							<div class="pop-body" style="">
								<!-- <div id="" class="category_preloader" style=""> </div> -->
							</div>
						</div>



	<div class="row cat-wrap">
		<div class="no-res">No Results</div>
		<div id="multi_list" >
		<?php

			if( isset($categories) && !empty($categories) ) {
					echo get_tree($categories, 1);
			}
			?>

		</div>
	 </div>

						</div>



                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->



<style>.inner-view .date-time{font-weight:normal}</style>