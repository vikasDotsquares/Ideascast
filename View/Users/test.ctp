<!DOCTYPE html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Working with 30000 tasks</title>
		<script type="text/javascript" src="http://192.168.7.20/ideascast/plugins/jQuery/jQuery-2.1.3.min.js"></script>
	<script type="text/javascript" src="http://192.168.7.20/ideascast/js/jquery-ui.min.js"></script>
	<script src="http://192.168.7.20/ideascast/js/new_gantt/codebase/dhtmlxgantt.js"></script>

	<link rel="stylesheet" href="http://192.168.7.20/ideascast/js/new_gantt/codebase/dhtmlxgantt.css?v=7.0.10">
	
	<!--<script src="http://192.168.7.20/ideascast/plugins/gantt_calender/codebase/dhtmlxgantt.js"></script>

	<link rel="stylesheet" href="http://192.168.7.20/ideascast/plugins/gantt_calender/codebase/dhtmlxgantt.css?v=7.0.10">
-->
	 
	<!--<script src="http://192.168.7.20/ideascast/plugins/gantt_calender/1.js?v=7.0.10"></script>-->
	<script src="http://192.168.7.20/ideascast/plugins/gantt_calender/data_large.js?v=7.0.10"></script>
	
	

	<style>
		html, body {
			height: 100%;
			padding: 0px;
			margin: 0px;
			overflow: hidden;
		}

		.gantt_task_cell.week_end {
			background-color: #EFF5FD;
		}

		.gantt_task_row.gantt_selected .gantt_task_cell.week_end {
			background-color: #F8EC9C;
		}

	</style>

	
</head>

<body>
<div class="container" style="max-width:100% !important">
 <div class="rows" style="">
      <div class="span11 response_age">
         <div id="calendar" style="margin: 0;">  </div>
      </div>
   </div>
   <div class="row">
      <div class="col-xs-12">
         &nbsp;
      </div>
   </div>
   <div class="row" id="gantt_block" style="height:100%; ">
      <?php
	 // pr($elementsArr); die;
	 // include 'get_workspaces_by_project.ctp';
	  ?>
   </div>
</div>
<script>
    var getListItemHTML = function (type, count, active) {
        return '<li' + (active ? ' class="active"' : '') + '><a href="#">' + type + 's <span class="badge">' + count + '</span></a></li>';
    };

    var updateInfo = function () {
        var state = gantt.getState(),
                tasks = gantt.getTaskByTime(state.min_date, state.max_date),
                types = gantt.config.types,
                result = {},
                html = "",
                active = false;
        //alert(state.min_date);alert(state.max_date);
        //console.log(state.min_date+'  '+state.max_date);
        // get available types
        for (var t in types) {
            result[types[t]] = 0;
        }
        // sort tasks by type
        for (var i = 0, l = tasks.length; i < l; i++) {
            if (tasks[i].type && result[tasks[i].type] != "undefined")
                result[tasks[i].type] += 1;
            else
                result[types.task] += 1;
        }
        // render list items for each type
        for (var j in result) {
            if (j == types.task)
                active = true;
            else
                active = false;
            html += getListItemHTML(j, result[j], active);
        }

        //document.getElementById("gantt_info").innerHTML = html;

    };
 /*  var h = $('.gantt_task').height()+20;
        $('.gantt_task').css('height',h+'px');
	$('.gantt_hor_scroll').css('width',$('.gantt_task').width());
	  var ganttmode = 'year';
	 gantt.config.work_time = true;
    gantt.config.min_column_width = 50;
    gantt.config.row_height = 20;
    gantt.config.grid_width = 415;
    gantt.config.date_grid = "%d %M %y";
    gantt.config.scale_height = 55;
    gantt.config.autosize = true;
    gantt.config.gantt_task_drag = false;
	 gantt.config.sort = true;

 */

	gantt.attachEvent("onLoadStart", function () {
		gantt.message("Loading...");
	});
	gantt.attachEvent("onLoadEnd", function () {
		gantt.message({
			text: "Loaded " + gantt.getTaskCount() + " tasks, " + gantt.getLinkCount() + " links",
			expire: 8 * 1000
		});
	});

	/* gantt.config.min_column_width = 30;
	gantt.config.scale_height = 60;
	gantt.config.work_time = true;

	gantt.config.scales = [
		{unit: "day", step: 1, format: "%d"},
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "year", step: 1, format: "%Y"}
	];


	gantt.config.row_height = 22;

	gantt.config.static_background = true; */
	
	
	var ganttmode = 'year';
	 gantt.config.work_time = true;
    gantt.config.min_column_width = 50;
    gantt.config.row_height = 20;
    gantt.config.grid_width = 415;
    gantt.config.date_grid = "%d %M %y";
    gantt.config.scale_height = 55;
    gantt.config.autosize = false;
    gantt.config.gantt_task_drag = false;
	 gantt.config.sort = false;
	
	gantt.templates.timeline_cell_class = function (task, date) {
		if (!gantt.isWorkTime(date))
			return "week_end";
		return "";
	};

	gantt.init("gantt_block");
	gantt.parse(taskData);
	
	
	 

   // gantt.parse(demo_tasks);


	// console.log(gantt);
	 //console.log(gantt);

    updateInfo();


</script>
<?php
   echo $this->Html->css(array('styles-inner.min'));
   echo $this->Html->css(array('projects/custom'));
   echo $this->Html->css('projects/bs-selectbox/bootstrap-select.min');
   echo $this->Html->css('projects/bootstrap-input');
   echo $this->Html->css('projects/manage_elements');

   echo $this->Html->script('projects/plugins/selectbox/bootstrap-select', array('inline' => true));
 

?>
<style>
.gantt_grid_scale .gantt_grid_head_cell {
color:#a6a6a6;
border-top:none!important;
border-right:none!important;
}



.gantt_grid_data .gantt_cell {
border-right: 1px solid #cecece;
color:#454545;
}

.gantt_task_link .gantt_link_arrow_right {
margin-top:-3px;
border-width:6px;
}

.gantt_task_link .gantt_link_arrow_left {
margin-left:-6px;
margin-top:-3px;
border-width:6px;
}

.gantt_task_link .gantt_link_arrow_down,.gantt_task_link .gantt_link_arrow_up {
border-width:6px;
}

.gantt_task_line .gantt_task_progress_drag {
bottom:-4px;
height:16px;
margin-left:-8px;
width:16px;
}

.gantt_hor_scroll{position:absolute ; top:-20px;}

.gantt_task .gantt_task_scale .gantt_scale_cell {
color:#a6a6a6;
border-right:1px solid #ebebeb;
}

.gantt_row.gantt_project,.gantt_row.odd.gantt_project {
background-color:#edffef;
}

.gantt_task_row.gantt_project,.gantt_task_row.odd.gantt_project {
background-color:#f5fff6;
}

.gantt_task_line.gantt_project {
background-color:#65c16f;
border:1px solid #3c9445;
}

.gantt_task_line.gantt_project .gantt_task_progress {
background-color:#46ad51;
}

.buttonBg {
background:#fff;
}

.gantt_cal_light .gantt_btn_set {
margin:5px 10px;
}

.gantt_btn_set.gantt_cancel_btn_set {
background:#fff;
color:#454545;
border:1px solid #cecece;
}

.gantt_btn_set.gantt_save_btn_set {
background:#3db9d3;
text-shadow:0 -1px 0 #248a9f;
color:#fff;
}

.gantt_btn_set.gantt_delete_btn_set {
background:#ec8e00;
text-shadow:0 -1px 0 #a60;
color:#fff;
}

.gantt_cal_light_wide {
width:580px;
padding:2px 0!important;
}

.gantt_cal_light_wide .gantt_cal_larea {
border-left:none!important;
border-right:none!important;
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
border:1px solid #cecece;
width:100%;
padding:0 10px;
}

.dhtmlx_popup_button.dhtmlx_cancel_button {
font-weight:700;
color:#454544;
}

.gantt_qi_big_icon.icon_edit {
color:#454545;
background:#fff;
}

.gantt_qi_big_icon.icon_delete {
text-shadow:0 -1px 0 #a60;
background:#ec8e00;
color:#fff;
border-width:0;
}

.gantt_container {
font-family:Arial;
font-size:13px;
border:1px solid #cecece;
position:relative;
white-space:nowrap;
}

.gantt_grid {
border-right:1px solid #cecece;
}

.gantt_task_scroll {
overflow-x:scroll;
}

.gantt_task {
position:relative;
}

.gantt_grid,.gantt_task {
overflow-x:hidden;
overflow-y:hidden;
display:inline-block;
vertical-align:top;
}

.gantt_grid_scale,.gantt_task_scale {
color:#6b6b6b;
font-size:12px;
border-bottom:1px solid #cecece;
background-color:#fff;
font-weight: bold;
}

.gantt_scale_line {
box-sizing:border-box;
-moz-box-sizing:border-box;
border-top:1px solid #cecece;
clear:both;
}

.gantt_scale_line:first-child {
border-top:none;
}

.gantt_grid_head_cell {
display:inline-block;
vertical-align:top;
border-right:1px solid #cecece;
text-align:center;
position:relative;
cursor:default;
height:100%;
-moz-user-select:0;
-webkit-user-select:none;
-user-select:none;
overflow:hidden;
}

.gantt_grid_data {
width:100%;
overflow:hidden;
}

.gantt_row {
position:relative;
-webkit-user-select:none;
-moz-user-select:0;
}

.gantt_add,.gantt_grid_head_add {
width:100%;
height:100%;
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTQ3MjMyMENDNkI0MTFFMjk4MTI5QTg3MDhFNDVDQTkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTQ3MjMyMERDNkI0MTFFMjk4MTI5QTg3MDhFNDVDQTkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo1NDcyMzIwQUM2QjQxMUUyOTgxMjlBODcwOEU0NUNBOSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo1NDcyMzIwQkM2QjQxMUUyOTgxMjlBODcwOEU0NUNBOSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PshZT8UAAABbSURBVHjaYrTdeZmBEsCER+4wEP+H4sPkGGCDg020ARR7gb4GIAcYDKMDdPnDyAbYkGG5DVW9cIQMvUdBBAuUY4vDz8iAcZinA2zgCHqAYQMseAywJcYFAAEGAM+UFGuohFczAAAAAElFTkSuQmCC);
background-position:center center;
background-repeat:no-repeat;
cursor:pointer;
position:relative;
-moz-opacity:.3;
opacity:.3;
}

.gantt_grid_head_cell.gantt_grid_head_add {
-moz-opacity:.6;
opacity:.6;
top:0;
}

.gantt_row,.gantt_task_row {
border-bottom:1px solid #ebebeb;
background-color:#fff;
}

.gantt_cell,.gantt_grid_head_cell,.gantt_row,.gantt_scale_cell,.gantt_task_cell,.gantt_task_row {
box-sizing:border-box;
-moz-box-sizing:border-box;
}

.gantt_grid_head_cell,.gantt_scale_cell {
line-height:inherit;
}

.gantt_grid_scale .gantt_grid_column_resize_wrap {
cursor:col-resize;
position:absolute;
width:13px;
}

.gantt_grid_column_resize_wrap .gantt_grid_column_resize {
background-color:#cecece;
height:100%;
width:1px;
margin:0 auto;
}

.gantt_grid .gantt_grid_resize_wrap {
cursor:col-resize;
position:absolute;
width:13px;
z-index:1;
}

.gantt_grid_resize_wrap .gantt_grid_resize {
background-color:#cecece;
width:1px;
margin:0 auto;
}

.gantt_drag_marker.gantt_grid_resize_area {
background-color:rgba(231,231,231,.5);
border-left:1px solid #cecece;
border-right:1px solid #cecece;
height:100%;
width:100%;
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
}

.gantt_cell {
display:inline-block;
vertical-align:top;
border-right:1px solid #ebebeb;
padding-left:6px;
padding-right:6px;
height:100%;
overflow:hidden;
white-space:nowrap;
font-size:13px;
}

.gantt_grid_data .gantt_last_cell,.gantt_grid_scale .gantt_last_cell,.gantt_task_bg .gantt_last_cell,.gantt_task_scale .gantt_last_cell {
border-right-width:0;
}

.gantt_task_bg {
overflow:hidden;
}

.gantt_scale_cell {
display:inline-block;
white-space:nowrap;
overflow:hidden;
border-right:1px solid #cecece;
text-align:center;
height:100%;
}

.gantt_task_cell {
display:inline-block;
height:100%;
border-right:1px solid #ebebeb;
}

.gantt_ver_scroll {
width:0;
background-color:transparent;
height:1px;
overflow-x:hidden;
overflow-y:scroll;
display:none;
position:absolute;
right:0;
}

.gantt_ver_scroll>div {
width:1px;
height:1px;
}

.gantt_hor_scroll {
height:0;
background-color:transparent;
width:100%;
clear:both;
overflow-x:scroll;
overflow-y:hidden;
display:none;
}

.gantt_hor_scroll>div {
width:5000px;
height:1px;
}

.gantt_tree_indent {
width:15px;
height:100%;
display:inline-block;
}

.gantt_tree_content,.gantt_tree_icon {
vertical-align:top;
}

.gantt_tree_icon {
width:28px;
height:100%;
display:inline-block;
background-repeat:no-repeat;
background-position:center center;
text-align:center;
}
.icon_element_add_black{
margin:2px 0;

}

.gantt_tree_icon .icon_element_add_black{
margin:0;
top: -1px;
position: relative;
background: none !important;
	}

.gantt_grid_head_cell span{ padding:0 3px}

.gantt_tree_content {
height:100%;
display:inline-block;
}

.gantt_tree_icon.gantt_open {
background-image:url(data:image/gif;base64,R0lGODlhEgASALMJAMrKyt3d3ejp6d7f3+/v75aWlvf39////wAAAP///wAAAAAAAAAAAAAAAAAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6M0I5RTczQjVDMDdBMTFFMTgxRjc4Mzk4M0Q3MjVFQzAiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6M0I5RTczQjZDMDdBMTFFMTgxRjc4Mzk4M0Q3MjVFQzAiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozQjlFNzNCM0MwN0ExMUUxODFGNzgzOTgzRDcyNUVDMCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozQjlFNzNCNEMwN0ExMUUxODFGNzgzOTgzRDcyNUVDMCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAkALAAAAAASABIAAARJMMlJq704661B+SAIXAVhnKhBFKSZnmuLImhslXPN3ibi+6pdBXc4IIpB2YkGE1IKAoL0ICUInJNCYMDtDgJYiScUGnHO6LQkAgA7);
width:18px;
cursor:pointer;
}

.gantt_tree_icon.gantt_close {
background-image:url(data:image/gif;base64,R0lGODlhEgASALMJAMrKyt3d3ejp6d7f3+/v75aWlvf39wAAAP///////wAAAAAAAAAAAAAAAAAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzY0QzNGM0VDMDdBMTFFMUE3MDlCNUM2QjU1NDA5RjgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzY0QzNGM0ZDMDdBMTFFMUE3MDlCNUM2QjU1NDA5RjgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNjRDM0YzQ0MwN0ExMUUxQTcwOUI1QzZCNTU0MDlGOCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozNjRDM0YzREMwN0ExMUUxQTcwOUI1QzZCNTU0MDlGOCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAkALAAAAAASABIAAARDMMlJq704661B+SAIXAVhnKhBFKSZnmv7wqxVzmpd3Uff5zKEUAi0uV4xm4DAbBIEOkohMKhaB4HoxBMKjTjgsFgSAQA7);
width:18px;
cursor:pointer;
}

.gantt_tree_icon.gantt_blank {
/* width:18px; */
width:10px;
}

.gantt_tree_icon.gantt_folder_open {
background-image:url(data:image/gif;base64,R0lGODlhEgASAJECAJeXl7Gvrf///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTdDRDM3QzVDMDZEMTFFMUJGMzhFMDhCN0RGRjBGQ0YiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTdDRDM3QzZDMDZEMTFFMUJGMzhFMDhCN0RGRjBGQ0YiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo1N0NEMzdDM0MwNkQxMUUxQkYzOEUwOEI3REZGMEZDRiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo1N0NEMzdDNEMwNkQxMUUxQkYzOEUwOEI3REZGMEZDRiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAIALAAAAAASABIAAAIzlI+pywcPm3mhWgkCsjBOvVkimElG9ZlCBlXd+2XjjLKg5GqoeZXqvsOQXK/ijUZTKVUFADs=);
}

.gantt_tree_icon.gantt_folder_closed {
background-image:url(data:image/gif;base64,R0lGODlhEgASAJECAJeXl7Gvrf///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTAyMTU1RTNDMDZEMTFFMUJGNzZCRThBRkFCRjg4MTIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTAyMTU1RTRDMDZEMTFFMUJGNzZCRThBRkFCRjg4MTIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo1MDIxNTVFMUMwNkQxMUUxQkY3NkJFOEFGQUJGODgxMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo1MDIxNTVFMkMwNkQxMUUxQkY3NkJFOEFGQUJGODgxMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAIALAAAAAASABIAAAIwlI+pywcPm3mhWgkCsjBOvVkimElG9ZlCuYIY6TYs+6bmHDO4igfdD3GNhheV0VQAADs=);
}

.gantt_tree_icon.gantt_file {
background-image:url(data:image/gif;base64,R0lGODlhEgASAJECAJeXl7Gvrf///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQzI4RjZDMDZEMTFFMTgwRjhBQURDQzI3NDU3QUEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQzI4RjdDMDZEMTFFMTgwRjhBQURDQzI3NDU3QUEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3OTFDMjhGNEMwNkQxMUUxODBGOEFBRENDMjc0NTdBQSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3OTFDMjhGNUMwNkQxMUUxODBGOEFBRENDMjc0NTdBQSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAIALAAAAAASABIAAAIylI+pwN16QJiUQiFThRlJm3RRFYSlR5qXMKmXaMDuuMoyOi8n/e6xn8NMHETgh5RaKQsAOw==);
}

.gantt_grid_head_cell .gantt_sort {
    position: absolute;
    right: 5px;
    top: 21px;
    width: 7px;
    height: 13px;
    background-repeat: no-repeat;
    background-position: center center;
}

.gantt_grid_head_cell .gantt_sort.gantt_asc {
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAANCAYAAABlyXS1AAAARUlEQVR4nGNgQAKGxib/GbABkIS7b8B/DAUwCRiGK0CXwFBAb1DfP/U/LszwHwi2X7qFgUEArBtdAVwCBmAKMCSQFSDzAWXXaOHsXeqkAAAAAElFTkSuQmCC);
}

.gantt_grid_head_cell .gantt_sort.gantt_desc {
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAANCAYAAABlyXS1AAAARUlEQVR42mNgQAL1/VP/M2ADIIntF2/9x1AAlrh0C47hCmA60DFYwX88gIFGwNDY5D8uDFbg7hvwHx2jmIBTAlkB0e4BAEjlaNtBWJPnAAAAAElFTkSuQmCC);
}

.gantt_deleted {
text-decoration:line-through;
}

.gantt_invalid {
background-color:FFE0E0;
}

.gantt_error {
color:red;
}

.gantt_status {
right:1px;
background:rgba(155,155,155,.1);
position:absolute;
top:1px;
-webkit-transition:opacity .2s;
transition:opacity .2s;
opacity:0;
padding:5px 10px;
}

#gantt_ajax_dots span {
-webkit-transition:opacity .2s;
transition:opacity .2s;
background-repeat:no-repeat;
opacity:0;
}

.dhtmlx_message_area {
position:fixed;
right:5px;
width:250px;
z-index:1000;
}

.dhtmlx-info {
min-width:120px;
font-family:Arial;
z-index:10000;
-webkit-transition:all .5s ease;
-moz-transition:all .5s ease;
-o-transition:all .5s ease;
transition:all .5s ease;
margin:5px 5px 10px;
}

.dhtmlx-info.hidden {
height:0;
overflow:hidden;
border-width:0;
margin:0;
padding:0;
}

.dhtmlx_modal_box {
overflow:hidden;
display:inline-block;
min-width:250px;
width:250px;
text-align:center;
position:fixed;
z-index:20000;
box-shadow:3px 3px 3px rgba(0,0,0,.07);
font-family:Arial;
border-radius:6px;
border:1px solid #cecece;
background:#fff;
}

.dhtmlx_popup_title {
border-top-left-radius:6px;
border-top-right-radius:6px;
color:#fff;
text-shadow:1px 1px #000;
height:40px;
line-height:40px;
font-size:20px;
border-width:0;
}

.dhtmlx_button,.dhtmlx_popup_button {
border:1px solid #cecece;
height:30px;
line-height:30px;
display:inline-block;
border-radius:4px;
background:#fff;
margin:0 5px;
}

.dhtmlx-info,.dhtmlx_button,.dhtmlx_popup_button {
user-select:none;
-webkit-user-select:none;
-moz-user-select:0;
cursor:pointer;
}

.dhtmlx_popup_text {
overflow:hidden;
font-size:14px;
color:#000;
min-height:30px;
border-radius:6px;
margin:15px 15px 5px;
}

.dhtmlx_popup_controls {
border-radius:6px;
padding:10px;
}

.dhtmlx_popup_button {
min-width:100px;
}

div.dhx_modal_cover {
background-color:#000;
cursor:default;
filter:alpha(opacity=20);
opacity:.2;
position:fixed;
z-index:19999;
left:0;
top:0;
width:100%;
height:100%;
border:none;
zoom:1;
}

.dhtmlx-info img,.dhtmlx_modal_box img {
float:left;
margin-right:20px;
}

.dhtmlx-alert-error,.dhtmlx-confirm-error {
border:1px solid red;
}

.dhtmlx_button input,.dhtmlx_popup_button div {
border-radius:4px;
font-size:14px;
-moz-box-sizing:content-box;
box-sizing:content-box;
vertical-align:top;
margin:0;
padding:0;
}

.dhtmlx-error,.dhtmlx-info {
font-size:14px;
color:#000;
box-shadow:3px 3px 3px rgba(0,0,0,.07);
background-color:#FFF;
border-radius:3px;
border:1px solid #fff;
padding:0;
}

.dhtmlx-info div {
background-color:#fff;
border-radius:3px;
border:1px solid #cecece;
padding:5px 10px;
}

.dhtmlx-error {
background-color:#d81b1b;
border:1px solid #ff3c3c;
box-shadow:3px 3px 3px rgba(0,0,0,.07);
}

.dhtmlx-error div {
background-color:#d81b1b;
border:1px solid #940000;
color:#FFF;
}

.gantt_data_area div,.gantt_grid div {
-ms-touch-action:none;
-webkit-tap-highlight-color:transparent;
}

.gantt_data_area {
position:relative;
overflow-x:hidden;
overflow-y:hidden;
-moz-user-select:0;
-webkit-user-select:none;
-user-select:none;
}

.gantt_links_area {
position:absolute;
left:0;
top:0;
}

.gantt_side_content,.gantt_task_content,.gantt_task_progress {
line-height:inherit;
overflow:hidden;
height:100%;
}

.gantt_task_content {
    font-size: 12px;
    color: #fff;
    width: 100%;
    top: 0;
    position: absolute;
    white-space: nowrap;
    text-align: center;
    margin: :auto;
    margin: 0 0 0 70px;
}

.gantt_task_progress {
text-align:center;
z-index:0;
background:#299cb4;
}

.gantt_task_line {
-webkit-border-radius:2px;
-moz-border-radius:2px;
border-radius:2px;
position:absolute;
-moz-box-sizing:border-box;
box-sizing:border-box;
/*background-color:#3db9d3;*/
border:1px solid #2898b0;
-webkit-user-select:none;
-moz-user-select:0;
cursor: default;
}

.gantt_task_line.gantt_drag_move div {
cursor:move;
}

.panel-heading{ padding: inherit;}


.gantt_touch_move,.gantt_touch_progress .gantt_touch_resize {
-moz-transform:scale(1.02,1.1);
-o-transform:scale(1.02,1.1);
-webkit-transform:scale(1.02,1.1);
transform:scale(1.02,1.1);
-moz-transform-origin:50%;
-o-transform-origin:50%;
-webkit-transform-origin:50%;
transform-origin:50%;
}

.gantt_touch_progress .gantt_task_progress_drag,.gantt_touch_resize .gantt_task_drag {
-moz-transform:scaleY(1.3);
-o-transform:scaleY(1.3);
-webkit-transform:scaleY(1.3);
transform:scaleY(1.3);
-moz-transform-origin:50%;
-o-transform-origin:50%;
-webkit-transform-origin:50%;
transform-origin:50%;
}

.gantt_side_content {
position:absolute;
white-space:nowrap;
color:#6e6e6e;
bottom:7px;
font-size:11px;
display:none;
}

.gantt_side_content.gantt_left {
right:100%;
padding-right:15px;
}

.gantt_side_content.gantt_right {
left:100%;
padding-left:15px;
}

.gantt_side_content.gantt_link_crossing {
bottom:8.75px;
}

.gantt_link_arrow,.gantt_task_link .gantt_line_wrapper {
position:absolute;
cursor:default;
}

.gantt_line_wrapper div {
background-color:#000;
}

.gantt_task_link:hover .gantt_line_wrapper div {
box-shadow:0 0 1px 0 #000;
}

.gantt_task_link div.gantt_link_arrow {
background-color:transparent;
width:0;
height:0;
border-style:solid;
}

.gantt_link_control {
position:absolute;
width:13px;
top:0;
}

.gantt_link_control div {
display:none;
cursor:pointer;
box-sizing:border-box;
position:relative;
top:50%;
margin-top:-7.5px;
vertical-align:middle;
border:1px solid #929292;
-webkit-border-radius:6.5px;
-moz-border-radius:6.5px;
border-radius:6.5px;
height:13px;
width:13px;
background-color:#f0f0f0;
}

.gantt_link_control.task_left {
left:-13px;
}

.gantt_link_control.task_right {
right:-13px;
}

.gantt_link_source,.gantt_link_target {
box-shadow:0 0 3px #3db9d3;
}

.gantt_link_target.link_finish_allow,.gantt_link_target.link_start_allow {
box-shadow:0 0 3px #ffbf5e;
}

.gantt_link_target.link_finish_deny,.gantt_link_target.link_start_deny {
box-shadow:0 0 3px #e87e7b;
}

.link_finish_allow .gantt_link_control.task_right div,.link_start_allow .gantt_link_control.task_left div {
background-color:#ffbf5e;
border-color:#ffa011;
}

.link_finish_deny .gantt_link_control.task_right div,.link_start_deny .gantt_link_control.task_left div {
background-color:#e87e7b;
border-color:#dd3e3a;
}

.gantt_link_arrow_right {
margin-top:-1px;
border-color:transparent transparent transparent #000 !important;
border-width:4px 0 4px 6px;
}

.gantt_link_arrow_left {
margin-top:-1px;
border-color:transparent #000 transparent transparent !important;
border-width:4px 6px 4px 0;
}

.gantt_link_arrow_up {
border-color:transparent transparent #ffa011!important;
border-width:0 4px 6px;
}

.gantt_link_arrow_down {
border-color:#ffa011 transparent transparent!important;
border-width:4px 6px 0 4px;
}

.gantt_task_drag,.gantt_task_progress_drag {
/*cursor:w-resize;
height:100%;
display:none;
position:absolute;*/
}

.gantt_task_drag {
width:6px;
background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAYAAAACCAYAAAB7Xa1eAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUh2QYDDjkw3UJvAwAAABRJREFUCNdj/P//PwM2wASl/6PTAKrrBf4+lD8LAAAAAElFTkSuQmCC);
z-index:1;
top:0;
}

.gantt_task_drag.task_left {
left:0;
}

.gantt_task_drag.task_right {
right:0;
}

.gantt_task_progress_drag {
height:8px;
width:8px;
bottom:-4px;
margin-left:-4px;
background-position:bottom;
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAYAAAB24g05AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MkY3Rjk0RUVDMkYzMTFFMkI1OThEQTA3ODU0OTkzMEEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkY3Rjk0RUZDMkYzMTFFMkI1OThEQTA3ODU0OTkzMEEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyRjdGOTRFQ0MyRjMxMUUyQjU5OERBMDc4NTQ5OTMwQSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyRjdGOTRFREMyRjMxMUUyQjU5OERBMDc4NTQ5OTMwQSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PobPBzIAAADkSURBVHjaYpk2bRoDDsAExL1QdjEQ/8OmiAWHZk4gXqymqhQM4ty6fU8OSMUA8XdiDBAB4k0a6iqWRga6EKcwMQXduHlnL5DpB8Rv0J2JDFSA+JiOtgZcMwiA2CAxkBxUDVYDLEAKgIpV9XQ0MZwFEgPJAZnHoWpRDAgC4n2W5saiQKfjClQGkBxQDciL+6B6wAbkA/EqJwdrTkUFOQZCAKQGpBbIXA3SCzJggo+XK7OEuBgDsQCkFqgHrBfsBT5eHgZSAUwP2IBfv36TbABMDygdtK1Zv6UESLORaAbIhG6AAAMAKN8wE24DXWcAAAAASUVORK5CYII=);
background-repeat:no-repeat;
z-index:2;
}

.gantt_link_tooltip {
box-shadow:3px 3px 3px #888;
background-color:#fff;
border-left:1px dotted #cecece;
border-top:1px dotted #cecece;
font-family:Tahoma;
font-size:8pt;
color:#444;
line-height:20px;
padding:6px;
}

.gantt_link_direction {
height:0;
border:0 #ffa011;
border-bottom-style:dashed;
border-bottom-width:2px;
transform-origin:0 0;
-ms-transform-origin:0 0;
-webkit-transform-origin:0 0;
z-index:2;
margin-left:1px;
position:absolute;
}

.gantt_task_row.gantt_selected .gantt_task_cell {
border-right-color:#ffec6e;
}

.gantt_task_line.gantt_selected {
box-shadow:0 0 5px #299cb4;
}

.gantt_task_line.gantt_project.gantt_selected {
box-shadow:0 0 5px #46ad51;
}

.gantt_task_line.gantt_milestone {
visibility:hidden;
background-color:#d33daf;
border:0 solid #61164f;
box-sizing:content-box;
-moz-box-sizing:content-box;
}

.gantt_task_line.gantt_milestone div {
visibility:visible;
}

.gantt_task_line.gantt_milestone .gantt_task_content {
background:inherit;
border:inherit;
border-radius:inherit;
box-sizing:border-box;
-moz-box-sizing:border-box;
-webkit-transform:rotate(45deg);
-moz-transform:rotate(45deg);
-ms-transform:rotate(45deg);
-o-transform:rotate(45deg);
transform:rotate(45deg);
border-width:1px;
}

.gantt_task_line.gantt_task_inline_color {
border-color:#999;
}

.gantt_task_line.gantt_task_inline_color .gantt_task_progress {
background-color:#363636;
opacity:.2;
}

.gantt_task_line.gantt_task_inline_color.gantt_project.gantt_selected,.gantt_task_line.gantt_task_inline_color.gantt_selected {
box-shadow:0 0 5px #999;
}

.gantt_task_link.gantt_link_inline_color:hover .gantt_line_wrapper div {
box-shadow:0 0 5px 0 #999;
}

.gantt_critical_task {
background-color:#e63030;
border-color:#9d3a3a;
}

.gantt_critical_task .gantt_task_progress {
background-color:rgba(0,0,0,.4);
}

.gantt_critical_link .gantt_line_wrapper>div {
background-color:#e63030;
}

.gantt_critical_link .gantt_link_arrow {
border-color:#e63030;
}

.gantt_unselectable,.gantt_unselectable div {
-webkit-user-select:none;
-moz-user-select:0;
}

.gantt_cal_light {
-webkit-tap-highlight-color:transparent;
background:#fff;
border-radius:6px;
font-family:Arial;
border:1px solid #cecece;
color:#6b6b6b;
font-size:12px;
position:absolute;
z-index:10001;
width:550px;
height:250px;
box-shadow:3px 3px 3px rgba(0,0,0,.07);
}

.gantt_cal_light select {
font-family:Arial;
border:1px solid #cecece;
font-size:13px;
margin:0;
padding:2px;
}

.gantt_cal_ltitle {
overflow:hidden;
white-space:nowrap;
-webkit-border-radius:6px 6px 0 0;
-moz-border-radius-topleft:6px;
-moz-border-radius-bottomleft:0;
-moz-border-radius-topright:6px;
-moz-border-radius-bottomright:0;
border-radius:6px 6px 0 0;
padding:7px 10px;
}

.gantt_cal_ltitle span {
white-space:nowrap;
}

.gantt_cal_lsection {
color:#727272;
font-weight:700;
font-size:13px;
padding:12px 0 5px 10px;
}

.gantt_cal_lsection .gantt_fullday {
float:right;
margin-right:5px;
font-size:12px;
font-weight:400;
line-height:20px;
vertical-align:top;
cursor:pointer;
}

.gantt_cal_ltext {
overflow:hidden;
padding:2px 10px;
}

.gantt_cal_ltext textarea {
overflow:auto;
font-family:Arial;
font-size:13px;
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
border:1px solid #cecece;
height:100%;
width:100%;
outline:0!important;
resize:none;
}

.gantt_cal_light .gantt_title {
padding-left:10px;
}

.gantt_cal_larea {
border:1px solid #cecece;
border-left:none;
border-right:none;
background-color:#fff;
overflow:hidden;
height:1px;
}

.gantt_btn_set {
float:left;
-webkit-border-radius:4px;
-moz-border-radius:4px;
border-radius:4px;
height:32px;
font-weight:700;
background:#fff;
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
cursor:pointer;
border-color:#cecece;
border-style:solid;
border-width:0;
margin:10px 7px 5px 10px;
padding:5px 15px 5px 10px;
}

.gantt_btn_set div {
float:left;
font-size:13px;
height:22px;
line-height:22px;
background-repeat:no-repeat;
vertical-align:middle;
}

.gantt_save_btn {
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTk1OUU5RDFDMzA0MTFFMkExMUZBQTdDNDAzOUE5RjMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTk1OUU5RDJDMzA0MTFFMkExMUZBQTdDNDAzOUE5RjMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxOTU5RTlDRkMzMDQxMUUyQTExRkFBN0M0MDM5QTlGMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxOTU5RTlEMEMzMDQxMUUyQTExRkFBN0M0MDM5QTlGMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PjDroXYAAAEXSURBVHjaYvz//z8DJYCRUgPIAUxAbAnEHiAHMIBcQCwGaRYXF3e6evXqoffv39/dv38/CymaGSUkJBzv3LlzCsj///fv3wdAihkkIQnEvkAshU8zLy+v7a1bt06ANP/79+87kDIAy505cybq06dPr3p7ezuwGQLTfOPGjWP/ESAZLg8kPKBO+g01RBJNszWyZqC6uSgWgIg/f/4shxnS2dnZBjMEqNkSFGBImi8CKTYMA4BYCGjIczRDHC5dunQQSfN7IKWI4UUkjjdMMdCwnw8ePLjwHxV4Yw1gZA5Q47z/2EELzhhCE+ABGvIQWSeQvwcU38QaAML2wHj+C/X3MyAlijeB4ZBoBOIPQGxJKIVSnBsBAgwABddBclWfcZUAAAAASUVORK5CYII=);
margin-top:2px;
width:21px;
}

.gantt_cancel_btn {
margin-top:2px;
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MDkzMDA3MzlDMzA0MTFFMjg2QTVFMzFEQzgwRkJERDYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDkzMDA3M0FDMzA0MTFFMjg2QTVFMzFEQzgwRkJERDYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowOTMwMDczN0MzMDQxMUUyODZBNUUzMURDODBGQkRENiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowOTMwMDczOEMzMDQxMUUyODZBNUUzMURDODBGQkRENiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmYuYOUAAAEdSURBVHjaYvz//z8DJYAFXWDlypU8QKoIiD2A2AwqfAqIdwBxX3h4+Bdk9YzILgBqtgdS84FYEYeF94E4EWjIQZgAE5LmQCB1AKoZZKMPEAtAMYh9GSp3AKjWD8UFQAEhIPshEIOc3wHENUBb/qJ57SyQMoJyPwKxElDNO1gYFEE17wMKVmIJlzNQzeegrjaA6qmBecEbSvfh0GwMxGeBhoPoemQ9MAO0kEIbl2YTqPAFKK2IbMB3AjabYIkRZmQD7kNpMyI0G0PpO8gGbIUFJj7NQDk2INWIrIcJKfBAKcwJqvkcDs0TgFgXGo19KCkRmpDWQdWDEk0NUoCBoq0FqhkE/IEWbKJKUmZEz43QzFSKIzN1481M5ACAAAMAlfl/lCwRpagAAAAASUVORK5CYII=);
width:20px;
}

.gantt_delete_btn {
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjFENzI3NUNDMzA0MTFFMjhBNjJGQTc3MUIyQzYzNEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjFENzI3NURDMzA0MTFFMjhBNjJGQTc3MUIyQzYzNEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMUQ3Mjc1QUMzMDQxMUUyOEE2MkZBNzcxQjJDNjM0RiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMUQ3Mjc1QkMzMDQxMUUyOEE2MkZBNzcxQjJDNjM0RiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmUD0gAAAABvSURBVHjaYvz//z8DIyMjAxYQicReji4J0ofKQNP8HwmgGQbXB8IsWGwDSSwDuioKjY9uBthVjFAXYHUGAQA2kYmBUoAUBpGk0LAwgBvwH+YX4mkwptgLowYMRgOITUyYKRFIN/wnDjQgJySAAAMApryKzL8wjfUAAAAASUVORK5CYII=);
margin-top:2px;
width:20px;
}

.gantt_cal_cover {
width:100%;
height:100%;
position:absolute;
z-index:10000;
top:0;
left:0;
background-color:#000;
opacity:.1;
filter:alpha(opacity=10);
}

.gantt_custom_button {
font-family:Arial;
font-size:13px;
font-weight:400;
margin-right:10px;
margin-top:-5px;
cursor:pointer;
float:right;
height:21px;
width:90px;
border:1px solid #CECECE;
text-align:center;
-webkit-border-radius:4px;
-moz-border-radius:4px;
-ms-border-radius:4px;
-o-border-radius:4px;
border-radius:4px;
padding:0 3px;
}

.gantt_custom_button div {
cursor:pointer;
float:none;
height:21px;
line-height:21px;
vertical-align:middle;
}

.gantt_cal_light_wide .gantt_cal_lsection {
border:0;
float:left;
text-align:right;
width:80px;
height:20px;
padding:5px 10px 0 0;
}

.gantt_cal_light_wide .gantt_wrap_section {
position:relative;
overflow:hidden;
border-bottom:1px solid #ebebeb;
padding:10px 0;
}

.gantt_cal_light_wide .gantt_section_time {
overflow:hidden;
padding-top:2px!important;
padding-right:0;
height:20px!important;
background:0 0;
}

.gantt_cal_light_wide .gantt_cal_ltext {
padding-right:0;
}

.gantt_cal_light_wide .gantt_cal_checkbox label {
padding-left:0;
}

.gantt_cal_light_wide .gantt_cal_lsection .gantt_fullday {
float:none;
margin-right:0;
font-weight:700;
cursor:pointer;
}

.gantt_cal_light_wide .gantt_custom_button {
position:absolute;
top:0;
right:0;
margin-top:2px;
}

.gantt_cal_light_wide .gantt_repeat_right {
margin-right:55px;
}

.gantt_cal_light_wide.gantt_cal_light_full {
width:738px;
}

.gantt_cal_wide_checkbox input {
margin-top:8px;
margin-left:14px;
}

.gantt_section_time {
background-color:#fff;
white-space:nowrap;
padding:2px 10px 5px!important;
}

.gantt_section_time .gantt_time_selects {
float:left;
height:25px;
}

.gantt_section_time .gantt_time_selects select {
height:23px;
border:1px solid #cecece;
padding:2px;
}

.gantt_duration {
width:100px;
height:23px;
float:left;
white-space:nowrap;
margin-left:20px;
line-height:23px;
}

.gantt_duration .gantt_duration_dec,.gantt_duration .gantt_duration_inc,.gantt_duration .gantt_duration_value {
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
text-align:center;
vertical-align:top;
height:100%;
border:1px solid #cecece;
}

.gantt_duration .gantt_duration_value {
width:40px;
border-left-width:0;
border-right-width:0;
padding:3px 4px;
}

.gantt_duration .gantt_duration_dec,.gantt_duration .gantt_duration_inc {
width:20px;
background:#fff;
padding:1px 1px 3px;
}

.gantt_duration .gantt_duration_dec {
-moz-border-top-left-radius:4px;
-moz-border-bottom-left-radius:4px;
-webkit-border-top-left-radius:4px;
-webkit-border-bottom-left-radius:4px;
border-top-left-radius:4px;
border-bottom-left-radius:4px;
}

.gantt_duration .gantt_duration_inc {
margin-right:4px;
-moz-border-top-right-radius:4px;
-moz-border-bottom-right-radius:4px;
-webkit-border-top-right-radius:4px;
-webkit-border-bottom-right-radius:4px;
border-top-right-radius:4px;
border-bottom-right-radius:4px;
}

.gantt_cal_quick_info {
border:1px solid #cecece;
border-radius:6px;
position:absolute;
z-index:300;
box-shadow:3px 3px 3px rgba(0,0,0,.07);
background-color:#fff;
width:300px;
transition:left .5s ease,right .5s;
-moz-transition:left .5s ease,right .5s;
-webkit-transition:left .5s ease,right .5s;
-o-transition:left .5s ease,right .5s;
}

.gantt_no_animate {
transition:none;
-moz-transition:none;
-webkit-transition:none;
-o-transition:none;
}

.gantt_cal_quick_info.gantt_qi_left .gantt_qi_big_icon {
float:right;
}

.gantt_cal_qi_title {
-webkit-border-radius:6px 6px 0 0;
-moz-border-radius-topleft:6px;
-moz-border-radius-bottomleft:0;
-moz-border-radius-topright:6px;
-moz-border-radius-bottomright:0;
border-radius:6px 6px 0 0;
color:#454545;
background-color:#fff;
border-bottom:1px solid #cecece;
padding:5px 0 8px 12px;
}

.gantt_cal_qi_tdate {
font-size:14px;
font-weight:700;
}

.gantt_cal_qi_content {
font-size:13px;
color:#454545;
overflow:hidden;
padding:16px 8px;
}

.gantt_cal_qi_controls {
-webkit-border-radius:0 0 6px 6px;
-moz-border-radius-topleft:0;
-moz-border-radius-bottomleft:6px;
-moz-border-radius-topright:0;
-moz-border-radius-bottomright:6px;
border-radius:0 0 6px 6px;
padding-left:7px;
}

.gantt_cal_qi_controls .gantt_menu_icon {
margin-top:6px;
background-repeat:no-repeat;
}

.gantt_cal_qi_controls .gantt_menu_icon.icon_edit {
width:20px;
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAB3RJTUUh2QYFCjI5ZQj5bAAAAFNJREFUOMvt0zEOACAIA0DkwTymH8bJTRTKZGJXyaWEKPKTCQAH4Ls37cItcDUzsxHNDLZNhCq7Gt1wh9ErV7EjyGAhyGLphlnsClWuS32rn0czAV+vNGrM/LBtAAAAAElFTkSuQmCC);
}

.gantt_cal_qi_controls .gantt_menu_icon.icon_delete {
width:20px;
background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjFENzI3NUNDMzA0MTFFMjhBNjJGQTc3MUIyQzYzNEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjFENzI3NURDMzA0MTFFMjhBNjJGQTc3MUIyQzYzNEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMUQ3Mjc1QUMzMDQxMUUyOEE2MkZBNzcxQjJDNjM0RiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMUQ3Mjc1QkMzMDQxMUUyOEE2MkZBNzcxQjJDNjM0RiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmUD0gAAAABvSURBVHjaYvz//z8DIyMjAxYQicReji4J0ofKQNP8HwmgGQbXB8IsWGwDSSwDuioKjY9uBthVjFAXYHUGAQA2kYmBUoAUBpGk0LAwgBvwH+YX4mkwptgLowYMRgOITUyYKRFIN/wnDjQgJySAAAMApryKzL8wjfUAAAAASUVORK5CYII=);
}

.gantt_qi_big_icon {
font-size:13px;
border-radius:4px;
font-weight:700;
background:#fff;
min-width:60px;
line-height:32px;
vertical-align:middle;
cursor:pointer;
border:1px solid #cecece;
margin:5px 9px 8px 0;
padding:0 10px 0 5px;
}

.gantt_cal_qi_controls div {
float:left;
height:32px;
text-align:center;
line-height:32px;
}

.gantt_tooltip {
box-shadow:3px 3px 3px rgba(0,0,0,.07);
background-color:#fff;
border-left:1px solid rgba(0,0,0,.07);
border-top:1px solid rgba(0,0,0,.07);
font-family:Arial;
font-size:8pt;
color:#454545;
position:absolute;
z-index:50;
padding:10px;
}

.gantt_marker {
height:100%;
width:2px;
top:0;
position:absolute;
text-align:center;
background-color:rgba(255,0,0,.4);
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
box-sizing:border-box;
}

.gantt_marker .gantt_marker_content {
background:inherit;
color:#fff;
position:absolute;
font-size:12px;
line-height:12px;
opacity:.8;
padding:5px;
}

.gantt_marker_area {
position:absolute;
top:0;
left:0;
}

.gantt_noselect {
-moz-user-select:0;
-webkit-user-select:none;
-user-select:none;
}

.gantt_drag_marker {
position:absolute;
font-family:Arial;
font-size:13px;
}

.gantt_drag_marker .gantt_row {
border-left:1px solid #d2d2d2;
border-top:1px solid #d2d2d2;
}

.gantt_drag_marker .gantt_cell {
border-color:#d2d2d2;
}

.gantt_row.gantt_over,.gantt_task_row.gantt_over {
background-color:#0070fe;
}

.gantt_row.gantt_transparent .gantt_cell {
opacity:.7;
}

.gantt_task_row.gantt_transparent {
background-color:#f8fdfd;
}

.gridHoverStyle,.gridSelection,.timelineSelection,.gantt_grid_data .gantt_row.odd:hover,.gantt_grid_data .gantt_row:hover,.gantt_grid_data .gantt_row.gantt_selected,.gantt_grid_data .gantt_row.odd.gantt_selected,.gantt_task_row.gantt_selected {
background-color:#fff3a1;
}

.chartHeaderBg,.gantt_row.odd,.gantt_task_row.odd,.gantt_link_control div:hover,.gantt_drag_marker,.gantt_drag_marker .gantt_row.odd {
background-color:#fff;
}

.dhtmlx_popup_button.dhtmlx_ok_button,.dhtmlx_popup_button.dhtmlx_delete_button {
background:#3db9d3;
text-shadow:0 -1px 0 #248a9f;
color:#fff;
font-weight:700;
border-width:0;
}

.gantt_grid_head_cell.gantt_grid_head_add:hover,.gantt_grid_data .gantt_row.odd:hover .gantt_add,.gantt_grid_data .gantt_row:hover .gantt_add {
-moz-opacity:1;
opacity:1;
}

.gantt_inserted,.gantt_updated,.gantt_time {
font-weight:700;
}

.gantt_status.gantt_status_visible,#gantt_ajax_dots span.gantt_dot_visible {
opacity:1;
}

.gantt_link_target .gantt_link_control div,.gantt_task_line.gantt_selected .gantt_link_control div,.gantt_task_line:hover .gantt_link_control div,.gantt_task_line.gantt_selected .gantt_task_drag,.gantt_task_line.gantt_selected .gantt_task_progress_drag,.gantt_task_line:hover .gantt_task_drag,.gantt_task_line:hover .gantt_task_progress_drag {
display:block;
}

.gantt_custom_button div:first-child,.gantt_drag_marker .gantt_tree_icon.gantt_blank,.gantt_drag_marker .gantt_tree_icon.gantt_close,.gantt_drag_marker .gantt_tree_icon.gantt_open,.gantt_drag_marker .gantt_tree_indent {
display:none;
}

.gantt_cal_light input,.gantt_cal_qi_tcontent {
font-size:13px;
}

.gantt_task_progress > span{
    float: left;
    margin: 0 0 0 40% !important;
    text-align: left;
}
</style>
<?php
   echo $this->Html->css(array('styles-inner.min'));
   echo $this->Html->css(array('projects/custom'));
   echo $this->Html->css('projects/bs-selectbox/bootstrap-select.min');
   echo $this->Html->css('projects/bootstrap-input');
   echo $this->Html->css('projects/manage_elements');

   echo $this->Html->script('projects/plugins/selectbox/bootstrap-select', array('inline' => true));


 
?>
</body>