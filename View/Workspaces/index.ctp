<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">

				<h1 class="pull-left"> Page Heading </h1>

				<div class="btn-group pull-right">
					<div class="btn-group action">
						<a data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button" aria-expanded="false" href="javascript:void(0);">
							Export <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li><a href="#"><i class="halflings-icon user"></i> Word</a></li>
							<li><a href="#"><i class="halflings-icon user"></i> Power Point</a></li>
							<li><a href="#"><i class="halflings-icon user"></i> PDF</a></li>
						</ul>
					</div>
				</div>

			</section>
		</div>
		<!-- END HEADING AND MENUS -->


		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box ">
						<!-- CONTENT HEADING -->
                        <div class="box-header">

                            <h3 class="box-title">Page Sub-Heading</h3>
							<p class="text-muted date-time"><span>small text</span></p>

							<!-- PAGE TOOLS BUTTONS -->
                            <div class="box-tools">
								<div class="btn-group">
									<a data-toggle="modal" class="btn btn-success btn-sm" href="#" data-target="#myModal"><i class="fa fa-fw fa-wrench"></i></a>
								</div>
							</div>


							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

                        </div>
						<!-- END CONTENT HEADING -->


                        <div class="box-body no-padding">
							<h1 class="box-title">Page Body</h1>


						</div>



                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->




<script type="text/javascript" >
    $('#myModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

// Submit Add Form
      jQuery("#formID").submit(function (e) {
        var postData = jQuery(this).serializeArray();

        jQuery.ajax({
            url: jQuery(this).attr("action"),
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {

                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action
    });
</script>
