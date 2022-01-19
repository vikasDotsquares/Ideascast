


<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
			
				<h1 class="pull-left"> Template Samples </h1>
				
				 
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
						 
                        </div>
						<!-- END CONTENT HEADING -->

						
                        <div class="box-body no-padding">
							<h1 class="box-title"></h1>
							<div class="row" style="margin-bottom: 20px !important"> 
								<?php for( $i=1; $i<=6; $i++) { ?> 
								<div class="col-sm-2" style="text-align: center !important;">
									<?php 
									
									echo $this->Html->image('layouts/sample/sample ('.$i.').png', ['class' => 'thumb']).'<br />'.$i; ?>
								</div>
								<?php } ?>
								</div>
								<div class="row" style="margin-bottom: 20px !important"> 
								<?php for( $i=7; $i<=12; $i++) { ?>
								<div class="col-sm-2" style="text-align: center !important;">
									<?php 
									
									echo $this->Html->image('layouts/sample/sample ('.$i.').png', ['class' => 'thumb']).'<br />'.$i; ?>
								</div>
								<?php } ?>
								</div><div class="row" style="margin-bottom: 50px !important"> 
								<?php for( $i=13; $i<=15; $i++) { ?>
								<div class="col-sm-2" style="text-align: center !important;">
									<?php 
									
									echo $this->Html->image('layouts/sample/sample ('.$i.').png', ['class' => 'thumb']).'<br />'.$i; ?>
								</div>
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
