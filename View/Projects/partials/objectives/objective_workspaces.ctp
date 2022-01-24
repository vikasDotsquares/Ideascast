
<div class=" ">
	<div class="table-responsive">
		<table class="table ">
		<?php
		if( isset($workspaces) && !empty($workspaces) ) {
			foreach($workspaces as $key => $value )  {
				$workspace = $value['workspaces'];
				if( !empty($workspace['wsp_title']) ) {
					$total_elements = 0;
					$taskStatusCount = $value[0];

					$total_completed = (isset($taskStatusCount['CMP']) && !empty($taskStatusCount['CMP'])) ? $taskStatusCount['CMP'] : 0;

					if(isset($taskStatusCount) && !empty($taskStatusCount)){
						$total_elements = $taskStatusCount['CMP'] + $taskStatusCount['NON'] + $taskStatusCount['PRG'] + $taskStatusCount['PND'] + $taskStatusCount['OVD'];
					}
			?>
				<?php

					$percent = 0;
					if( $total_elements > 0 ) {
						$percent = (( $total_completed/$total_elements ) * 100);
						$percent = ceil($percent);
					}
				?>

				<tr class="">
					<td width="40%">
						<label class="owp"><a href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspace['id'])); ?>" class=" tipText  " title="Open Workspace"><?php echo strip_tags(ucfirst($workspace['wsp_title'])); ?></a></label>

						<p class="text-muted date ">
							<span><b>Start Date:</b> <?php
							echo ( isset($workspace['start_date']) && !empty($workspace['start_date'])) ? date('d M Y', strtotime($workspace['start_date'])) : 'N/A';
							?></span>
						</p>

						<p class="text-muted date ">
							<span><b>End Date:</b> <?php
							echo ( isset($workspace['end_date']) && !empty($workspace['end_date'])) ? date('d M Y', strtotime($workspace['end_date'])) : 'N/A';
							?></span>
						</p>

						<p class="text-muted date-time">
							<span>Created: <?php echo _displayDate($workspace['created'], 'd M, Y'); ?></span>

						</p>
					</td>
					<td width="20%" style="vertical-align: middle ! important; text-align: right">
						Tasks Complete
					</td>
					<td class="" width="10%" style="vertical-align: middle ! important; text-align: left">
						<div class="completed"><?php echo $total_completed; ?></div>
					</td>
					<td width="30%" style="vertical-align: middle ! important;">

						<div class="process_wrapper">

								<div class="process_numbers">
									<span class="pull-left">0</span>
									<span class="pull-right"><?php echo $total_elements; ?></span>
								</div>

								<div class="process">
									<div class="process_bar">
										<div class="fill" style="width: <?php echo $percent; ?>%"></div>
									</div>
								</div>

							<div class="input_completion"><?php echo $percent; ?>%</div>
						</div>

					</td>
				</tr>

				<?php } ?>
			<?php } ?>

		<?php }else{ ?>
			<tr>
				<td class="no-data-found">No Workspaces</td>
			</tr>
		<?php } ?>

		</table>
	</div>
</div>