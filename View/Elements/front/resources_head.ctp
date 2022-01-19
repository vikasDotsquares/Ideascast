<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />


<?php
        echo $this->Html->meta('icon', $this->Html->url(SITEURL.'favicon.png'));
        echo $this->Html->css(
                array(
                    'bootstrap.min',
                    'font-awesome.min',
                    'ionicons.min',
                    'AdminLTE.min',
                    'skins/_all-skins.min',
                    '/plugins/jquery-ui-1.11.4.custom/jquery-ui.min',

					'styles-inner.min',
					'projects/custom.min',
					'projects/user_themes.min.css',
					'projects/bootstrap-input.min',
                    'projects/bs-selectbox/bootstrap-select.min',
					'projects/socket_notifications',
					'projects/competency_global'
					 
                )
        );

	?>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
      <?php  echo $this->Html->script(array(
            '/plugins/jQuery/jQuery-2.1.3.min',
			'/plugins/jquery-ui-1.11.4.custom/jquery-ui.min',
			'dashboard',
            'projects/plugins/selectbox/bootstrap-select',
            'jstz',
			'/plugins/iCheck/icheck.min',
            'projects/plugins/loadsh/lodash',
            'projects/reminders'
        )
       );
    ?>

<script type="text/javascript">
    // alert('resolve')
</script>
<?php if( $this->Session->read('Auth.User.role_id') == 2 ){ ?>
<!-- <script type="text/javascript" src="<?php echo CHATURL; ?>/socket.io/socket.io.js"></script><?php } ?> -->
<?php
	// Add JS object to accessible in every view file.
	echo $this->Html->scriptBlock('var $js_config = '.$this->Js->object($jsVars).';');


echo $this->Html->script(array(
			//'drag-drop-context/jquery.cookie',
		)
    );
?>


