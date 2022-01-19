<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php //pr($this->request->params);
if( isset($this->request->params['pass'][0]) && !empty($this->request->params['pass'][0]) && $this->request->params['action'] == 'blogdetails' ){
$postID = $this->request->params['pass'][0];
$postData = $this->Common->getPostDetails($postID);
?>
<meta property="og:url"           content="<?php echo SITEURL?>blogdetail/<?php echo $postData['Post']['id'];?>" />
<meta property="og:type"          content="jeera.ideascast.com" />
<meta property="og:title"         content="<?php echo $postData['Post']['title'];?>" />
<meta property="og:description"   content="<?php echo strip_tags($postData['Post']['description']);?>" />
<meta property="og:image"         content="<?php echo ( isset($postData['Post']['blog_img']) && !empty($postData['Post']['blog_img']) )? SITEURL.POST_PIC_PATH.$postData['Post']['blog_img']: SITEURL.POST_PIC_PATH.'no_image.jpg'; ?>" />


<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@ideascast">
<meta name="twitter:creator" content="@ideascast">
<meta name="twitter:title" content="<?php echo $postData['Post']['title'];?>">
<meta name="twitter:description" content="<?php echo strip_tags($postData['Post']['description']);?>">
<meta name="twitter:image" content="<?php echo ( isset($postData['Post']['blog_img']) && !empty($postData['Post']['blog_img']) )? SITEURL.POST_PIC_PATH.$postData['Post']['blog_img']: SITEURL.POST_PIC_PATH.'no_image.jpg'; ?>">
<?php } ?>

<script type="text/javascript" > var SITEURL='<?php echo SITEURL; ?>'</script>
    <link rel="apple-touch-icon" href="<?php echo SITEURL?>images//j_black.png"/>
    <link rel="apple-touch-icon-precomposed" href="<?php echo SITEURL?>images//j_black.png"/>
<?php
        // echo $this->Html->meta('icon');
		echo $this->Html->meta('icon', $this->Html->url(SITEURL.'favicon.png'));
        echo $this->Html->css(
                array(
                    'bootstrap.min',
                    'font-awesome.min',
                    'ionicons.min',
                    'AdminLTE',
                    'skins/_all-skins.min',
					'/plugins/iCheck/minimal/_all',
                    '/plugins/iCheck/flat/blue',
					'/plugins/iCheck/flat/_all',
                    '/plugins/morris/morris',
                    '/plugins/jvectormap/jquery-jvectormap-1.2.2',
                    '/plugins/datepicker/datepicker3',
                    '/plugins/daterangepicker/daterangepicker-bs3',
                    '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min',
                    'flexslider',
                    'styles',
					'custom',
					'webresponsive',
					'empoweringteamwork',
					// '/plugins/fullcalendar/fullcalendar.min',
					// '/plugins/fullcalendar/fullcalendar.print',
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
            '/plugins/jQuery/jQuery-2.1.3.min','jquery.validate','additional-methods','custom_validate', 'jquery-ui.min','moment.min', '/plugins/fullcalendar/fullcalendar.min','jquery.cookie',
        )
       );
    ?>


<?php
	// Add JS object to accessible in every view file.
// if(isset($jsVars) && !empty($jsVars))
	echo $this->Html->scriptBlock('var $js_config = '.$this->Js->object($jsVars).';');
?>
<script type="text/javascript" >

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){

  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),

  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)

  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59335628-1', 'auto');

  ga('send', 'pageview');

</script>