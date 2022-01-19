<?php $confidence_graph = $this->Permission->confidence_graph($project_id);
$confidence_graph = (isset($confidence_graph['0']['0']['data']) && !empty($confidence_graph['0']['0']['data'])) ?   $confidence_graph['0']['0'] ['data'] : [];
// pr($confidence_graph);
?>
<div class="task-charts-sec" data-id="<?php echo $id; ?>">
	<div class="task-charts-sec-inner">
		<div class="task-charts-image">
			<div id="confidence_level" style="width:263px; height:106px"></div>
			<?php if(isset($confidence_graph) && !empty($confidence_graph)) { ?>
			<script>
			Morris.Line({
				element: 'confidence_level',
				data:
				// 1 - CHANGE CHART DATA AT RUNTIME FROM SQL
				<?php echo $confidence_graph; ?>
				,
				xkey: 'cl_date',
				ykeys: ['cl_value'],
				labels: ['Level'],
				dataLabels: false,
				ymax: 100,
				ymin: 0,
				grid: false,
				axes: false,
				pointSize: 0,
				lineWidth: 2,
				lineColors: ['#5f9322', '#e5030d', '#e76915', '#e3a809'], // 2 - CHANGE COLOR AT RUNTIME BASED ON CURRENT CONFIDENCE LEVEL VALUE '#e5030d' or '#e76915' or '#e3a809' or '#5f9322' (red,orange,yellow,green)
				postUnits: '%',
				hideHover: true,
				hoverCallback: function (index, options, content, row) {
					//custom tooltip
					if(row.cl_value) {
						console.log(row.cl_value)
						return moment(row.cl_date).format('DD MMM, YYYY') + '<br>Level: ' + row.cl_value + '%' + '<br>' + row.cl_count   + ((row.cl_count == 1 ) ? ' Task' : ' Tasks');
					}
					else {
						return moment(row.cl_date).format('DD MMM, YYYY') + '<br>None Set';
					}
				}
			});
			</script>
			<?php } ?>
		</div>
		<div class="task-charts-titles">
			<span class="task-charts-titles-left">Confidence Level </span>
			<span class="task-charts-titles-right"> 90 days</span>
		</div>
	</div>
</div>