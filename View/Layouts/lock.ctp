<!DOCTYPE html>
<html lang="en">
<head>
<!-- start: Meta -->
    <meta charset="utf-8">

    <title><?php echo (isset($title_for_layout)&&!empty($title_for_layout)?$title_for_layout:'IdeasCast');?></title>
  <meta name="description" content="<?php echo (isset($keywords_for_layout)&&!empty($keywords_for_layout)?$keywords_for_layout:'IdeasCast');?>">
    <meta content="<?php if(isset($description_for_layout)  && !empty($description_for_layout)){ echo $description_for_layout; }else{ echo 'IdeasCast'; } ?>" name="keywords"/>
    <!-- end: Meta -->
    <?php echo $this->element('lock/head'); ?>
    <?php //echo $this->Html->css('styles'); ?>
</head>
<body class="">

<?php echo $this->element('lock/header'); ?>

<div class="main clearfix">

      <?php echo $this->fetch('content');	?>

 </div>

<?php echo $this->element('lock/footer');
 echo $this->element('sql_dump');
?>


</body>
</html>
