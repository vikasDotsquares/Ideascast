<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" > var SITEURL='<?php echo SITEURL; ?>'</script>
<?php
        echo $this->Html->meta('icon', $this->Html->url(SITEURL.'favicon.png'));
        echo $this->Html->css(
                array(
					'admin_inner/styles-inner',
                    'admin_inner/bootstrap.min',
                    'admin_inner/font-awesome.min',
                    'admin_inner/ionicons.min',
                    'admin_inner/AdminLTE',
                    'admin_inner/skins/_all-skins',
                    '/admin_inner/plugins/iCheck/flat/blue',
                    '/admin_inner/plugins/morris/morris',
                    '/admin_inner/plugins/jvectormap/jquery-jvectormap-1.2.2',
                    '/admin_inner/plugins/datepicker/datepicker3',
                    '/admin_inner/plugins/daterangepicker/daterangepicker-bs3',
                    '/admin_inner/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min',
                    'admin_inner/flexslider',
					'admin_inner/styles-inner',
					
					// 'drag-drop-context/context-menus',
						 
                )
        );
        
	?>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
      <?php  echo $this->Html->script(array(
            '/admin_inner/plugins/jQuery/jQuery-2.1.3.min',
			'admin_inner/jquery-ui.min' ,
			'admin_inner/dashboard'
        )
       );  
    ?>
  
	
<?php 
	// Add JS object to accessible in every view file.
	echo $this->Html->scriptBlock('var $js_config = '.$this->Js->object($jsVars).';'); 
?>
<?php  
echo $this->Html->script(array( 
			'admin_inner/drag-drop-context/jquery.cookie',
		)
    );  
?>
<script type="text/javascript" >
	$(function() {
		$('a:not([title=""]),a:not([data-original-title=""])').tooltip({ container: 'body', placement: 'auto'});
	})
</script>
