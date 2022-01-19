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
	
    <title><?php echo (isset($title_for_layout)&&!empty($title_for_layout)?$title_for_layout:'IdeasCast');?></title>
  <meta name="description" content="<?php echo (isset($keywords_for_layout)&&!empty($keywords_for_layout)?$keywords_for_layout:'IdeasCast');?>">
    <meta content="<?php if(isset($description_for_layout)  && !empty($description_for_layout)){ echo $description_for_layout; }else{ echo 'IdeasCast'; } ?>" name="keywords"/>
    <!-- end: Meta -->	
    <?php echo $this->element('front/head'); ?>	
    <?php //echo $this->Html->css('styles'); ?>	
<style>
#successFlashMsg {
    margin-bottom: 4px;
    padding: 10px 5px;
}
</style>	
</head>
<body class="<?php echo isset($is_home)?$is_home:'inner_page_view';?>">

<?php echo $this->element('front/header'); ?>

	<?php echo $this->Session->flash();?>

<div class="main clearfix">
   
      <?php echo $this->fetch('content');	?>
	 
 </div>

<?php echo $this->element('front/footer');
 echo $this->element('sql_dump');
?>


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
	  
		if( $("#successFlashMsg").length > 0 ) {
			setTimeout(function() {
				
				$("#successFlashMsg").animate({
					opacity: 0,
					height: 0
					}, 1000, function() {
					$(this).remove()
				})
				
			}, 4000)
		}
    });
 </script> 
</body>
</html>
