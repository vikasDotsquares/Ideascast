<?php $workspaces = get_project_workspace($project_id);

// $this->objView->loadHelper('Permission')->wsp_of_project($post['project_id']);
 ?>

<div class="idcomp-inner-first main-container">

	<div class="idcomp-heading">

		<h5 style="display: inline-block;">Decisions</h5>
		<input type="hidden" value="<?php echo $project_id; ?>" name="dec_project_id" data-id="<?php echo $project_id; ?>" >
		<label class="custom-dropdown" style="margin: 0 0 0 10px; width: 60%;">
			<select class="aqua" name="dec_workspace_id">
				<option value="0">All Workspaces</option>
				<?php
					if( isset($workspaces) && !empty($workspaces) ) {
						foreach( $workspaces as $k => $v ) {
							if( !empty($v['Workspace']['title']) )
							echo '<option value="'.$v['Workspace']['id'].'">'.strip_tags($v['Workspace']['title']).'</option> ';
						}
					}
				?>
			</select>
		</label>
		<div class="btn-group pull-right">
			<a href="#" class="list-icon show_listing tipText btn btn-sm" title="Show List" >
				<i class="fa fa-list-ul"></i>
			</a>

			<a href="#" class="graph-icon-wrapper show_graphs tipText btn btn-sm" title="Show Graph" data-type="dec_project_id">
				<i class="graph-icon"></i>
			</a>
		</div>
	</div>

	<div class="idcomp-descp" id="decisions" style='max-height: 450px;'>

		<div class="idcomp-graphs" style="position: relative" >
			<?php
				$dp = $dc = 0;
				if( isset($workspaces) && !empty($workspaces) ) {
					$dec_data = $chart_data = null;
					foreach( $workspaces as $k => $v ) {
						$dec_data = _workspace_decision_feedbacks($v['Workspace']['id']);
						$dp += (isset($dec_data['progress'])) ? $dec_data['progress'] : 0;
						$dc += (isset($dec_data['complete'])) ? $dec_data['complete'] : 0;
					}
					$chart_data = [
						[
							'label' => 'Live',
							'value' => $dp,
						],
						[
							'label' => 'Completed',
							'value' => $dc,
						]
					];
					// pr($chart_data);
				}
			?>
			<div class="chart dec-donut-chart" id="dec_donut_chart_<?php echo $project_id; ?>" style="height: 320px; position: relative;"></div>
			<div style="" class="chart_overlay">
				<div class="donut-empty">0</div>
			</div>

			<div id="dc_detail_<?php echo $project_id; ?>" class="dc_detail_<?php echo $project_id; ?> number_box">
				<div class="donut-detail first" style="">
					<a href="#" class="square-text decision-progressing" data-type="progressing" data-count="<?php echo $dp; ?>" data-value="decision" data-pid="<?php echo $project_id; ?>"><?php echo $dp; ?> Live</a>
				</div>
				<div class="donut-detail second" style=" ">
					<a href="#" class="square-text decision-completed" data-type="completed" data-count="<?php echo $dc; ?>" data-value="decision" data-pid="<?php echo $project_id; ?>"><?php echo $dc; ?> Completed</a>
				</div>
			</div>


			<?php if( $dp != 0 || $dc != 0 ) { ?>
			<script type="text/javascript" >
			$(function(){
				$("#dec_donut_chart_<?php echo $project_id; ?>").removeClass('donut-empty')
				var ddonut_data = jQuery.parseJSON('<?php echo json_encode($chart_data); ?>')
				$.dec_donut['<?php echo $project_id; ?>'] = new Morris.Donut({
					element: 'dec_donut_chart_<?php echo $project_id; ?>',
					gridTextSize: 50,
					resize: true,
					colors: [ "#02B8AB", "#FD625E" ],
					data: ddonut_data,
					hideHover: true,
					gridTextWeight: 'bold',
					formatter: function (x) {
						return Math.floor(x)
					}
				})
			})
			</script>
				<?php }
				else { ?>
				<script type="text/javascript" >
					$(function(){
						$("#dec_donut_chart_<?php echo $project_id; ?>").addClass('donut-empty').html('0')
					})
				</script>
			<?php } ?>

		</div>

		<div class="idcomp-lists" style="display: none;">

			<?php echo $this->element('../Projects/partials/objectives/objective_dfv_decisions', [ 'project_id' => $project_id ]); ?>

		</div>

	</div>

</div>

<div class="idcomp-inner-first main-container">

	<div class="idcomp-heading">

		<h5 style="display: inline-block;">Feedback</h5>

		<input type="hidden" value="<?php echo $project_id; ?>" name="fed_project_id" data-id="<?php echo $project_id; ?>" >
		<label class="custom-dropdown" style="margin: 0 0 0 10px; width: 60%;">

			<select class="aqua" name="fed_workspace_id">
				<option value="0">All Workspaces</option>

				<?php
					if( isset($workspaces) && !empty($workspaces) ) {
						foreach( $workspaces as $k => $v ) {
							if( !empty($v['Workspace']['title']) )
							echo '<option value="'.$v['Workspace']['id'].'">'.strip_tags($v['Workspace']['title']).'</option> ';
						}
					}
				?>
			</select>

		</label>

		<div class="btn-group pull-right">
			<a href="#" class="list-icon show_listing tipText btn btn-sm" title="Show List" >
				<i class="fa fa-list-ul"></i>
			</a>

			<a href="#" class="graph-icon-wrapper show_graphs tipText btn btn-sm" title="Show Graph" data-type="fed_project_id">
				<i class="graph-icon"></i>
			</a>
		</div>
	</div>


	<div class="idcomp-descp" id="feedbacks" style="max-height: 450px;">

		<div class="idcomp-graphs" style="position: relative" >
			<?php
					$fedp_data = $fedc_data = 0;
					$fchart_data = null;
					$fp = $fc = 0;
				if( isset($workspaces) && !empty($workspaces) ) {
					$workspaces_id = Set::extract($workspaces, '/Workspace/id');
					$fedp_data = _workspace_feedbacks($workspaces_id, false);
					$fedc_data = _workspace_feedbacks($workspaces_id, true);

					$fchart_data = [
						[
							'label' => 'Live',
							'value' => $fedp_data,
						],
						[
							'label' => 'Completed',
							'value' => $fedc_data,
						]
					];
				}
				?>
				<div class="chart fed-donut-chart" id="fed_donut_chart_<?php echo $project_id; ?>"  style="height: 320px; position: relative;"></div>
			<div style="padding-top: 10px;" class="chart_overlay">
				<div class="donut-empty">0</div>
			</div>


			<div id="fed_detail_<?php echo $project_id; ?>" class="fed_detail_<?php echo $project_id; ?> number_box">
				<div class="donut-detail first" style=" ">
					<a href="#" class="square-text feedback-live" data-type="live" data-value="feedback" data-count="<?php echo $fedp_data; ?>" data-pid="<?php echo $project_id; ?>"><?php echo $fedp_data; ?> Live</a>
				</div>
				<div class="donut-detail second" style="">
					<a href="#" class="square-text feedback-progressing" data-type="completed" data-count="<?php echo $fedc_data; ?>"  data-value="feedback" data-pid="<?php echo $project_id; ?>"><?php echo $fedc_data; ?> Completed</a>
				</div>
			</div>


				<!-- <div id="fed_detail_<?php echo $project_id; ?>" class="fed_detail_<?php echo $project_id; ?>">
					<div class="donut-detail first" style=" padding: 10px 10px 0px 0px;">
						<div class="el-box donut-feedback-progressing" style=""></div>
						<span><?php echo $fedp_data; ?> Live</span>
					</div>
					<div class="donut-detail second" style="padding: 10px 10px 30px 0px;">
						<div class="el-box donut-feedback-completed" style=""></div>
						<span><?php echo $fedc_data; ?> Completed</span>
					</div>
				</div> -->

				<?php if( $fedp_data != 0 || $fedc_data != 0 ) { ?>
				<script type="text/javascript" >
					$(function(){
						$("#fed_donut_chart_<?php echo $project_id; ?>").removeClass('donut-empty')
						var ddonut_data = jQuery.parseJSON('<?php echo json_encode($fchart_data); ?>')
						$.fed_donut['<?php echo $project_id; ?>'] = new Morris.Donut({
							element: 'fed_donut_chart_<?php echo $project_id; ?>',
							gridTextSize: 50,
							resize: true,
							colors: [ "#1D75BA", "#AEC7EB" ],
							data: ddonut_data,
							hideHover: true,
							gridTextWeight: 'bold',
							formatter: function (x) {
								return Math.floor(x)
							}
						})
					})
				</script>
					<?php }
					else { ?>
					<script type="text/javascript" >
						$(function(){
							$("#fed_donut_chart_<?php echo $project_id; ?>").addClass('donut-empty').html('0')
						})
					</script>
				<?php } ?>
		</div>

		<div class="idcomp-lists" style="display: none;">

			<?php echo $this->element('../Projects/partials/objectives/objective_dfv_feedbacks', [ 'project_id' => $project_id ]); ?>

		</div>


	</div>

</div>

<div class="idcomp-inner-first main-container">
	<div class="idcomp-heading">
		<h5 style="display: inline-block;">Votes</h5>
		<input type="hidden" value="<?php echo $project_id; ?>" name="vot_project_id" data-id="<?php echo $project_id; ?>" >
		<label class="custom-dropdown" style="margin: 0 0 0 10px; width: 60%;">
			<select class="aqua" name="vot_workspace_id">
				<option value="0">All Workspaces</option>
				<?php
					// $workspaces = get_project_workspace($project_id);
					// $ws = null;
					if( isset($workspaces) && !empty($workspaces) ) {
						foreach( $workspaces as $k => $v ) {
							if( !empty($v['Workspace']['title']) )
								echo '<option value="'.$v['Workspace']['id'].'">'.strip_tags($v['Workspace']['title']).'</option> ';
						}
					}
				?>
			</select>
		</label>

		<div class="btn-group pull-right">
			<a href="#" class="list-icon show_listing tipText btn btn-sm" title="Show List" >
				<i class="fa fa-list-ul"></i>
			</a>

			<a href="#" class="graph-icon-wrapper show_graphs tipText btn btn-sm" title="Show Graph" data-type="vot_project_id">
				<i class="graph-icon"></i>
			</a>
		</div>

	</div>

	<div class="idcomp-descp" id="votes" style="max-height: 450px;">
		<div class="idcomp-graphs"  style="position: relative"  >
			<?php
				$vchart_data = null;
				$vedp_data = $vedc_data = 0;
					$vp = $vc = 0;
				if( isset($workspaces) && !empty($workspaces) ) {
					$workspaces_id = Set::extract($workspaces, '/Workspace/id');
					$vedp_data = _workspace_votes($workspaces_id, false);

					$vedc_data = _workspace_votes($workspaces_id, true);

					$vchart_data = [
						[
							'label' => 'Live',
							'value' => $vedp_data,
						],
						[
							'label' => 'Completed',
							'value' => $vedc_data,
						]
					];
				}
				?>
				<div class="chart vot-donut-chart" id="vot_donut_chart_<?php echo $project_id; ?>"  style="height: 320px; position: relative;"></div>
			<div style="" class="chart_overlay">
				<div class="donut-empty">0</div>
			</div>


			<div id="vot_detail_<?php echo $project_id; ?>" class="vot_detail_<?php echo $project_id; ?> number_box">
				<div class="donut-detail first" style=" ">
					<a href="#" class="square-text vote-live" data-type="live" data-value="vote" data-count="<?php echo $vedp_data; ?>" data-pid="<?php echo $project_id; ?>"><?php echo $vedp_data; ?> Live</a>
				</div>
				<div class="donut-detail second" style="">
					<a href="#" class="square-text vote-progressing" data-type="completed" data-count="<?php echo $vedc_data; ?>" data-value="vote" data-pid="<?php echo $project_id; ?>"><?php echo $vedc_data; ?> Completed</a>
				</div>
			</div>


				<!-- <div id="vot_detail_<?php echo $project_id; ?>" class="vot_detail_<?php echo $project_id; ?>">
					<div class="donut-detail first" style=" padding: 10px 10px 0px 0px;">
						<div class="el-box donut-vote-progressing" style=""></div>
						<span> <?php echo $vedp_data; ?> Live</span>
					</div>
					<div class="donut-detail second" style="padding: 10px 10px 30px 0px;">
						<div class="el-box donut-vote-completed" style=""></div>
						<span><?php echo $vedc_data; ?> Completed</span>
					</div>
				</div> -->

				<?php if( $vedp_data != 0 || $vedc_data != 0 ) { ?>
				<script type="text/javascript" >
				$(function(){
					$("#vot_donut_chart_<?php echo $project_id; ?>").removeClass('donut-empty')
					var ddonut_data = jQuery.parseJSON('<?php echo json_encode($vchart_data); ?>')
					$.vot_donut['<?php echo $project_id; ?>'] = new Morris.Donut({
						element: 'vot_donut_chart_<?php echo $project_id; ?>',
						gridTextSize: 50,
						resize: true,
						colors: [ "#FF0000", "#FFA500" ],
						data: ddonut_data,
						hideHover: true,
						gridTextWeight: 'bold',
						formatter: function (x) {
							return Math.floor(x)
						}
					})
				})
				</script>
				<?php }
				else { ?>
				<script type="text/javascript" >
					$(function(){
						$("#vot_donut_chart_<?php echo $project_id; ?>").addClass('donut-empty').html('0')
					})
				</script>
				<?php } ?>
		</div>

		<div class="idcomp-lists" style="display: none;">

			<?php echo $this->element('../Projects/partials/objectives/objective_dfv_votes', [ 'project_id' => $project_id ]); ?>

		</div>


	</div>
</div>
<script type="text/javascript" >
/* $(function(){
	$('.vote_lst .detail-body').each(function(){
		$(this).prev('.detail-head').find('.el-box').addClass($(this).data('class'));
	})
}) */
</script>