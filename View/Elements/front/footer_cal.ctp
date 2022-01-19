<?php /* ?><footer class="footer clearfix">
<script type="text/javascript" >
$(function(){
var a = $('body').width() - 230;
$('.cnts').width($('body').width());
$('.cnts').css('margin-left','230px');
//$('.footer p').css('margin-right','230px');
$('.footer .footer-content').css('margin-right','230px');
})

</script>
  <div class="containers ">
  <div class="footer-content"> <a href="#" class="cd-top"><i class="fa fa-angle-up"></i></a>

    <p>
      &copy; <?php echo date('Y'); ?> IdeasCast Limited. All rights reserved.</p>

    </div>
    </div>

</footer><?php */ ?>


<?php
if( $_SERVER['SERVER_NAME'] != SERVER_NAME &&  $this->Session->read('Auth.User.role_id') == 2 ){
          //echo $this->Html->script('projects/socket/socket_connection', array('inline' => true));
      }
/*if( $_SERVER['SERVER_NAME'] != "prod.ideascast.com" ){
echo $this->Html->script('projects/socket/socket_connection', array('inline' => true));
}*/ ?>
    <script type="text/javascript" >
    $(function() {
  		$('.footer-content a.cd-top').on('click', function(e) {
  				// e.preventDefault()
  				$('html, body').animate({
  					scrollTop: 0
  				}, 800)
  		})

    function msToTime(duration) {
        var milliseconds = parseInt((duration % 1000) / 100),
            seconds = Math.floor((duration / 1000) % 60),
            minutes = Math.floor((duration / (1000 * 60)) % 60),
            hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        return hours + ":" + minutes + ":" + seconds + "." + milliseconds;
    }
    function getPageLoadTime() {
        var loadedSeconds = (new Date().getTime() - $js_config.start_time);
        if($js_config.page_load) {
            console.log('Page load time ::  ' + msToTime(loadedSeconds));
        }
    }
    if ($js_config.page_load) {
        window.onload = getPageLoadTime;
    }
	})
    </script>

      <script type="text/javascript" >
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <?php  echo $this->Html->script(array(
            'bootstrap.min',
         )
        );
    ?>
    <?php  echo $this->Html->script(array(
        '/plugins/sparkline/jquery.sparkline.min',
        '/plugins/jvectormap/jquery-jvectormap-1.2.2.min',
        '/plugins/jvectormap/jquery-jvectormap-world-mill-en',
        '/plugins/knob/jquery.knob',
        '/plugins/daterangepicker/daterangepicker',
        '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min',
        '/plugins/slimScroll/jquery.slimscroll.min',
        'app',
        'jquery.cookie',
        'demo',
        'front.custom',
        'projects/plugins/colored_tooltip',
        ));
    ?>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/bg-BG.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/nl-NL.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/fr-FR.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/de-DE.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/el-GR.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/it-IT.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/hu-HU.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/pl-PL.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/pt-BR.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/ro-RO.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/es-CO.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/es-MX.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/es-ES.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/ru-RU.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/sk-SR.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/sv-SE.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/zh-TW.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/cs-CZ.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/ko-KR.js"></script>
	<script type="text/javascript" src="<?php echo SITEURL ?>/twitter-cal/js/language/id-ID.js"></script>
  <style type="text/css">
    section.content {
      min-height: 800px;
  }
  </style>