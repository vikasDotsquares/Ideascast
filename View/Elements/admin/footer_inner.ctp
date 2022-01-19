<footer class="footer clearfix">
  <div class="container">
  <div class="footer-content"> <a href="#0" class="cd-top"><i class="fa fa-angle-up"></i></a>
    <p> 
      
      &copy; <?php echo date('Y') ?> IdeasCast Limited. All rights reserved</p>
    
    </div>
    </div>

</footer>
	

	
    <script type="text/javascript" >
      $.widget.bridge('uibutton', $.ui.button);
    </script>
  <?php  echo $this->Html->script(array(
            //'bootstrap', 
			'bootstrap-dialog/bootstrap3.3.5.min'
         )
        );  
    ?>
    <!-- <script  type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> -->
    <?php  echo $this->Html->script(array(
       // '/plugins/morris/morris.min', 
        '/admin_inner/plugins/sparkline/jquery.sparkline.min', 
        '/admin_inner/plugins/jvectormap/jquery-jvectormap-1.2.2.min', 
        '/admin_inner/plugins/jvectormap/jquery-jvectormap-world-mill-en', 
        '/admin_inner/plugins/knob/jquery.knob', 
        '/admin_inner/plugins/daterangepicker/daterangepicker', 
        '/admin_inner/plugins/datepicker/bootstrap-datepicker', 
        //'/admin_inner/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min', 
        '/admin_inner/plugins/iCheck/icheck.min', 
        '/admin_inner/plugins/slimScroll/jquery.slimscroll.min', 
        '/admin_inner/plugins/fastclick/fastclick.min', 
        'admin_inner/app', 
        'admin_inner/pages/dashboard', 
        'admin_inner/demo', 
        'admin_inner/jquery.flexslider',
        'admin_inner/front.custom',
		
		
		
		'drag-drop-context/templates-lib',
		// 'drag-drop-context/model-boxes', 
		// 'drag-drop-context/jquery.ddslick', 
        ));  
    ?>
	
<script type="text/javascript" >
	
    jQuery(document).ready(function(){
		jQuery('.footer-content a.cd-top').click(function(e) {
				e.preventDefault()
				jQuery('html, body').animate({
					scrollTop: 0
				}, 800)
		})
	})
    
</script>