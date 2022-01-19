<style>

</style>


<?php 
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/plugins/manage_categories', array('inline' => true));
?> 

<script type="text/javascript" > 
	$(function() {
		
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

					<p class="text-muted date-time"> </p>
					
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
							
                        </div>
						<!-- END CONTENT HEADING -->

						
                        <div class="box-body border-top clearfix">
							
							<!-- right click menus -->
							<div id="categoryContextOptions" class="dropdown clearfix">
							 
								<ul id="context_menu" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block; margin-bottom:5px;">
									<li>
										<a tabindex="-1" href="#" name="add_category"><i class="fa fa-long-arrow-right"></i> Add Category </a>
									</li>
									<li>
										<a tabindex="-1" href="#" name="add_sub_category"><i class="fa fa-level-down"></i> Add Sub Category</a>
									</li> 
									
										<li class="divider"></li>
										
									<li>
										<a tabindex="-1" href="#" name="update_category" class="update_category"><i class="fa fa-edit"></i> Update Category</a>
									</li>
										
									<li>
										<a tabindex="-1" href="#" name="remove_node" class="remove_node"><i class="fa fa-trash"></i> Remove</a>
									</li>
									
								</ul>
							</div>
							
	<div class="row">
	
		<?php  
		
			if( isset($categories) && !empty($categories) ) {
				 
			
		?>
		<div id="multi_list" >
		
			<ul class="nav nav-list tree categories_container" id="">
			
				<?php echo get_tree($categories); ?>
			
			</ul>
			
		</div>
		
		<?php } ?>
		
		
		
		
		
		
		<!-- 
			<div id="multi_list" >
				<ul class="nav nav-list tree categories_container" id="">
					<li><a class="tree-toggler nav-header">Header 1</a>
						<ul class="nav nav-list tree">
							<li><a href="#">Link</a></li>
							<li><a href="#">Link</a></li>
							<li><a class="tree-toggler nav-header">Header 1.1</a>
								<ul class="nav nav-list tree">
									<li><a href="#">Link</a></li>
									<li>
										<a class="tree-toggler nav-header">Header 1.1.1</a> 
										<ul class="nav nav-list tree">
											<li><a href="#">Link sample</a></li>
											<li><a href="#">Link sample 2</a></li>
										</ul>
									</li>
									<li><a class="tree-toggler nav-header">Header 1.1.1</a>
										<ul class="nav nav-list tree">
											<li>
												<a class="tree-toggler nav-header">Header 1.1.1.1</a>
												<ul class="nav nav-list tree">
													<li><a href="#">Link sample</a></li>
													<li><a href="#">Link sample 2</a></li>
												</ul>
											</li> 
											<li><a href="#">Link</a></li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="divider"></li>
					<li><a class="tree-toggler nav-header">Header 2</a>
						<ul class="nav nav-list tree">
							<li><a href="#">Link</a></li>
							<li><a href="#">Link</a></li>
							<li><a class="tree-toggler nav-header">Header 2.1</a>
								<ul class="nav nav-list tree">
									<li><a href="#">Link</a></li>
									<li><a href="#">Link</a></li>
									<li><a class="tree-toggler nav-header">Header 2.1.1</a>
										<ul class="nav nav-list tree">
											<li><a href="#">Link</a></li>
											<li><a href="#">Link</a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li><a class="tree-toggler nav-header">Header 2.2</a>
								<ul class="nav nav-list tree">
									<li><a href="#">Link</a></li>
									<li><a href="#">Link</a></li>
									<li><a class="tree-toggler nav-header">Header 2.2.1</a>
										<ul class="nav nav-list tree">
											<li><a href="#">Link</a></li>
											<li><a href="#">Link</a></li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		 -->
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


		