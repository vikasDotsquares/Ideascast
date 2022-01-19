<script type="text/javascript" >
$(function(){
		console.log('in')
	$('.collapible').hover( function(){
		console.log('in')
		var $parent = $(this).parent(),
			$that = $(this);
			
		$('.collapible').not($that[0]).css('width','20%')
		
		$that.css('width','60%')
		
	},
	function(){ 
		$('.collapible').css('width','33.3333%')
	})
})
</script>
<style>
.collapible {
	min-height: 150px;
	border: 1px solid #ccc;
	transition: width 0.5s ease-in-out 0s;
	margin-left: -1px;
}

</style>
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
							
							<div class="col-sm-12 exp_col">
								<div class="col-sm-4 collapible">Lorem Ipsum is simply dummy text of the printing and typesetting industry.  </div>
								<div class="col-sm-4 collapible">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
								<div class="col-sm-4 collapible">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</div>
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

</style>


		