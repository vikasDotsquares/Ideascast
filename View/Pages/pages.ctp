<section class="inner-content">
	<div class="">
			<div class="row">
			<div class="col-sm-12">
            <div class="inner-pages">
				<div class="" style="min-height: 400px;">
					<div class="page_heading">
						<h1><?php if(isset($pages['Page']['name']) && !empty($pages['Page']['name'])){
							echo $pages['Page']['name'];
						} ?></h1>
					</div>
					
					
						<?php
						if(isset($pages['Page']['content']) && !empty($pages['Page']['content'])){  ?>
						<div class="inner-pages_content">
							<?php echo $pages['Page']['content']; ?>	
						</div>
						<?php }else{ ?>
						<div class="form_box box1" style="text-align: center;">	
							<?php	echo 'Coming soon...'; ?>
						</div>
						<?php } ?>
					
				</div>
			</div>
		</div>
	</div>
	</div>
</section>
   
