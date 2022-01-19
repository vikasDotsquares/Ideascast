 
 
<script type="text/javascript" >
$(function(){
	$(document.body).on('click', '.collapse-row', function(event) {
		
		var $row = $(this).parent().parent(); 
		
		$row.find('td').css({'padding': 0, 'height': 0});
		 
	})
})
</script>


<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
			
				<h1 class="pull-left"> 
					Page Heading 
					<p class="text-muted date-time"><span>small text</span></p>
				</h1>
				
				 
				
			</section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
						
						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding">
						
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							
                        </div>
						<!-- END CONTENT HEADING -->

						
                        <div class="box-body border-top" style="min-height: 500px">
						
							<div class="btn btn-default btn-sm" data-toggle="collapse" data-target="#template">
								<i class="fa fa-expand "></i> Expand/Collapse
							</div>
							<div class="table-wrapper in" id="template">
							
							<?php 
							if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {
									
									?>
								<table class="table table-bordered" id="tbl" >
									
									<tbody>
										<tr>
											<?php
												$max_boxes = max( array_map( 'count', $data['templateRows'] ) );
												$setWidth = 0;
												$row_group = $data['templateRows'];
												
												foreach( $row_group as $row_id => $row_data ) {
													$setWidth++;
													
													foreach( $row_data as $row_index => $row_detail ) {
														
														$last = false;
														$colspan = $rowspan = '';
														
														if( $row_detail['size_w'] > 0 && $row_detail['size_h'] > 0 ) {
															if( $row_detail['size_w'] > 1 ) {
																$colspan = ' colspan="' . $row_detail['size_w'] . '" ';
															}
															if( $row_detail['size_h'] > 1 ) {
																$rowspan = ' rowspan="' . $row_detail['size_h'] . '" ';
															}
														}
														
														$tdWidth = 0;
														if( isset( $setWidth ) && !empty( $setWidth ) ) {
															$tdWidth = ( 100 / $max_boxes );
															$tdWidth = number_format( $tdWidth, 4 );
															$tdWidth = $tdWidth . '%';
														}
														
													?>
													
												<?php 
												$icon = '<i class="fa fa-check" style="opacity: 0"></i>';
												$col_style = 'background: #EEEEEE;';
												 ?>	
													
												<td <?php echo (!empty( $colspan )) ? $colspan : ''; ?> <?php echo (!empty( $rowspan )) ? $rowspan : ''; ?> valign="top" class="area_box" id="<?php echo $row_detail['area_id']; ?>" style="text-align: center; <?php echo $col_style; ?>" <?php if( $setWidth == 1 ) { ?> width="<?php echo $tdWidth; ?>" <?php } ?> >
													<?php echo $icon; ?> 
												</td>
												<?php
												}
												// end first foreach
												// End table row started just after first foreach
												// It prints till the total number of rows reaches
												if( $row_id < (count( $row_group ) - 1) )
												echo '</tr><tr>';
											} // end second foreach
										?>
									</tbody>
								</table>
							<?php } ?>
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


<style>
.table-wrapper { 
    padding: 6px;
    width: 100%;
}
#tbl {
	margin: 0;
}
#tbl td {
	padding: 100px 0;
	border: 1px solid #ccc; 
}
</style>


		