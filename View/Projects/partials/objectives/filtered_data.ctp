<script >
	$(function(){

		$.inc = 0;
		// Enable/Disable select box option
		$.check_range_status = function()
		{
			var dateRange 	= $('[name=date_range]'),
			taskStatus 	= $('[name=task_status]');

			if( dateRange.val() == 1 ) {

				$("option[value='1']", taskStatus).prop("disabled", true);
				$("option[value='4']", taskStatus).prop("disabled", true);
				$("option[value='5']", taskStatus).prop("disabled", true);

				$("option[value='2']", taskStatus).prop("disabled", false);
				$("option[value='3']", taskStatus).prop("disabled", false);

			}
			else if( dateRange.val() == 2 ) {

				$("option[value='1']", taskStatus).prop("disabled", true);
				$("option[value='2']", taskStatus).prop("disabled", true);
				$("option[value='3']", taskStatus).prop("disabled", true);
				$("option[value='5']", taskStatus).prop("disabled", true);

				$("option[value='4']", taskStatus).prop("disabled", false);

			}
			else if( dateRange.val() == 3 || dateRange.val() == 4 ) {

				$("option[value='1']", taskStatus).prop("disabled", true);

				$("option[value='2']", taskStatus).prop("disabled", false);
				$("option[value='3']", taskStatus).prop("disabled", false);
				$("option[value='4']", taskStatus).prop("disabled", false);
				$("option[value='5']", taskStatus).prop("disabled", false);

			}
			else {
				$('option', taskStatus).prop('disabled', false);
			}

		}

		$.chart_html = function(values, total, project_id) {
			var o =
			{
				progress_text: ['NON','NOS','PRG','CMP','OVD'],
				bg_classes: ['gray','dark-gray','yellow','green','red' ],
				borders: [ '#A6A6A6','#948A54','#FFC000','#00B050','#FF0000' ],
			}

			var $wrapper = $('<div style="vertical-align: bottom;" id="chart_wrapper" class="chart_wrapper"></div>');
			// $element.append($wrapper);
				var n = 0,
					bar_left = 0;
				for (i = 0; i < values.length; i++) {
					var x = (60*i)+30,
						h = values[i],
						hstr = (Math.round( values[i] * 100 )/100 ),
						hstr = hstr.toFixed()
						hout = h+2,
						to_px = '';

					bar_left = (53 * i) + 15,
					top_px = '-30px';
					to_px = '40%';

					if( h <= 0 ) {
						total_text = '';
					}
					else if( h > 0 && h < 10) {
						to_px = '10%';
					}
					else if( h > 10 && h < 20) {
						to_px = '20%';
					}

					var link = '';
					if(o.progress_text[i] == 'NON') {
						//link = $js_config.base_url + 'dashboards/task_center/'+project_id+'/status:4';
						link = $js_config.task_centers+project_id+'/status:4';
					}
					else if(o.progress_text[i] == 'NOS') {
						//link = $js_config.base_url + 'dashboards/task_center/'+project_id+'/status:6';;
						link = $js_config.task_centers+project_id+'/status:6';
					}
					else if(o.progress_text[i] == 'PRG') {
						//link = $js_config.base_url + 'dashboards/task_center/'+project_id+'/status:7';;
						link = $js_config.task_centers+project_id+'/status:7';
					}
					else if(o.progress_text[i] == 'CMP') {
						//link = $js_config.base_url + 'dashboards/task_center/'+project_id+'/status:5';;
						link = $js_config.task_centers+project_id+'/status:5';
					}
					else if(o.progress_text[i] == 'OVD') {
						//link = $js_config.base_url + 'dashboards/task_center/'+project_id+'/status:1';;
						link = $js_config.task_centers+project_id+'/status:1';
					}
					var link_before = '<a href="'+link+'" class="tipText" title="'+total[i]+'">',
						link_after = '</a>';

					var main_start = '<div class="main_bar" data-height="'+hout+'%" style="left: '+bar_left+'px; height: 0%; border: 1px solid '+o.borders[i]+'">',
					main_end = '</div>',
					middle = link_before + '<div class="back-'+o.bg_classes[i]+' bar_middle" data-original-title="'+hstr+'%" style=""></div>' + link_after,
					total_text = '<div class="total_text"  style="top: '+to_px+'; ">'+total[i]+'</div>',
					percentage_text = '<div style="top: '+top_px+'; " class="percentage_text">'+hstr+'%</div>',
					percentage = '<div class="status_text">'+o.progress_text[i]+'</div>';

					if( h <= 0 ) {
						total_text = '';
					}

					var all = main_start + middle + percentage + percentage_text /*+ total_text*/ + main_end;

					$wrapper.append(all);

				}


			return $wrapper;
		}




		$('body').delegate('.ws_slider', 'change', function(event){
			var $completed1 = $(this).parents('td:first').prev('td').find('.completed'),
				$completed2 = $(this).parents('td:first').find('.input_completion');

				$completed1.text($(this).val())
				$completed2.text($(this).val()+"%")
		})

		$.downStart = false;
		$('body').delegate('.ws_slider', 'mousedown', function(event){
			$.downStart = true;
		})
		$('body').delegate('.ws_slider', 'mouseup', function(event){
			$.downStart = false;
		})
		$('body').delegate('.ws_slider', 'mousemove', function(event){
			if( $.downStart == true ) {
				var $completed = $(this).parent().next('.input_completion')
				$completed.text($(this).val()+"%")
			}
		})

		$('.ws_slider').each(function(){
			var $completed = $(this).parent().next('.input_completion')
			$completed.text($(this).val()+"%")
		})

	/**********************************************************/
	})

			var PieCharts =
			{

				init: function(args) {
					this.options = args;
					// this.bindEvents();
					this.drawChart()
					this.curPerc = 0;

				},

				bindEvents: function() {
					// this.form.on('submit', $.proxy(this.showName, this));
				},

				drawChart: function() {
					var options = this.options

					var canvas = document.getElementById(options.el);
					var ctx = canvas.getContext("2d");
					var lastend = -10;
					var data = options.data; // If you add more data values make sure you add more colors
					var myTotal = 0; // Automatically calculated so don't touch
					var myColor = options.colors; // Colors of each slice
					 // Colors of each slice
					// console.log($(canvas).parent())
					// canvas.width = $(canvas).parent()[0].clientWidth;
					// canvas.height = $(canvas).parent()[0].clientHeight;

					for (var e = 0; e < data.length; e++) {
						myTotal += data[e];
					}
					if(myTotal == 0) {
						ctx.textAlign="center";
						// ctx.font="20px Georgia";
						// ctx.strokeText("N/A",10,50);
						ctx.font="40px Verdana";

						ctx.strokeStyle = '#000';
						ctx.strokeText("N/A",canvas.width / 2, canvas.height / 2);
					}
					else {
						for (var i = 0; i < data.length; i++) {
							// ctx.shadowOffsetX = 5;
							// ctx.shadowOffsetY = 5;
							// ctx.shadowBlur = 9;
							// ctx.shadowColor = '#666666';


							ctx.fillStyle = myColor[i];
							ctx.beginPath();
							ctx.moveTo(canvas.width / 2, canvas.height / 2);
							// ctx.moveTo(0, 0);
							// Arc Parameters: x, y, radius, startingAngle (radians), endingAngle (radians), antiClockwise (boolean)
							ctx.arc(canvas.width / 2, canvas.height / 2, canvas.height / 2, lastend, lastend + (Math.PI * 2 * (data[i] / myTotal)), false);
							ctx.lineTo(canvas.width / 2, canvas.height / 2);
							ctx.fill();
							// ctx.stroke();
							lastend += Math.PI * 2 * (data[i] / myTotal);

							// drawSegmentLabel(canvas, ctx, i);
						}
					}
				}
			}


</script>

<?php

if( isset($projects) && !empty($projects) ) {

	$projectsCnt = (isset($projects) && !empty($projects)) ? count($projects) : 0;
	foreach( $projects as $key => $value ) {
		// pr($value, 1);
		$project = $value['project'];
		$taskData = $value['task'];
		$currencyData = $value['currencies'];
		$alignData = $value['aligneds'];

		$total_task = $taskData['CMP'] + $taskData['NON'] + $taskData['PRG'] + $taskData['PND'] + $taskData['OVD'];
		$elements_overdue = $taskData['OVD'];
		$project_details[$key] = $value;
		$rag_data = $this->ViewModel->getRAGStatus($key, true, $total_task, $taskData['OVD'], $project['rag_status']);
		// pr($project);
		// pr($taskData, 1);
		// pr($rag_data);

        $bar_color =  ($rag_data['rag_color'] == 1) ? 'bg-red' : ( ( $rag_data['rag_color'] == 2) ? 'bg-yellow' : 'bg-green' );
		$percent = $rag_data['percent'];

		$manageCostUrl = SITEURL.'costs/index/'.$taskData['prj_role'] . ':' . $project['id'];

		$workspaces = $this->Permission->wsp_of_project($project['id']);
		$task_count = $total_task;

		$total_risk = $taskData['low_risk'] + $taskData['medium_risk'] + $taskData['high_risk'] + $taskData['severe_risk'];

		// $workspaces1 = $this->Permission->wspAndTasks($key);
		// pr($workspaces1, 1);
?>
<?php //$total = $this->ViewModel->element_status_counts( $project['id'] ); ?>
	<div class="objective_projects">

		<div class="panel <?php echo $project['color_code']; ?> panels-workspace " style="margin-bottom: 0px; margin-top: 5px; width: 100% !important; " data-project="<?php echo $project['id']; ?>">

			<div class="panel-heading clearfix">
				<div class="col-sm-7 col-md-8 col-sm-12 mar-to clearfix nopadding">
					<a href="#" title="" class="btn btn-default btn-xs tipText show_hide_panel pull-left" style="margin-right: 10px" data-original-title="Show/Hide"  data-id="<?php echo $project['id']; ?>"  >
						<i class="fa <?php if( isset($projects) && !empty($projects) && count($projects) > 1){ ?>fa-plus <?php }else{ ?>fa-minus<?php } ?> show_hide_icon"></i>
					</a>

				<h3 class="panel-title pull-left" style="padding-top: 2px;">
					<?php echo htmlentities(ucfirst($project['title'])); ?>
				</h3>
				</div>
				<div class="col-sm-5 col-md-4 col-sm-12 mar-three nopadding">
				<div class="full-coln-wid" style="">
					<div class="input-group input-group-bg" style="cursor: default">
						<div class="progress-status progress-status-wid hidden-sm " style="background-color: rgba(255,2555,255,0.85) !important;padding: 2px 0;margin-top: 3px;float: right;width: 100%;margin-right: 20px;" >
							<div class="progress tipText " style="border-style: none !important;" title="<?php //echo "Tasks: ".$taskData['CMP']." Completed / ".$total_task." Outstanding"; ?>" >
								<div class="progress-bar <?php  echo $bar_color ?>"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;  line-height: 17px;">RAG
								</div>
							</div>
						</div>
						<span class="input-group-addon btns">
					<?php
					if( $projectsCnt <= 1) { ?>

						<a href="#" title="Show Full" class="btn btn-default btn-xs tipText show_full_toggle" style=" ">
							<i class="fa fa-arrows-v"></i>
						</a>
						<a href="<?php echo $manageCostUrl; ?>" title="Project Assets" class="btn btn-default btn-xs tipText">
							<i class="projectassetsicon"></i>
						</a>

					<?php }
					else {
					?>
						<a href="#" title="Show Full" class="btn btn-default btn-xs tipText show_full_toggle" style="display: none;">
							<i class="fa fa-arrows-v"></i>
						</a>

						<a href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $project['id'])); ?>" title="Open Project" class="btn btn-default btn-xs tipText ">
							<i class="fa fa-folder-open"></i>
						</a>

					<?php } ?>

									</span>
								</div>
								</div>
				</div>

			</div>
			<div class="panel-body clearfix" style="display:<?php if( $projectsCnt > 1 ) { echo 'none'; }else { echo 'block'; } ?>;">

				<div class="idcomp-outer">
					<div class="idcomp-inner">
						<div class="idcomp-heading">
							Schedule
							<a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'event_gantt', $taskData['prj_role'] => $project['id'])); ?>" title="" class="btn btn-primary btn-xs tipText pull-right" data-original-title="Gantt">
								<i class="fa fa-calendar"></i>
							</a>
						</div>

						<div class="idcomp-descp idcomp-schedule idcomp-risk-expo " id="schedule">

							<div class="dates col-sm-2">
								<div class="dates_started ">
									<span class="number-text">Start:</span>
									<span class="number-text"><?php echo (isset($project['start_date']) && !empty($project['start_date'])) ? _displayDate(date('Y-m-d', strtotime($project['start_date'])), 'd M, Y') : 'N/A'; ?></span>
								</div>
								<div class="dates_end ">
									<span class="number-text">End:</span>
									<span class="number-text"><?php echo (isset($project['end_date']) && !empty($project['end_date'])) ? _displayDate(date('Y-m-d', strtotime($project['end_date'])), 'd M, Y') : 'N/A';  ?></span>
								</div>
								<div class="dates_total ">
									<span class="number-text">Duration</span>
									<span class="bg-black numbers"><?php echo $total_days = daysLeft(date('Y-m-d',strtotime($project['start_date'])), date('Y-m-d',strtotime($project['end_date'])), 1);

									?>
									<?php $total_daysN = daysLeft(date('Y-m-d',strtotime($project['start_date'])), date('Y-m-d',strtotime($project['end_date'])));

									?>
									</span>
								</div>
							</div>

							<div class="dates col-sm-2">

								<div class="dates_completed col-sm-2">
									<span class="number-text">Days Completed</span>
									<span class="bg-black numbers">
									<?php
									if(date('Y-m-d') > date('Y-m-d',strtotime($project['start_date']))){
										echo $total_complete_days = daysLeft($project['start_date'], date('Y-m-d 12:00:00'));
									}else{
										echo $total_complete_days = daysLeft($project['start_date'], date('Y-m-d 12:00:00'));
									}

								  ?>
									</span>
								</div>
								<div class="dates_remaining col-sm-2">
									<span class="number-text">Days Remaining</span>
									<span class="bg-black numbers"><?php
									if(date('Y-m-d')  <= date('Y-m-d',strtotime($project['end_date'])) && date('Y-m-d')  >= date('Y-m-d',strtotime($project['start_date']))){
										echo $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project['end_date'], 1);
									}else{
										echo $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project['end_date']);
									}
									?></span>
								</div>
							</div>


							<div class="pie_project col-sm-4">
							  <div class="col-sm-6" style="padding: 0px">
								<canvas id="canvas_pie_project_<?php echo $project['id'] ?>" style="width:100%"></canvas>
							  </div>
							  <div class="pie_project_status col-sm-6 text-center">
									<h3>Project</h3>
									<p class=" status"> <?php if(!empty($total_daysN) && !empty($total_complete_days)) {
									echo 100 - (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)) > 0 ? 100 - (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)).'%<br/>Remaining': '0'.'%<br/>Remaining'; }else{echo"N/A";} ?> </p>
							  </div>
						  </div>

							<div class="pie_tasks col-sm-4 ">
								<div class="col-sm-6" style="padding: 0px">
									<canvas id="canvas_pie_tasks_<?php echo $project['id'] ?>" style="width:100%"></canvas>
								</div>

								  <div class="pie_tasks_status col-sm-6  text-center">
									<h3>Tasks</h3>
									<?php
										$completedElements = (isset($taskData['CMP']) && !empty($taskData['CMP'])) ? $taskData['CMP'] : 0;
									?>
									<p>
										<a href="<?php echo TASK_CENTERS.$project['id'].'/status:5'; ?>" class="text-blue">
										 <?php echo $total_completedElements = $completedElements; ?> Completed
										</a>
									</p>
									<p class="status">

										<a href="<?php echo TASK_CENTERS.$project['id'].'/status:8'; ?>" class="text-red">
											<?php echo $remaining_el = $task_count - $completedElements ;?> Outstanding
										</a>
									</p>
								 </div>
							</div>

							<script type="text/javascript" >

								function pie() {
									PieCharts.init({el: 'canvas_pie_project_<?php echo $project['id'] ?>', data: [<?php echo $total_complete_days ?>, <?php echo $total_remain_days ?>], colors: ['#00863D', '#FFC000' ]});
									PieCharts.init({el: 'canvas_pie_tasks_<?php echo $project['id'] ?>', data: [<?php echo $total_completedElements; ?>, <?php echo $remaining_el; ?>], colors: ['#3c8dbc', '#e75300']});
								}
									pie();
									$(window).on('resize', function(event){
										pie()
									})

							</script>

						</div>
					</div>
				</div>

				<!-- Cost section start from here....  -->
				<div class="idcomp-outer">
					<div class="idcomp-inner">
						<div class="idcomp-heading">
							COSTS
							<a href="<?php echo $manageCostUrl;?>" class="btn btn-primary btn-xs tipText pull-right" data-original-title="Cost Center" ><i class="fa-manage-dash-cost"></i>
							</a>


						</div>

						<div class="idcomp-descp idcomp-schedule " id="schedulecost">
							 <?php

								$totalspendcost = $taskData['stotal'];
								$totalestimatedcost = $taskData['etotal'];
								$projectCurrencyName = $currencyData['sign'];


								if($projectCurrencyName == 'USD') {
									$projectCurrencysymbol = '<i class="fa fa-dollar"></i>';
								}
								else if($projectCurrencyName == 'GBP') {
									$projectCurrencysymbol = '<i class="fa fa-gbp"></i>';
								}
								else if($projectCurrencyName == 'EUR') {
									$projectCurrencysymbol = '<i class="fa fa-eur"></i>';
								}
								else if($projectCurrencyName == 'DKK' || $projectCurrencyName == 'ISK') {
									$projectCurrencysymbol = '<span style="font-weight: 600">Kr</span>';
								}



								$estimatedcost = 0;
								if( isset($totalestimatedcost) && $totalestimatedcost > 0 ){
									$estimatedcost = $totalestimatedcost;
								}

								$spendcost = 0;
								if( isset($totalspendcost) && $totalspendcost > 0 ){
									$spendcost = $totalspendcost;
								}



								$projectbudget = ( isset($project['budget']) && $project['budget'] > 0 )? $project['budget'] : 0;
							 ?>
							<div class="col-cost">
								<label class="label-text">Budget:</label>
								<div class="field-area-select">
									<?php echo $projectCurrencysymbol.$this->Form->input('budget', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>number_format($projectbudget, 2, '.', '') )); ?>
								</div>
							</div>

							<div class="col-cost">
							  <label class="label-text">Total Estimate:</label>
							  <div class="field-area-select">
								<?php echo $projectCurrencysymbol.$this->Form->input('budget', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>number_format($estimatedcost, 2, '.', '') )); ?>
							  </div>
						    </div>
							<div class="col-cost">
								<label class="label-text">Total Spend:</label>
								<div class="field-area-select">
								<?php echo $projectCurrencysymbol.$this->Form->input('budget', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>number_format($spendcost, 2, '.', ''))); ?>
								</div>
							</div>
						</div>

					</div>
				</div>

				<!-- Risk section start from here....   -->
				<div class="idcomp-outer">
					<div class="idcomp-inner">
						<div class="idcomp-heading display-table-full">
							RISK EXPOSURE

							<?php /* <a class="color-icon tipText pull-right" style="cursor: default;" title="" href="javascript:;" data-original-title="Risk Map">&nbsp;</a> */
							if( ($total_risk) > 0 ){
							?>
								<a href="<?php echo Router::url(array('controller' => 'risks', 'action' => 'index', $project['id'])); ?>" class="btn btn-primary btn-xs tipText pull-right" data-original-title="Risk Center" ><i class="fa fa-exclamation-circle"></i></i>
							</a>
							<?php } else { ?>
								<a href="javascript:;" style="cursor:default;" class="btn btn-primary btn-xs tipText pull-right" data-original-title="No Risk" ><i class="fa fa-exclamation-circle"></i></i>
							</a>
							<?php } ?>

						</div>

						<div class="idcomp-descp idcomp-schedule " id="schedulecost">
							 <?php
								//$project['id']
								$severeRisks = $taskData['severe_risk'];
								$highRisks = $taskData['high_risk'];

								$total_open = $taskData['open_risk'];
								$total_review = $taskData['review_risk'];
								$total_signoff = $taskData['signoff_risk'];
								$total_overdue = $taskData['overdue_risk'];
							 ?>
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-4 dashboard-exposure">
									<div class="col-sm-6 col-md-6">
										<div class="input-group">
											<input type="text" class="form-control serve-ex" placeholder="High:">
											<span class="input-group-addon"><?php echo $highRisks;?></span>
										</div>
									</div>

									<div class=" col-sm-6 col-md-6">
										<div class="input-group">
											<input type="text" class="form-control high-ex" placeholder="Severe:">
											<span class="input-group-addon"><?php echo $severeRisks;?></span>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-8 exposure-status">
									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control risk-input-tab tipText" placeholder="Open:" data-original-title="Risk Open">
											<a class="btn btn-xs btn-default tipText riskflag" data-original-title="Open" style="cursor: default;"><i class="fa fa-flag-o"></i></a>
											<span class="input-group-addon"><?php echo $total_open;?></span>
										</div>
									</div>

									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control risk-input-tab tipText" placeholder="Reviewing:" data-original-title="Risk Reviewing">
											<a class="btn btn-xs btn-default tipText riskflag" style="cursor: default;" data-original-title="Reviewing"><i class="fa fa-flag"></i></a>
											<span class="input-group-addon"><?php echo $total_review;?></span>
										</div>
									</div>

									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control risk-input-tab tipText" placeholder="Signed-off:" data-original-title="Risk Signed-off">
											<a class="btn btn-xs btn-default tipText riskflag bg-green" style="cursor: default;" data-original-title="Risk Signed-off"><i class="fa fa-flag-checkered"></i></a>
											<span class="input-group-addon"><?php echo $total_signoff;?></span>
										</div>
									</div>

									<div class="col-sm-3">
										<div class="input-group">
											<input type="text" class="form-control risk-input-tab tipText"  placeholder="Overdue:" data-original-title="Overdue" >
											<a class="btn btn-xs btn-default text-red tipText riskflag" data-original-title="Overdue" style="cursor: default;"><i class="fa fa-flag"></i></a>
											<span class="input-group-addon"><?php echo $total_overdue;?></span>
										</div>
									</div>
								</div>

							</div>

						</div>

					</div>
				</div>
				<!-- Risk Exposer   -->




				<div class="show_full clearfix" style="display:none;">

					<div class="idcomp-outer">
						<div class="idcomp-inner">
							<div class="idcomp-heading" style="">
								Team
								<div class="btn-group pull-right">
									<a class="btn btn-primary btn-xs text-bold more tipText" title="Project Sharing" href="<?php echo Router::url(array('controller' => 'shares', 'action' => 'index', $project['id'])); ?>">
										<i class="fa fa-fw fa-users"></i>
									</a>

									<?php
									$ProjectLevel = ProjectLevel($project['id']);
									if(isset($ProjectLevel) && !empty($ProjectLevel)){
									?>
									<a class="btn btn-primary btn-xs text-bold more tipText" title="" href="<?php echo Router::url(array('controller' => 'shares', 'action' => 'sharing_map', $project['id'])); ?>" data-original-title="Sharing Map">
										<i class="fa fa-fw fa-share"></i>
									</a>
									<?php } ?>
								</div>

							</div>

							<div style="padding: 10px; min-height: 170px;" id="creator_sharers" class="project-owner-sharer">
								<?php //echo $this->element('../Projects/partials/objectives/objective_creator_sharers', ['project_id' => $project['id'] ]); ?>
							</div>


						</div>
					</div>


					<div class="idcomp-outer pro-key">
						<div class="idcomp-inner-first">
							<div class="idcomp-heading">
								Project

								<!-- <a class="btn btn-primary btn-xs text-bold more tipText pull-right" title="Project Report" href="<?php //echo Router::url(array('controller' => 'projects', 'action' => 'reports', $project['id'])); ?>">
									<i class="fa fa-fw fa-bar-chart-o"></i>
								</a> -->
							</div>
							<div class="idcomp-descp" id="project_detail">
								<?php echo $this->element('../Projects/partials/objectives/objective_project_detail', ['project_data' => $project, 'align' => $alignData ]); ?>
							</div>
						</div>

						<div class="idcomp-inner-second">
							<div class="idcomp-heading">
								WORKSPACES

								<a href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $project['id'])); ?>" title="Project" class="btn btn-primary btn-xs tipText pull-right">
									<i class="fa fa-bars"></i>
								</a>

							</div>
							<div class="idcomp-descp obj-wsps" id="workspaces">
								<?php //echo $this->element('../Projects/partials/objectives/objective_workspaces', ['project_id' => $project['id'], 'workspaces' => $workspaces ]); ?>
							</div>
						</div>
					</div>

					<div class="idcomp-outer tsk-detail">
						<input type="hidden" value="<?php echo $project['id']; ?>" name="data_project_ids[]">
						<div class="idcomp-inner-first" style="position: relative; height: 400px;">
							<div class="idcomp-heading">

								<span class="chart-heading">Tasks</span>
								<input type="hidden" value="<?php echo $project['id']; ?>" name="data_project_id" data-target="targetdiv_<?php echo $project['id']; ?>">


								<label class="custom-dropdown" style="width: 79%; margin-left: 10px;">
									<select class="aqua" name="workspace_id">
										<option value="0">All Workspaces</option>
								<?php
									// $workspaces = get_project_workspace($project['id']);
									$ws = null;
									if( isset($workspaces) && !empty($workspaces) ) {
										foreach( $workspaces as $k => $v ) {
											if( !empty($v['Workspace']['title']) )
												echo '<option value="'.$v['Workspace']['id'].'">'.strip_tags(ucfirst($v['Workspace']['title'])).'</option> ';
										}
									}
								?>
									</select>
								</label>
								<span class="task_btn_new pull-right">
								<!-- <a class="btn btn-primary btn-xs tipText" title="Task List" href="<?php //echo Router::url(array('controller' => 'entities', 'action' => 'task_list', 'project'=>$project['id'])); ?>"><i class="fa fa-tasks text-white">&nbsp;</i></a> -->
								</span>

							</div>
							<div style="width: 100%; display: block; height: 300px;">
								<div class="task_total"></div>
								<div class="idcomp-descp task_chart" id="task_chart">
									<div id="" class="count"><span style=" ">Count</span></div>
									<div id="targetdiv_<?php echo $project['id']; ?>" class="target"></div>
									<div style="display: block; padding: 10px;">Task Status</div>
								</div>
							</div>

						<script type="text/javascript" >
						$(function(){
							var val = $(this).val(),
								$el = $("#targetdiv_<?php echo $project['id']; ?>"),
								$task_total = $el.parent('.task_chart:first').prev('.task_total'),
								runAjaxs = true;

								if( runAjaxs ) {
									var project_id = '<?php echo $project['id']; ?>'
									$.ajax({
										url: $js_config.base_url + 'projects/objective_task_chart',
										data: $.param({
											project_id: project_id
										}),
										type: 'post',
										dataType: 'json',
										success: function (response) {
											if( response.success ) {
												runAjaxs = false;
												var total = response.content.counters;
												var values = response.content.counter_data;

												var html = $.chart_html(values, total, project_id)
												$el.html('').html(html)

												setTimeout(function(){
													$('.main_bar').each(function(){
														var h = $(this).data('height')
														$(this).css('height', h)
													})
												}, 100)

												$task_total.html("Tasks: " + response.content.total_elements)
											}
										}
									});
								}
						})
						</script>

						</div>
						<div class="idcomp-inner-second task_detail_wrapper" >

							<?php //$workspaces = get_project_workspace($project['id']); ?>
							<div class="idcomp-heading" style="display: block; float: left; width: 100%; ">
								<span style="padding: 6px 0px 5px 0; display: inline-block;">Tasks Details</span>
								<div class="pull-right sorting_wrapper text-right" id="sorting_wrapper_<?php echo $project['id']; ?>" style="width: 80%;">

									<input type="hidden" value="<?php echo $project['id']; ?>" name="sort_project_id" >
									<label class="custom-dropdown" style="min-width: 20%;">
										<select class="aqua sort_select" name="date_range">
											<option value="0">Select Period</option>
											<option value="1">Ending Soon</option>
											<option value="2">Ended Last</option>
											<option value="3">Schedule-Longest</option>
											<option value="4">Schedule-Shortest</option>
										</select>
									</label>

									<label class="custom-dropdown" style="min-width: 23%;">
										<select class="aqua sort_select" name="task_status">
											<option value="0">Task Status</option>
											<option value="1">Not Specified</option>
											<option value="2">Not Started</option>
											<option value="3">Progressing</option>
											<option value="4">Completed</option>
											<option value="5">Overdue</option>
										</select>
									</label>

									<label class="custom-dropdown" style="max-width: 40%; min-width: 40%;">
										<select class="aqua sort_select" name="key_result">
											<option value="0">All Workspaces</option>
											<?php
												if( isset($workspaces) && !empty($workspaces) ) {
													foreach( $workspaces as $k => $v ) {
														if( !empty($v['Workspace']['title']) )
														echo '<option value="'.$v['Workspace']['id'].'">'.strip_tags(ucfirst($v['Workspace']['title'])).'</option> ';
													}
												}
											?>
										</select>
									</label>
								</div>

							</div>
							<div class="idcomp-descp task_detail" id="task_detail">
								<?php //echo $this->element('../Projects/partials/objectives/objective_task_detail', ['project_id' => $project['id'], 'elements' => $task_data ]); ?>
							</div>
						</div>
					</div>

					<div style="" class="dfv_more">
						<a href="#" title="Show More" class="tipText text-bold show_dfv" style=" " data-pid="<?php echo $project['id']; ?>">Show More</a>
						<span class="t">(Decisions/Feedback/Votes)</span>
						<div class="loader hide"></div>
					</div>

					<div class="idcomp-outer dfv_wrapper" style="display: none;">

					</div>
				</div>
			</div>
		</div>
	</div> <!-- END objective_projects -->
<?php
	}

}
else {
?>
	<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px"  class="bg-blakish">No Project Selected
	</div>
<?php
}
 ?>
<script type="text/javascript" >
	$(function() {

		$('.show_dfv').each(function(){
			$(this).data('ajax', false)
		})

		$('body').delegate('.sort_select', 'change', function(event){
			$.check_range_status()
			var $wrapper = $(this).parents('.sorting_wrapper:first'),
				$date_range = $wrapper.find("[name=date_range]"),
				$task_status = $wrapper.find("[name=task_status]"),
				$key_result = $wrapper.find("[name=key_result]"),
				$task_detail = $wrapper.parents(".task_detail_wrapper:first").find('.task_detail'),
				run_ajax = true,
				params = {
					dateRange: $date_range.val(),
					taskStatus: $task_status.val(),
					keyResult: $key_result.val(),
					project_id: $wrapper.find('[name=sort_project_id]').val()
				};

			// $task_detail.html('<div class="loader"></div>')
			setTimeout(function(){
				if( run_ajax ) {
					$.ajax({
						url: $js_config.base_url + 'projects/objective_task_sort',
						data: $.param(params),
						type: 'post',
						global: true,
						dataType: 'json',
						success: function (response) {
							// console.log(response)
							$task_detail.html(response)
						}
					});
					run_ajax = false;
				}
			}, 1000)

		})


		$('body').delegate('[name=date_range]', 'change', function(event){
				$.check_range_status()
				$('[name=task_status]').prop("selectedIndex", 0)
		})

		var clicked = true;
		$('body').on( 'click', function(event) {
			if( clicked ){
				$.check_range_status()
				clicked = false;
			}
		})

		$('[name=workspace_id]').off('change').on( 'change', function(event) {

			var $that = $(this),
				val = $that.val(),
				$project_field = $(this).parent().prev('input[name=data_project_id]'),
				project_id = $project_field.val(),
				target = $project_field.data('target'),
				$el = $("#"+target),
				$task_total = $that.parents('.idcomp-heading:first').next('div').find('.task_total'),
				runAjax = true;
				uurl = '<?php echo SITEURL.'entities/task_list/project:'; ?>'+project_id;

				if(val !=0){
				 	$('.task_btn_new a').attr('href',uurl+'/status:0/workspace:'+val);
				}else{
					$('.task_btn_new a').attr('href',uurl );
				}

			if( runAjax ) {
				$.ajax({
					url: $js_config.base_url + 'projects/objective_task_chart',
					data: $.param({
						project_id: project_id,
						workspace_id: val
					}),
					type: 'post',
					dataType: 'json',
					success: function (response) {
						if( response.success ) {
							runAjax = false;
							var total = response.content.counters;
							var values = response.content.counter_data;

							var html = $.chart_html(values, total, project_id)
							$el.html('').html(html)
							setTimeout(function(){
								$('.main_bar').each(function(){
										var h = $(this).data('height')
										$(this).css('height', h)
								})
							}, 100)
							$task_total.html("Tasks: " + response.content.total_elements)
						}
					}
				});
			}
		})


})

</script>
<style>
.panels-workspace .mar-to {
	margin-top:5px;
}

.panels-workspace .mar-three {
	/* display: none; */
}

.panels-workspace .input-group-bg  .input-group-addon {
  background-color: #eee;
  color: #000;
  font-size: 14px;
  font-weight: 400;
  line-height: 1;
  padding: 5px 12px;
  text-align: center;
}
</style>