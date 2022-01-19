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
				'/plugins/iCheck/minimal/_all.min',
                '/plugins/iCheck/flat/blue.min',
				'/plugins/iCheck/flat/_all.min',
                '/plugins/morris/morris',
                '/plugins/jvectormap/jquery-jvectormap-1.2.2',
                '/plugins/jquery-ui-1.11.4.custom/jquery-ui.min',
                '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min',
				'/plugins/fullcalendar/fullcalendar.min',
				'projects/alert.min',
				'projects/custom.min',
				'projects/user_themes.min',
                'styles-inner.min',
                'projects/bs-selectbox/bootstrap-select.min',
                'projects/bootstrap-input.min',
                'star-rating.min',
				'projects/socket_notifications',
                'projects/dropdown.min',
                'projects/competency_global'
            )
    );

    echo $this->Html->script(array(
             '/plugins/jQuery/jQuery-2.1.3.min',
			 '/plugins/jquery-ui-1.11.4.custom/jquery-ui.min',
			 'moment.min',
			'/plugins/fullcalendar/fullcalendar.min',
			'dashboard.min',
            'projects/plugins/selectbox/bootstrap-select.min',
            'jstz',
            'projects/plugins/loadsh/lodash.min',
            'projects/reminders',
        )
    );


    if( $this->Session->read('Auth.User.role_id') == 2 ){
?>

<script type="text/javascript">
    $.startTime = new Date().getTime();
</script>
<?php }
	// Add JS object to accessible in every view file.
	echo $this->Html->scriptBlock('var $js_config = '.$this->Js->object($jsVars).';');

?>
<script type="text/JavaScript">
  function loadFile(url) {
    var script = document.createElement('SCRIPT');
    script.src = url;
    document.getElementsByTagName('HEAD')[0].appendChild(script);
  }
</script>
<script type="text/javascript">
    function reloadIt() {
        var clocktime = new Date();
        var utchours = clocktime.getUTCHours();
        var utcminutes = clocktime.getUTCMinutes();
        var utcseconds = clocktime.getUTCSeconds();
        var utcyear = clocktime.getUTCFullYear();
        var utcmonth = clocktime.getUTCMonth()+1;
        var utcday = clocktime.getUTCDate();

        if (utchours <10) { utchours = "0" + utchours }
        if (utcminutes <10) { utcminutes = "0" + utcminutes }
        if (utcseconds <10) { utcseconds = "0" + utcseconds }
        if (utcmonth <10) { utcmonth = "0" + utcmonth }
        if (utcday <10) { utcday = "0" + utcday }

        var utctime = utcyear + utcmonth + utcday;
        utctime += utchours + utcminutes + utcseconds;
        x = utctime

        isNew = self.location.href
        if(!isNew.match('#','x')) {
            self.location.replace(isNew + '#' + x)
        }
    }

    $(function(){
        // This will turn-off the common methods in the console if it exists, and they can be called without error. In the case of a browser like IE6 with no console, the dummy methods will be created to prevent errors.

        // TO TURN-OFF CONSOLE.LOG FUNCTION COMPLETLY; SET BELOW VARIABLE TO FALSE
        var debug = true; // set to false to disable debugging

        if (debug === false) {
            if (typeof(window.console) === 'undefined') {
                window.console = {};
            }

            var methods = ["log", "debug", "warn", "info"];
            for (var i = 0; i < methods.length; i++) {
                console[methods[i]] = function() {};
            }
        }
    })
</script>


