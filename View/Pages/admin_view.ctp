<div class="shell">
		
		<!-- Small Nav -->
		<div class="small-nav">
		<?php
		$this->Html->addCrumb('Dashboard', array('admin'=>true,'controller'=>'dashboards','action'=>'index'));
		$this->Html->addCrumb('Pages', array('admin'=>true,'controller'=>'pages','action'=>'index'));
		$this->Html->addCrumb(h($page['Page']['name']));
		echo $this->Html->getCrumbs('  > ');?>
		</div>
		<!-- End Small Nav -->
		<?php //echo $this->Session->flash(); ?>
		<br />
		<!-- Main -->
		<div id="main">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content">

				
				<!-- Box -->
				<div class="box col-md-10">
					<!-- Box Head -->
					<div class="box-head">
						<h2><?php echo h($page['Page']['name']);?></h2>
					</div>
					<!-- End Box Head -->
					<!-- Form -->
				<?php echo $this->Form->create(''); ?>

						
						
						<div class="form">
                        	<div class="foremrow">
							<div class="formfull">
								<p>
                                <label>Title</label>
								<?php echo h($page['Page']['name']);?>
                                </p>
                                
                                <p>
								<label>Meta Title</label>
								<?php echo h($page['Page']['meta_title']);?>
								</p>
								
								 <p>
								<label>Meta Keywords</label>
								<?php echo h($page['Page']['meta_keywords']);?>
								</p>
								<p>
								<label>Meta Description</label>
								<?php echo h($page['Page']['meta_description']);?>
								</p>
								<p>
                                <label>Page Content</label>
								<?php echo $page['Page']['content'];?>
                                </p>
								 <p>
								<label>Status</label>
								<?php echo ($page['Page']['status'])?'Active':'In active';?>
                                </p>
								</div>
								
							
                                
							</div>
                        
                           
								
						  
							
						</div>
						
						
					<!-- End Form -->	
				</div>
				<!-- End Box -->

			</div>
			<!-- End Content -->
	

			
			<div class="cl">&nbsp;</div>			
		</div>
		<!-- Main -->
	</div>
