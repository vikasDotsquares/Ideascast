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

<link rel="stylesheet" href="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/redactor.css"/>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/redactor.js"></script>

<script src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/RedactorCallbacksFix.js"></script>

<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontcolor.js"></script>
<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontfamily.js"></script>
<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/fontsize.js"></script>
<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/imagemanager.js"></script>
<script  type="text/javascript" src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/redactor/plugins/video.js"></script>

<link rel="stylesheet" href="<?php echo SITEURL; ?>js/projects/plugins/sketch/dist/drawerjs.css"/>
<script src="<?php echo SITEURL; ?>js/projects/plugins/sketch/dist/drawerjs.redactor.js"></script>

<!-- Custom localization example -->
<script src="<?php echo SITEURL; ?>js/projects/plugins/sketch/examples/redactor/DrawerLocalization.js"></script>
<script type="text/javascript" >
    $(document).ready(function () {

        $('body').delegate('#sketch_title', 'keyup focus', function(event){
            var characters = 50;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error-message');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

        $('body').delegate('#sketch_description', 'keyup focus', function(event){
            var characters = 250;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error-message');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

        $('.edit_sketch_panel .panel-heading > .panel-title, .edited-sketch-right > a.tipText , .skts_status > div > .tipText').tooltip({
            template: '<div class="tooltip CUSTOM-CLASSs" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important; width : 200px; cursor:pointer;"> </div></div>'
            , 'container': 'body', 'placement': 'top',
        })


        $('.pophover').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
    });
</script>

<?php echo $this->Html->css('projects/sketch'); ?>
<style>
	.gif_preloader {
    	background: #67a028 none repeat scroll 0 0;
    	border: 10px solid #19bee1;
    	border-radius: 100%;
    	height: 120px;
    	left: 50%;
    	margin: -60px 0 0 -60px;
    	position: absolute;
    	top: 50%;
    	width: 120px;
    }
    .fliter.margin-top {
        padding :15px 0 0 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd
    }
</style>
