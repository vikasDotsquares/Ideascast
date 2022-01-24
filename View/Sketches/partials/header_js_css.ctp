<?php
echo $this->Html->css('projects/dropdown', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->css('projects/task_lists', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/marks/jquery.mark.min', array('inline' => true));

echo $this->Html->script('canvas', array('inline' => true));
echo $this->Html->script('canvas2image', array('inline' => true));
?>

<link rel="stylesheet" href="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/redactor.css"/>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/redactor.js"></script>

<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/RedactorCallbacksFix.js"></script>

<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontcolor.js"></script>
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontfamily.js"></script>
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontsize.js"></script>
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/imagemanager.js"></script>
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/video.js"></script>

<link rel="stylesheet" href="<?php echo SITEURL;?>js/projects/plugins/sketch/dist/drawerJs.css"/>
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/dist/drawerJs.redactor.js"></script>

<!-- Custom localization example -->
<script src="<?php echo SITEURL;?>js/projects/plugins/sketch/examples/redactor/DrawerLocalization.js"></script>