<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<!-- Apple devices fullscreen -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<!-- Apple devices fullscreen -->
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo $title_for_layout; ?></title>
        <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css(
                array(
                    'bootstrap.min',
                    'font-awesome.min',
                    'AdminLTE',
                )
        );
		?>
		 <?php  echo $this->Html->script(array('jquery.min','bootstrap.min'));  ?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script  type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script  type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">
       <?php echo $this->fetch('content');	?>
    </body>
</html>
<script type="text/javascript" >
    $(function () {
        setTimeout(function () {
            $("#successFlashMsg").slideUp(3000);
        }, 2000)
        
    });
</script>

<style>
html,body { height: 100%; }
</style>