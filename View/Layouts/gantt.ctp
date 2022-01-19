<script src="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
<script src="<?php echo SITEURL; ?>plugins/gantt_calender/common/third-party/jquery-1.11.1.min.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>plugins/gantt_calender/common/third-party/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo SITEURL; ?>plugins/gantt_calender/common/third-party/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<script src="<?php echo SITEURL; ?>plugins/gantt_calender/common/third-party/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<?php echo $this->fetch('content');	?>

<script type="text/javascript">
	$(funtion(){
		console.log('-------------------------')
	    function msToTime(duration) {
	        var milliseconds = parseInt((duration % 1000) / 100),
	            seconds = Math.floor((duration / 1000) % 60),
	            minutes = Math.floor((duration / (1000 * 60)) % 60),
	            hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

	        hours = (hours < 10) ? "0" + hours : hours;
	        minutes = (minutes < 10) ? "0" + minutes : minutes;
	        seconds = (seconds < 10) ? "0" + seconds : seconds;

	        return minutes + ":" + seconds + "." + milliseconds;
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
