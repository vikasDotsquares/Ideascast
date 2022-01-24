<?php
$current_user = $this->Session->read('Auth.User.id');
$task_count_breakdown_graph = $this->Permission->task_count_breakdown_graph($project_id, $current_user);
$task_count_breakdown_graph = (isset($task_count_breakdown_graph['0']['0']['data']) && !empty($task_count_breakdown_graph['0']['0']['data'])) ?   $task_count_breakdown_graph['0']['0']['data']  : [];
 ?>
<div class="task-charts-sec" data-id="<?php echo $id; ?>">
	<div class="task-charts-sec-inner">
		<div class="task-charts-image">
			<div id="task_count_burndown" style="width:263px; height:106px"></div>
			<?php if(isset($task_count_breakdown_graph) && !empty($task_count_breakdown_graph)) { ?>
			<script>
			Morris.Line({
					element: 'task_count_burndown',
					data:
					// 1 - CHANGE CHART DATA AT RUNTIME FROM SQL
					<?php echo $task_count_breakdown_graph; ?>
					,
					xkey: 'tb_date',
					ykeys: ['tb_value'],
					labels: ['Tasks'],
					dataLabels: false,
					ymin: 0,
					grid: false,
					axes: false,
					pointSize: 0,
					lineWidth: 2,
					lineColors: ['#3c8dbc'], //blue
					postUnits: '',
					hideHover: true,
					hoverCallback: function (index, options, content, row) {
						//custom tooltip
						if(row.tb_value) {
							return moment(row.tb_date).format('DD MMM, YYYY') + '<br>Tasks: ' + row.tb_value;
						}
						else {
							return moment(row.tb_date).format('DD MMM, YYYY') + '<br>Tasks: None';
						}
					}
			});
			</script>
			<?php } ?>
		</div>
		<div class="task-charts-titles">
			<span class="task-charts-titles-left">Task Count Burndown </span>
			<span class="task-charts-titles-right"> 180 days</span>
		</div>
	</div>
</div>