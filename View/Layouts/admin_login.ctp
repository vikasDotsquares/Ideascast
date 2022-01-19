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
?><!DOCTYPE html>
<html lang="en">
<head>
<!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->	
    <?php echo $this->element('admin/head'); ?>	
    <?php echo $this->Html->css('admin_inner/styles'); ?>	
</head>
<body class="<?php echo isset($is_home)?$is_home:'inner_page_view';?>">

<?php echo $this->element('admin/header'); ?>

	<?php echo $this->Session->flash();?>

<div class="main clearfix">
   
      <?php echo $this->fetch('content');	?>
	 
 </div>

<?php echo $this->element('admin/footer');?>


<script type="text/javascript">
   /*  $(function(){
      SyntaxHighlighter.all();
    }); */
    $(window).load(function(){
      $('.flexslider').flexslider({
        animation: "slide",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
    });
 </script> 
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