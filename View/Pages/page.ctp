


<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix"> </section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box ">
						<!-- CONTENT HEADING -->
                        <div class="box-header"> 
						
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
							<section class="weare clearfix paddingNone">
								<div class="container">
									<div class="row" style="padding: 0 0 70px;">
									
										<div class="col-sm-12">
											
											<?php //if( isset($pages) && !empty($pages) ) { ?>
												 
												
												<div class="section-title">
													<h2><?php if(isset($pages['Page']['name']) && !empty($pages['Page']['name'])){
														echo $pages['Page']['name'];
													} ?></h2>
												</div>
												
												
												<div class="short-desc">
													<?php
														// if( !isset($db_data) && !empty($db_data) ) {
															// $this->set('data', $db_data);
														// }
												if(isset($pages['Page']['content']) && !empty($pages['Page']['content'])){						
													echo $pages['Page']['content'];
												} ?>
													
												<br />
												<br />
												<br />
												<br />
												<br />
												<br />
												<br />
												<br />
												<br />
												</div>
												
												
											<?php //}
											 	?>
										</div>
										<!--<div class="col-sm-4">
											<div class="thumb"><img src="<?php echo SITEURL?>images/think positive.jpg" alt="We are IdeasCast"></div>
										</div> -->
									</div>
								</div>
							</section>
						</div>
						
						
					   
                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->
		
	</div>
</div>
<!-- END OUTER WRAPPER -->





<!-- ---------------- MODEL BOX INNER HTML LOADED BY JS ------------------------ -->

<div class="hide" >
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">POPUP MODAL HEADING</h3>
		
	</div>
	
	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<h5 class="project-name"> popup box heading </h5>
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit" class="btn btn-warning">Save changes</button>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
	</div>
</div>

<!-- ---------------- JS TO OPEN MODEL BOX ------------------------ -->
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


		



 
