
<?php

$workspaces = get_project_workspace($project_id); ?>


		<div class="table-responsive">

			<table class="table">

		<?php

			if( isset($data) && !empty($data) ) {

				foreach($data as $index => $element_data ) {
					$element = $element_data['element'];
					$element_id = $element['id'];
					$area_id = $element['area_id'];
					$area_detail = $this->ViewModel->getAreaDetail( $area_id );
			?>
						<tr>
							<td width="40%">

								<ul class="list-unstyled ">
									<li>
										<div class="el-detail">
											<div class="detail-head">
												<?php
												$class_name = 'undefined';
													if( isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0 ) {
														if( ((isset( $element['start_date'] ) && !empty( $element['start_date'] )) && date( 'Y-m-d', strtotime( $element['start_date'] ) ) > date( 'Y-m-d' )  )  && $element['sign_off'] != 1 ) {
															$class_name = 'not_started';
														}
														else if( ( (isset( $element['end_date'] ) && !empty( $element['end_date'] )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) < date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
															$class_name = 'overdue';
														}
														else if( isset( $element['sign_off'] ) && !empty( $element['sign_off'] ) ) {
															$class_name = 'completed';
														}
														else if( (((isset( $element['end_date'] ) && !empty( $element['end_date'] )) && (isset( $element['start_date'] ) && !empty( $element['start_date'] ))) && (date( 'Y-m-d', strtotime( $element['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) >= date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
															$class_name = 'progressing';
														}
													}
													else {
														$class_name = 'undefined';
													}
												?>
												<div class="el-box element-<?php echo $class_name ; ?>">
													<a class="tipText" title="Open Task" href="<?php echo SITEURL.'entities/update_element/'.$element['id'].'#tasks' ?>"></a>
												</div>
												<h5><?php echo ucfirst($element['title']) ?></h5>
											</div>
											<div class="detail-body">
												<p>
													<span class=" "><?php echo ucfirst($area_detail['title']); ?></span>
												</p>
												<p>
												<?php
													if( isset($element['start_date']) && !empty($element['start_date']) ) {
												?>

												<span class="text-blakish">Start: </span>
												<span class="text-red" style="margin-right: 10px;"><?php
												echo date('d m, Y h:i A',strtotime($element['start_date']));
												//echo _displayDate($element['start_date']); ?></span>

												<?php
													}
													else {
												?>
													<span class="text-blakish">Start: </span>
													<span class="text-red" style="margin-right: 10px;">Unknown</span>
												<?php
													}
												?>
												</p>
												<p>
												<?php
													if( isset($element['end_date']) && !empty($element['end_date']) ) {
												?>

												<span class="text-blakish">End: </span>
												<span class="text-red"><?php
												echo date('d m, Y h:i A',strtotime($element['end_date']));
												//echo _displayDate($element['end_date']); ?></span>

												<?php
													}
													else {
												?>
													<span class="text-blakish">End: </span>
													<span class="text-red" style="margin-right: 10px;">Unknown</span>
												<?php
													}
												?>
												</p>
												<p>
													<span class="text-black">Last Update: </span>
													<span class="text-pure-red"><?php echo _displayDate(date('Y-m-d',strtotime($element['modified']))); ?></span>
												</p>
											</div>
										</div>
									</li>
								</ul>
							</td>

							<td width="20%">
								<?php
								    //pr($post);
									$staDate = $element['start_date'];
									if(isset($post['dateRange']) && !empty($post['dateRange'])){
									if($post['dateRange']==1)
										$staDate = date('Y-m-d');
									}

									$daysLeft = daysLeft($staDate, date('Y-m-d', strtotime($element['end_date'])));
									$remainingDays = 100 - $daysLeft;
									$day_text = "N/A";
									$stlo= "top:31px;";
									if(  $class_name == 'not_started' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element['start_date'])));
										$remainingDays = 100;
										$day_text = "Start in ".$daysLeft." days";
										$stlo= "top:23px;";
									}
									else if(  $class_name == 'progressing' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element['end_date'])));
										$day_text = "Due ".$daysLeft." days";
										$stlo= "top:23px;";
										if( $daysLeft > 100 ) $remainingDays = 100;
									}
									else if(  $class_name == 'completed' ) {
										$remainingDays = 100;
										$daysLeft = 0;
										$day_text = "100%";
									}
									else if(  $class_name == 'overdue' ) {
										$daysLeft = daysLeft( date('Y-m-d', strtotime($element['end_date'])), date('Y-m-d'));
										$day_text = "Overdue ".$daysLeft." days";
										$stlo= "top:23px;";
									}
								?>
								<div class="c100 p<?php echo $remainingDays; ?> small <?php echo $class_name ?>">
									<span style="<?php echo $stlo; ?>"><?php echo $day_text; ?> </span>
									<div class="slice">
										<div class="bar"></div>
										<div class="fill"></div>
									</div>
								</div>
							</td>

							<td width="40%" align="center" valign="middle" class="el-icons">
							<?php

								$status_short_term = $total_links = $total_notes = $total_docs = $total_mindmaps = $decision_short_term = $total_feedbacks = $total_votes = 0;

								$elements_details = null;

								$element_decisions = _element_decisions( $element_id, 'decision' );
								$element_feedbacks = _element_decisions( $element_id, 'feedback' );
								$element_statuses = _element_statuses( $element_id );

								$element_assets = element_assets( $element_id, true );

								$elements_details = array_merge( $element_assets, $element_decisions, $element_feedbacks, $element_statuses );


								$total_links 			= $elements_details['links'];
								$total_notes 			= $elements_details['notes'];
								$total_docs 			= $elements_details['docs'];
								$total_mindmaps 		= $elements_details['mindmaps'];
								$total_feedbacks 		= $elements_details['feedbacks'];
								$total_votes 			= $elements_details['votes'];

								$status_short_term 		= $elements_details['status_short_term'];
								$decision_short_term 	= $elements_details['decision_short_term'];

								$feedback_tiptext 	= $elements_details['feedback_tiptext'];
								$decision_tiptext 	= $elements_details['decision_tiptext'];
								$status_tiptext 	= $elements_details['status_tiptext'];

							?>
								<ul class="list-unstyled element-icons-list">
									<!-- <li class="istatus">
										<span class="label bg-mix"><?php echo $status_short_term; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#tasks"  data-original-title="<?php echo $status_tiptext; ?>" class="btn btn-xs bg-element tipText" data-original-title="<?php echo $status_tiptext; ?>"><i class="fa fa-exclamation"></i></span>
									</li> -->
									<li class="ico_links">
										<span class="label bg-mix "><?php echo $total_links; ?></span>
										<span data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText {is_blocked}"><i class="asset-all-icon linkwhite"></i></span>
									</li>
									<li class="inote">
										<span class="label bg-mix"><?php echo $total_notes; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText {is_blocked}"><i class="asset-all-icon notewhite"></i></span>
									</li>

									<li class="idoc">
										<span class="label bg-mix"><?php echo $total_docs; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText {is_blocked}"><i class="asset-all-icon documentwhite"></i></span>
									</li>
									<li class="imup">
										<span class="label bg-mix"><?php echo $total_mindmaps; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText {is_blocked}" title=" Mind Maps"><i class="asset-all-icon mindmapwhite"></i></span>
									</li>
									<li class="idiss">
										<span class="label bg-mix"><?php echo $decision_short_term; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#decisions"  data-original-title="<?php echo $decision_tiptext; ?>" class="btn btn-xs bg-orange tipText {is_permited}" data-original-title="<?php echo $decision_tiptext; ?>"><i class="asset-all-icon decisionwhite"></i></span>

									</li>

									<li class="ifeed">
										<span class="label bg-mix"><?php echo $total_feedbacks; ?></span>
										<span data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#feedbacks"  data-original-title=" Live Feedbacks" class="btn btn-xs bg-teal tipText {is_permited}" data-original-title=" Feedbacks"><i class="asset-all-icon feedbackwhite"></i></span>
									</li>

									<li class="ivote">
										<span class="label bg-mix"><?php echo $total_votes; ?></span>
										<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/<?php echo $element_id; ?>#votes"  data-original-title=" Live Votes" class="btn btn-xs bg-yellow tipText {is_permited}" data-original-title=" Votes"><i class="asset-all-icon votewhite"></i></span>

									</li>
								</ul>
							</td>
						</tr>


					<?php } ?>

				<?php }else{ ?>
						<tr>
							<td width="100%" style="border-top: medium none; text-align: center; font-size: 16px;" colspan="3" class="bg-blakish">No Tasks
							</td>
						</tr>
				<?php } ?>
			</table>
		</div>