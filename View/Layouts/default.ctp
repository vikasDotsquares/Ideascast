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
</head>
<body class="<?php echo isset($is_home)?$is_home:'inner_page_view'; echo isset($bodyclass)? " ".$bodyclass : '';  ?>">

<?php echo $this->element('front/header'); ?>
        <div id="ajax_overlay" class="ajax_overlay_preloader" style="display:none">
            <div id="" class="gif_preloader" style="">
                <div id="" class="loading_text" style="">Loading..</div>
            </div>
        </div>

	<?php //echo $this->Session->flash();?>

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

		/*if( $("#successFlashMsg").length > 0 ) {
			setTimeout(function() {

				$("#successFlashMsg").animate({
					//opacity: 0,
					//height: 0
					}, 1000, function() {
					//$(this).remove()
				})

			}, 4000)
		}*/
    });


	  $(document).ajaxSuccess(function (event, jqXHR, ajaxSettings, data) {
                        $('.tooltip').hide()

                        if ($(".ajax_overlay_preloader").length > 0) {
                            $(".ajax_overlay_preloader").fadeOut(150);
                            $("body").removeClass('noscroll');
                        }

                        if ($.inArray('msg', data) == -1) {
                            if (data['msg'] != '' && !data['success'] && !data['msg'] == 'undefined') {
                                $(".ajax_flash").text(data['msg']).fadeIn(500)
                                setTimeout(function () {
                                    if ($(".ajax_flash").length > 0) {
                                        $(".ajax_flash").fadeOut(600).text('');
                                    }
                                }, 3000)
                            }
                        }
                    });

                    /*
                     * @todo  Global setup of AJAX on document. It can be used when any ajax call is performed
                     * */
                    $(document).ajaxSend(function (e, xhr) {


                        window.theAJAXInterval = 1;
                        // $("#ajax_overlay_text").textAnimate("..........");
                        $(".ajax_overlay_preloader")
                                .fadeIn(300)
                                .bind('click', function (e) {
                                    $(this).fadeOut(300);
                                });

                        $("body").addClass('noscroll');
                    })
                            .ajaxComplete(function () {
                                setTimeout(function () {
                                    $(".ajax_overlay_preloader").fadeOut(300);
                                    $("body").removeClass('noscroll');
                                    clearInterval(window.theAJAXInterval);
                                }, 2000)

                                // console.clear()
                            });
                    /*
                     * @todo  Initially stop all global AJAX events.
                     * */
                    $.ajaxSetup({
                        global: false,
                        headers: {
                            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                        }

                    })
 </script>
</body>
</html>
