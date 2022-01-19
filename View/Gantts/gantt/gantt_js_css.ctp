<link rel="stylesheet" href="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
<?php echo $this->Html->css(array('styles-inner.min')); ?>

<script type="text/javascript">
$(function () {
		
    gantt.attachEvent("onLoadStart", function () {
		gantt.message("Loading...");
	});
	gantt.attachEvent("onLoadEnd", function () {
		gantt.message({
			text: "Loaded " + gantt.getTaskCount() + " tasks, " + gantt.getLinkCount() + " links",
			expire: 8 * 1000
		});
	});

	gantt.config.min_column_width = 30;
	gantt.config.scale_height = 60;
	gantt.config.work_time = true;

	gantt.config.scales = [
		{unit: "day", step: 1, format: "%d"},
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "year", step: 1, format: "%Y"}
	];


	gantt.config.row_height = 22;

	gantt.config.static_background = true;
	gantt.templates.timeline_cell_class = function (task, date) {
		if (!gantt.isWorkTime(date))
			return "week_end";
		return "";
	};
	
	var demo_tasks = {
        data: [<?php echo $data; ?>],
        links: [<?php echo $link; ?>],
    };
	
	gantt.config.show_task_cells = false;
	
	/* gantt.config.static_background = true;
	gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
	gantt.init("gantt_here");
	gantt.config.branch_loading = true;
	gantt.config.scales = [
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "week", step: 1, format: function (date) {
			return "Week #" + gantt.date.getWeek(date);
		}},
		{unit: "day", step: 1, format: "%D", css: function(date) {
		if(!gantt.isWorkTime({ date: date, unit: "day"})){
				return "weekend"
			}
		}}
	];
	gantt.config.smart_scales = true; */
	
	
	
	gantt.init("gantt_block");
	gantt.parse(demo_tasks);
});
</script>